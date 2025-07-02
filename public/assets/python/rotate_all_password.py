import pymysql
from vault_config import DB_CONFIG, AES_KEY, LINUX_ROOT_PASSWORD, DB_ROOT_PASSWORD
from encrypt_password import (
    get_identity_info,
    generate_password,
    encrypt_aes192,
    change_linux_password,
    change_db_password,
    save_to_vault,
    AES_KEY
)

def get_all_identity_ids():
    conn = pymysql.connect(**DB_CONFIG)
    try:
        with conn.cursor() as cur:
            cur.execute("SELECT id FROM identities")
            rows = cur.fetchall()
            return [row[0] for row in rows]
    finally:
        conn.close()

def rotate_identity(identity_id):
    print(f"Rotasi password untuk: {identity_id}")
    try:
        info = get_identity_info(identity_id)
        ip = info['ip']
        username = info['username']
        platform = info['platform']

        password = generate_password()
        print(f"Password baru: {password}")

        if platform == 'linux':
            success = change_linux_password(ip, username, password)
        elif platform == 'database':
            success = change_db_password(ip, username, password)
        else:
            print(f"Platform tidak dikenali: {platform}")
            return

        if success:
            encrypted = encrypt_aes192(password, AES_KEY)
            save_to_vault(identity_id, encrypted)
        else:
            print(f"Gagal mengubah password untuk {identity_id}")
    except Exception as e:
        print(f"Error pada {identity_id}: {e}")

def main():
    identity_ids = get_all_identity_ids()
    print(f"Total identity ditemukan: {len(identity_ids)}")
    for identity_id in identity_ids:
        rotate_identity(identity_id)

if __name__ == "__main__":
    main()
