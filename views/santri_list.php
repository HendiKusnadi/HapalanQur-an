<?php
declare(strict_types=1);

use App\Database;
use App\Models\Santri;
use App\QuranData;

$db = Database::getConnection();
$santriModel = new Santri($db);

$search = $_GET['search'] ?? '';
$halaqahId = isset($_GET['halaqah_id']) && $_GET['halaqah_id'] !== '' ? (int)$_GET['halaqah_id'] : null;

$santriList = $santriModel->getAll($search, $halaqahId);
$allSurah = QuranData::getAllSurah();

// Ambil data halaqah untuk filter & form
$stmtH = $db->query("SELECT * FROM halaqah ORDER BY nama ASC");
$halaqahList = $stmtH->fetchAll();
?>

<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-users text-emerald-600"></i> Data Santri Tahfidz
            </h2>
            <p class="text-sm text-slate-500">Kelola profil santri, halaqah, arah hafalan, dan pantau progres hafalan.</p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="api/export_santri.php" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 px-3.5 py-2.5 rounded-xl border border-slate-300 shadow-xs transition-all">
                <i class="fa-solid fa-file-excel text-emerald-600"></i> Export Santri
            </a>
            <button onclick="openModalTambahSantri()" class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 rounded-xl shadow-md transition-all active:scale-95">
                <i class="fa-solid fa-user-plus"></i> Tambah Santri Baru
            </button>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3">
        <form method="GET" action="santri" class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto flex-1">
            <div class="relative w-full sm:w-72">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari Nama atau NIS..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <select name="halaqah_id" onchange="this.form.submit()" class="w-full sm:w-56 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="">Semua Halaqah</option>
                <?php foreach ($halaqahList as $hl): ?>
                    <option value="<?= $hl['id'] ?>" <?= $halaqahId === (int)$hl['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($hl['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="w-full sm:w-auto bg-slate-800 text-white font-bold text-xs px-4 py-2 rounded-xl">
                Filter
            </button>
        </form>

        <span class="text-xs font-semibold text-slate-500 whitespace-nowrap">
            Total: <b><?= count($santriList) ?></b> Santri
        </span>
    </div>

    <!-- Grid Kartu Santri -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach ($santriList as $s): 
            $persen = min(100, round(((float)$s['total_juz_hafal'] / max(1, (int)$s['target_juz'])) * 100));
        ?>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 hover:shadow-md transition-shadow flex flex-col justify-between">
                <div>
                    <!-- Header Kartu -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-800 font-extrabold flex items-center justify-center text-lg flex-shrink-0">
                                <?= mb_substr($s['nama'], 0, 1) ?>
                            </div>
                            <div>
                                <a href="santri-detail?id=<?= $s['id'] ?>" class="font-extrabold text-slate-800 hover:text-emerald-700 text-sm block">
                                    <?= htmlspecialchars($s['nama']) ?>
                                </a>
                                <span class="text-[11px] text-slate-400">NIS: <?= htmlspecialchars($s['nis']) ?></span>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $s['jenis_kelamin'] === 'L' ? 'bg-sky-100 text-sky-800' : 'bg-pink-100 text-pink-800' ?>">
                            <?= $s['jenis_kelamin'] === 'L' ? 'Ikhwan' : 'Akhwat' ?>
                        </span>
                    </div>

                    <!-- Info Halaqah -->
                    <div class="text-xs text-slate-600 mb-3 flex items-center gap-1.5">
                        <i class="fa-solid fa-users text-slate-400"></i>
                        <span class="truncate"><?= htmlspecialchars($s['halaqah_nama'] ?? 'Halaqah Umum') ?></span>
                    </div>

                    <!-- Posisi Terakhir -->
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs mb-3">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block mb-0.5">Posisi Terakhir:</span>
                        <div class="font-extrabold text-slate-800">
                            QS. <?= $s['surah_nama'] ?> : <?= $s['terakhir_ayat'] ?> (Hal. <?= $s['terakhir_halaman'] ?>)
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="space-y-1 mb-4">
                        <div class="flex items-center justify-between text-[11px] font-bold text-slate-700">
                            <span>Capaian: <?= (float)$s['total_juz_hafal'] ?> / <?= $s['target_juz'] ?> Juz</span>
                            <span class="text-emerald-700 font-extrabold"><?= $persen ?>%</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-emerald-600 h-full rounded-full" style="width: <?= $persen ?>%"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 block"><?= round((float)$s['total_halaman_hafal']) ?> Halaman dihafal</span>
                    </div>
                </div>

                <!-- Footer Aksi -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2 text-xs">
                    <a href="santri-detail?id=<?= $s['id'] ?>" class="text-slate-600 hover:text-emerald-700 font-bold px-2 py-1 rounded">
                        Rapor & Detail
                    </a>
                    <a href="setoran?santri_id=<?= $s['id'] ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-1.5 rounded-lg shadow-xs">
                        + Setor
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<!-- Modal Tambah Santri Baru -->
<div id="modal-tambah-santri" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-base text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-emerald-600"></i> Tambah Data Santri Baru
            </h3>
            <button onclick="closeModalTambahSantri()" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="form-tambah-santri" onsubmit="submitSantriBaru(event)" class="space-y-4 text-xs">
            <div>
                <label class="block font-bold text-slate-600 mb-1">NIS (Nomor Induk Santri)</label>
                <input type="text" name="nis" required placeholder="Contoh: 2026006" class="w-full bg-slate-50 border border-slate-300 rounded-lg p-2.5 text-sm font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Nama Lengkap Santri</label>
                <input type="text" name="nama" required placeholder="Nama lengkap santri" class="w-full bg-slate-50 border border-slate-300 rounded-lg p-2.5 text-sm font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="w-full bg-slate-50 border border-slate-300 rounded-lg p-2.5 text-sm font-semibold">
                        <option value="L">Laki-laki (Ikhwan)</option>
                        <option value="P">Perempuan (Akhwat)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Target Hafalan</label>
                    <select name="target_juz" class="w-full bg-slate-50 border border-slate-300 rounded-lg p-2.5 text-sm font-semibold">
                        <option value="30">30 Juz (Khatam)</option>
                        <option value="20">20 Juz</option>
                        <option value="15">15 Juz</option>
                        <option value="10">10 Juz</option>
                        <option value="5">5 Juz</option>
                        <option value="3">3 Juz</option>
                        <option value="1">1 Juz (Juz 30)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Halaqah</label>
                <select name="halaqah_id" class="w-full bg-slate-50 border border-slate-300 rounded-lg p-2.5 text-sm font-semibold">
                    <option value="">Pilih Halaqah...</option>
                    <?php foreach ($halaqahList as $hl): ?>
                        <option value="<?= $hl['id'] ?>"><?= htmlspecialchars($hl['nama']) ?> (<?= htmlspecialchars($hl['pembimbing']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Arah Hafalan</label>
                    <select name="arah_hafalan" class="w-full bg-slate-50 border border-slate-300 rounded-lg p-2.5 text-sm font-semibold">
                        <option value="juz30_mundur">Juz 30 (Mulai An-Naba)</option>
                        <option value="juz1_maju">Juz 1 (Mulai Al-Baqarah)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Surah Awal</label>
                    <select name="terakhir_surah_id" class="w-full bg-slate-50 border border-slate-300 rounded-lg p-2.5 text-sm font-semibold">
                        <?php foreach ($allSurah as $sId => $sData): ?>
                            <option value="<?= $sId ?>" <?= $sId == 78 ? 'selected' : '' ?>>
                                <?= $sId ?>. <?= htmlspecialchars($sData['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModalTambahSantri()" class="px-4 py-2 rounded-xl text-slate-600 font-bold hover:bg-slate-100">Batal</button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-2 rounded-xl shadow-md">Simpan Santri</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModalTambahSantri() {
    document.getElementById('modal-tambah-santri').classList.remove('hidden');
}
function closeModalTambahSantri() {
    document.getElementById('modal-tambah-santri').classList.add('hidden');
}

async function submitSantriBaru(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('form-tambah-santri'));
    const base = window.BASE_URL || '/';

    try {
        let response = null;
        try {
            response = await fetch(base + 'api/save_santri.php', { method: 'POST', body: formData });
            if (!response.ok && response.status !== 401 && response.status !== 400) throw new Error();
        } catch (_) {
            response = await fetch(base + 'api/save-santri', { method: 'POST', body: formData });
        }

        if (response.status === 401) {
            showToast('Sesi login telah berakhir. Silakan login kembali di tab baru.', 'danger');
            if (confirm('Sesi login Anda telah berakhir. Buka halaman login di tab baru? (Data santri tetap aman)')) {
                window.open('login', '_blank');
            }
            return;
        }

        const res = await response.json();
        if (res.success) {
            showToast(res.message || 'Santri berhasil ditambahkan!', 'success');
            setTimeout(() => window.location.reload(), 700);
        } else {
            showToast(res.message || 'Gagal menyimpan santri', 'danger');
        }
    } catch (err) {
        showToast(err.message || 'Terjadi kesalahan jaringan', 'danger');
    }
}
</script>

