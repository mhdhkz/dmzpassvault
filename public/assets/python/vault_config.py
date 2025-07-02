# vault_config.py

import os
from dotenv import load_dotenv

load_dotenv()

DB_CONFIG = {
    "host": os.getenv("DB_HOST"),
    "user": os.getenv("DB_USER"),
    "password": os.getenv("DB_PASSWORD"),
    "database": os.getenv("DB_NAME")
}

AES_KEY = os.getenv("AES_KEY").encode("utf-8")
LINUX_ROOT_PASSWORD = os.getenv("LINUX_ROOT_PASSWORD")
DB_ROOT_PASSWORD = os.getenv("DB_ROOT_PASSWORD")
