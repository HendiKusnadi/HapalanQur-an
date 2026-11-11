@echo off
title Pack APK Source - SIM Tahfidz Quran
echo ========================================================
echo    PACKING SOURCE ANDROID APK (MANDIRI & OFFLINE)
echo ========================================================
echo.
echo Sedang mengompres folder android-app ke SIM-Tahfidz-Android.zip...
powershell -NoProfile -Command "$ProgressPreference='SilentlyContinue'; if (Test-Path 'SIM-Tahfidz-Android.zip') { Remove-Item 'SIM-Tahfidz-Android.zip' }; Compress-Archive -Path 'android-app\*' -DestinationPath 'SIM-Tahfidz-Android.zip' -Force"
echo.
if exist "SIM-Tahfidz-Android.zip" (
    echo [SUKSES] File SIM-Tahfidz-Android.zip berhasil dibuat!
    echo.
    echo Anda dapat langsung mengunggah file zip ini ke:
    echo 1. WebIntoApp.com (Free Online APK Builder)
    echo 2. Atau pilih folder 'android-app' di software Website 2 APK Builder
) else (
    echo [GAGAL] Gagal membuat file zip.
)
echo.
pause
