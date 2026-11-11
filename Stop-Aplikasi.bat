@echo off
title Hentikan Server SIM Tahfidz
echo Menghentikan proses server PHP...
taskkill /f /im php.exe >nul 2>&1
echo.
echo ========================================================
echo   Server SIM Tahfidz Al-Qur'an telah dinonaktifkan.
echo ========================================================
timeout /t 2 >nul
exit

