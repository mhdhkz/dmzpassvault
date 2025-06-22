🔐 Password Vault System

Sistem ini dirancang untuk:

Membuat dan menyimpan password secara terenkripsi (AES-192)

Mengelola rotasi password untuk server Linux dan MariaDB

Menyediakan dekripsi aman saat dibutuhkan

Mencatat setiap aktivitas akses dan perubahan password

🚀 Fitur Utama

Enkripsi dua lapis: Python (AES-192) + MariaDB (AES_ENCRYPT)

Sinkronisasi langsung password ke server target

Audit log setiap akses dan rotasi password

Rotasi massal melalui cron (otomatis)

🛠️ Instalasi

1. Clone Repository

git clone https://github.com/kamu/password-vault.git
cd password-vault

2. Instalasi Dependensi

pip install mysql-connector-python paramiko pycryptodome

3. Konfigurasi

Edit config.py:

DB_CONFIG = { ... }                # koneksi MariaDB
SQL_ENCRYPTION_KEY = '...'        # kunci enkripsi di SQL
PYTHON_AES_KEY = b'...'           # 24 byte key AES-192
SSH_CREDENTIALS = { ... }         # kredensial default SSH

⚙️ Cara Pemakaian

📌 1. Simpan Password Baru ke Vault & Sinkronisasi

🔹 Untuk server Linux

python encrypt_and_store.py \
  --admin_username admin \
  --platform_id PF001 \
  --name TEST \
  --username dhika \
  --functionality "WEB SERVER" \
  --ip_addr_srv 192.168.102.128 \
  --target linux \
  --linux_user dhika

🔹 Untuk server Database (MariaDB)

python encrypt_and_store.py \
  --admin_username admin \
  --platform_id PF002 \
  --name TESTDB \
  --username dhika \
  --functionality "DB SERVER" \
  --ip_addr_srv 127.0.0.1 \
  --target mariadb \
  --db_user dhika

📌 2. Dekripsi Password

python decrypt_and_retrieve.py --identity_id ID001

Menampilkan password terakhir dari vault, mencatat last_accessed_at dan password_audit_logs

📌 3. Rotasi Semua Password Secara Otomatis

python rotate_password.py

Akan menyesuaikan rotasi hanya ke Linux atau MariaDB sesuai platform.

🧱 Struktur Tabel Penting

admin_accounts – akun admin yang menyimpan

identities – daftar server/user terdaftar

password_vaults – password hasil enkripsi dua lapis

password_audit_logs – log akses/update password

password_jobs – log rotasi massal otomatis

platforms – daftar platform (Linux, MariaDB, dll)

👨‍💻 Catatan Developer

Script menggunakan kombinasi enkripsi kuat (AES-192) dan penyimpanan aman

Pastikan server tujuan bisa diakses oleh SSH user default yang didefinisikan di config.py

Gunakan cron di Linux untuk menjadwalkan rotate_password.py

📄 Lisensi

MIT License

PS C:\Users\Admin> node.exe --version
v22.16.0
PS C:\Users\Admin> npm --version
11.4.2
PS C:\Users\Admin>