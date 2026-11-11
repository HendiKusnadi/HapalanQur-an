<?php
declare(strict_types=1);

// Built-in PHP CLI Server router static file pass-through
if (php_sapi_name() === 'cli-server') {
    $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $filePath = __DIR__ . $uriPath;
    if ($uriPath !== '/' && file_exists($filePath) && !is_dir($filePath)) {
        // Jika file fisik nyata ada dan bukan index.php, biarkan server bawaan PHP melayani langsung
        if (realpath($filePath) !== realpath(__FILE__)) {
            return false;
        }
    }
}

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

$baseDir = dirname(__DIR__);

// Load Autoload / Core Classes
require_once $baseDir . '/app/QuranData.php';
require_once $baseDir . '/app/Database.php';
require_once $baseDir . '/app/Security.php';
require_once $baseDir . '/app/Auth.php';
require_once $baseDir . '/app/ProgressEngine.php';
require_once $baseDir . '/app/Models/Santri.php';
require_once $baseDir . '/app/Models/Setoran.php';

use App\Database;
use App\Auth;
use App\Security;

// Terapkan HTTP Security Headers & Konfigurasi Session
Security::init();
Auth::startSession();

// Tentukan Base Path jika aplikasi berada di dalam subdirektori (Apache/Laragon)
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$baseUrl = ($scriptDir !== '') ? ($scriptDir . '/') : '/';
$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

if ($scriptDir !== '' && str_starts_with($uriPath, $scriptDir)) {
    $uriPath = substr($uriPath, strlen($scriptDir));
}
$uriPath = '/' . ltrim($uriPath, '/');
$route = trim($uriPath, '/');

// Redirect 301 untuk trailing slash pada URL selain root (mencegah kebingungan relative path di browser)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $uriPath !== '/' && str_ends_with($uriPath, '/') && !file_exists(__DIR__ . $uriPath)) {
    $cleanUri = rtrim($uriPath, '/');
    if ($scriptDir !== '') {
        $cleanUri = $scriptDir . $cleanUri;
    }
    $qStr = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
    header("Location: {$cleanUri}{$qStr}", true, 301);
    exit;
}

// Jika route kosong atau index.php, cek parameter ?page= (backward compatibility)
if ($route === '' || $route === 'index.php') {
    $route = $_GET['page'] ?? 'player';
}

// Redirect 301 untuk URL legacy yang masih menggunakan query "?page=" jika GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['page']) && $route !== '') {
    $cleanTarget = str_replace('_', '-', (string)$_GET['page']);
    $queryParams = $_GET;
    unset($queryParams['page']);
    $queryString = !empty($queryParams) ? '?' . http_build_query($queryParams) : '';
    
    // Redirect hanya jika URL aslinya mengandung page=
    if (strpos($_SERVER['REQUEST_URI'] ?? '', 'page=') !== false) {
        $fullTarget = ($scriptDir !== '') ? ($scriptDir . '/' . $cleanTarget) : $cleanTarget;
        header("Location: {$fullTarget}{$queryString}", true, 301);
        exit;
    }
}

// Clean API Routing (misal: /api/quran-ayat, /api/save-setoran, atau dari nested path /santri/api/...)
if (preg_match('#(?:^|/)api/(.+)#', $route, $apiMatches)) {
    $endpoint = $apiMatches[1];
    $apiFileBasename = str_replace('-', '_', preg_replace('/\.php$/', '', $endpoint));
    $apiPath = __DIR__ . '/api/' . $apiFileBasename . '.php';

    if (file_exists($apiPath)) {
        require $apiPath;
        exit;
    }

    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'API endpoint not found: ' . $endpoint]);
    exit;
}

// Parameterized Clean URLs (misal: /santri/5, /laporan-santri/5, /cetak-rapor/5)
if (preg_match('#^santri/(\d+)$#', $route, $matches)) {
    $_GET['id'] = (int)$matches[1];
    $route = 'santri-detail';
} elseif (preg_match('#^laporan-santri/(\d+)$#', $route, $matches)) {
    $_GET['id'] = (int)$matches[1];
    $route = 'laporan-santri';
} elseif (preg_match('#^cetak-rapor/(\d+)$#', $route, $matches)) {
    $_GET['id'] = (int)$matches[1];
    $route = 'cetak-rapor';
} elseif (preg_match('#^cetak-laporan/(\d+)$#', $route, $matches)) {
    $_GET['id'] = (int)$matches[1];
    $route = 'cetak-laporan';
}

// Map Clean Route ke Internal Page Identifier
$page = match ($route) {
    '', 'player', 'hafalan' => 'player',
    'dashboard' => 'dashboard',
    'setoran', 'input-setoran' => 'setoran',
    'murojaah', 'murojaah-center' => 'murojaah',
    'santri' => 'santri',
    'santri-detail', 'santri_detail' => 'santri_detail',
    'laporan-santri', 'laporan_santri', 'laporan-siswa' => 'laporan_santri',
    'rekap', 'rekapitulasi' => 'rekap',
    'pengaturan', 'settings' => 'pengaturan',
    'panduan', 'buku-panduan' => 'panduan',
    'landing', 'landingpage', 'beranda', 'home' => 'landing',
    'login' => 'login',
    'logout' => 'logout',
    'cetak-rapor', 'cetak_rapor' => 'cetak_rapor',
    'cetak-laporan', 'cetak_laporan_pdf', 'cetak-laporan-pdf' => 'cetak_laporan_pdf',
    'backup-db', 'download-db' => 'download_db',
    'download-apk' => 'download_apk',
    default => '404',
};

