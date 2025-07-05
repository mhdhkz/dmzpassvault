import os
import subprocess
import pymysql
import datetime
from vault_config import DB_CONFIG

def insert_job_log(start_time):
    conn = pymysql.connect(**DB_CONFIG)
    try:
        with conn.cursor() as cursor:
            cursor.execute("""
              INSERT INTO password_jobs (job_type, status, started_at, created_at, updated_at)
              VALUES (%s, %s, %s, %s, %s)
          """, ('rotate_all', 'running', start_time, start_time, start_time))
            conn.commit()
            return cursor.lastrowid
    finally:
        conn.close()

def update_job_log(job_id, end_time, total_success, total_failed):
    conn = pymysql.connect(**DB_CONFIG)
    try:
        with conn.cursor() as cursor:
            cursor.execute("""
              UPDATE password_jobs
              SET status = %s, finished_at = %s, total_success = %s, total_failed = %s, updated_at = %s
              WHERE id = %s
          """, ('done', end_time, total_success, total_failed, end_time, job_id))
            conn.commit()
    finally:
        conn.close()

def run_rotation():
    start_time = datetime.datetime.now()
    job_id = insert_job_log(start_time)

    try:
        script_path = os.path.join(os.path.dirname(__file__), 'rotate_all_password.py')
        result = subprocess.run(['python', script_path], capture_output=True, text=True)
        output = result.stdout + result.stderr

        print("Output subprocess:")
        print(output)

        total_success = output.count("[OK] Password berhasil digenerate")
        total_failed = output.count("Gagal mengubah password") + output.count("Error pada")

        update_job_log(job_id, datetime.datetime.now(), total_success, total_failed)
        print(f"Job #{job_id} selesai. {total_success} sukses, {total_failed} gagal.")
    except Exception as e:
        print(f"Job #{job_id} gagal dijalankan: {e}")
        update_job_log(job_id, datetime.datetime.now(), 0, 0)

if __name__ == "__main__":
    run_rotation()
