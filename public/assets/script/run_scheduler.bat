@echo off
cd /d C:\laragon\www\dmzpassvault
php artisan schedule:run >> C:\laragon\www\dmzpassvault\scheduler.log 2>&1