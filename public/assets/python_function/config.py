# config.py

DB_CONFIG = {
    'host': 'localhost',
    'user': 'vault_adm',
    'password': 'V@ult_4dm12345',
    'database': 'dmzpassvault',
    'charset': 'utf8mb4',
    'collation' : 'utf8mb4_general_ci'
}

SQL_ENCRYPTION_KEY = 'mariadb_sql_passkey'

PYTHON_AES_KEY = b'Hosh!zor4Yozor4Aozor4567'  # 24-byte AES-192 key

SSH_CREDENTIALS = {
    'username': 'root',
    'password': 'dhika123'
}
