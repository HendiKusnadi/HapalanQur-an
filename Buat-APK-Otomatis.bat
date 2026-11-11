@echo off
setlocal
title Build APK Otomatis - SIM Tahfidz Quran
cls
echo ========================================================
echo       BUILD APK SIM TAHFIDZ AL-QUR'AN (1-KLIK)
echo ========================================================
echo.
echo Sedang merakit dan menandatangani file SIM-Tahfidz-Quran.apk...
echo.

"E:\laragon\bin\python\python-3.10\python.exe" "scripts\package_apk.py"

echo.
pause

