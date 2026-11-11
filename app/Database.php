<?php
declare(strict_types=1);

namespace App;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $dbPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'hafalan.sqlite';
            $dbDir = dirname($dbPath);

            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0777, true);
            }

            try {
                self::$pdo = new PDO('sqlite:' . $dbPath);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                // Aktifkan WAL mode untuk performa optimal
                self::$pdo->exec('PRAGMA journal_mode = WAL;');
                self::$pdo->exec('PRAGMA foreign_keys = ON;');

                // Jalankan inisialisasi tabel
                self::migrate();
            } catch (PDOException $e) {
                die('Koneksi Database Gagal: ' . htmlspecialchars($e->getMessage()));
            }
        }

        return self::$pdo;
    }

    private static function migrate(): void
    {
        $db = self::$pdo;

        $db->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                nama_lengkap TEXT NOT NULL,
                role TEXT DEFAULT 'admin', -- 'admin', 'musyrif'
                terakhir_login DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS login_attempts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ip_address TEXT NOT NULL,
                username TEXT,
                attempted_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE INDEX IF NOT EXISTS idx_login_attempts_ip ON login_attempts(ip_address, attempted_at);

            CREATE TABLE IF NOT EXISTS pengaturan (
                kunci TEXT PRIMARY KEY,
                nilai TEXT
            );

            CREATE TABLE IF NOT EXISTS halaqah (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nama TEXT NOT NULL,
                pembimbing TEXT NOT NULL,
                keterangan TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS santri (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nis TEXT UNIQUE NOT NULL,
                nama TEXT NOT NULL,
                jenis_kelamin TEXT CHECK(jenis_kelamin IN ('L', 'P')) DEFAULT 'L',
                halaqah_id INTEGER,
                target_juz INTEGER DEFAULT 30,
                arah_hafalan TEXT DEFAULT 'juz30_mundur', -- 'juz30_mundur' atau 'juz1_maju'
                terakhir_surah_id INTEGER DEFAULT 78,
                terakhir_ayat INTEGER DEFAULT 0,
                terakhir_halaman INTEGER DEFAULT 582,
                terakhir_juz INTEGER DEFAULT 30,
                total_halaman_hafal REAL DEFAULT 0,
                total_juz_hafal REAL DEFAULT 0,
                status_aktif INTEGER DEFAULT 1,
                foto TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (halaqah_id) REFERENCES halaqah(id) ON DELETE SET NULL
            );

            CREATE TABLE IF NOT EXISTS setoran (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                santri_id INTEGER NOT NULL,
                jenis_setoran TEXT NOT NULL CHECK(jenis_setoran IN ('hafalan_baru', 'murojaah_baru', 'murojaah_sebelumnya')),
                surah_mulai_id INTEGER NOT NULL,
                ayat_mulai INTEGER NOT NULL,
                surah_selesai_id INTEGER NOT NULL,
                ayat_selesai INTEGER NOT NULL,
                halaman_mulai INTEGER NOT NULL,
                halaman_selesai INTEGER NOT NULL,
                juz INTEGER NOT NULL,
                jumlah_halaman REAL NOT NULL DEFAULT 1.0,
                penilaian TEXT NOT NULL CHECK(penilaian IN ('mumtaz', 'jayyid_jiddan', 'jayyid', 'maqbul', 'rasib')),
                status_kelulusan TEXT NOT NULL CHECK(status_kelulusan IN ('lulus', 'ulang')),
                perubahan_otomatis TEXT DEFAULT 'tetap', -- 'tambah', 'kurang', 'tetap'
                catatan TEXT,
                musyrif_nama TEXT,
                tanggal_setoran DATE NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (santri_id) REFERENCES santri(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS murojaah_antrean (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                santri_id INTEGER NOT NULL,
                jenis TEXT NOT NULL CHECK(jenis IN ('baru', 'sebelumnya')),
                judul TEXT NOT NULL,
                surah_mulai_id INTEGER,
                ayat_mulai INTEGER,
                surah_selesai_id INTEGER,
                ayat_selesai INTEGER,
                halaman_mulai INTEGER,
                halaman_selesai INTEGER,
                juz INTEGER,
                status TEXT CHECK(status IN ('menunggu', 'selesai', 'dilewati')) DEFAULT 'menunggu',
                frekuensi INTEGER DEFAULT 0,
                tanggal_terakhir_setoran DATE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (santri_id) REFERENCES santri(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS quran_teks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                surah_id INTEGER NOT NULL,
                nomor_ayat INTEGER NOT NULL,
                teks_arab TEXT NOT NULL,
                teks_latin TEXT,
                terjemahan TEXT,
                juz INTEGER,
                halaman INTEGER,
                UNIQUE(surah_id, nomor_ayat)
            );

            CREATE INDEX IF NOT EXISTS idx_quran_surah_ayat ON quran_teks(surah_id, nomor_ayat);

            CREATE INDEX IF NOT EXISTS idx_setoran_santri ON setoran(santri_id);
            CREATE INDEX IF NOT EXISTS idx_setoran_jenis ON setoran(jenis_setoran);
            CREATE INDEX IF NOT EXISTS idx_setoran_tanggal ON setoran(tanggal_setoran);
        ");

        // Seed user admin jika belum ada
        $checkUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($checkUsers == 0) {
            $defaultPass = password_hash('admin123', PASSWORD_DEFAULT);
            $stmtUser = $db->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
            $stmtUser->execute(['admin', $defaultPass, 'Ustadz Pembimbing (Admin)', 'admin']);
        }

        // Seed data dasar jika pengaturan belum ada
        $check = $db->query("SELECT COUNT(*) FROM pengaturan")->fetchColumn();
        if ($check == 0) {
            $stmt = $db->prepare("INSERT INTO pengaturan (kunci, nilai) VALUES (?, ?)");
            $stmt->execute(['nama_pesantren', 'Pondok Pesantren & Rumah Tahfidz Al-Qur\'an']);
            $stmt->execute(['nama_aplikasi', 'SIM Tahfidz Quran']);
            $stmt->execute(['default_musyrif', 'Ust. Ahmad Fauzan, Al-Hafizh']);
            $stmt->execute(['sabqi_threshold_setoran', '5']); // Murojaah Baru menyimpan 5 setoran terakhir
            $stmt->execute(['manzil_interval_hari', '7']); // Murojaah Lama rotasi 7 hari
        }

        // Seed halaqah default jika belum ada
        $checkHalaqah = $db->query("SELECT COUNT(*) FROM halaqah")->fetchColumn();
        if ($checkHalaqah == 0) {
            $db->exec("
                INSERT INTO halaqah (nama, pembimbing, keterangan) VALUES 
                ('Halaqah Abu Bakar Ash-Shiddiq', 'Ust. Ahmad Fauzan, Al-Hafizh', 'Tingkat Menengah - Ziyadah & Mutqin 5-10 Juz'),
                ('Halaqah Utsman bin Affan', 'Ust. Muhammad Wildan, S.Pd.I', 'Tingkat Dasar - Ziyadah Juz 30 & 29'),
                ('Halaqah Ali bin Abi Thalib', 'Ust. Syahrul Ramadhan, Al-Hafizh', 'Tingkat Lanjutan - 15-30 Juz');
            ");
        }

        // Seed santri contoh jika belum ada
        $checkSantri = $db->query("SELECT COUNT(*) FROM santri")->fetchColumn();
        if ($checkSantri == 0) {
            $db->exec("
                INSERT INTO santri (nis, nama, jenis_kelamin, halaqah_id, target_juz, arah_hafalan, terakhir_surah_id, terakhir_ayat, terakhir_halaman, terakhir_juz, total_halaman_hafal, total_juz_hafal) VALUES
                ('2026001', 'Muhammad Zaky Al-Farisi', 'L', 1, 30, 'juz30_mundur', 78, 20, 582, 30, 21.0, 1.0),
                ('2026002', 'Ahmad Raihan Pratama', 'L', 2, 10, 'juz30_mundur', 82, 19, 587, 30, 12.0, 0.6),
                ('2026003', 'Fathir Abdurrahman', 'L', 1, 30, 'juz1_maju', 2, 141, 21, 1, 21.0, 1.0),
                ('2026004', 'Nabila Nur Azizah', 'P', 2, 5, 'juz30_mundur', 91, 15, 595, 30, 6.0, 0.3),
                ('2026005', 'Hafizh Shiddiq Maulana', 'L', 3, 30, 'juz30_mundur', 67, 30, 564, 29, 42.0, 2.0);
            ");

            // Seed sample setoran untuk demonstrasi
            $today = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $db->exec("
                INSERT INTO setoran (santri_id, jenis_setoran, surah_mulai_id, ayat_mulai, surah_selesai_id, ayat_selesai, halaman_mulai, halaman_selesai, juz, jumlah_halaman, penilaian, status_kelulusan, perubahan_otomatis, catatan, musyrif_nama, tanggal_setoran) VALUES
                (1, 'hafalan_baru', 78, 1, 78, 20, 582, 582, 30, 1.0, 'mumtaz', 'lulus', 'tambah', 'Makhraj huruf shad dan tho sangat bagus', 'Ust. Ahmad Fauzan, Al-Hafizh', '$today'),
                (1, 'murojaah_baru', 78, 1, 78, 10, 582, 582, 30, 0.5, 'jayyid_jiddan', 'lulus', 'tetap', 'Lancar, tempo bacaan tartil', 'Ust. Ahmad Fauzan, Al-Hafizh', '$today'),
                (2, 'hafalan_baru', 81, 1, 81, 29, 586, 586, 30, 1.0, 'mumtaz', 'lulus', 'tambah', 'Hafal tuntas satu surah At-Takwir', 'Ust. Muhammad Wildan, S.Pd.I', '$yesterday'),
                (3, 'murojaah_sebelumnya', 1, 1, 2, 50, 1, 8, 1, 8.0, 'mumtaz', 'lulus', 'tetap', 'Murojaah awal Juz 1 sangat lancar', 'Ust. Ahmad Fauzan, Al-Hafizh', '$today');
            ");
        }

        // Jalankan seed ayat Al-Qur'an
        if (class_exists('App\QuranSeeds')) {
            QuranSeeds::seedInitialSurahs($db);
        }
    }
}

