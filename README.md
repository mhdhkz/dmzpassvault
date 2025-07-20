# 🔐 DMZ Password Vault — Password Management System

This project was developed as part of my undergraduate thesis. While the system is functional, I fully acknowledge that it may still contain bugs and potential security vulnerabilities. I welcome and encourage feedback, discussions, or suggestions for improvements to make this application better and more secure.

**DMZ Password Vault** is an integrated web application built with Laravel and Python to **securely, automatically, and centrally manage server and database credentials**. It is designed with multi-layer encryption and a comprehensive audit trail system, aligned with **CIS (Center for Internet Security)** standards.

---

## 🎯 Objectives

The system is designed to:

- 🔁 Automatically rotate user passwords for Rocky Linux and MariaDB platforms.
- 🔐 Store credentials using **double-layer encryption** via AES-192 (Python) and `AES_ENCRYPT()` (MariaDB).
- 📬 Allow users to request password access with a built-in approval mechanism.
- 📜 Record all password-related activities for auditing and security purposes.

---

## 🧰 Technologies Used

| Component      | Technology                                       |
| -------------- | ------------------------------------------------ |
| Framework      | Laravel 12 with PHP 8.3.16                       |
| Backend        | Python 3.11                                      |
| Frontend       | Blade, Bootstrap 5, SweetAlert2, DataTables, etc |
| Database       | MariaDB Community Edition 11.6.2                 |
| Encryption     | AES-192 (Python) + `AES_ENCRYPT()` (MariaDB)     |
| Authentication | Laravel Jetstream / Fortify                      |
| SSH Connection | Paramiko (Python)                                |
| Scheduler      | Laravel Task Scheduler + Windows Task Scheduler  |

---

## 📋 Main Features

### ✅ 1. User Roles & Management

- Three roles available: `admin`, `user`, and `system`.
- `admin` role has full access to all features.
- `user` role is limited to:
  - Viewing the dashboard
  - Viewing the identity list
  - Submitting password access requests
  - Viewing, editing, and deleting their own requests
  - Decrypting passwords if their request has been approved
- `system` role is reserved for automated tasks and system logging.

### 🔐 2. Server & Credential (Identity) Management

- Add, edit, and delete server or database accounts.
- Filter by hostname, IP address, platform type, and function.

### 🧪 3. Password Generation & Encryption

- Passwords are generated following CIS standards.
- Backend process:
  1. A secure, random password is generated.
  2. The new password is applied to the server or database.
  3. The target system's password is updated via SSH or SQL.
  4. The password is encrypted using AES-192 in Python.
  5. Then it is re-encrypted using `AES_ENCRYPT()` in MariaDB.
  6. The fully encrypted password is stored in the database.

### 📨 4. Vault Access Request

- Users may request access to one or more identities.
- Each request includes a validity period (`start_at`, `end_at`) and a unique ID (`REQYYMMDDNNN`).
- Admins can approve, reject, or delete submitted requests.
- Password decryption is only permitted if the request is active and approved.
- Backend flow:
  - The system queries the encrypted password from the vault.
  - If found, the password is decrypted at the database level.
  - The result is then decrypted again using the Python script.
  - The final password is displayed for the user to access the system.

### 📊 5. Dashboard & Monitoring

- Displays statistics on users, identities, password jobs, requests, and logs.
- Includes pie charts for request status overview.
- Bar charts show activity trends over the last 10 days.

### 🧾 6. Audit Trail & Logging

- Logs every sensitive action, including:
  - Password creation, rotation, access, and decryption.
- Each log contains: `event_type`, `note`, `user_id`, `ip_address`, and `identity_id`.

---

## ⏱️ Automatic Password Rotation

- Passwords are automatically rotated if:
  - The request access period has expired, or
  - The password is older than 7 days.
- Scheduled via `php artisan schedule:run` and triggered by Windows Task Scheduler.

---

## ⚖️ Rules & Conditions

| Action                    | Condition                                                   |
| ------------------------- | ----------------------------------------------------------- |
| Generate Password         | Only accessible by `admin`.                                 |
| Decrypt Password          | Only allowed if the request is `approved` and by the owner. |
| Edit/Change User Password | By `admin` or the user themselves.                          |
| Delete User               | Only by `admin`, not allowed to delete themselves.          |
| Auto Rotation             | Triggered on expiration or password age > 7 days.           |
| View Vault                | Only visible to the requester during the active period.     |
| Identity Access           | Governed by role and platform mapping.                      |

---

## 🔒 Security Mechanism

- Passwords are **never stored in plaintext**.
- Double-layer encryption: AES-192 in Python → `AES_ENCRYPT()` in MariaDB.
- Access is granted only via valid, approved requests.
- All operations are logged with timestamps, user info, and IP addresses.

---

## 📁 Key Directory Structure

| Directory/File                | Description                                           |
| ----------------------------- | ----------------------------------------------------- |
| `resources/views/`            | Blade templates for vault, identity, dashboard, forms |
| `app/Http/Controllers/`       | Laravel controllers                                   |
| `app/Models/`                 | Eloquent models                                       |
| `resources/assets/js/`        | JavaScript modules (DataTables, validation, modals)   |
| `scripts/encrypt_password.py` | Python script for password generation and encryption  |
| `scripts/vault_config.py`     | Python `.env` config and AES key handling             |

---

## 🛠️ Requirements

### Server

- PHP >= 8.1
- Composer
- MariaDB >= 11.6.x
- Node.js & npm
- Python 3.11
- Windows Task Scheduler / cron (Linux alternative)

### Python Libraries

- paramiko==2.12.0
- cryptography==40.0.2
- pymysql==1.1.0
- python-dotenv==1.0.1
- pycryptodome==3.20.0

> All Python dependencies are listed in `requirements.txt`.

---

## 🚀 Installation & Usage

```bash
# Laravel Setup
composer install
php artisan key:generate
php artisan migrate --seed
yarn && yarn build

# Python Setup
pip install -r requirements.txt
python scripts/encrypt_password.py --help

# Scheduler
php artisan schedule:work
# or schedule via Windows Task Scheduler every 1 minute
```

---

## 📞 Contact & Contribution

Interested in contributing or discussing improvements? Feel free to reach out via dhikamahendra789@outlook.co.id or create an issue/pull request on this repository.
