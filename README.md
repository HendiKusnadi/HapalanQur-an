# SIM Tahfidz Al-Qur'an (Frontend Menghafal Tikrar & Backend Setoran Otomatis)

Aplikasi lengkap manajemen dan pembelajaran Al-Qur'an berbasis **PHP 8.3+** dan **SQLite 3** dengan arsitektur **Mobile-First PWA (Progressive Web App)** yang memisahkan dan mengintegrasikan:
1. **Frontend Santri**: Aplikasi interaktif menghafal Al-Qur'an berulang (*Metode Tikrar*), pemutar audio murottal multi-Qari per ayat, filter Juz & Surah, serta rentang ayat kustom.
2. **Backend Admin / Ustadz**: Pencatatan setoran hafalan baru, murojaah baru (Sabqi), murojaah lama (Manzil) dengan penambahan & pengurangan capaian otomatis, visual 30 Juz, export Excel, dan cetak rapor resmi.

---

## 🎧 Fitur Baru Frontend Santri (Player Hafalan Berulang / Tikrar)

1. **Pilih Berdasarkan Juz atau Surah Langsung**:
   - Filter Juz (1 s/d 30) otomatis menyaring surah-surah di dalamnya.
   - Pilihan langsung 114 Surah lengkap dengan nama Arab & Latin.
2. **Pilih Rentang Ayat Kustom**:
   - Tentukan ayat mulai dan selesai (misal: Ayat 1 s/d 5, Ayat 6 s/d 10).
   - Tombol cepat preset: `[Ayat 1-5]`, `[Ayat 6-10]`, `[Ayat 1-10]`, `[⏮ 5 Ayat Sebelumnya]`, `[⏭ 5 Ayat Berikutnya]`.
3. **Pengulangan Audio Terstruktur (Metode Tikrar)**:
   - **Ulang Tiap Ayat**: Berapa kali setiap ayat diulang sebelum pindah (1x, 3x, 5x, 7x, 10x, 20x, 40x mutqin).
   - **Ulang Putaran Blok**: Berapa kali seluruh rentang (misal ayat 1-5) diulang berputar (1x, 3x, 5x, 10x, Loop Terus).
   - **Jeda Waktu Menirukan (Echo Pause)**: Opsi jeda 1.5 detik s/d 5 detik setelah ayat berbunyi agar santri dapat menirukan bacaan.
   - **Kecepatan Audio**: 0.75x (pelan bertajwid), 1.0x (normal), 1.25x (cepat).
4. **12+ Pilihan Qari Ternama Dunia**:
   - Misyari Rasyid Al-Afasy (Tartil & Jelas)
   - Mahmoud Khalil Al-Hushari (Standar Tajwid Internasional)
   - Al-Hushari Mujawwad (Langgam Mujawwad)
   - Muhammad Siddiq Al-Minshawi (Syahdu & Khusyuk)
   - Abdurrahman As-Sudais (Imam Masjidil Haram, Tegas)
   - Sa'ud Ash-Shuraim (Tempo Cepat & Ritmis)
   - Saad Al-Ghamdi (Merdu & Lembut)
   - Maher Al-Muaiqly (Imam Masjidil Haram, Tartil)
   - Abu Bakr Asy-Syathiri (Halus & Khusyuk)
   - Hani Ar-Rifai (Menyentuh Hati)
   - Yasser Ad-Dussary (Hijazi Modern)
   - Ali Al-Hudhaify (Imam Nabawi, Pelan & Teliti)
5. **Tampilan Teks Arab Utsmani & Uji Hafalan**:
   - Teks Arab mushaf berukuran besar dengan font *Amiri*.
   - **Highlighting Real-time**: Ayat yang sedang diputar menyala (*highlight*) dan layar otomatis bergulir (*auto-scroll*).
   - **Fitur Uji Hafalan (Blind Test)**: Tombol untuk menyamarkan / menutup teks Arab dengan efek blur sehingga santri bisa menguji kemandirian hafalan sambil mendengarkan audio.
   - Toggle terjemahan Bahasa Indonesia.
6. **Integrasi ke Backend Ustadz**:
   - Tombol **"Sudah Hafal? Setorkan ke Ustadz"** langsung membuka form setoran ustadz dengan surah dan ayat yang sudah terisi otomatis!

---

## 🔐 Autentikasi & Login Admin / Ustadz [BARU ✨]

Sistem keamanan akun berbasis session untuk melindungi data santri dan pencatatan setoran:
- **Pemisahan Hak Akses**:
  - **Santri (Publik)**: Halaman menghafal Tikrar (`index.php?page=player`) dapat diakses langsung oleh santri tanpa perlu login.
  - **Ustadz / Admin (Privat)**: Halaman Admin, Input Setoran, Data Santri, Pusat Murojaah, Rekapitulasi, Laporan Siswa, dan Pengaturan dilindungi autentikasi login.
- **Akun Default Bawaan**:
  - **Username**: `admin`
  - **Kata Sandi**: `admin123`
  - *(Tersedia tombol "Isi Otomatis" di halaman login untuk kemudahan uji coba)*.
