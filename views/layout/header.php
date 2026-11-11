<?php
declare(strict_types=1);

/**
 * Header Template SIM Tahfidz Quran
 */
$pageTitle = $pageTitle ?? 'SIM Tahfidz Al-Qur\'an';
$activeNav = $activeNav ?? 'dashboard';

$namaAplikasi = 'SIM Tahfidz Qur\'an';
$namaPesantren = 'Pondok Pesantren & Rumah Tahfidz';

if (class_exists('App\Database')) {
    try {
        $db = App\Database::getConnection();
        $stmtApp = $db->query("SELECT nilai FROM pengaturan WHERE kunci = 'nama_aplikasi'");
        $valApp = $stmtApp->fetchColumn();
        if ($valApp) $namaAplikasi = $valApp;

        $stmtPes = $db->query("SELECT nilai FROM pengaturan WHERE kunci = 'nama_pesantren'");
        $valPes = $stmtPes->fetchColumn();
        if ($valPes) $namaPesantren = $valPes;
    } catch (\Throwable $e) {}
}

if (!isset($baseUrl)) {
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    $baseUrl = ($scriptDir !== '') ? ($scriptDir . '/') : '/';
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <base href="<?= htmlspecialchars($baseUrl) ?>">
    <script>
        window.BASE_URL = "<?= htmlspecialchars($baseUrl) ?>";
    </script>
    <meta name="theme-color" content="#059669">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= htmlspecialchars($namaAplikasi) ?></title>
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/png" href="icons/icon-192.png">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
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
                    },
                    fontFamily: {
                        arabic: ['Amiri', 'Traditional Arabic', 'Scheherazade New', 'serif']
                    }
                }
            }
        }
    </script>
    
    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts Amiri for Arabic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        .font-quran {
            font-family: 'Amiri', serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        /* Hide scrollbar for tabs */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
        }
    </style>
