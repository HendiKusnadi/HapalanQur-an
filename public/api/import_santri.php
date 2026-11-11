<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/QuranData.php';
require_once dirname(__DIR__, 2) . '/app/Database.php';
require_once dirname(__DIR__, 2) . '/app/Auth.php';
require_once dirname(__DIR__, 2) . '/app/Models/Santri.php';

use App\Database;
use App\Auth;
use App\Models\Santri;

if (!Auth::check()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak: Silakan login sebagai ustadz/admin terlebih dahulu.']);
    exit;
}

// Download CSV Template
if (isset($_GET['action']) && $_GET['action'] === 'template') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="template_import_santri.csv"');
    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($out, ['nis', 'nama', 'jenis_kelamin', 'target_juz', 'arah_hafalan', 'id_halaqah']);
    fputcsv($out, ['2026101', 'Fulan bin Fulan', 'L', '30', 'juz30_mundur', '1']);
    fputcsv($out, ['2026102', 'Fulanah binti Fulan', 'P', '10', 'juz30_mundur', '2']);
    fclose($out);
    exit;
}

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file_csv'])) {
    echo json_encode(['success' => false, 'message' => 'File CSV tidak ditemukan']);
    exit;
}

$file = $_FILES['file_csv'];
if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
    echo json_encode(['success' => false, 'message' => 'Gagal mengunggah file']);
    exit;
}

try {
    $db = Database::getConnection();
    $santriModel = new Santri($db);

    $handle = fopen($file['tmp_name'], 'r');
    if (!$handle) {
        echo json_encode(['success' => false, 'message' => 'Tidak dapat membaca file CSV']);
        exit;
    }

    $header = fgetcsv($handle); // Skip header row
    $importedCount = 0;

    $stmtCheck = $db->prepare("SELECT id FROM santri WHERE nis = ?");
    $stmtInsert = $db->prepare("
        INSERT INTO santri (nis, nama, jenis_kelamin, halaqah_id, target_juz, arah_hafalan, terakhir_surah_id, terakhir_ayat, terakhir_halaman, terakhir_juz, total_halaman_hafal, total_juz_hafal, status_aktif)
        VALUES (?, ?, ?, ?, ?, ?, 78, 0, 582, 30, 0, 0, 1)
    ");

    $db->beginTransaction();
    while (($row = fgetcsv($handle)) !== false) {
        if (empty($row[0]) || empty($row[1])) {
            continue;
        }

        $nis = trim($row[0]);
        $nama = trim($row[1]);
        $jk = strtoupper(trim($row[2] ?? 'L')) === 'P' ? 'P' : 'L';
        $targetJuz = (int)($row[3] ?? 30);
        $arah = trim($row[4] ?? 'juz30_mundur');
        $halaqahId = !empty($row[5]) ? (int)$row[5] : null;

        // Cek apakah NIS sudah ada
        $stmtCheck->execute([$nis]);
        $exists = $stmtCheck->fetchColumn();

        if (!$exists) {
            $stmtInsert->execute([$nis, $nama, $jk, $halaqahId, $targetJuz, $arah]);
            $importedCount++;
        }
    }
    $db->commit();
    fclose($handle);

    echo json_encode([
        'success' => true,
        'imported_count' => $importedCount,
        'message' => "Berhasil mengimpor {$importedCount} data santri baru."
    ]);
} catch (\Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

