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

$id = (int)($input['id'] ?? 0);
$nis = trim((string)($input['nis'] ?? ''));
$nama = trim((string)($input['nama'] ?? ''));

if ($id <= 0 || empty($nis) || empty($nama)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

try {
    $db = Database::getConnection();
    $santriModel = new Santri($db);

    $ok = $santriModel->update($id, [
        'nis' => $nis,
        'nama' => $nama,
        'jenis_kelamin' => $input['jenis_kelamin'] ?? 'L',
        'halaqah_id' => !empty($input['halaqah_id']) ? (int)$input['halaqah_id'] : null,
        'target_juz' => (int)($input['target_juz'] ?? 30),
        'arah_hafalan' => $input['arah_hafalan'] ?? 'juz30_mundur',
        'status_aktif' => (int)($input['status_aktif'] ?? 1)
    ]);

    echo json_encode(['success' => $ok, 'message' => $ok ? 'Profil santri berhasil diperbarui' : 'Gagal memperbarui']);
} catch (\Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

