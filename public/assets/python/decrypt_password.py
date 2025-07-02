import argparse
import base64
import pymysql
import json
from Crypto.Cipher import AES
from Crypto.Util.Padding import unpad
from vault_config import DB_CONFIG, AES_KEY


# ===== FUNGSI DEKRIPSI =====
def decrypt_aes192(encrypted_data: bytes, key: bytes) -> str:
    raw = base64.b64decode(encrypted_data)
    iv = raw[:16]
    ct = raw[16:]
    cipher = AES.new(key, AES.MODE_CBC, iv)
    pt = unpad(cipher.decrypt(ct), AES.block_size)
    return pt.decode('utf-8')

# ===== AMBIL DATA DARI DATABASE =====
def get_encrypted_password(identity_id):
    conn = pymysql.connect(**DB_CONFIG)
    try:
        with conn.cursor() as cur:
            cur.execute("SELECT encrypted_password FROM password_vaults WHERE identity_id = %s", (identity_id,))
            row = cur.fetchone()
            if not row:
                raise Exception("Data tidak ditemukan.")
            return row[0]
    finally:
        conn.close()

# ===== MAIN LOGIC =====
def main(identity_id):
    try:
        encrypted = get_encrypted_password(identity_id)
        decrypted = decrypt_aes192(encrypted, AES_KEY)
        print(json.dumps({
            "status": "ok",
            "decrypted": decrypted,
            "message": "Password berhasil didekripsi"
        }))
    except Exception as e:
        print(json.dumps({
            "status": "error",
            "message": str(e)
        }))

# ===== CLI ENTRY =====
if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Decrypt password dari database.")
    parser.add_argument("--identity", required=True, help="ID dari identity (contoh: ID001)")
    args = parser.parse_args()
    main(args.identity)
