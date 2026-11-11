<?php
declare(strict_types=1);

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

require_once dirname(__DIR__, 2) . '/app/Database.php';

use App\Database;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode tidak valid']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?? [];

$surahId = (int)($data['surah_id'] ?? 0);
$verses = $data['verses'] ?? [];

if ($surahId <= 0 || empty($verses)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

try {
    $db = Database::getConnection();
    $stmt = $db->prepare("
        INSERT OR REPLACE INTO quran_teks 
        (surah_id, nomor_ayat, teks_arab, teks_latin, terjemahan, juz, halaman) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $db->beginTransaction();
    foreach ($verses as $v) {
        $stmt->execute([
            $surahId,
            (int)$v['nomor_ayat'],
            $v['teks_arab'] ?? '',
            $v['teks_latin'] ?? '',
            $v['terjemahan'] ?? '',
            (int)($v['juz'] ?? 30),
            (int)($v['halaman'] ?? 1)
        ]);
    }
    $db->commit();

    echo json_encode(['success' => true, 'count' => count($verses), 'message' => 'Teks ayat berhasil disimpan ke cache lokal']);
} catch (\Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

