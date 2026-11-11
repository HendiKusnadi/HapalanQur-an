#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Skrip Otomatis Pembungkus APK SIM Tahfidz Al-Qur'an
Menginjeksikan aset aplikasi mandiri (android-app/) ke dalam kontainer APK Android
dan menandatanganinya secara otomatis dengan Android APK Signature Scheme v2 & v3.
"""

import os
import sys
import zipfile
import subprocess

BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
TEMPLATE_APK = os.path.join(BASE_DIR, 'tools', 'android-template.apk')
OUTPUT_APK = os.path.join(BASE_DIR, 'SIM-Tahfidz-Quran.apk')
TEMP_UNSIGNED = os.path.join(BASE_DIR, 'tools', 'temp-unsigned.apk')
APP_DIR = os.path.join(BASE_DIR, 'android-app')
SIGN_APK_BIN = r"E:\laragon\bin\python\python-3.10\Scripts\sign-apk.exe"

def build():
    print("=" * 60)
    print("     PEMBUATAN APK SIM TAHFIDZ AL-QUR'AN (MANDIRI)")
    print("=" * 60)

    if not os.path.exists(TEMPLATE_APK):
        print(f"[ERROR] Template APK tidak ditemukan di: {TEMPLATE_APK}")
        sys.exit(1)

    print("[1/3] Menginjeksikan berkas HTML, JS, dan Data Al-Qur'an...")
    with zipfile.ZipFile(TEMPLATE_APK, 'r') as zin, zipfile.ZipFile(TEMP_UNSIGNED, 'w', compression=zipfile.ZIP_DEFLATED) as zout:
        for item in zin.infolist():
            # Lewati berkas tanda tangan lama
            if item.filename.startswith('META-INF/') and any(item.filename.endswith(ext) for ext in ['.SF', '.RSA', '.MF', '.DSA', '.EC']):
                continue
            # Lewati aset web default lama
            if item.filename.startswith('assets/web/') or item.filename == 'assets/local/index.html':
                continue
            data = zin.read(item.filename)
            zout.writestr(item, data)

        # Injeksi berkas aplikasi dari android-app/
        index_path = os.path.join(APP_DIR, 'index.html')
        with open(index_path, 'rb') as f:
            content = f.read()
            zout.writestr('assets/web/index.html', content)
            zout.writestr('assets/local/index.html', content)

        manifest_path = os.path.join(APP_DIR, 'manifest.json')
        if os.path.exists(manifest_path):
            with open(manifest_path, 'rb') as f:
                zout.writestr('assets/web/manifest.json', f.read())

        for sub in ['js/db.js', 'js/quran-data.js', 'icons/icon-192.png', 'icons/icon-512.png']:
            p = os.path.join(APP_DIR, sub)
            if os.path.exists(p):
                with open(p, 'rb') as f:
                    zout.writestr('assets/web/' + sub.replace('\\', '/'), f.read())

    print("[2/3] Menandatangani file APK dengan Signature Scheme v2 + v3...")
    cmd = [SIGN_APK_BIN, "sign", TEMP_UNSIGNED, OUTPUT_APK]
    res = subprocess.run(cmd, capture_output=True, text=True)
    if res.returncode != 0:
        print("[ERROR] Gagal menandatangani APK:")
        print(res.stderr)
        sys.exit(1)

    # Bersihkan file sementara
    if os.path.exists(TEMP_UNSIGNED):
        os.remove(TEMP_UNSIGNED)

    print("[3/3] Memverifikasi integritas sertifikat APK...")
    v_res = subprocess.run([SIGN_APK_BIN, "verify", OUTPUT_APK], capture_output=True, text=True)
    print(v_res.stdout.strip())

    size_mb = os.path.getsize(OUTPUT_APK) / (1024 * 1024)
    print("=" * 60)
    print(f"[BERHASIL] File APK siap di-install di HP Android!")
    print(f"Lokasi File : {OUTPUT_APK}")
    print(f"Ukuran Berkas : {size_mb:.2f} MB")
    print("=" * 60)

if __name__ == '__main__':
    build()
