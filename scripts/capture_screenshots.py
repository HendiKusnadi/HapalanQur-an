import os
import sys
import time
import subprocess
from playwright.sync_api import sync_playwright

BASE_DIR = r"H:\CT\CTHapalanQuran"
PHP_EXE = os.path.join(BASE_DIR, "php", "php.exe")
PUBLIC_DIR = os.path.join(BASE_DIR, "public")
APP_HTML = f"file:///{BASE_DIR.replace(os.sep, '/')}/android-app/index.html"
OUT_DIR = os.path.join(BASE_DIR, "landingpage", "screenshots")
CHROME_PATH = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
PORT = 8099

os.makedirs(OUT_DIR, exist_ok=True)

def run():
    print("=" * 60)
    print("      MENGAMBIL SCREENSHOT ASLI APLIKASI TAHFIDZ")
    print("=" * 60)

    # 1. Jalankan server PHP portable sementara di port 8099
    php_proc = subprocess.Popen(
        [PHP_EXE, "-S", f"127.0.0.1:{PORT}", "-t", PUBLIC_DIR],
        stdout=subprocess.DEVNULL,
        stderr=subprocess.DEVNULL
    )
    time.sleep(1.2)
    print(f"[OK] Server PHP aktif di http://127.0.0.1:{PORT}")

    try:
        with sync_playwright() as p:
            browser = p.chromium.launch(
                executable_path=CHROME_PATH,
                headless=True,
                args=['--no-sandbox', '--disable-gpu']
            )

            # -------------------------------------------------------------
            # A. TANGKAPAN LAYAR DESKTOP / LAPTOP (1280 x 800)
            # -------------------------------------------------------------
            print("\n>>> Mengambil Tangkapan Layar Versi Laptop (1280x800)...")
            desktop = browser.new_page(viewport={'width': 1280, 'height': 800}, device_scale_factor=1.5)

            # Login Admin
            try:
                desktop.goto(f"http://127.0.0.1:{PORT}/login", wait_until="networkidle")
                desktop.fill('input[name="username"]', 'admin')
                desktop.fill('input[name="password"]', 'admin123')
                desktop.click('button[type="submit"]')
                desktop.wait_for_timeout(700)
            except Exception as e:
                print(f"Login note: {e}")

            # 1. Dashboard 30 Juz
            desktop.goto(f"http://127.0.0.1:{PORT}/dashboard", wait_until="networkidle")
            desktop.wait_for_timeout(800)
            d_path = os.path.join(OUT_DIR, "laptop_dashboard.png")
            desktop.screenshot(path=d_path)
            print(f"  [✓] {os.path.basename(d_path)} ({os.path.getsize(d_path):,} bytes)")

            # 2. Input Setoran Otomatis
            desktop.goto(f"http://127.0.0.1:{PORT}/setoran", wait_until="networkidle")
            desktop.wait_for_timeout(800)
            s_path = os.path.join(OUT_DIR, "laptop_setoran.png")
            desktop.screenshot(path=s_path)
            print(f"  [✓] {os.path.basename(s_path)} ({os.path.getsize(s_path):,} bytes)")

            # 3. Cetak Rapor Resmi A4
            desktop.goto(f"http://127.0.0.1:{PORT}/cetak-rapor/1", wait_until="networkidle")
            desktop.wait_for_timeout(800)
            r_path = os.path.join(OUT_DIR, "laptop_rapor.png")
            desktop.screenshot(path=r_path)
            print(f"  [✓] {os.path.basename(r_path)} ({os.path.getsize(r_path):,} bytes)")

            # 4. Player Tikrar Desktop
            desktop.goto(f"http://127.0.0.1:{PORT}/player", wait_until="networkidle")
            desktop.wait_for_timeout(800)
            p_path = os.path.join(OUT_DIR, "laptop_player.png")
            desktop.screenshot(path=p_path)
            print(f"  [✓] {os.path.basename(p_path)} ({os.path.getsize(p_path):,} bytes)")

            desktop.close()

            # -------------------------------------------------------------
            # B. TANGKAPAN LAYAR SMARTPHONE HP (412 x 892)
            # -------------------------------------------------------------
            print("\n>>> Mengambil Tangkapan Layar Versi HP Android (412x892)...")
            mobile = browser.new_page(viewport={'width': 412, 'height': 892}, device_scale_factor=2)
            mobile.goto(APP_HTML)
            mobile.wait_for_timeout(800)

            # 1. Mobile Player Tikrar
            mobile.evaluate("switchMobileTab('player')")
            mobile.wait_for_timeout(600)
            mp_path = os.path.join(OUT_DIR, "mobile_player.png")
            mobile.screenshot(path=mp_path)
            print(f"  [✓] {os.path.basename(mp_path)} ({os.path.getsize(mp_path):,} bytes)")

            # 2. Mobile Form Setoran
            mobile.evaluate("switchMobileTab('setoran')")
            mobile.wait_for_timeout(600)
            ms_path = os.path.join(OUT_DIR, "mobile_setoran.png")
            mobile.screenshot(path=ms_path)
            print(f"  [✓] {os.path.basename(ms_path)} ({os.path.getsize(ms_path):,} bytes)")

            # 3. Mobile Murojaah Center
            mobile.evaluate("switchMobileTab('murojaah')")
            mobile.wait_for_timeout(600)
            mm_path = os.path.join(OUT_DIR, "mobile_murojaah.png")
            mobile.screenshot(path=mm_path)
            print(f"  [✓] {os.path.basename(mm_path)} ({os.path.getsize(mm_path):,} bytes)")

            # 4. Mobile Santri List
            mobile.evaluate("switchMobileTab('santri')")
            mobile.wait_for_timeout(600)
            msa_path = os.path.join(OUT_DIR, "mobile_santri.png")
            mobile.screenshot(path=msa_path)
            print(f"  [✓] {os.path.basename(msa_path)} ({os.path.getsize(msa_path):,} bytes)")

            # 5. Mobile Rapor & Backup
            mobile.evaluate("switchMobileTab('settings')")
            mobile.wait_for_timeout(600)
            mr_path = os.path.join(OUT_DIR, "mobile_rapor.png")
            mobile.screenshot(path=mr_path)
            print(f"  [✓] {os.path.basename(mr_path)} ({os.path.getsize(mr_path):,} bytes)")

            mobile.close()
            browser.close()

    finally:
        php_proc.terminate()
        print("\n[OK] Server PHP dinonaktifkan.")

    print("=" * 60)
    print(f"[SELESAI] Seluruh tangkapan layar tersimpan di:\n{OUT_DIR}")
    print("=" * 60)

if __name__ == '__main__':
    run()
