<?php
declare(strict_types=1);

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak didukung']);
    exit;
}

$raw = file_get_contents('php://input');
$input = json_decode($raw, true) ?? $_POST;

$nis = trim((string)($input['nis'] ?? ''));
$nama = trim((string)($input['nama'] ?? ''));

if (empty($nis) || empty($nama)) {
    echo json_encode(['success' => false, 'message' => 'NIS dan Nama wajib diisi']);
    exit;
}

try {
    $db = Database::getConnection();
    $santriModel = new Santri($db);

    $id = $santriModel->create([
        'nis' => $nis,
        'nama' => $nama,
        'jenis_kelamin' => $input['jenis_kelamin'] ?? 'L',
        'halaqah_id' => !empty($input['halaqah_id']) ? (int)$input['halaqah_id'] : null,
        'target_juz' => (int)($input['target_juz'] ?? 30),
        'arah_hafalan' => $input['arah_hafalan'] ?? 'juz30_mundur',
        'terakhir_surah_id' => (int)($input['terakhir_surah_id'] ?? 78),
        'terakhir_ayat' => 0,
        'total_halaman_hafal' => 0.0,
        'total_juz_hafal' => 0.0
    ]);

    echo json_encode(['success' => true, 'id' => $id, 'message' => 'Santri berhasil disimpan']);
} catch (\Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()]);
}
