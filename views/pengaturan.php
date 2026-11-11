<?php
declare(strict_types=1);

use App\Database;
use App\Auth;
use App\Security;

$db = Database::getConnection();

// Proses simpan pengaturan jika form disubmit
$message = null;
$errorMsg = null;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['action'])) {
    if (!Security::verifyCsrf()) {
        $errorMsg = 'Sesi keamanan kedaluwarsa. Silakan muat ulang halaman.';
    } elseif ($_POST['action'] === 'simpan_pengaturan') {
        $namaApp = trim($_POST['nama_aplikasi'] ?? 'SIM Tahfidz Quran');
        $namaPes = trim($_POST['nama_pesantren'] ?? 'Pondok Pesantren Tahfidz');
        $defaultM = trim($_POST['default_musyrif'] ?? 'Musyrif');

        $stmt = $db->prepare("REPLACE INTO pengaturan (kunci, nilai) VALUES (?, ?)");
        $stmt->execute(['nama_aplikasi', $namaApp]);
        $stmt->execute(['nama_pesantren', $namaPes]);
        $stmt->execute(['default_musyrif', $defaultM]);

        $message = 'Pengaturan berhasil diperbarui!';
    } elseif ($_POST['action'] === 'tambah_halaqah') {
        $namaHalaqah = trim($_POST['nama_halaqah'] ?? '');
        $pembimbing = trim($_POST['pembimbing'] ?? '');
        $ket = trim($_POST['keterangan'] ?? '');

        if (!empty($namaHalaqah) && !empty($pembimbing)) {
            $stmt = $db->prepare("INSERT INTO halaqah (nama, pembimbing, keterangan) VALUES (?, ?, ?)");
            $stmt->execute([$namaHalaqah, $pembimbing, $ket]);
            $message = 'Halaqah berhasil ditambahkan!';
        }
    } elseif ($_POST['action'] === 'update_profil_admin') {
        $userId = Auth::id() ?? 1;
        $username = trim($_POST['username'] ?? '');
        $namaLengkap = trim($_POST['nama_lengkap'] ?? '');

        if ($username !== '' && $namaLengkap !== '') {
            $res = Auth::updateProfile($userId, $username, $namaLengkap);
            if ($res['success']) {
                $message = $res['message'];
            } else {
                $errorMsg = $res['message'];
            }
        }
    } elseif ($_POST['action'] === 'ganti_password_admin') {
        $userId = Auth::id() ?? 1;
        $oldPass = $_POST['password_lama'] ?? '';
        $newPass = $_POST['password_baru'] ?? '';
        $confirmPass = $_POST['konfirmasi_password'] ?? '';

        if ($newPass !== $confirmPass) {
            $errorMsg = 'Konfirmasi kata sandi baru tidak cocok!';
        } else {
            $res = Auth::changePassword($userId, $oldPass, $newPass);
            if ($res['success']) {
                $message = $res['message'];
            } else {
                $errorMsg = $res['message'];
            }
        }
    }
}

// Ambil data pengaturan
$settings = [];
$rows = $db->query("SELECT kunci, nilai FROM pengaturan")->fetchAll();
foreach ($rows as $r) {
    $settings[$r['kunci']] = $r['nilai'];
}

$halaqahList = $db->query("SELECT h.*, COUNT(s.id) as total_santri FROM halaqah h LEFT JOIN santri s ON h.id = s.halaqah_id GROUP BY h.id ORDER BY h.id ASC")->fetchAll();
?>

