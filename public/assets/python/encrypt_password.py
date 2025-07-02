import argparse
import string
import random
import base64
import shlex
import pymysql
import paramiko
from Crypto.Cipher import AES
from Crypto.Util.Padding import pad
from datetime import datetime
from vault_config import DB_CONFIG, AES_KEY, LINUX_ROOT_PASSWORD, DB_ROOT_PASSWORD


# ===== UTILITY =====
def generate_password(length=14):
    chars = string.ascii_letters + string.digits + string.punctuation
    while True:
        pwd = ''.join(random.choices(chars, k=length))
        if (any(c.islower() for c in pwd)
            and any(c.isupper() for c in pwd)
            and any(c.isdigit() for c in pwd)
            and any(c in string.punctuation for c in pwd)):
            return pwd

def encrypt_aes192(plaintext: str, key: bytes) -> bytes:
    cipher = AES.new(key, AES.MODE_CBC)
    ct_bytes = cipher.encrypt(pad(plaintext.encode('utf-8'), AES.block_size))
    return base64.b64encode(cipher.iv + ct_bytes)

# ===== AKSI GANTI PASSWORD SERVER =====
def change_linux_password(ip_addr, target_user, new_password):
    print(f"[SSH] Login ke {ip_addr} sebagai root, ubah password user {target_user}")
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(ip_addr, username='root', password=LINUX_ROOT_PASSWORD, timeout=5)

        # Escape username:password dengan shlex.quote
        escaped_combo = shlex.quote(f"{target_user}:{new_password}")
        command = f"echo {escaped_combo} | chpasswd"

        stdin, stdout, stderr = ssh.exec_command(command)
        error = stderr.read().decode()
        if error:
            print(f"❌ chpasswd error: {error}")
            ssh.close()
            return False

        ssh.close()
        return True
    except Exception as e:
        print(f"❌ SSH error: {e}")
        return False

def change_db_password(ip_addr, target_user, new_password):
    print(f"[DB] Login ke {ip_addr} dan ubah password user DB {target_user}")
    try:
        db = pymysql.connect(host=ip_addr, user='root', password=DB_ROOT_PASSWORD)
        with db.cursor() as cur:
            # Gunakan parameterized query → aman dari karakter spesial
            query = "ALTER USER %s@'%%' IDENTIFIED BY %s"
            cur.execute(query, (target_user, new_password))
            db.commit()
        db.close()
        return True
    except Exception as e:
        print(f"❌ DB error: {e}")
        return False



# ===== SIMPAN KE VAULT =====
def save_to_vault(identity_id, encrypted_password, updated_by=1):
    conn = pymysql.connect(**DB_CONFIG)
    try:
        with conn.cursor() as cur:
            cur.execute("SELECT id FROM password_vaults WHERE identity_id = %s", (identity_id,))
            row = cur.fetchone()

            if row:
                cur.execute("""
                    UPDATE password_vaults
                    SET encrypted_password = %s,
                        last_changed_by = %s,
                        last_changed_at = %s
                    WHERE identity_id = %s
                """, (encrypted_password, updated_by, datetime.now(), identity_id))
            else:
                cur.execute("SELECT id FROM password_vaults WHERE id LIKE 'p%%' ORDER BY id DESC LIMIT 1")
                last_id = cur.fetchone()
                new_number = int(last_id[0][1:]) + 1 if last_id else 1
                new_id = f"p{new_number:03d}"

                cur.execute("""
                    INSERT INTO password_vaults (id, identity_id, encrypted_password, last_changed_by, last_changed_at)
                    VALUES (%s, %s, %s, %s, %s)
                """, (new_id, identity_id, encrypted_password, updated_by, datetime.now()))
        conn.commit()
        print("✅ Password terenkripsi dan disimpan ke database.")
    finally:
        conn.close()

# ===== AMBIL DATA DARI IDENTITIES =====
def get_identity_info(identity_id):
    conn = pymysql.connect(**DB_CONFIG)
    try:
        with conn.cursor() as cur:
            cur.execute("""
                SELECT i.ip_addr_srv, i.username, p.name
                FROM identities i
                JOIN platforms p ON i.platform_id = p.id
                WHERE i.id = %s
            """, (identity_id,))
            result = cur.fetchone()
            if not result:
                raise Exception(f"Identity {identity_id} tidak ditemukan.")
            return {
                'ip': result[0],
                'username': result[1],
                'platform': result[2].lower()
            }
    finally:
        conn.close()

# ===== MAIN LOGIC =====
def main(identity_id):
    print(f"🚀 Mulai proses untuk identity {identity_id}")
    info = get_identity_info(identity_id)
    ip = info['ip']
    username = info['username']
    platform = info['platform']

    password = generate_password()
    print(f"🔐 Password baru: {password}")

    success = False
    if platform == 'linux':
        success = change_linux_password(ip, username, password)
    elif platform == 'database':
        success = change_db_password(ip, username, password)
    else:
        print(f"❌ Platform {platform} tidak dikenali.")
        return

    if success:
        encrypted = encrypt_aes192(password, AES_KEY)
        save_to_vault(identity_id, encrypted)
    else:
        print("❌ Gagal mengubah password di server target.")

# ===== CLI ENTRY =====
if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Generate, update, encrypt, and store password.")
    parser.add_argument("--identity", required=True, help="ID dari identity (contoh: ID001)")

    args = parser.parse_args()
    main(args.identity)
