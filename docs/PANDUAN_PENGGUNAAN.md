# BUKU PANDUAN LENGKAP PENGGUNAAN SIM TAHFIDZ AL-QUR'AN
**Frontend Menghafal Tikrar & Backend Setoran Otomatis**

---

## 📑 DAFTAR ISI
1. [Pendahuluan](#1-pendahuluan)
2. [Panduan Frontend Santri (Aplikasi Menghafal Tikrar)](#2-panduan-frontend-santri-aplikasi-menghafal-tikrar)
   - [2.1 Pemilihan Juz, Surah & Rentang Ayat](#21-pemilihan-juz-surah--rentang-ayat)
   - [2.2 Pengaturan Pengulangan Audio (Metode Tikrar)](#22-pengaturan-pengulangan-audio-metode-tikrar)
   - [2.3 Metode Sambung Akumulatif (Tikrar Berantai / Tangga Hafalan)](#23-metode-sambung-akumulatif-tikrar-berantai--tangga-hafalan)
   - [2.4 Pilihan 12 Qari Masyhur Dunia](#24-pilihan-12-qari-masyhur-dunia)
   - [2.5 Mode Uji Hafalan (Blind Test / Tutup Teks)](#25-mode-uji-hafalan-blind-test--tutup-teks)
   - [2.6 Perekam Suara Mandiri Santri](#26-perekam-suara-mandiri-santri)
   - [2.7 Menyetorkan Hafalan ke Ustadz](#27-menyetorkan-hafalan-ke-ustadz)
3. [Panduan Backend Ustadz & Musyrif](#3-panduan-backend-ustadz--musyrif)
   - [3.1 Login & Keamanan Akun](#31-login--keamanan-akun)
   - [3.2 Dashboard Kendali Utama](#32-dashboard-kendali-utama)
   - [3.3 Input Setoran & 3 Kategori Hafalan](#33-input-setoran--3-kategori-hafalan)
   - [3.4 Cara Kerja Logika Otomatisasi (Tambah/Kurang Capaian)](#34-cara-kerja-logika-otomatisasi-tambahkurang-capaian)
   - [3.5 Pusat Kendali Murojaah (Sabqi & Manzil)](#35-pusat-kendali-murojaah-sabqi--manzil)
   - [3.6 Data Santri, Kartu Kendali & Peta Visual 30 Juz](#36-data-santri-kartu-kendali--peta-visual-30-juz)
   - [3.7 Laporan Detail Per Siswa (PDF & Excel)](#37-laporan-detail-per-siswa-pdf--excel)
   - [3.8 Rekapitulasi & Cetak Rapor Resmi A4](#38-rekapitulasi--cetak-rapor-resmi-a4)
   - [3.9 Pengaturan Pesantren, Halaqah & Backup Database](#39-pengaturan-pesantren-halaqah--backup-database)
4. [Tanya Jawab & Pemecahan Masalah (FAQ)](#4-tanya-jawab--pemecahan-masalah-faq)

---

## 1. PENDAHULUAN
Aplikasi **SIM Tahfidz Al-Qur'an** adalah sistem informasi manajemen pembelajaran dan setoran hafalan Al-Qur'an modern berbasis **PHP 8.3+** dan **SQLite 3**. Aplikasi ini memadukan kemandirian santri dalam menghafal Al-Qur'an dengan pengulangan terstruktur di sisi depan (*frontend*), serta kemudahan ustadz/musyrif dalam mencatat, menguji, dan melacak otomatisasi capaian santri di sisi pengelola (*backend*).

---

## 2. PANDUAN FRONTEND SANTRI (APLIKASI MENGHAFAL TIKRAR)
*Akses: `http://localhost:8080/index.php?page=player` (Dapat diakses langsung tanpa login).*

### 2.1 Pemilihan Juz, Surah & Rentang Ayat
- **Filter Juz**: Pilih Juz 1 s/d 30 pada menu dropdown. Sistem secara cerdas akan menyaring surah-surah yang tercakup di dalam Juz tersebut.
- **Pilih Surah**: Santri dapat memilih langsung 1 dari 114 Surah Al-Qur'an.
- **Rentang Ayat**: Masukkan nomor ayat awal dan akhir (misal: Ayat 1 s/d 5).
- **Tombol Pintas Preset**:
  - `[Ayat 1-5]` : Langsung membatasi 5 ayat pertama.
  - `[Ayat 6-10]`: Membatasi ayat 6 sampai 10.
  - `[Ayat 1-10]`: Membatasi 10 ayat pertama.
  - `[⏮ 5 Ayat Sebelumnya]`: Memundurkan jendela hafalan 5 ayat ke belakang.
  - `[⏭ 5 Ayat Berikutnya]`: Memajukan jendela hafalan 5 ayat ke depan.

### 2.2 Pengaturan Pengulangan Audio (Metode Tikrar)
- **Ulang Tiap Ayat**: Berapa kali satu ayat diulang sebelum berpindah ke ayat berikutnya (1x, 3x, 5x, 7x, 10x, 20x, 40x mutqin).
- **Ulang Seluruh Rentang**: Berapa kali seluruh blok ayat diulang (1 putaran, 3 putaran, 5 putaran, 10 putaran, atau Loop Tanpa Batas).
- **Jeda Menirukan (Echo Pause)**: Memberikan jeda waktu hening (1.5 detik, 3 detik, atau 5 detik) setelah ayat dilantunkan Qari, memberi waktu bagi santri untuk membaca ulang ayat tersebut dengan tajwid yang sama.
- **Kecepatan Audio**: Mengatur tempo bacaan menjadi `0.75x` (lambat bertajwid), `1.0x` (normal), atau `1.25x` (sedang/cepat).

### 2.3 Metode Sambung Akumulatif (Tikrar Berantai / Tangga Hafalan)
Metode hafalan pesantren yang menyambungkan hafalan baru dengan ayat-ayat sebelumnya agar sambungan (*wasl*) tidak terputus.

#### Cara Mengaktifkan:
1. Centang kotak **[✓] Metode Sambung Akumulatif (Tikrar Berantai)**.
2. Label status di sebelah kanan akan berubah warna menjadi hijau **`Aktif ✓`**.
3. Pilih opsi **Putar Sambung** (`1x Sambung` atau `2x Sambung`).

#### Contoh Alur Pemutaran (Rentang Ayat 1 s/d 4, Diulang 3x):
```
[1] Ayat 1 diulang 3x (Drill)
[2] Lanjut Ayat 2 diulang 3x (Drill)
    ➔ FASE SAMBUNG: Ayat 1 (1x) disambung ke Ayat 2 (1x)
[3] Lanjut Ayat 3 diulang 3x (Drill)
    ➔ FASE SAMBUNG: Ayat 1 s/d Ayat 3 diputar berurutan
[4] Lanjut Ayat 4 diulang 3x (Drill)
    ➔ FASE SAMBUNG: Ayat 1 s/d Ayat 4 diputar berurutan
[5] Selesai 1 siklus blok! (Dapat berulang jika diset putaran blok > 1).
```

#### Indikator Visual:
- **Sticky Audio Player**: Menampilkan badge `🎯 Fokus Baru: Ayat X` saat mengulang ayat target baru, dan `🔗 Sambung: Ayat 1 s/d X` saat fase menyambung.
- **Mushaf Cards**: Ayat yang sedang bersuara dilingkari cincin hijau zamrud menyala. Ayat-ayat yang berada dalam satu rantai sambung diberi latar belakang toska lembut.

### 2.4 Pilihan 12 Qari Masyhur Dunia
Santri dapat memilih lantunan murattal dari 12 Qari terbaik:
1. Misyari Rasyid Al-Afasy (Tartil & Jelas)
2. Mahmoud Khalil Al-Hushari (Standar Tajwid Internasional)
3. Muhammad Siddiq Al-Minshawi (Syahdu & Khusyuk)
4. Abdurrahman As-Sudais (Imam Masjidil Haram, Tegas)
5. Sa'ud Ash-Shuraim (Tempo Cepat & Ritmis)
6. Saad Al-Ghamdi (Merdu & Lembut)
7. Maher Al-Muaiqly (Imam Masjidil Haram, Tartil)
8. Abu Bakr Asy-Syathiri (Halus & Khusyuk)
9. Hani Ar-Rifai (Menyentuh Hati)
10. Yasser Ad-Dussary (Hijazi Modern)
11. Ali Al-Hudhaify (Imam Nabawi, Pelan & Teliti)
12. Al-Hushari (Langgam Mujawwad)

### 2.5 Mode Uji Hafalan (Blind Test / Tutup Teks)
- Klik tombol **`Uji Hafalan (Tutup Teks)`**. Seluruh teks Arab akan diburamkan (*blur*).
- Santri ditantang melafalkan ayat secara mandiri dari ingatan sebelum audio Qari berbunyi.
- Klik **`Buka Teks Arab`** untuk menampilkan kembali tulisan mushaf.

### 2.6 Perekam Suara Mandiri Santri
- Klik **`Mulai Rekam`** dan izinkan akses mikrofon pada peramban.
- Santri membacakan ayat yang sedang dihafal.
- Klik **`Selesai Rekam`**, lalu klik **`Putar Rekaman Saya`** untuk mendengarkan kembali rekaman sendiri dan membandingkan makhraj huruf dengan bacaan Qari.

### 2.7 Menyetorkan Hafalan ke Ustadz
Jika santri merasa hafalannya telah lancar (*mutqin*), klik tombol emas **`Sudah Hafal? Setorkan ke Ustadz`**. Aplikasi akan langsung mengarahkan ustadz ke formulir setoran dengan nama surah dan ayat yang telah terisi otomatis.

---

## 3. PANDUAN BACKEND USTADZ & MUSYRIF
*Akses: `http://localhost:8080/index.php?page=dashboard` (Wajib Login).*

### 3.1 Login & Keamanan Akun
- **URL Login**: `index.php?page=login`
- **Username Bawaan**: `admin`
- **Kata Sandi Bawaan**: `admin123`
- **Keluar**: Klik tombol merah **`Keluar`** pada sudut kanan atas bilah navigasi.

### 3.2 Dashboard Kendali Utama
- **Metrik Harian**: Menampilkan jumlah setoran masuk hari ini, santri aktif, total halaqah, dan rata-rata nilai setoran.
- **Filter Halaqah**: Memfilter data berdasarkan kelompok bimbingan ustadz tertentu.
- **Tabel Setoran Terkini**: Menampilkan daftar santri yang menyetor hari ini lengkap dengan nilai kelancaran.

### 3.3 Input Setoran & 3 Kategori Hafalan
Buka menu **Setoran** (`index.php?page=setoran`):
1. **Pilih Santri**: Pilih nama atau NIS santri dari dropdown. Posisi terakhir santri (surah, ayat, halaman, juz) akan dimuat otomatis.
2. **Pilih Kategori Setoran**:
   - **Hafalan Baru (Sabaq)**: Sistem menghitung target hafalan berikutnya secara otomatis. Ustadz dapat menambah/mengurangi target dengan tombol `+10 Ayat`, `+1 Hal`, dll.
   - **Murojaah Baru (Sabqi)**: Menampilkan antrean 5 setoran baru terakhir santri. Klik salah satu antrean untuk menguji ulang bagian tersebut.
   - **Murojaah Lama (Manzil)**: Menampilkan juz-juz lama santri yang paling lama belum disetorkan. Disertai peringatan jika sudah lebih dari 7 hari belum murojaah.
3. **Penilaian**:
   - *Mumtaz* (Istimewa - 90-100)
   - *Jayyid Jiddan* (Sangat Baik - 80-89)
   - *Jayyid* (Baik - 70-79)
   - *Maqbul* (Cukup - 60-69)
   - *Rasib* (Mengulang - <60)

### 3.4 Cara Kerja Logika Otomatisasi (Tambah/Kurang Capaian)
Ustadz dapat memilih mode otomatisasi pada formulir setoran:
- **`+ Tambah Otomatis (Saat Lulus)`**:
  - Total halaman hafalan bertambah sesuai jumlah halaman yang disetor.
  - Posisi hafalan santri (surah, ayat, halaman, dan juz) melompat maju ke ayat selesai yang baru.
  - Jika santri menyelesaikan ayat terakhir suatu surah, sistem otomatis memajukan posisi ke surah berikutnya.
- **`= Posisi Tetap (Murojaah)`**:
  - Posisi capaian santri tidak bergeser. Digunakan untuk setoran Murojaah Sabqi atau Manzil.
- **`- Kurang Otomatis (Saat Mengulang)`**:
  - Mengurangi total halaman santri dan memundurkan posisi jika santri dinilai perlu mengulang atau menurunkan target.
- **Tombol Koreksi Cepat di Profil**:
  - Ustadz dapat mengoreksi langsung total capaian santri dengan tombol `+1 Hal`, `+0.5 Hal`, `-0.5 Hal`, `-1 Hal` tanpa menghapus riwayat setoran.
- **Rollback Otomatis Saat Menghapus Setoran**:
  - Jika ustadz menghapus salah satu setoran yang salah input, sistem otomatis mengembalikan capaian dan posisi santri ke kondisi sebelum setoran tersebut disetorkan.

### 3.5 Pusat Kendali Murojaah (Sabqi & Manzil)
Buka menu **Murojaah** (`index.php?page=murojaah`):
- **Tab Murojaah Baru (Sabqi)**: Memantau 5 antrean hafalan baru setiap santri yang harus diulang sebelum mutqin.
- **Tab Murojaah Lama (Manzil)**: Menyorot santri-santri yang memiliki tunggakan juz lama (peringatan darurat > 7 hari belum murojaah). Ustadz dapat mengklik tombol **Setorkan Sekarang** untuk langsung menguji juz tersebut.

### 3.6 Data Santri, Kartu Kendali & Peta Visual 30 Juz
Buka menu **Santri** (`index.php?page=santri`):
- **Daftar & Pencarian Santri**: Cari berdasarkan nama, NIS, atau halaqah.
- **Import / Export**: Unggah data santri massal via format CSV atau unduh daftar santri ke Excel.
- **Kartu Kendali Tahfidz**:
  - Klik nama santri untuk membuka detail.
  - **Peta 30 Juz Interaktif**: 30 kotak juz berwarna:
    - 🟢 *Hijau*: Juz telah tuntas dihafal.
    - 🔵 *Biru*: Juz sedang aktif dihafal.
    - ⚪ *Abu-abu*: Juz belum dihafal.
  - **Riwayat Lengkap**: Menampilkan seluruh histori setoran santri dari awal masuk hingga saat ini.

### 3.7 Laporan Detail Per Siswa (PDF & Excel)
Buka menu **Laporan Siswa** (`index.php?page=laporan_santri`):
1. Pilih nama santri dari dropdown pencarian.
2. Muncul tabel rincian per Juz (Juz 1 s/d 30) beserta seluruh surat di bawahnya.
3. Status ketercapaian:
   - 🟢 **HAPAL TUNTAS ✓**: Seluruh ayat di surat tersebut telah selesai dihafal.
   - 🟡 **BELUM TUNTAS - Ayat X s/d Y**: Baru sebagian ayat yang disetorkan (menampilkan persentase progress).
   - ⚪ **BELUM DIHAFAL**: Surat belum pernah disetorkan.
4. **Cetak PDF**: Klik `[Cetak Laporan PDF]` untuk mencetak dokumen format A4 rapi.
5. **Ekspor Excel**: Klik `[Ekspor Excel (.csv)]` untuk mengunduh spreadsheet laporan resmi.

### 3.8 Rekapitulasi & Cetak Rapor Resmi A4
Buka menu **Rekap** (`index.php?page=rekap`):
- Tentukan rentang tanggal laporan (misal: 1 semester atau 1 bulan).
- Filter berdasarkan halaqah atau jenis setoran.
- Klik **`Cetak Rapor Resmi`** untuk mencetak rapor berstandar resmi pesantren lengkap dengan checklist 30 juz dan kolom tanda tangan pengasuh pesantren serta musyrif.

### 3.9 Pengaturan Pesantren, Halaqah & Backup Database
Buka menu **Pengaturan** (`index.php?page=pengaturan`):
- **Identitas Lembaga**: Ubah nama aplikasi, nama pondok pesantren, dan nama default musyrif.
- **Daftar Halaqah**: Tambah atau edit halaqah/kelas tahfidz beserta ustadz pembimbingnya.
- **Keamanan Akun**: Perbarui nama lengkap ustadz, username login, serta ganti kata sandi.
- **Backup Database**: Klik tombol `[Unduh / Backup File Database (.sqlite)]` untuk mengunduh berkas cadangan `database/hafalan.sqlite` dalam satu klik.

---

## 4. TANYA JAWAB & PEMECAHAN MASALAH (FAQ)

**Q: Bagaimana cara menjalankan aplikasi di komputer baru?**
> Cukup klik ganda file `run.bat` di Windows. Skrip otomatis mendeteksi PHP 8.3 dan membuka browser di `http://localhost:8080`.

**Q: Mengapa checkbox Metode Sambung Akumulatif tidak berubah status?**
> Pastikan peramban tidak menggunakan cache lama dengan menekan tombol `Ctrl + F5`. Status akan langsung menyala hijau bertuliskan `Aktif ✓`.

**Q: Apakah santri bisa mengakses menu setoran tanpa login?**
> Tidak bisa. Seluruh menu pengubahan data, penilaian, dan laporan santri otomatis terproteksi dan mewajibkan login akun ustadz.

**Q: Di mana file database disimpan?**
> Seluruh data tersimpan aman di satu file SQLite lokal: `H:\CT\CTHapalanQuran\database\hafalan.sqlite`.