<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-2">
            <i class="fa-solid fa-sliders text-emerald-600"></i> Pengaturan & Konfigurasi
        </h2>
        <p class="text-sm text-slate-500">Kelola identitas lembaga, halaqah santri, dan manajemen database SQLite.</p>
    </div>

    <?php if ($message): ?>
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($errorMsg): ?>
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
            <?= htmlspecialchars($errorMsg) ?>
        </div>
    <?php endif; ?>

    <!-- Form Akun & Keamanan Admin -->
    <?php
    $currentUser = Auth::user();
    ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 space-y-6">
        <div class="border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-800 text-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-user-shield text-emerald-600"></i> Akun & Keamanan Ustadz / Admin
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Kelola nama profil ustadz dan perbarui kata sandi masuk.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Update Profil -->
            <form method="POST" action="pengaturan" class="space-y-3 text-xs bg-slate-50 p-4 rounded-xl border border-slate-200">
                <?= Security::csrfField() ?>
                <input type="hidden" name="action" value="update_profil_admin">
                <span class="font-bold text-slate-800 block text-xs border-b border-slate-200 pb-2">Profil Pengguna:</span>
                
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Nama Lengkap Ustadz</label>
                    <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($currentUser['nama_lengkap'] ?? '') ?>" required class="w-full bg-white border border-slate-300 rounded-lg p-2 text-xs font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-600 mb-1">Username Login</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($currentUser['username'] ?? '') ?>" required class="w-full bg-white border border-slate-300 rounded-lg p-2 text-xs font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div class="pt-1">
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs px-4 py-2 rounded-xl transition-all">
                        Simpan Profil
                    </button>
                </div>
            </form>

            <!-- Ganti Password -->
            <form method="POST" action="pengaturan" class="space-y-3 text-xs bg-slate-50 p-4 rounded-xl border border-slate-200">
                <?= Security::csrfField() ?>
                <input type="hidden" name="action" value="ganti_password_admin">
                <span class="font-bold text-slate-800 block text-xs border-b border-slate-200 pb-2">Ganti Kata Sandi:</span>
                
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Kata Sandi Lama</label>
                    <input type="password" name="password_lama" required placeholder="Masukkan kata sandi lama" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-xs font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-600 mb-1">Kata Sandi Baru</label>
                    <input type="password" name="password_baru" required placeholder="Minimal 5 karakter" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-xs font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-600 mb-1">Ulangi Kata Sandi Baru</label>
                    <input type="password" name="konfirmasi_password" required placeholder="Konfirmasi kata sandi baru" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-xs font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div class="pt-1">
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs px-4 py-2 rounded-xl transition-all">
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Form Identitas Lembaga -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 space-y-4">
        <h3 class="font-extrabold text-slate-800 text-sm uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-mosque text-emerald-600"></i> Identitas Pesantren & Lembaga
        </h3>

        <form method="POST" action="pengaturan" class="space-y-4 text-xs">
            <?= Security::csrfField() ?>
            <input type="hidden" name="action" value="simpan_pengaturan">

            <div>
                <label class="block font-bold text-slate-600 mb-1">Nama Aplikasi</label>
                <input type="text" name="nama_aplikasi" value="<?= htmlspecialchars($settings['nama_aplikasi'] ?? 'SIM Tahfidz Qur\'an') ?>" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Nama Pondok Pesantren / Rumah Tahfidz</label>
                <input type="text" name="nama_pesantren" value="<?= htmlspecialchars($settings['nama_pesantren'] ?? 'Pondok Pesantren Al-Qur\'an') ?>" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Nama Default Musyrif / Ustadz Pembimbing</label>
                <input type="text" name="default_musyrif" value="<?= htmlspecialchars($settings['default_musyrif'] ?? 'Ust. Pembimbing, Al-Hafizh') ?>" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div class="pt-2">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-md transition-all active:scale-95">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    <!-- Kelola Halaqah -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-slate-800 text-sm uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-users-rectangle text-emerald-600"></i> Daftar Halaqah / Kelas Tahfidz
            </h3>
        </div>

        <div class="space-y-3">
            <?php foreach ($halaqahList as $hl): ?>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                    <div>
                        <div class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($hl['nama']) ?></div>
                        <div class="text-slate-500 mt-0.5">
                            Pembimbing: <b class="text-slate-700"><?= htmlspecialchars($hl['pembimbing']) ?></b> • <?= htmlspecialchars($hl['keterangan'] ?? '') ?>
                        </div>
                    </div>
                    <span class="bg-emerald-100 text-emerald-800 font-bold px-2.5 py-1 rounded-full text-xs">
                        <?= $hl['total_santri'] ?> Santri
                    </span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Form Tambah Halaqah -->
        <form method="POST" action="pengaturan" class="pt-3 border-t border-slate-100 space-y-3 text-xs">
            <?= Security::csrfField() ?>
            <input type="hidden" name="action" value="tambah_halaqah">
            <span class="font-bold text-slate-700 block">+ Tambah Halaqah Baru:</span>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <input type="text" name="nama_halaqah" required placeholder="Nama Halaqah (mis: Halaqah Abu Bakar)" class="bg-slate-50 border border-slate-300 rounded-xl p-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <input type="text" name="pembimbing" required placeholder="Nama Musyrif / Pembimbing" class="bg-slate-50 border border-slate-300 rounded-xl p-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <input type="text" name="keterangan" placeholder="Keterangan (Tingkat Juz)" class="bg-slate-50 border border-slate-300 rounded-xl p-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs px-4 py-2 rounded-xl">
                Tambah Halaqah
            </button>
        </form>
    </div>

    <!-- Informasi Database SQLite & Backup -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 space-y-3">
        <h3 class="font-extrabold text-slate-800 text-sm uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-database text-emerald-600"></i> Database SQLite & Backup
        </h3>
        <p class="text-xs text-slate-600 leading-relaxed">
            Aplikasi menggunakan SQLite 3 dengan mode <b>WAL (Write-Ahead Logging)</b> untuk kecepatan dan keandalan tinggi. File tersimpan aman di <code>database/hafalan.sqlite</code>.
        </p>

        <div class="pt-2 flex items-center gap-3">
            <a href="backup-db" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs transition-all">
                <i class="fa-solid fa-download"></i> Unduh / Backup File Database (.sqlite)
            </a>
        </div>
    </div>

</div>