- **Manajemen Akun & Ganti Password**:
  - Tersedia di menu **Pengaturan** (`index.php?page=pengaturan`) untuk mengubah nama lengkap ustadz, username, serta mengganti kata sandi lama ke yang baru.
- **Perlindungan Endpoint API**: Seluruh endpoint penulisan, koreksi, dan ekspor data santri otomatis memverifikasi sesi login ustadz.

---

## 👨‍🏫 Fitur Backend Admin / Ustadz

1. **Setoran Hafalan Baru (Sabaq)**: Rekomendasi target berikutnya dihitung otomatis.
2. **Murojaah Baru (Sabqi)**: Antrean otomatis 5 setoran baru terakhir.
3. **Murojaah Lama (Manzil)**: Rotasi juz lama berdasarkan prinsip *least recently reviewed* dengan alert jika > 7 hari belum murojaah.
4. **Otomatis Menambah & Mengurang**:
   - `+ Tambah Otomatis` saat lulus (total halaman & juz bertambah, posisi melompat maju).
   - `- Kurang Otomatis` saat mengulang atau menurunkan target.
   - Tombol penyesuaian cepat: `+1 Hal`, `+0.5 Hal`, `-0.5 Hal`, `-1 Hal`.
   - Rollback otomatis saat setoran dihapus.
5. **Laporan & Export Excel**:
   - Export seluruh data santri ke format `.csv` (Excel).
   - Export rekapitulasi setoran ke format `.csv` (Excel).
   - **Cetak Rapor Tahfidz Resmi** format A4 lengkap dengan kop pesantren, checklist 30 juz, dan kolom tanda tangan resmi.

---

## 🛠️ Persyaratan Sistem

- **PHP**: Minimal **PHP 8.3**
- **Ekstensi PHP yang dibutuhkan**:
  - `pdo_sqlite`
  - `sqlite3`
  - `mbstring`
- **Database**: SQLite 3 (otomatis dibuat di `database/hafalan.sqlite`)

---

## 🚀 Cara Menjalankan Aplikasi

### Cara 1: Menggunakan File Batch (Paling Praktis di Windows)
Cukup klik ganda (double click) file:
```
run.bat
```
Skrip ini akan otomatis mendeteksi instalasi PHP 8.3 di komputer Anda (Laragon, XAMPP, atau PATH), menjalankan server lokal pada port `8080`, dan langsung membuka peramban web ke `http://localhost:8080`.

---

### Cara 2: Menjalankan via Terminal / Command Prompt
Buka terminal di folder proyek ini (`H:\CT\CTHapalanQuran`):

```bash
# Menjalankan PHP built-in server
php -S localhost:8080 -t public
```

Jika PHP belum terdaftar di Environment PATH Anda (misal menggunakan PHP Laragon):
```powershell
& "E:\laragon\bin\php\php-8.3.20-Win32-vs16-x64\php.exe" -S localhost:8080 -t public
```

