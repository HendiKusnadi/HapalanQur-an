<?php
declare(strict_types=1);

use App\Auth;
use App\Database;
use App\Security;

Auth::startSession();

// Jika sudah login, redirect langsung ke dashboard
if (Auth::check()) {
    header('Location: dashboard');
    exit;
}

$db = Database::getConnection();
$namaPesantren = $db->query("SELECT nilai FROM pengaturan WHERE kunci = 'nama_pesantren'")->fetchColumn() ?: 'Pondok Pesantren Tahfidz Al-Qur\'an';
$namaAplikasi = $db->query("SELECT nilai FROM pengaturan WHERE kunci = 'nama_aplikasi'")->fetchColumn() ?: 'SIM Tahfidz Quran';

$errorMessage = null;
$infoMessage = null;

if (isset($_GET['msg']) && $_GET['msg'] === 'logout') {
    $infoMessage = 'Anda telah berhasil keluar dari akun ustadz/admin.';
}

$ip = Security::getClientIp();

// Proses submit login
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Cek Rate Limiting (Mencegah Brute Force)
    $rateCheck = Security::checkRateLimit($ip, $username);

    if (!$rateCheck['allowed']) {
        $errorMessage = $rateCheck['message'];
    } elseif ($username === '' || $password === '') {
        $errorMessage = 'Silakan isi username dan kata sandi!';
    } elseif (!Security::verifyCsrf()) {
        $errorMessage = 'Sesi keamanan kedaluwarsa. Silakan muat ulang halaman.';
    } else {
        $loginResult = Auth::login($username, $password);
        if ($loginResult['success']) {
            Security::clearLoginAttempts($ip, $username);
            $redirect = $_SESSION['redirect_after_login'] ?? 'dashboard';
            unset($_SESSION['redirect_after_login']);
            
            // Konversi redirect lama jika masih berupa index.php?page=...
            $redirect = preg_replace('/^index\.php\?page=/', '', $redirect);
            header('Location: ' . $redirect);
            exit;
        } else {
            Security::recordFailedLogin($ip, $username);
            $newRate = Security::checkRateLimit($ip, $username);
            $sisa = $newRate['remaining_attempts'] ?? 0;
            $warning = ($sisa > 0 && $sisa <= 3) ? " (Sisa kesempatan: {$sisa}x)" : '';
            $errorMessage = $loginResult['message'] . $warning;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login Ustadz & Pengelola - <?= htmlspecialchars($namaAplikasi) ?></title>
    
    <!-- PWA Manifest & Icons -->
    <link rel="manifest" href="./manifest.json">
    <link rel="icon" type="image/png" href="./icons/icon-192.png">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>
<body class="min-h-full flex items-center justify-center p-4 bg-gradient-to-br from-slate-900 via-emerald-950 to-teal-900">

    <div class="w-full max-w-md">
        
        <!-- Logo & Header Branding -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-slate-950 text-3xl shadow-2xl shadow-emerald-500/40 mb-4 border-2 border-white/20">
                <i class="fa-solid fa-book-quran"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                <?= htmlspecialchars($namaAplikasi) ?>
            </h1>
            <p class="text-xs sm:text-sm text-emerald-200/80 mt-1">
                <?= htmlspecialchars($namaPesantren) ?>
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl p-6 sm:p-8 shadow-2xl border border-white/30">
            <div class="mb-6">
                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-lock text-emerald-600"></i>
                    <span>Masuk Panel Ustadz / Admin</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Silakan masukkan akun ustadz/musyrif untuk mengelola setoran santri.
                </p>
            </div>

            <!-- Pesan Notifikasi / Error -->
            <?php if ($errorMessage): ?>
                <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-base flex-shrink-0 text-rose-600"></i>
                    <span><?= htmlspecialchars($errorMessage) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($infoMessage): ?>
                <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-base flex-shrink-0 text-emerald-600"></i>
                    <span><?= htmlspecialchars($infoMessage) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="login" class="space-y-4">
                <?= Security::csrfField() ?>
                
                <!-- Username -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Username
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" name="username" id="input-username" required autofocus value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" placeholder="Masukkan username admin" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-2xl text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                        <span>Kata Sandi</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <input type="password" name="password" id="input-password" required placeholder="Masukkan kata sandi" class="w-full pl-10 pr-11 py-3 bg-slate-50 border border-slate-300 rounded-2xl text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition-all">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors" title="Lihat / Sembunyikan Kata Sandi">
                            <i class="fa-solid fa-eye" id="icon-toggle-password"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full mt-2 py-3.5 px-4 bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-extrabold text-sm rounded-2xl shadow-lg shadow-emerald-700/30 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Masuk ke Panel Ustadz</span>
                </button>

            </form>

            <!-- Box Demo Akun Default -->
            <div class="mt-6 p-3.5 rounded-2xl bg-amber-50/80 border border-amber-200/90 text-xs text-amber-900">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 font-bold">
                        <i class="fa-solid fa-key text-amber-600"></i>
                        <span>Akun Default:</span>
                    </div>
                    <button type="button" onclick="fillDemoCredentials()" class="text-[11px] font-extrabold bg-amber-400 hover:bg-amber-500 text-amber-950 px-2.5 py-1 rounded-lg transition-all active:scale-95 shadow-xs">
                        Isi Otomatis
                    </button>
                </div>
                <div class="mt-1 font-mono text-[11px] text-amber-800">
                    Username: <span class="font-bold">admin</span> | Sandi: <span class="font-bold">admin123</span>
                </div>
            </div>

            <!-- Tombol Kembali ke Player Santri -->
            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <a href="player" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-emerald-700 transition-colors">
                    <i class="fa-solid fa-headphones-simple"></i>
                    <span>Kembali ke Aplikasi Menghafal Santri (Tanpa Login)</span>
                </a>
            </div>

        </div>

        <!-- Footer Copyright -->
        <p class="text-center text-xs text-emerald-300/60 mt-6">
            &copy; <?= date('Y') ?> <?= htmlspecialchars($namaPesantren) ?> • SIM Tahfidz Al-Qur'an
        </p>

    </div>

    <script>
        function togglePasswordVisibility() {
            const input = document.getElementById('input-password');
            const icon = document.getElementById('icon-toggle-password');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa-solid fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fa-solid fa-eye';
            }
        }

        function fillDemoCredentials() {
            document.getElementById('input-username').value = 'admin';
            document.getElementById('input-password').value = 'admin123';
        }
    </script>
</body>
</html>
