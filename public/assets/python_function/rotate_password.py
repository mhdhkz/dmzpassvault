#rotate_pass.py (Final Revised)
import base64, secrets, string, socket, traceback
import mysql.connector
import paramiko
from Crypto.Cipher import AES
from datetime import datetime
from config import DB_CONFIG, SQL_ENCRYPTION_KEY, PYTHON_AES_KEY, SSH_CREDENTIALS

assert len(PYTHON_AES_KEY) == 24, "AES-192 key harus 24 byte"

def pad(data):
    pad_len = 16 - len(data) % 16
    return data + chr(pad_len) * pad_len

def encrypt_aes192(plaintext):
    cipher = AES.new(PYTHON_AES_KEY, AES.MODE_CBC)
    ct_bytes = cipher.encrypt(pad(plaintext).encode())
    iv = base64.b64encode(cipher.iv).decode()
    ct = base64.b64encode(ct_bytes).decode()
    return f"{iv}:{ct}"

def generate_password(length=14):
    chars = string.ascii_letters + string.digits + string.punctuation
    while True:
        pw = ''.join(secrets.choice(chars) for _ in range(length))
        if (any(c.islower() for c in pw) and any(c.isupper() for c in pw)
            and any(c.isdigit() for c in pw) and any(c in string.punctuation for c in pw)):
            return pw

def get_local_ip():
    try:
        return socket.gethostbyname(socket.gethostname())
    except:
        return '127.0.0.1'

def get_next_vault_id():
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()
    cursor.execute("SELECT COUNT(*) FROM password_vaults")
    count = cursor.fetchone()[0] + 1
    conn.close()
    return f"VAULT{count:03d}"

def log_audit_event(identity_id, event_type, triggered_by='cronjob', actor_ip=None):
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()
    cursor.execute("""
        INSERT INTO password_audit_logs (identity_id, event_type, triggered_by, actor_ip_addr)
        VALUES (%s, %s, %s, %s)
    """, (identity_id, event_type, triggered_by, actor_ip))
    conn.commit()
    conn.close()

def log_password_job(identity_id, status):
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()
    cursor.execute("""
        INSERT INTO password_jobs (identity_id, scheduled_at, status)
        VALUES (%s, %s, %s)
    """, (identity_id, datetime.now(), status))
    conn.commit()
    conn.close()

def update_linux_password(host, linux_user, new_password):
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(
        hostname=host,
        username=SSH_CREDENTIALS['username'],
        password=SSH_CREDENTIALS['password']
    )
    stdin, stdout, stderr = ssh.exec_command(f"echo '{linux_user}:{new_password}' | chpasswd")
    err = stderr.read().decode()
    ssh.close()
    if err:
        raise Exception(err)

def update_mariadb_password(conn, db_user, new_password):
    cursor = conn.cursor()
    cursor.execute(f"ALTER USER '{db_user}'@'%' IDENTIFIED BY %s", (new_password,))
    conn.commit()

def rotate_all_passwords():
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor(dictionary=True)
    cursor.execute("""
        SELECT i.*, p.name AS platform_name
        FROM identities i
        JOIN platforms p ON i.platform_id = p.id
    """)
    identities = cursor.fetchall()
    conn.close()

    for identity in identities:
        identity_id = identity['id']
        hostname = identity['hostname']
        target_ip = identity['ip_addr_srv']
        username = identity['username']
        platform = identity['platform_name'].lower()
        ip = get_local_ip()
        job_status = 'completed'

        new_pw = generate_password()
        encrypted = encrypt_aes192(new_pw)
        vault_id = get_next_vault_id()

        try:
            conn = mysql.connector.connect(**DB_CONFIG)
            cursor = conn.cursor()
            cursor.execute("""
                INSERT INTO password_vaults (
                    id, identity_id, encrypted_password, last_changed_by, last_changed_ip
                ) VALUES (
                    %s, %s, AES_ENCRYPT(%s, %s), %s, %s
                )
            """, (vault_id, identity_id, encrypted, SQL_ENCRYPTION_KEY, 'cronjob', ip))
            conn.commit()
            conn.close()
            print(f"[✓] Vault updated for {hostname} ({identity_id})")
        except Exception as e:
            print(f"[!] Gagal menyimpan ke vault: {e}")
            traceback.print_exc()
            job_status = 'failed'
            continue

        # Platform-specific update
        if 'linux' in platform:
            try:
                update_linux_password(target_ip, username, new_pw)
                print(f"[✓] Password Linux user '{username}' berhasil diganti.")
            except Exception as e:
                print(f"[!] Gagal update Linux: {e}")
                traceback.print_exc()
                job_status = 'failed'

        if 'mariadb' in platform:
            try:
                conn2 = mysql.connector.connect(**DB_CONFIG)
                update_mariadb_password(conn2, username, new_pw)
                conn2.close()
                print(f"[✓] Password MariaDB user '{username}' berhasil diganti.")
            except Exception as e:
                print(f"[!] Gagal update MariaDB: {e}")
                traceback.print_exc()
                job_status = 'failed'

        log_audit_event(identity_id, 'updated', 'cronjob', ip)
        log_password_job(identity_id, job_status)

if __name__ == "__main__":
    rotate_all_passwords()
