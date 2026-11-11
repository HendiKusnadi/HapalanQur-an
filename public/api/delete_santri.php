<?php
declare(strict_types=1);

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

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
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID santri tidak valid']);
    exit;
}

try {
    $db = Database::getConnection();
    $santriModel = new Santri($db);

    $ok = $santriModel->delete($id);
    echo json_encode(['success' => $ok, 'message' => $ok ? 'Data santri berhasil dihapus' : 'Gagal menghapus']);
} catch (\Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

