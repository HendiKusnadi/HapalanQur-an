# PANDUAN LENGKAP: MEMBUAT VERSI ANDROID APK MANDIRI
## SIM Tahfidz Al-Qur'an (Database Mandiri di HP: Tinggal Install, Tinggal Pakai)

---

### A. Konsep & Arsitektur Database Mandiri di HP
Pada versi Android APK ini:
1. **Tanpa Perlu PHP / Web Server**: Sistem ini dirancang khusus menggunakan **Client-Side Engine** modern (JavaScript + HTML5 Local Storage & IndexedDB) yang berjalan langsung di prosesor dan memori HP Android.
2. **Database Mandiri (Per-Device Storage)**: 
   - Setiap HP memiliki database lokal tersendiri.
   - Data santri, setoran hafalan baru, murojaah, dan rekor kelulusan tersimpan aman di HP masing-masing tanpa takut tercampur dengan HP santri/ustadz lain.
   - Bekerja **100% OFFLINE** tanpa perlu kuota internet di pondok pesantren / asrama tahfidz.
3. **Otomatisasi Tetap Aktif**:
   - Penambahan hafalan otomatis saat lulus setoran.
   - Pengurangan hafalan otomatis jika terjadi kekeliruan mutqin.
   - Antrean Murojaah Sabqi (1-5 halaman sebelum hafalan terakhir) & Manzil (seperempat/setengah juz) tetap terhitung otomatis.
   - Dilengkapi perekam suara setoran santri & pemutar audio murottal EveryAyah.
4. **Fitur Backup & Restore JSON**:
   - Terdapat tombol **Cadangkan Database (JSON)** dan **Pulihkan Database (JSON)** di menu tab "Pengaturan".
   - Data dapat dicadangkan dan dikirim via WhatsApp antar ustadz atau jika santri ganti HP.

---

### B. Cara Mengubah Folder Ini Menjadi File .APK (Pilih Salah Satu)

#### Cara 1: Menggunakan Website 2 APK Builder (Paling Mudah di Laptop Windows, 100% Offline)
1. Buka atau unduh aplikasi **Website 2 APK Builder** di laptop Windows.
2. Pada pilihan website type, pilih: **Local Website Folder**.
3. Klik tombol **Browse** dan arahkan ke folder:
   `android-app` (di dalam folder aplikasi ini).
4. Masukkan identitas aplikasi:
   - **App Title**: `SIM Tahfidz Quran`
   - **Package Name**: `com.chattutor.simtahfidz`
   - **App Version**: `1.0.0`
5. Pada bagian **App Icon**, klik browse dan pilih icon:
   `android-app/icons/icon-512.png`
6. Centang opsi:
   - Keep Screen ON (agar layar tidak mati saat santri tikrar)
   - Enable Zoom / Responsive
7. Klik tombol **GENERATE APK**.
8. Dalam waktu sekitar 10-20 detik, file installer `.apk` sudah jadi! File ini tinggal Anda bagikan ke HP Android para ustadz/santri.

---

#### Cara 2: Menggunakan Converter Online (WebIntoApp.com - Gratis & Cepat)
Jika Anda tidak ingin menginstall software builder di laptop:
1. Di folder utama aplikasi, jalankan file **`Buat-Zip-APK.bat`** (cukup dobel klik). File `SIM-Tahfidz-Android.zip` akan otomatis terbuat/diperbarui.
2. Buka browser dan kunjungi: **`https://www.webintoapp.com`**
3. Klik tombol **Make App**.
4. Pilih tab **HTML / Zip File**, lalu upload file:
   `SIM-Tahfidz-Android.zip`
5. Isi formulir:
   - **App Name**: `SIM Tahfidz Quran`
   - **App Icon**: Upload file `icons/icon-512.png`
6. Klik **Make App / Download**.
7. Anda akan mendapatkan file `.apk` siap install di HP Android mana pun!

---

#### Cara 3: Pasang Langsung Tanpa APK (Progressive Web App - PWA)
Aplikasi ini sudah memenuhi standar PWA modern:
1. Buka file `index.html` dari browser HP Android (Chrome / Samsung Internet).
2. Tekan tombol menu titik tiga di pojok kanan atas browser.
3. Pilih **"Tambahkan ke Layar Utama"** atau **"Instal Aplikasi"**.
4. Aplikasi akan terpasang di menu HP Android seperti aplikasi native lengkap dengan icon dan berjalan offline tanpa bilah URL browser.

---

### C. Doa Santri & Lisensi Wakaf Gratis

> **Doa Terbaik untuk Para Pejuang Al-Qur'an:**  
> *"Semoga ananda dan seluruh santri yang menggunakan aplikasi ini dianugerahi kemudahan dalam menghafal Al-Qur'an, menjadi Hafidz & Hafidzah yang mutqin, mengamalkan kalamullah, berakhlak mulia, cerdas, serta istiqomah hingga akhir hayat. Aamiin ya Rabbal 'Alamin."*

**PERINGATAN PENTING & HAK CIPTA:**
- **DILARANG KERAS MEMPERJUALBELIKAN APLIKASI INI DALAM BENTUK APA PUN!**
- **APLIKASI INI MURNI 100% GRATIS (WAKAF DIGITAL) DARI CHATTUTOR.**
- Hak Cipta & Hak Guna: Bebas digunakan oleh seluruh Pesantren, Rumah Tahfidz, Madrasah, Sekolah Islam, Ustadz, Santri, dan Wali Santri di seluruh dunia secara cuma-cuma.

**Informasi & Kontak Pengembang:**
- Website Utama: [https://chattutor.id](https://chattutor.id)
- Portal Sekolah: [https://webschool.id](https://webschool.id)
- YouTube: [ChatTutor Official](https://youtube.com/@chattutor)
- WhatsApp Layanan Resmi: **0897 5775 046** (https://wa.me/628975775046)