</head>
<body class="h-full flex flex-col bg-slate-50 text-slate-800 antialiased pb-20 md:pb-0">

    <!-- Top Navbar -->
    <header class="bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 text-white shadow-md sticky top-0 z-40 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand & Logo -->
                <div class="flex items-center space-x-3">
                    <a href="player" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center text-amber-300 text-xl shadow-inner group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-book-quran"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold tracking-tight text-white flex items-center gap-2">
                                <?= htmlspecialchars($namaAplikasi) ?>
                                <span class="bg-emerald-500/40 text-emerald-100 text-[10px] px-2 py-0.5 rounded-full font-semibold border border-emerald-400/30">Auto</span>
                            </h1>
                            <p class="text-xs text-emerald-200/80 truncate max-w-[200px] sm:max-w-xs">
                                <?= htmlspecialchars($namaPesantren) ?>
                            </p>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    <a href="player" class="px-3.5 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2 <?= $activeNav === 'player' ? 'bg-amber-400 text-amber-950 shadow-md scale-105' : 'bg-white/10 text-amber-300 hover:bg-white/20' ?>">
                        <i class="fa-solid fa-headphones-simple"></i>
                        <span>Menghafal (Player)</span>
                    </a>

                    <?php if (class_exists('App\Auth') && App\Auth::check()): ?>
                        <a href="dashboard" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 <?= $activeNav === 'dashboard' ? 'bg-white/20 text-white shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' ?>">
                            <i class="fa-solid fa-chart-pie"></i>
                            <span>Admin</span>
                        </a>
                        <a href="setoran" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 <?= $activeNav === 'setoran' ? 'bg-white/20 text-white shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' ?>">
                            <i class="fa-solid fa-circle-plus"></i>
                            <span>Setoran</span>
                        </a>
                        <a href="murojaah" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 <?= $activeNav === 'murojaah' ? 'bg-white/20 text-white shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' ?>">
                            <i class="fa-solid fa-arrows-rotate"></i>
                            <span>Murojaah</span>
                        </a>
                        <a href="santri" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 <?= $activeNav === 'santri' ? 'bg-white/20 text-white shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' ?>">
                            <i class="fa-solid fa-users"></i>
                            <span>Santri</span>
                        </a>
                        <a href="rekap" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 <?= $activeNav === 'rekap' ? 'bg-white/20 text-white shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' ?>">
                            <i class="fa-solid fa-clipboard-list"></i>
                            <span>Rekap</span>
                        </a>
                        <a href="laporan-santri" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 <?= $activeNav === 'laporan_santri' ? 'bg-white/20 text-white shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' ?>" title="Laporan Lengkap Per Siswa">
                            <i class="fa-solid fa-file-invoice"></i>
                            <span>Laporan Siswa</span>
                        </a>
                        <a href="pengaturan" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 <?= $activeNav === 'pengaturan' ? 'bg-white/20 text-white shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' ?>" title="Pengaturan">
                            <i class="fa-solid fa-sliders"></i>
                        </a>

                        <a href="panduan" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-1.5 <?= $activeNav === 'panduan' ? 'bg-white/20 text-white shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' ?>" title="Buku Panduan Lengkap">
                            <i class="fa-solid fa-book-open"></i>
                            <span>Panduan</span>
                        </a>

                        <a href="landing" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-1.5 text-emerald-100 hover:bg-white/10 hover:text-white" title="Landing Page & Download APK">
                            <i class="fa-solid fa-earth-asia"></i>
                            <span>Landing Page</span>
                        </a>

                        <!-- Tombol Icon Info ChatTutor -->
                        <button type="button" onclick="openModalInfoChatTutor()" class="px-3 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-1.5 bg-amber-400/25 text-amber-300 hover:bg-amber-400/35 border border-amber-300/40 shadow-xs" title="Tentang ChatTutor, Doa Hafidz, & Lisensi Gratis">
                            <i class="fa-solid fa-circle-info text-amber-300 text-base"></i>
                            <span>Info</span>
                        </button>

                        <!-- User Profile Badge & Logout -->
                        <div class="flex items-center gap-2 pl-2 border-l border-white/20 ml-2">
                            <span class="text-xs font-bold text-emerald-100 flex items-center gap-1.5 bg-emerald-900/60 px-2.5 py-1.5 rounded-lg border border-emerald-500/30">
                                <i class="fa-solid fa-user-shield text-amber-300"></i>
                                <span class="max-w-[120px] truncate"><?= htmlspecialchars(App\Auth::name()) ?></span>
                            </span>
                            <a href="logout" class="bg-rose-500/20 hover:bg-rose-600 hover:text-white text-rose-200 px-2.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1 border border-rose-400/30" title="Keluar dari Akun">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <span>Keluar</span>
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Navigasi Publik untuk Santri / Tamu -->
                        <a href="panduan" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-1.5 <?= $activeNav === 'panduan' ? 'bg-white/20 text-white shadow-sm' : 'text-emerald-100 hover:bg-white/10 hover:text-white' ?>" title="Buku Panduan Lengkap">
                            <i class="fa-solid fa-book-open"></i>
                            <span>Panduan</span>
                        </a>

                        <a href="landing" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-1.5 text-emerald-100 hover:bg-white/10 hover:text-white" title="Landing Page & Download APK">
                            <i class="fa-solid fa-earth-asia"></i>
                            <span>Landing Page</span>
                        </a>

                        <!-- Tombol Icon Info ChatTutor -->
                        <button type="button" onclick="openModalInfoChatTutor()" class="px-3 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-1.5 bg-amber-400/25 text-amber-300 hover:bg-amber-400/35 border border-amber-300/40 shadow-xs" title="Tentang ChatTutor, Doa Hafidz, & Lisensi Gratis">
                            <i class="fa-solid fa-circle-info text-amber-300 text-base"></i>
                            <span>Info</span>
                        </button>

                        <!-- Tombol Login Ustadz jika belum login -->
                        <a href="login" class="bg-amber-400 hover:bg-amber-300 text-amber-950 px-4 py-2 rounded-xl text-xs font-extrabold shadow-md transition-all flex items-center gap-1.5 active:scale-95">
                            <i class="fa-solid fa-lock"></i>
                            <span>Login Ustadz</span>
                        </a>
                    <?php endif; ?>
                </nav>

                <!-- Mobile Quick Action Button -->
                <div class="flex items-center space-x-2 md:hidden">
                    <button type="button" onclick="openModalInfoChatTutor()" class="bg-amber-400/25 border border-amber-300/40 text-amber-300 font-bold p-2 rounded-lg text-xs flex items-center justify-center" title="Informasi ChatTutor">
                        <i class="fa-solid fa-circle-info text-sm"></i>
                    </button>
                    <a href="panduan" class="bg-white/15 hover:bg-white/25 text-white font-bold p-2 rounded-lg text-xs flex items-center justify-center" title="Buku Panduan">
                        <i class="fa-solid fa-book-open"></i>
                    </a>
                    <?php if (class_exists('App\Auth') && App\Auth::check()): ?>
                        <a href="setoran" class="bg-amber-400 text-amber-950 font-bold px-3 py-1.5 rounded-lg text-xs flex items-center gap-1.5 shadow active:scale-95">
                            <i class="fa-solid fa-plus"></i> Setor
                        </a>
                        <a href="logout" class="bg-rose-500/30 text-rose-100 px-2.5 py-1.5 rounded-lg text-xs font-bold" title="Keluar">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </a>
                    <?php else: ?>
                        <a href="login" class="bg-amber-400 text-amber-950 font-bold px-3 py-1.5 rounded-lg text-xs flex items-center gap-1.5 shadow active:scale-95">
                            <i class="fa-solid fa-lock"></i> Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

