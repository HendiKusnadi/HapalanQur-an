<?php
declare(strict_types=1);

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

require_once dirname(__DIR__, 2) . '/app/QuranData.php';
require_once dirname(__DIR__, 2) . '/app/Database.php';
require_once dirname(__DIR__, 2) . '/app/Auth.php';
require_once dirname(__DIR__, 2) . '/app/ProgressEngine.php';

use App\Database;
use App\Auth;
use App\ProgressEngine;
use App\QuranData;

if (!Auth::check()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak: Silakan login sebagai ustadz/admin terlebih dahulu.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak didukung']);
    exit;
}

// Support both JSON payload and standard form data
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (str_contains($contentType, 'application/json')) {
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true) ?? [];
} else {
    $input = $_POST;
}

$santriId = (int)($input['santri_id'] ?? 0);
if ($santriId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Pilih santri terlebih dahulu']);
    exit;
}

$jenis = $input['jenis_setoran'] ?? 'hafalan_baru';
$surahMulai = (int)($input['surah_mulai_id'] ?? 1);
$ayatMulai = (int)($input['ayat_mulai'] ?? 1);
$surahSelesai = (int)($input['surah_selesai_id'] ?? $surahMulai);
$ayatSelesai = (int)($input['ayat_selesai'] ?? $ayatMulai);

$halMulai = (int)($input['halaman_mulai'] ?? QuranData::estimatePageBySurahAyah($surahMulai, $ayatMulai));
$halSelesai = (int)($input['halaman_selesai'] ?? QuranData::estimatePageBySurahAyah($surahSelesai, $ayatSelesai));
$juz = (int)($input['juz'] ?? QuranData::getJuzByPage($halMulai));

$penilaian = $input['penilaian'] ?? 'mumtaz';
$statusKelulusan = $input['status_kelulusan'] ?? in_array($penilaian, ['mumtaz', 'jayyid_jiddan', 'jayyid']) ? 'lulus' : 'ulang';
$modeOtomatis = $input['mode_otomatis'] ?? ($statusKelulusan === 'lulus' && $jenis === 'hafalan_baru' ? 'tambah' : 'tetap');

$catatan = trim((string)($input['catatan'] ?? ''));
$musyrif = trim((string)($input['musyrif_nama'] ?? 'Ust. Pembimbing'));
$tanggal = !empty($input['tanggal_setoran']) ? $input['tanggal_setoran'] : date('Y-m-d');

try {
    $db = Database::getConnection();
    $engine = new ProgressEngine($db);

    $result = $engine->recordSetoran([
        'santri_id' => $santriId,
        'jenis_setoran' => $jenis,
        'surah_mulai_id' => $surahMulai,
        'ayat_mulai' => $ayatMulai,
        'surah_selesai_id' => $surahSelesai,
        'ayat_selesai' => $ayatSelesai,
        'halaman_mulai' => $halMulai,
        'halaman_selesai' => $halSelesai,
        'juz' => $juz,
        'penilaian' => $penilaian,
        'status_kelulusan' => $statusKelulusan,
        'mode_otomatis' => $modeOtomatis,
        'catatan' => $catatan,
        'musyrif_nama' => $musyrif,
        'tanggal_setoran' => $tanggal,
    ]);

    // Kirim juga data santri terbaru
    if ($result['success']) {
        $result['santri'] = $engine->getSantri($santriId);
    }

    echo json_encode($result);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