// Handle Landing Page
if ($page === 'landing') {
    $landingFile = $baseDir . '/landingpage/index.html';
    if (file_exists($landingFile)) {
        header('Content-Type: text/html; charset=utf-8');
        readfile($landingFile);
        exit;
    }
}

// Handle Download APK Langsung dari Web Server
if ($page === 'download_apk' || $route === 'downloads/SIM-Tahfidz-Quran.apk') {
    $apkFile = $baseDir . '/SIM-Tahfidz-Quran.apk';
    if (file_exists($apkFile)) {
        header('Content-Type: application/vnd.android.package-archive');
        header('Content-Disposition: attachment; filename="SIM-Tahfidz-Quran.apk"');
        header('Content-Length: ' . filesize($apkFile));
        readfile($apkFile);
        exit;
    }
}

// Handle Logout
if ($page === 'logout') {
    Auth::logout();
    header('Location: login?msg=logout');
    exit;
}

// Handle Login Page
if ($page === 'login') {
    require_once $baseDir . '/views/login.php';
    exit;
}

// Handle Database Download / Backup Action (Khusus Admin yang sudah login)
if ($page === 'download_db' || (isset($_GET['action']) && $_GET['action'] === 'download_db')) {
    Auth::requireLogin();
    $dbFile = $baseDir . '/database/hafalan.sqlite';
    if (file_exists($dbFile)) {
        header('Content-Type: application/x-sqlite3');
        header('Content-Disposition: attachment; filename="hafalan_backup_' . date('Ymd_His') . '.sqlite"');
        header('Content-Length: ' . filesize($dbFile));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        readfile($dbFile);
        exit;
    }
    http_response_code(404);
    echo "Database file not found.";
    exit;
}

// Protected Pages Check (Halaman 'player' dan 'panduan' dapat diakses publik)
$publicPages = ['player', 'panduan'];
if (!in_array($page, $publicPages, true)) {
    Auth::requireLogin();
}

// Standalone Printable Layouts
if ($page === 'cetak_rapor') {
    require_once $baseDir . '/views/cetak_rapor.php';
    exit;
}

if ($page === 'cetak_laporan_pdf') {
    require_once $baseDir . '/views/cetak_laporan_pdf.php';
    exit;
}

// 404 Not Found Page
if ($page === '404') {
    http_response_code(404);
    $activeNav = '';
    $pageTitle = '404 - Halaman Tidak Ditemukan';
    require_once $baseDir . '/views/layout/header.php';
    echo '
    <div class="max-w-md mx-auto text-center py-16 bg-white rounded-3xl shadow-sm border border-slate-200 p-8 my-10">
        <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-3xl mx-auto mb-4">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h2 class="text-xl font-extrabold text-slate-800 mb-2">Halaman Tidak Ditemukan</h2>
        <p class="text-xs text-slate-500 mb-6">Alamat URL yang Anda tuju tidak valid atau format URL telah diperbarui ke tautan bersih tanpa ekstensi .php.</p>
        <a href="player" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-md transition-all">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Menu Utama
        </a>
    </div>';
    require_once $baseDir . '/views/layout/footer.php';
    exit;
}

// Determine Active Nav
$activeNav = match ($page) {
    'player' => 'player',
    'setoran' => 'setoran',
    'murojaah' => 'murojaah',
    'santri', 'santri_detail' => 'santri',
    'rekap', 'laporan_santri' => 'rekap',
    'pengaturan' => 'pengaturan',
    'panduan' => 'panduan',
    default => 'dashboard',
};

// Determine Page Title
$pageTitle = match ($page) {
    'player' => 'Aplikasi Menghafal Al-Qur\'an Berulang (Tikrar)',
    'setoran' => 'Input Setoran Hafalan',
    'murojaah' => 'Pusat Kendali Murojaah',
    'santri' => 'Data Santri',
    'santri_detail' => 'Kartu Kendali Tahfidz',
    'rekap' => 'Rekapitulasi & Rapor',
    'laporan_santri' => 'Laporan Lengkap Per Juz & Surah',
    'pengaturan' => 'Pengaturan Aplikasi',
    'panduan' => 'Buku Panduan Lengkap Penggunaan',
    default => 'Dashboard Admin Tahfidz',
};

// Render Header
require_once $baseDir . '/views/layout/header.php';

// Render Page Body
$viewFile = match ($page) {
    'player' => $baseDir . '/views/frontend_player.php',
    'setoran' => $baseDir . '/views/setoran_form.php',
    'murojaah' => $baseDir . '/views/murojaah_center.php',
    'santri' => $baseDir . '/views/santri_list.php',
    'santri_detail' => $baseDir . '/views/santri_detail.php',
    'rekap' => $baseDir . '/views/rekap.php',
    'laporan_santri' => $baseDir . '/views/laporan_santri_detail.php',
    'pengaturan' => $baseDir . '/views/pengaturan.php',
    'panduan' => $baseDir . '/views/panduan.php',
    default => $baseDir . '/views/dashboard.php',
};

if (file_exists($viewFile)) {
    require_once $viewFile;
} else {
    echo '<div class="p-8 text-center text-rose-600 font-bold">File tampilan tidak ditemukan!</div>';
}

// Render Footer
require_once $baseDir . '/views/layout/footer.php';
