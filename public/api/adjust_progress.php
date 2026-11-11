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

$santriId = (int)($input['santri_id'] ?? 0);
$action = $input['action'] ?? ''; // 'tambah' atau 'kurang'
$pages = (float)($input['pages'] ?? 1.0);

if ($santriId <= 0 || !in_array($action, ['tambah', 'kurang'])) {
    echo json_encode(['success' => false, 'message' => 'Parameter tidak valid']);
    exit;
}

try {
    $db = Database::getConnection();
    $engine = new ProgressEngine($db);
    $res = $engine->adjustSantriProgress($santriId, $action, $pages);
    echo json_encode($res);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
