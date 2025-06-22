#generate_pass.py (Final Revised)

import secrets, string, base64, argparse, mysql.connector, paramiko, socket
from Crypto.Cipher import AES
from config import DB_CONFIG, SQL_ENCRYPTION_KEY, PYTHON_AES_KEY, SSH_CREDENTIALS


def pad(data):
    pad_len = 16 - len(data) % 16
    return data + chr(pad_len) * pad_len

def generate_password(length=14):
    chars = string.ascii_letters + string.digits + string.punctuation
    while True:
        pw = ''.join(secrets.choice(chars) for _ in range(length))
        if (any(c.islower() for c in pw) and any(c.isupper() for c in pw) and
            any(c.isdigit() for c in pw) and any(c in string.punctuation for c in pw)):
            return pw

def encrypt_aes192(plaintext):
    cipher = AES.new(PYTHON_AES_KEY, AES.MODE_CBC)
    ct_bytes = cipher.encrypt(pad(plaintext).encode())
    iv = base64.b64encode(cipher.iv).decode()
    ct = base64.b64encode(ct_bytes).decode()
    return f"{iv}:{ct}"

def get_admin_id_by_username(username):
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()
    cursor.execute("SELECT id FROM admin_accounts WHERE username=%s", (username,))
    result = cursor.fetchone()
    conn.close()
    return result[0] if result else None

def generate_identity_id():
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()
    cursor.execute("SELECT COUNT(*) FROM identities")
    count = cursor.fetchone()[0] + 1
    conn.close()
    return f"ID{count:03d}"

def generate_vault_id():
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()
    cursor.execute("SELECT COUNT(*) FROM password_vaults")
    count = cursor.fetchone()[0] + 1
    conn.close()
    return f"VAULT{count:03d}"

def insert_identity(identity_id, admin_id, platform_id, hostname, username, functionality, ip_addr_srv):
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()
    query = """
    INSERT INTO identities (id, admin_id, platform_id, hostname, username, functionality, ip_addr_srv)
    VALUES (%s, %s, %s, %s, %s, %s, %s)
    """
    cursor.execute(query, (identity_id, admin_id, platform_id, hostname, username, functionality, ip_addr_srv))
    conn.commit()
    conn.close()

def store_password(vault_id, identity_id, encrypted):
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()
    query = """
    INSERT INTO password_vaults (
        id, identity_id, encrypted_password, last_changed_by, last_changed_ip
    ) VALUES (
        %s, %s, AES_ENCRYPT(%s, %s), %s, %s
    )
    """
    ip = get_local_ip()
    cursor.execute(query, (vault_id, identity_id, encrypted, SQL_ENCRYPTION_KEY, 'system', ip))
    conn.commit()
    conn.close()

def update_linux_password(linux_user, new_password, target_ip):
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(
        target_ip,
        username=SSH_CREDENTIALS['username'],
        password=SSH_CREDENTIALS['password']
    )
    ssh.exec_command(f"echo '{linux_user}:{new_password}' | chpasswd")
    ssh.close()

def update_mariadb_password(db_user, new_password):
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()
    query = f"ALTER USER '{db_user}'@'%' IDENTIFIED BY %s"
    cursor.execute(query, (new_password,))
    conn.commit()
    conn.close()

def get_local_ip():
    try:
        hostname = socket.gethostname()
        return socket.gethostbyname(hostname)
    except:
        return '127.0.0.1'
    
def run_from_laravel(params):
    class Args:
        pass
    args = Args()
    for k, v in params.items():
        setattr(args, k, v)

    admin_id = get_admin_id_by_username(args.admin_username)
    identity_id = generate_identity_id()
    vault_id = generate_vault_id()
    insert_identity(identity_id, admin_id, args.platform_id, args.name, args.username, args.functionality, args.ip_addr_srv)

    new_pw = generate_password()
    encrypted = encrypt_aes192(new_pw)
    store_password(vault_id, identity_id, encrypted)

    if args.target in ["linux", "all"]:
        update_linux_password(args.linux_user, new_pw, args.ip_addr_srv)
    if args.target in ["mariadb", "all"]:
        update_mariadb_password(args.db_user, new_pw)

    return {"message": "Success", "identity_id": identity_id, "vault_id": vault_id, "plaintext_password": new_pw}

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Sync password ke Linux dan/atau MariaDB.")
    parser.add_argument("--admin_username", required=True)
    parser.add_argument("--platform_id", required=True)
    parser.add_argument("--name", required=True)
    parser.add_argument("--username", required=True)
    parser.add_argument("--functionality", required=True)
    parser.add_argument("--ip_addr_srv", required=True)
    parser.add_argument("--target", choices=["linux", "mariadb", "all"], required=True)
    parser.add_argument("--linux_user")
    parser.add_argument("--db_user")

    args = parser.parse_args()

    admin_id = get_admin_id_by_username(args.admin_username)
    identity_id = generate_identity_id()
    vault_id = generate_vault_id()

    insert_identity(identity_id, admin_id, args.platform_id, args.name, args.username, args.functionality, args.ip_addr_srv)

    new_pw = generate_password()
    encrypted = encrypt_aes192(new_pw)

    store_password(vault_id, identity_id, encrypted)

    if args.target in ["linux", "all"]:
        if not args.linux_user: 
            raise ValueError("Parameter --linux_user diperlukan untuk target linux")
        update_linux_password(args.linux_user, new_pw, args.ip_addr_srv)

    if args.target in ["mariadb", "all"]:
        if not args.db_user:
            raise ValueError("Parameter --db_user diperlukan untuk target mariadb")
        update_mariadb_password(args.db_user, new_pw)

    print(f"[✓] Password untuk {args.name} berhasil disimpan dan disinkronkan.")