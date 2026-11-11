@echo off
setlocal
title Pack APK Source - SIM Tahfidz Quran
cls
echo ========================================================
echo    PACKING SOURCE ANDROID APK (MANDIRI DAN OFFLINE)
echo ========================================================
echo.
echo Sedang mengompres folder android-app ke SIM-Tahfidz-Android.zip...

powershell -NoProfile -Command "$ProgressPreference='SilentlyContinue'; if (Test-Path 'SIM-Tahfidz-Android.zip') { Remove-Item 'SIM-Tahfidz-Android.zip' -Force }; Compress-Archive -Path 'android-app\*' -DestinationPath 'SIM-Tahfidz-Android.zip' -Force"

if exist "SIM-Tahfidz-Android.zip" goto SUKSES
goto GAGAL

:SUKSES
echo.
echo ========================================================
echo [BERHASIL] File SIM-Tahfidz-Android.zip siap digunakan!
echo ========================================================
echo.
echo Ukuran berkas zip:
dir SIM-Tahfidz-Android.zip | findstr "SIM-Tahfidz-Android.zip"
echo.
echo Langkah mudah membuat APK:
echo 1. Kunjungi https://www.webintoapp.com
echo 2. Upload file SIM-Tahfidz-Android.zip
echo 3. Beri nama SIM Tahfidz Quran dan unduh file APK-nya!
echo.
echo Atau buka aplikasi Website 2 APK Builder di folder D:\
echo dan pilih folder android-app.
echo.
goto END

:GAGAL
echo.
echo [ERROR] Gagal membuat file SIM-Tahfidz-Android.zip.
echo.
goto END

:END
pause
