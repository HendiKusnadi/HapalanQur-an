# Panduan Memasang & Membuat File APK Android SIM Tahfidz Al-Qur'an

Aplikasi **SIM Tahfidz Al-Qur'an** telah dilengkapi arsitektur **Progressive Web App (PWA)** standar internasional (`manifest.json`, `sw.js`, dan icon resolusi tinggi). Anda dapat menggunakannya sebagai APK di Android melalui 3 cara mudah:

---

## Cara 1: Pasang Langsung Jadi APK di HP Android (Paling Cepat & Praktis)

Ini adalah cara resmi Google Chrome / Android (WebAPK) tanpa perlu compile kodingan sama sekali:

1. Pastikan komputer/server Anda menyala dan HP Android Anda terhubung ke jaringan WiFi yang sama (atau aplikasi sudah di-upload ke hosting/domain Anda).
2. Di HP Android, buka peramban **Google Chrome** atau **Microsoft Edge**.
3. Buka alamat aplikasi, misalnya:
   ```
   http://192.168.x.x:8080   (alamat IP komputer Anda)
   atau domain Anda jika sudah online
   ```
4. Ketuk tombol **titik tiga (⋮)** di pojok kanan atas browser Chrome.
5. Pilih menu **"Tambahkan ke Layar Utama" (Add to Home Screen)** atau **"Instal Aplikasi" (Install App)**.
6. Ketuk **Instal**.
7. Android akan otomatis membuat **File WebAPK Asli** lengkap dengan icon Al-Qur'an di menu aplikasi HP Anda. Aplikasi akan terbuka mandiri dalam mode *fullscreen* (layar penuh) tanpa address bar browser, layaknya aplikasi APK native dari Play Store!

---

## Cara 2: Membuat File `.apk` Menggunakan PWABuilder (Gratis & Online)

Jika Anda ingin mendapatkan berkas file fisik berformat `.apk` untuk dikirim via WhatsApp, Flashdisk, atau diunggah ke Google Play Store:

1. Upload aplikasi ke server online / hosting gratis (misal: Railway, Render, cPanel, atau VPS).
2. Kunjungi situs resmi dari Microsoft: 👉 **[https://www.pwabuilder.com/](https://www.pwabuilder.com/)**
3. Masukkan tautan URL aplikasi Anda, lalu klik **Start**.
4. PWABuilder akan mendeteksi `manifest.json`, icon, dan service worker yang sudah kami siapkan dengan skor 100%.
5. Klik tombol **"Package for Android"**.
6. Klik **Download Package** / **Generate APK**.
7. Anda akan mendapatkan file `.apk` siap pasang di semua HP Android!

---

## Cara 3: Build APK Menggunakan Bubblewrap CLI (Untuk Developer)

Jika Anda seorang programmer yang ingin membungkus aplikasi ini menjadi APK Trusted Web Activity (TWA) secara offline di terminal:

```bash
# 1. Install Bubblewrap CLI
npm install -g @bubblewrap/cli

# 2. Inisialisasi proyek TWA
bubblewrap init --manifest="https://domain-anda.com/manifest.json"

# 3. Build APK
bubblewrap build
```

Hasil build akan menghasilkan file `app-release-signed.apk` yang siap dibagikan ke seluruh santri dan ustadz!

