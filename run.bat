@echo off
title SIM Tahfidz Al-Qur'an (PHP 8.3 & SQLite)
echo ========================================================
echo   SIM TAHFIDZ AL-QUR'AN - SETORAN & MUROJAAH OTOMATIS
echo ========================================================
echo.

REM Deteksi file php.exe (Minimal PHP 8.3)
set PHP_BIN=

if exist "E:\laragon\bin\php\php-8.3.20-Win32-vs16-x64\php.exe" (
    set "PHP_BIN=E:\laragon\bin\php\php-8.3.20-Win32-vs16-x64\php.exe"
    goto FOUND_PHP
)

for /f "delims=" %%I in ('where php 2^>nul') do (
    set "PHP_BIN=%%I"
    goto FOUND_PHP
)

for /d %%D in ("E:\laragon\bin\php\php-8.3*") do (
    if exist "%%D\php.exe" (
        set "PHP_BIN=%%D\php.exe"
        goto FOUND_PHP
    )
)

for /d %%D in ("C:\laragon\bin\php\php-8.3*") do (
    if exist "%%D\php.exe" (
        set "PHP_BIN=%%D\php.exe"
        goto FOUND_PHP
    )
)

if exist "C:\tools\php\php.exe" (
    set "PHP_BIN=C:\tools\php\php.exe"
    goto FOUND_PHP
)

if exist "C:\php\php.exe" (
    set "PHP_BIN=C:\php\php.exe"
    goto FOUND_PHP
)

:FOUND_PHP
if "%PHP_BIN%"=="" (
    echo [ERROR] PHP executable tidak ditemukan!
    echo Silakan pastikan PHP 8.3 terpasang di komputer Anda.
    pause
    exit /b 1
)

echo [OK] Menggunakan PHP: %PHP_BIN%
"%PHP_BIN%" -v
echo.
echo ========================================================
echo Membuka aplikasi di browser: http://localhost:8080 ...
echo Tekan Ctrl + C di jendela ini untuk menghentikan server.
echo ========================================================
echo.

REM Buka browser otomatis setelah 1 detik
start "" http://localhost:8080

REM Jalankan PHP Built-in Server dengan Front-Controller Router Bersih
"%PHP_BIN%" -S localhost:8080 -t public public/index.php
pause

