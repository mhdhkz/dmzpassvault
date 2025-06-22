#retrieve_pass.py (Final Revised)
import base64
import mysql.connector
from Crypto.Cipher import AES
from config import DB_CONFIG, SQL_ENCRYPTION_KEY, PYTHON_AES_KEY, SSH_CREDENTIALS
import socket

def get_local_ip():
    try:
        return socket.gethostbyname(socket.gethostname())
    except:
        return '127.0.0.1'

def unpad(data):
    pad_len = data[-1]
    return data[:-pad_len]

def decrypt_aes192(encrypted_combined):
    iv_b64, ct_b64 = encrypted_combined.split(":")
    iv = base64.b64decode(iv_b64)
    ct = base64.b64decode(ct_b64)

    cipher = AES.new(PYTHON_AES_KEY, AES.MODE_CBC, iv)
    decrypted = cipher.decrypt(ct)
    return unpad(decrypted).decode()

def log_audit_event(identity_id, event_type, triggered_by='system', actor_ip=None):
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()
    cursor.execute("""
        INSERT INTO password_audit_logs (identity_id, event_type, triggered_by, actor_ip_addr)
        VALUES (%s, %s, %s, %s)
    """, (identity_id, event_type, triggered_by, actor_ip))
    conn.commit()
    conn.close()

def update_last_accessed(vault_id):
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()
    cursor.execute("""
        UPDATE password_vaults
        SET last_accessed_at = NOW()
        WHERE id = %s
    """, (vault_id,))
    conn.commit()
    conn.close()

def get_latest_decrypted_from_db(identity_id):
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()
    cursor.execute("""
        SELECT id, AES_DECRYPT(encrypted_password, %s)
        FROM password_vaults
        WHERE identity_id = %s
        ORDER BY last_changed_at DESC
        LIMIT 1
    """, (SQL_ENCRYPTION_KEY, identity_id))
    result = cursor.fetchone()
    conn.close()

    if result and result[1]:
        vault_id = result[0]
        decrypted_layer1 = result[1].decode()
        if ":" not in decrypted_layer1:
            print("[!] Format tidak valid")
            return None
        update_last_accessed(vault_id)
        ip = get_local_ip()
        log_audit_event(identity_id, 'accessed', triggered_by='system', actor_ip=ip)
        return decrypt_aes192(decrypted_layer1)
    else:
        print("[!] Tidak ada data ditemukan")
        return None

def run_from_laravel(identity_id):
    pw = get_latest_decrypted_from_db(identity_id)
    return {"decrypted_password": pw}
    
if __name__ == "__main__":
    import argparse
    parser = argparse.ArgumentParser(description="Decrypt latest password by identity_id.")
    parser.add_argument("--identity_id", required=True, help="Contoh: ID001")

    args = parser.parse_args()

    decrypted = get_latest_decrypted_from_db(args.identity_id)
    if not decrypted:
        print("[!] Password tidak ditemukan atau gagal didekripsi.")
    else:
        print(f"[✓] Password terdekripsi: {decrypted}")