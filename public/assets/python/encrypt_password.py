
import json
import argparse
import string
import random
import base64
import shlex
import pymysql
import paramiko
import sys
from datetime import datetime
from Crypto.Cipher import AES
from Crypto.Util.Padding import pad
from vault_config import DB_CONFIG, AES_KEY, LINUX_ROOT_PASSWORD, DB_ROOT_PASSWORD, DB_ENCRYPTION_KEY

# ===== UTILITIES =====
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

# ===== SSH =====
def change_linux_password(ip_addr, target_user, new_password):
    print(f"[SSH] Login ke {ip_addr} sebagai root, ubah password user {target_user}", file=sys.stderr, flush=True)
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(ip_addr, username='root', password=LINUX_ROOT_PASSWORD, timeout=5)

        escaped = shlex.quote(f"{target_user}:{new_password}")
        command = f"echo {escaped} | chpasswd"
        stdin, stdout, stderr = ssh.exec_command(command)
        error = stderr.read().decode()
        ssh.close()

        if error:
            print(f"chpasswd error: {error}", file=sys.stderr, flush=True)
            return False
        return True
    except Exception as e:
        print(f"SSH error: {e}", file=sys.stderr, flush=True)
        return False

# ===== DATABASE =====
def change_db_password(ip_addr, target_user, new_password):
    print(f"[DB] Login ke {ip_addr} dan ubah password user DB {target_user}", file=sys.stderr, flush=True)
    try:
        db = pymysql.connect(host=ip_addr, user='root', password=DB_ROOT_PASSWORD)
        with db.cursor() as cur:
            cur.execute("ALTER USER %s@'%%' IDENTIFIED BY %s", (target_user, new_password))
        db.commit()
        db.close()
        return True
    except Exception as e:
        print(f"DB error: {e}", file=sys.stderr, flush=True)
        return False

# ===== SIMPAN (dengan AES_ENCRYPT) =====
def save_to_vault(identity_id, encrypted_password, updated_by):
    conn = pymysql.connect(**DB_CONFIG)
    try:
        with conn.cursor() as cur:
            cur.execute("SELECT id FROM password_vaults WHERE identity_id = %s", (identity_id,))
            row = cur.fetchone()

            if row:
                cur.execute("""
                    UPDATE password_vaults
                    SET encrypted_password = AES_ENCRYPT(%s, %s),
                        last_changed_by = %s,
                        last_changed_at = %s
                    WHERE identity_id = %s
                """, (encrypted_password, DB_ENCRYPTION_KEY, updated_by, datetime.now(), identity_id))
            else:
                cur.execute("SELECT id FROM password_vaults WHERE id LIKE 'p%%' ORDER BY id DESC LIMIT 1")
                last_id = cur.fetchone()
                new_number = int(last_id[0][1:]) + 1 if last_id else 1
                new_id = f"P{new_number:03d}"

                cur.execute("""
                    INSERT INTO password_vaults (id, identity_id, encrypted_password, last_changed_by, last_changed_at)
                    VALUES (%s, %s, AES_ENCRYPT(%s, %s), %s, %s)
                """, (new_id, identity_id, encrypted_password, DB_ENCRYPTION_KEY, updated_by, datetime.now()))
        conn.commit()
        print("Password terenkripsi dan disimpan ke database.", file=sys.stderr, flush=True)
    finally:
        conn.close()

# ===== AMBIL DATA =====
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

# ===== AUDIT LOG =====
def insert_audit_log(identity_id, event_type, user_id, triggered_by, note=None, ip_addr=None):
    conn = pymysql.connect(**DB_CONFIG)
    try:
        with conn.cursor() as cur:
            cur.execute("""
                INSERT INTO password_audit_logs (
                    identity_id, event_type, event_time, user_id,
                    triggered_by, actor_ip_addr, note, created_at, updated_at
                )
                VALUES (%s, %s, %s, %s, %s, %s, %s, NOW(), NOW())
            """, (
                identity_id,
                event_type,
                datetime.now(),
                user_id,
                triggered_by,
                ip_addr,
                note
            ))
        conn.commit()
    finally:
        conn.close()

# ...
# ===== JOB LOG =====
def insert_password_job_log(identity_id, status, message, job_type='rotate', triggered_by='system', actor_ip=None):
    conn = pymysql.connect(**DB_CONFIG)
    try:
        with conn.cursor() as cur:
            cur.execute("""
                INSERT INTO password_jobs (
                    identity_id, job_type, status, message,
                    triggered_by, actor_ip_addr, created_at, updated_at
                )
                VALUES (%s, %s, %s, %s, %s, %s, NOW(), NOW())
            """, (
                identity_id,
                job_type,
                status,  # HARUS salah satu dari: 'pending', 'running', 'done', 'failed'
                message[:255],
                triggered_by,
                actor_ip
            ))
        conn.commit()
    finally:
        conn.close()


# ===== PROSES SINGLE =====
def main(identity_id, updated_by, ip_addr=None):
    print(f"Mulai proses untuk identity {identity_id}", file=sys.stderr, flush=True)

    try:
        info = get_identity_info(identity_id)
        ip = info['ip']
        username = info['username']
        platform = info['platform']

        password = generate_password()
        print(f"Password baru: {password}", file=sys.stderr, flush=True)

        success = False
        if platform == 'linux':
            success = change_linux_password(ip, username, password)
        elif platform == 'database':
            success = change_db_password(ip, username, password)
        else:
            raise Exception(f"Platform {platform} tidak dikenali.")

        if not success:
            insert_password_job_log(identity_id, 'failed', 'Gagal mengubah password di server target.',
                                    'rotate', 'system' if updated_by == 1 else 'user', ip_addr)
            return {
                "identity_id": identity_id,
                "status": "error",
                "message": "Gagal mengubah password di server target."
            }

        encrypted = encrypt_aes192(password, AES_KEY)
        save_to_vault(identity_id, encrypted, updated_by)

        insert_audit_log(
            identity_id=identity_id,
            event_type='rotated',
            user_id=updated_by,
            triggered_by='system' if updated_by == 1 else 'user',
            note='Rotasi password otomatis oleh sistem' if updated_by == 1 else 'Rotasi password manual oleh user',
            ip_addr=ip_addr
        )

        insert_password_job_log(identity_id, 'done',
                                f"Password berhasil diubah untuk {username}@{ip}",
                                'rotate',
                                'system' if updated_by == 1 else 'user',
                                ip_addr)

        return {
            "identity_id": identity_id,
            "status": "success",
            "message": f"Password berhasil diubah untuk {username}@{ip}",
            "encrypted": encrypted.decode(),
            "plain_password": password
        }

    except Exception as e:
        insert_password_job_log(identity_id, 'failed', str(e),
                                'rotate', 'system' if updated_by == 1 else 'user', ip_addr)
        return {
            "identity_id": identity_id,
            "status": "error",
            "message": str(e)
        }

# ===== MULTIPLE LOOPING =====
def main_batch(identity_ids, updated_by, ip_addr=None):
    results = []
    for identity_id in identity_ids:
        try:
            result = main(identity_id, updated_by, ip_addr)
            results.append(result)
        except Exception as e:
            results.append({
                "identity_id": identity_id,
                "status": "error",
                "message": str(e)
            })
    print(json.dumps(results), flush=True)

# ===== ENTRY POINT =====
if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument('--identities', required=True, help="List of identity IDs in JSON format")
    parser.add_argument('--updated_by', type=int, required=True)
    parser.add_argument('--ip_addr', required=False)
    args = parser.parse_args()

    identity_list = json.loads(args.identities)
    main_batch(identity_list, args.updated_by, args.ip_addr)
