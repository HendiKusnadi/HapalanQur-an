<?php
declare(strict_types=1);

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

require_once dirname(__DIR__, 2) . '/app/QuranData.php';
require_once dirname(__DIR__, 2) . '/app/Database.php';
require_once dirname(__DIR__, 2) . '/app/ProgressEngine.php';

use App\Database;
use App\ProgressEngine;
use App\QuranData;

$santriId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($santriId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID Santri tidak valid']);
    exit;
}

try {
    $db = Database::getConnection();
    $engine = new ProgressEngine($db);
    $santri = $engine->getSantri($santriId);

    if (!$santri) {
        echo json_encode(['success' => false, 'message' => 'Santri tidak ditemukan']);
        exit;
    }

    $surahInfo = QuranData::getSurah((int)$santri['terakhir_surah_id']);
    $santri['terakhir_surah_nama'] = $surahInfo['nama'] ?? 'Surah ' . $santri['terakhir_surah_id'];
    $santri['terakhir_surah_arab'] = $surahInfo['arab'] ?? '';

    // Hitung saran target berikutnya untuk hafalan baru
    $nextTarget = QuranData::calculateNextTarget(
        (int)$santri['terakhir_surah_id'],
        (int)$santri['terakhir_ayat'],
        $santri['arah_hafalan'] ?? 'juz30_mundur',
        10
    );

    $nextSurah = QuranData::getSurah($nextTarget['next_surah_id']);
    $nextTarget['surah_nama'] = $nextSurah['nama'] ?? 'Surah ' . $nextTarget['next_surah_id'];
    $nextTarget['surah_arab'] = $nextSurah['arab'] ?? '';
    $nextTarget['total_ayat_surah'] = $nextSurah['ayat'] ?? 0;
    $nextTarget['juz'] = QuranData::getJuzByPage($nextTarget['next_hal_mulai']);

    // Ambil antrean Murojaah Baru (Sabqi)
    $murojaahBaru = $engine->getMurojaahBaruQueue($santriId, 5);

    // Ambil rekomendasi Murojaah Sebelumnya (Manzil)
    $murojaahLama = $engine->getMurojaahSebelumnyaRecommendations($santriId);

    // Ambil riwayat setoran terakhir santri ini
    $stmtRecent = $db->prepare("
        SELECT * FROM setoran
        WHERE santri_id = ?
        ORDER BY tanggal_setoran DESC, id DESC
        LIMIT 5
    ");
    $stmtRecent->execute([$santriId]);
    $recentSetoran = $stmtRecent->fetchAll();

    foreach ($recentSetoran as &$rs) {
        $s1 = QuranData::getSurah((int)$rs['surah_mulai_id']);
        $s2 = QuranData::getSurah((int)$rs['surah_selesai_id']);
        $rs['surah_mulai_nama'] = $s1['nama'] ?? '';
        $rs['surah_selesai_nama'] = $s2['nama'] ?? '';
    }

    echo json_encode([
        'success' => true,
        'santri' => $santri,
        'next_target' => $nextTarget,
        'murojaah_baru' => $murojaahBaru,
        'murojaah_sebelumnya' => $murojaahLama,
        'recent_setoran' => $recentSetoran
    ]);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