Lalu buka browser Anda di:
👉 **[http://localhost:8080](http://localhost:8080)**

---

## 📂 Struktur Direktori Proyek

```
CTHapalanQuran/
├── app/
│   ├── Database.php            # Koneksi SQLite PDO, migrasi, dan seed data otomatis
│   ├── QuranData.php           # Referensi lengkap 114 Surah, 30 Juz, 604 Halaman Mushaf
│   ├── ProgressEngine.php      # Engine logika Tambah/Kurang otomatis, Sabqi & Manzil
│   └── Models/
│       ├── Santri.php          # Pengelolaan data santri & target capaian
│       └── Setoran.php         # Riwayat setoran & ringkasan statistik
├── database/
│   └── hafalan.sqlite          # Database SQLite aplikasi (WAL mode)
├── public/
│   ├── api/
│   │   ├── santri_info.php     # Endpoint data santri, rekomendasi target & antrean
│   │   ├── save_setoran.php    # Endpoint simpan setoran & eksekusi otomatisasi
│   │   ├── adjust_progress.php # Endpoint tombol cepat tambah/kurang (+/- hal)
│   │   ├── delete_setoran.php  # Endpoint hapus & rollback otomatis
│   │   ├── save_santri.php     # Endpoint tambah santri baru
│   │   └── surah_info.php      # Helper info ayat & halaman surah
│   ├── icons/                  # Icon PWA 192x192 dan 512x512
│   ├── index.php               # Front controller aplikasi
│   ├── manifest.json           # Manifest PWA untuk instalasi APK mobile
│   └── sw.js                   # Service Worker PWA
├── views/
│   ├── layout/
│   │   ├── header.php          # Navbar, meta PWA, styling Tailwind & Amiri font
│   │   └── footer.php          # Bottom nav mobile, toast notification system
│   ├── dashboard.php           # Ringkasan KPI harian & setoran terkini
│   ├── setoran_form.php        # Form input setoran cepat & pemilih otomatis
│   ├── santri_list.php         # Daftar & filter santri
│   ├── santri_detail.php       # Profil, Mushaf 30 Juz, koreksi cepat, cetak rapor
│   ├── murojaah_center.php     # Pusat pantauan antrean Sabqi & peringatan Manzil
│   ├── rekap.php               # Laporan rekapitulasi & cetak PDF
│   └── pengaturan.php          # Pengaturan pesantren, halaqah & backup database
├── index.php                   # Gateway redirector ke public/
├── run.bat                     # Skrip 1-klik jalankan aplikasi di Windows
└── README.md                   # Dokumentasi lengkap
```

### 4. Metode Sambung Akumulatif (Tikrar Berantai / Tangga Hafalan) [BARU ✨]
Fitur unggulan khusus metode tahfidz pesantren dengan tombol sakelar (ceklis aktif / tidak aktif):
- **Logika Pengulangan Berantai**:
  - **Ayat 1**: Diulang sebanyak $N$ kali (misal 3x).
  - **Ayat 2**: Diulang sebanyak $N$ kali (3x).
  - **Fase Sambung (Wasl)**: Otomatis memutar sambungan **Ayat 1 s/d Ayat 2** (1x atau 2x).
  - **Ayat 3**: Diulang sebanyak $N$ kali (3x).
  - **Fase Sambung (Wasl)**: Otomatis memutar sambungan **Ayat 1 s/d Ayat 3**.
  - **Ayat 4**: Diulang sebanyak $N$ kali (3x).
  - **Fase Sambung (Wasl)**: Otomatis memutar sambungan **Ayat 1 s/d Ayat 4**, dan seterusnya hingga akhir rentang.
- **Visualisasi Dinamis**:
  - Badge di sticky player menandai status fase: `🎯 Fokus Target: Ayat X` vs `🔗 Fase Sambung: Ayat 1 s/d X`.
  - Kartu ayat Al-Qur'an yang sedang berada dalam rantai wasl tersorot warna toska secara otomatis.
- **Pengaturan Fleksibel**:
  - Opsi putaran sambung: `1x Sambung` atau `2x Sambung`.
  - Checkbox sakelar untuk mengaktifkan atau menonaktifkan mode akumulatif kapan saja.

---

## 📊 Laporan Lengkap Santri Per Juz & Per Surat (PDF & Excel) [BARU ✨]

Menu khusus di halaman **Laporan Santri** (`index.php?page=laporan_santri`):
1. **Pencarian Santri Cepat**:
   - Dilengkapi `<select>` interaktif dengan filter pencarian nama atau NIS santri.
2. **Tabel Rinci 30 Juz & Seluruh Surat di Bawahnya**:
   - Memetakan setiap Juz (1 s/d 30) beserta seluruh surat yang tercakup di dalamnya.
   - Status terperinci per surat:
     - 🟢 **HAPAL TUNTAS ✓** (Jika seluruh ayat di surat tersebut telah selesai dihafal).
     - 🟡 **BELUM TUNTAS - Ayat X s/d Y** (Jika baru sebagian ayat yang disetorkan, menampilkan persentase progress).
     - ⚪ **BELUM DIHAFAL** (Jika surat belum pernah disetorkan).
3. **Ekspor & Cetak Lengkap**:
   - **Cetak PDF**: Format A4 rapi siap cetak atau simpan sebagai PDF laporan resmi.
   - **Ekspor Excel (.csv / .xls)**: File spreadsheet terformat rapi per juz dan per surat untuk arsip pesantren atau wali santri.

---

## 📖 Alur Penggunaan oleh Musyrif / Ustadz

1. **Memilih Santri**:
   - Di menu **Input Setoran**, pilih nama santri dari daftar dropdown.
   - Posisi terakhir (Surah, Ayat, Halaman, dan Juz) langsung tampil otomatis.
2. **Memilih Kategori**:
   - **Hafalan Baru (Sabaq)**: Rekomendasi target berikutnya langsung terisi otomatis. Musyrif dapat menambah/mengurang dengan tombol cepat `+10 Ayat`, `+1 Hal`, dsb.
   - **Murojaah Baru (Sabqi)**: Klik salah satu dari 5 bagian hafalan baru terakhir untuk mengulanginya.
   - **Murojaah Sebelumnya (Manzil)**: Klik salah satu juz yang direkomendasikan sistem (yang paling lama belum disetorkan).
3. **Penilaian & Otomatisasi**:
   - Tentukan nilai kelancaran: *Mumtaz*, *Jayyid Jiddan*, *Jayyid*, *Maqbul*, atau *Rasib*.
   - Mode otomatisasi:
     - `+ Tambah Otomatis`: Total hafalan bertambah dan posisi santri melompat maju.
     - `= Posisi Tetap`: Posisi tidak berubah (cocok untuk murojaah).
     - `- Kurang Otomatis`: Memundurkan posisi atau menurunkan capaian jika santri mengulang.
4. **Menyimpan**:
   - Klik **Simpan & Proses Otomatis**. Notifikasi langsung mengonfirmasi penambahan atau pengurangan hafalan secara instan.

