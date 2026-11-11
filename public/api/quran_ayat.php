<?php
declare(strict_types=1);

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

require_once dirname(__DIR__, 2) . '/app/QuranData.php';
require_once dirname(__DIR__, 2) . '/app/Database.php';

use App\Database;
use App\QuranData;

$surahId = isset($_GET['surah']) ? (int)$_GET['surah'] : 1;
$surahId = max(1, min(114, $surahId));

$ayatMulai = isset($_GET['ayat_mulai']) ? (int)$_GET['ayat_mulai'] : 1;
$ayatSelesai = isset($_GET['ayat_selesai']) ? (int)$_GET['ayat_selesai'] : 0;
$qari = $_GET['qari'] ?? 'Alafasy_128kbps';

$surahInfo = QuranData::getSurah($surahId);
if (!$surahInfo) {
    echo json_encode(['success' => false, 'message' => 'Surah tidak ditemukan']);
    exit;
}

$totalAyat = (int)$surahInfo['ayat'];
$ayatMulai = max(1, min($totalAyat, $ayatMulai));
if ($ayatSelesai <= 0 || $ayatSelesai < $ayatMulai) {
    $ayatSelesai = min($totalAyat, $ayatMulai + 4); // Default 5 ayat
}
$ayatSelesai = min($totalAyat, $ayatSelesai);

$db = Database::getConnection();

// Ambil dari database lokal
$stmt = $db->prepare("
    SELECT * FROM quran_teks 
    WHERE surah_id = ? AND nomor_ayat BETWEEN ? AND ?
    ORDER BY nomor_ayat ASC
");
$stmt->execute([$surahId, $ayatMulai, $ayatSelesai]);
$cachedVerses = $stmt->fetchAll();

// Daftar Qari yang didukung EveryAyah
$qariList = [
    'Alafasy_128kbps' => ['nama' => 'Misyari Rasyid Al-Afasy', 'kualitas' => '128kbps', 'karakter' => 'Tartil & Jelas (Rekomendasi Pemula)'],
    'Husary_128kbps' => ['nama' => 'Mahmoud Khalil Al-Hushari', 'kualitas' => '128kbps', 'karakter' => 'Standar Tajwid & Makhraj Dunia'],
    'Husary_Mujawwad_128kbps' => ['nama' => 'Al-Hushari (Mujawwad)', 'kualitas' => '128kbps', 'karakter' => 'Irama Mujawwad Bertajwid'],
    'Minshawy_Murattal_128kbps' => ['nama' => 'Muhammad Siddiq Al-Minshawi', 'kualitas' => '128kbps', 'karakter' => 'Syahdu, Khusyuk & Bernyawa'],
    'Abdurrahmaan_As-Sudais_192kbps' => ['nama' => 'Abdurrahman As-Sudais', 'kualitas' => '192kbps', 'karakter' => 'Imam Masjidil Haram, Tegas & Lantang'],
    'Saood_ash-Shuraym_128kbps' => ['nama' => "Sa'ud Ash-Shuraim", 'kualitas' => '128kbps', 'karakter' => 'Tempo Sedang-Cepat & Ritmis'],
    'Ghamadi_40kbps' => ['nama' => 'Saad Al-Ghamdi', 'kualitas' => '40kbps', 'karakter' => 'Alunan Lembut & Menenangkan'],
    'MaherAlMuaiqly128kbps' => ['nama' => 'Maher Al-Muaiqly', 'kualitas' => '128kbps', 'karakter' => 'Imam Masjidil Haram, Tartil'],
    'Abu_Bakr_Ash-Shaatree_128kbps' => ['nama' => 'Abu Bakr Asy-Syathiri', 'kualitas' => '128kbps', 'karakter' => 'Lantunan Halus & Stabil'],
    'Hani_Rifai_192kbps' => ['nama' => 'Hani Ar-Rifai', 'kualitas' => '192kbps', 'karakter' => 'Sangat Mengharukan & Menyentuh Hati'],
    'Yasser_Ad-Dussary_128kbps' => ['nama' => 'Yasser Ad-Dussary', 'kualitas' => '128kbps', 'karakter' => 'Lagu Khas Hijazi Modern'],
    'Hudhaify_128kbps' => ['nama' => 'Ali Al-Hudhaify', 'kualitas' => '128kbps', 'karakter' => 'Imam Masjid Nabawi, Lambat & Teliti'],
];

if (!isset($qariList[$qari])) {
    $qari = 'Alafasy_128kbps';
}

// Generate URL audio per ayat untuk pengisi suara yang dipilih
function formatAyahAudioUrl(string $qariId, int $surah, int $ayah): string {
    $sStr = str_pad((string)$surah, 3, '0', STR_PAD_LEFT);
    $aStr = str_pad((string)$ayah, 3, '0', STR_PAD_LEFT);
    return "https://everyayah.com/data/{$qariId}/{$sStr}{$aStr}.mp3";
}

$verses = [];
$needClientFetch = count($cachedVerses) < (($ayatSelesai - $ayatMulai) + 1);

if (!$needClientFetch) {
    foreach ($cachedVerses as $cv) {
        $aNum = (int)$cv['nomor_ayat'];
        $verses[] = [
            'surah_id' => $surahId,
            'nomor_ayat' => $aNum,
            'teks_arab' => $cv['teks_arab'],
            'teks_latin' => $cv['teks_latin'] ?? '',
            'terjemahan' => $cv['terjemahan'] ?? '',
            'juz' => (int)($cv['juz'] ?: QuranData::getJuzByPage((int)($cv['halaman'] ?? $surahInfo['hal_mulai']))),
            'halaman' => (int)($cv['halaman'] ?: QuranData::estimatePageBySurahAyah($surahId, $aNum)),
            'audio_url' => formatAyahAudioUrl($qari, $surahId, $aNum)
        ];
    }
} else {
    // Siapkan placeholder terstruktur jika teks belum ter-cache lokal
    // Client JavaScript akan otomatis fetch teks dari CDN Al-Quran dan menyimpannya kembali!
    for ($a = $ayatMulai; $a <= $ayatSelesai; $a++) {
        // Cek jika ayat ini ada di cached
        $found = null;
        foreach ($cachedVerses as $cv) {
            if ((int)$cv['nomor_ayat'] === $a) {
                $found = $cv;
                break;
            }
        }

        $verses[] = [
            'surah_id' => $surahId,
            'nomor_ayat' => $a,
            'teks_arab' => $found ? $found['teks_arab'] : '',
            'teks_latin' => $found ? ($found['teks_latin'] ?? '') : '',
            'terjemahan' => $found ? ($found['terjemahan'] ?? '') : '',
            'juz' => (int)($found ? $found['juz'] : QuranData::getJuzByPage(QuranData::estimatePageBySurahAyah($surahId, $a))),
            'halaman' => (int)($found ? $found['halaman'] : QuranData::estimatePageBySurahAyah($surahId, $a)),
            'audio_url' => formatAyahAudioUrl($qari, $surahId, $a)
        ];
    }
}

echo json_encode([
    'success' => true,
    'surah' => $surahInfo,
    'ayat_mulai' => $ayatMulai,
    'ayat_selesai' => $ayatSelesai,
    'total_ayat_selected' => ($ayatSelesai - $ayatMulai) + 1,
    'qari_aktif' => [
        'id' => $qari,
        'info' => $qariList[$qari]
    ],
    'qari_list' => $qariList,
    'need_fetch_text' => $needClientFetch,
    'verses' => $verses
]);

