<?php
declare(strict_types=1);

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

require_once dirname(__DIR__, 2) . '/app/QuranData.php';

use App\QuranData;

$surahId = (int)($_GET['id'] ?? 1);
$ayat = (int)($_GET['ayat'] ?? 1);

$surah = QuranData::getSurah($surahId);

if (!$surah) {
    echo json_encode(['success' => false, 'message' => 'Surah tidak ditemukan']);
    exit;
}

$hal = QuranData::estimatePageBySurahAyah($surahId, $ayat);
$juz = QuranData::getJuzByPage($hal);

echo json_encode([
    'success' => true,
    'surah' => $surah,
    'halaman' => $hal,
    'juz' => $juz
]);
