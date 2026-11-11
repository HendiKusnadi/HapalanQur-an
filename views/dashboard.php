<?php
declare(strict_types=1);

use App\Database;
use App\Models\Santri;
use App\Models\Setoran;
use App\ProgressEngine;
use App\QuranData;

$db = Database::getConnection();
$santriModel = new Santri($db);
$setoranModel = new Setoran($db);
$engine = new ProgressEngine($db);

$summary = $setoranModel->getTodaySummary();
$recentSetoran = $setoranModel->getRecent(10);
$allSantri = $santriModel->getAll();
$totalSantriCount = count($allSantri);

// Hitung total akumulasi hafalan seluruh santri
$totalHalamanSeluruh = 0.0;
$totalJuzSeluruh = 0.0;
foreach ($allSantri as $s) {
    $totalHalamanSeluruh += (float)$s['total_halaman_hafal'];
    $totalJuzSeluruh += (float)$s['total_juz_hafal'];
}

// Santri dengan Murojaah Tertunda (> 7 hari tidak murojaah manzil)
$santriPerluMurojaah = [];
foreach ($allSantri as $s) {
    $recs = $engine->getMurojaahSebelumnyaRecommendations((int)$s['id']);
    if (!empty($recs) && $recs[0]['hari_lalu'] >= 7) {
        $santriPerluMurojaah[] = [
            'santri' => $s,
            'top_rec' => $recs[0]
        ];
    }
}
?>

<!-- Welcome Banner / Quick Action -->
<div class="relative overflow-hidden bg-gradient-to-br from-emerald-800 via-emerald-700 to-teal-800 rounded-2xl shadow-xl text-white p-6 sm:p-8 mb-8">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 bg-emerald-600/40 border border-emerald-400/30 px-3 py-1 rounded-full text-xs font-semibold text-emerald-100 mb-3">
                <i class="fa-solid fa-sparkles text-amber-300"></i> Sistem Otomatis Tambah & Kurang Aktif
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Setoran & Murojaah Al-Qur'an</h2>
            <p class="text-emerald-100/90 text-sm sm:text-base mt-1 max-w-xl">
                Catat hafalan baru, murojaah sabaq (baru), dan murojaah manzil (lama). Sistem otomatis menghitung kemajuan, memajukan, atau memundurkan capaian santri.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="setoran" class="inline-flex items-center justify-center gap-2 bg-amber-400 text-amber-950 font-bold px-5 py-3 rounded-xl shadow-lg hover:bg-amber-300 active:scale-95 transition-all text-sm">
                <i class="fa-solid fa-plus-circle text-lg"></i>
                <span>Input Setoran Cepat</span>
            </a>
            <a href="murojaah" class="inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold px-4 py-3 rounded-xl text-sm transition-all">
                <i class="fa-solid fa-arrows-rotate"></i>
                <span>Antrean Murojaah</span>
            </a>
            <a href="panduan?tab=admin" class="inline-flex items-center justify-center gap-2 bg-emerald-950/60 hover:bg-emerald-950/90 border border-emerald-400/30 text-emerald-200 font-semibold px-4 py-3 rounded-xl text-sm transition-all" title="Buka Panduan Ustadz / Admin">
                <i class="fa-solid fa-book-open text-amber-300"></i>
                <span>Panduan</span>
            </a>
        </div>
    </div>
    <!-- Decorative Quran Background Watermark -->
    <div class="absolute -right-8 -bottom-10 text-white/5 text-[180px] pointer-events-none font-quran select-none">
        ﷽
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
    <!-- Card 1: Setoran Hari Ini -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/70 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Setoran Hari Ini</span>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-book-open-reader"></i>
            </div>
        </div>
        <div class="mt-3">
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-800"><?= $summary['total_setoran'] ?></div>
            <div class="flex items-center gap-2 mt-1 text-xs text-slate-500">
                <span class="text-emerald-600 font-semibold"><?= $summary['total_santri_setor'] ?> Santri</span> telah setor
            </div>
        </div>
    </div>

    <!-- Card 2: Hafalan Baru (Ziyadah) -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/70 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Hafalan Baru (Sabaq)</span>
            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-star"></i>
            </div>
        </div>
        <div class="mt-3">
            <div class="text-2xl sm:text-3xl font-extrabold text-teal-700"><?= $summary['total_baru'] ?></div>
            <div class="flex items-center gap-2 mt-1 text-xs text-slate-500">
                <span class="bg-teal-100 text-teal-800 px-1.5 py-0.5 rounded font-medium">+ Tambah Otomatis</span>
            </div>
        </div>
    </div>

    <!-- Card 3: Murojaah Hari Ini -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/70 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Murojaah Hari Ini</span>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-arrows-rotate"></i>
            </div>
        </div>
        <div class="mt-3">
            <div class="text-2xl sm:text-3xl font-extrabold text-amber-700">
                <?= $summary['total_murojaah_baru'] + $summary['total_murojaah_lama'] ?>
            </div>
            <div class="flex items-center gap-2 mt-1 text-xs text-slate-500">
                <span>Baru: <b><?= $summary['total_murojaah_baru'] ?></b></span> •
                <span>Lama: <b><?= $summary['total_murojaah_lama'] ?></b></span>
            </div>
        </div>
    </div>

    <!-- Card 4: Total Santri & Capaian -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/70 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Santri Aktif</span>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-users-line"></i>
            </div>
        </div>
        <div class="mt-3">
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-800"><?= $totalSantriCount ?></div>
            <div class="flex items-center gap-1.5 mt-1 text-xs text-slate-500">
                <span>Total: <b><?= round($totalJuzSeluruh, 1) ?> Juz</b> (<?= round($totalHalamanSeluruh) ?> Hal)</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column (2 Cols): Riwayat Setoran Terkini -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/70 p-5 sm:p-6">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Riwayat Setoran Terbaru</h3>
                </div>
                <a href="rekap" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                    Lihat Semua <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>

            <?php if (empty($recentSetoran)): ?>
                <div class="text-center py-12 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                    <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                        <i class="fa-solid fa-book"></i>
                    </div>
                    <p class="text-slate-600 font-medium text-sm">Belum ada riwayat setoran hari ini.</p>
                    <a href="setoran" class="inline-block mt-3 text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200">
                        + Tambah Setoran Pertama
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <th class="pb-3 pr-4">Santri</th>
                                <th class="pb-3 px-3">Jenis & Bagian</th>
                                <th class="pb-3 px-3">Penilaian</th>
                                <th class="pb-3 px-3">Otomatis</th>
                                <th class="pb-3 pl-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($recentSetoran as $row): 
                                $badgeJenis = match($row['jenis_setoran']) {
                                    'hafalan_baru' => ['bg-teal-50 text-teal-800 border-teal-200', 'Hafalan Baru'],
                                    'murojaah_baru' => ['bg-amber-50 text-amber-800 border-amber-200', 'Murojaah Baru'],
                                    'murojaah_sebelumnya' => ['bg-blue-50 text-blue-800 border-blue-200', 'Murojaah Lama'],
                                    default => ['bg-slate-50 text-slate-700 border-slate-200', $row['jenis_setoran']]
                                };

                                $badgeNilai = match($row['penilaian']) {
                                    'mumtaz' => ['bg-emerald-100 text-emerald-800', 'Mumtaz (⭐⭐⭐⭐⭐)'],
                                    'jayyid_jiddan' => ['bg-teal-100 text-teal-800', 'Jayyid Jiddan (⭐⭐⭐⭐)'],
                                    'jayyid' => ['bg-sky-100 text-sky-800', 'Jayyid (⭐⭐⭐)'],
                                    'maqbul' => ['bg-amber-100 text-amber-800', 'Maqbul (⭐⭐)'],
                                    'rasib' => ['bg-rose-100 text-rose-800', 'Rasib (⭐ Ulang)'],
                                    default => ['bg-slate-100 text-slate-700', $row['penilaian']]
                                };

                                $autoIcon = match($row['perubahan_otomatis']) {
                                    'tambah' => '<span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200"><i class="fa-solid fa-arrow-trend-up"></i> +'.$row['jumlah_halaman'].' Hal</span>',
                                    'kurang' => '<span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200"><i class="fa-solid fa-arrow-trend-down"></i> -'.$row['jumlah_halaman'].' Hal</span>',
                                    default => '<span class="text-[11px] font-medium text-slate-400">Tetap</span>'
                                };
                            ?>
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <!-- Santri -->
                                    <td class="py-3.5 pr-4">
                                        <a href="santri-detail?id=<?= $row['santri_id'] ?>" class="font-bold text-slate-800 hover:text-emerald-700">
                                            <?= htmlspecialchars($row['santri_nama']) ?>
                                        </a>
                                        <div class="text-[11px] text-slate-500">
                                            NIS: <?= htmlspecialchars($row['santri_nis']) ?> • <?= date('d M Y', strtotime($row['tanggal_setoran'])) ?>
                                        </div>
                                    </td>

                                    <!-- Cakupan -->
                                    <td class="py-3.5 px-3">
                                        <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md border mb-1 <?= $badgeJenis[0] ?>">
                                            <?= $badgeJenis[1] ?>
                                        </span>
                                        <div class="font-semibold text-slate-800 text-xs">
                                            <?= htmlspecialchars($row['cakupan_teks']) ?>
                                        </div>
                                        <div class="text-[11px] text-slate-500">
                                            Juz <?= $row['juz'] ?> (Hal. <?= $row['halaman_mulai'] ?> - <?= $row['halaman_selesai'] ?>)
                                        </div>
                                    </td>

                                    <!-- Nilai -->
                                    <td class="py-3.5 px-3">
                                        <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-lg <?= $badgeNilai[0] ?>">
                                            <?= $badgeNilai[1] ?>
                                        </span>
                                        <?php if (!empty($row['catatan'])): ?>
                                            <p class="text-[11px] text-slate-500 italic mt-0.5 truncate max-w-[150px]" title="<?= htmlspecialchars($row['catatan']) ?>">
                                                "<?= htmlspecialchars($row['catatan']) ?>"
                                            </p>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Status Otomatis -->
                                    <td class="py-3.5 px-3">
                                        <?= $autoIcon ?>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-3.5 pl-3 text-right">
                                        <button onclick="hapusSetoran(<?= $row['id'] ?>)" class="text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors" title="Batalkan & Rollback Otomatis">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column (1 Col): Peringatan Murojaah & Akses Cepat -->
    <div class="space-y-6">
        
        <!-- Box: Peringatan Murojaah Sebelumnya (Manzil) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/70 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm">Peringatan Murojaah</h4>
                        <p class="text-[11px] text-slate-500">> 7 Hari Belum Diulang</p>
                    </div>
                </div>
                <span class="bg-rose-100 text-rose-800 text-xs font-bold px-2 py-0.5 rounded-full">
                    <?= count($santriPerluMurojaah) ?>
                </span>
            </div>

            <?php if (empty($santriPerluMurojaah)): ?>
                <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200 text-center text-xs text-emerald-800">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg mb-1 block"></i>
                    Alhamdulillah, seluruh santri rutin murojaah dalam 7 hari terakhir!
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach (array_slice($santriPerluMurojaah, 0, 4) as $item): 
                        $santriObj = $item['santri'];
                        $rec = $item['top_rec'];
                    ?>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 hover:border-amber-300 transition-colors flex items-center justify-between">
                            <div>
                                <a href="santri-detail?id=<?= $santriObj['id'] ?>" class="font-bold text-xs text-slate-800 hover:text-emerald-700 block">
                                    <?= htmlspecialchars($santriObj['nama']) ?>
                                </a>
                                <p class="text-[11px] text-rose-600 font-semibold mt-0.5">
                                    <i class="fa-solid fa-circle-dot text-[8px]"></i> Wajib Murojaah Juz <?= $rec['juz'] ?> (<?= $rec['nama'] ?>)
                                </p>
                                <span class="text-[10px] text-slate-400">
                                    <?= $rec['hari_lalu'] >= 999 ? 'Belum pernah murojaah' : $rec['hari_lalu'] . ' hari lalu' ?>
                                </span>
                            </div>
                            <a href="setoran?santri_id=<?= $santriObj['id'] ?>&jenis=murojaah_sebelumnya&juz=<?= $rec['juz'] ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold px-2.5 py-1.5 rounded-lg shadow-sm">
                                Setor
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Box: Santri Progres Tertinggi -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/70 p-5">
            <h4 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                <i class="fa-solid fa-medal text-amber-500"></i> Capaian Santri Teratas
            </h4>
            <div class="space-y-3">
                <?php 
                // Sort santri by total_juz_hafal desc
                $topSantri = $allSantri;
                usort($topSantri, fn($a, $b) => ((float)$b['total_juz_hafal']) <=> ((float)$a['total_juz_hafal']));
                foreach (array_slice($topSantri, 0, 4) as $idx => $ts): 
                    $medalColor = match($idx) {
                        0 => 'bg-amber-100 text-amber-800 border-amber-300',
                        1 => 'bg-slate-200 text-slate-700 border-slate-300',
                        2 => 'bg-amber-700/10 text-amber-900 border-amber-400',
                        default => 'bg-slate-50 text-slate-600 border-slate-200'
                    };
                ?>
                    <div class="flex items-center justify-between text-xs p-2 rounded-xl hover:bg-slate-50">
                        <div class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-[10px] border <?= $medalColor ?>">
                                <?= $idx + 1 ?>
                            </span>
                            <div>
                                <a href="santri-detail?id=<?= $ts['id'] ?>" class="font-bold text-slate-800 hover:text-emerald-700 block">
                                    <?= htmlspecialchars($ts['nama']) ?>
                                </a>
                                <span class="text-[10px] text-slate-500"><?= htmlspecialchars($ts['halaqah_nama'] ?? 'Halaqah Umum') ?></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-extrabold text-emerald-700"><?= (float)$ts['total_juz_hafal'] ?> Juz</div>
                            <span class="text-[10px] text-slate-400"><?= round((float)$ts['total_halaman_hafal']) ?> Hal</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<script>
// Fungsi Hapus Setoran & Rollback Otomatis
async function hapusSetoran(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus data setoran ini? Bila setoran ini menambah hafalan santri, sistem akan MENGURANGI/MENGEMBALIKAN akumulasi hafalan secara otomatis.')) {
        return;
    }

    const base = window.BASE_URL || '/';
    try {
        let response = null;
        const payload = JSON.stringify({ id: id });
        try {
            response = await fetch(base + 'api/delete_setoran.php', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: payload });
            if (!response.ok && response.status !== 401 && response.status !== 400) throw new Error();
        } catch (_) {
            response = await fetch(base + 'api/delete-setoran', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: payload });
        }

        if (response.status === 401) {
            showToast('Sesi login telah berakhir. Silakan login kembali di tab baru.', 'danger');
            if (confirm('Sesi login Anda telah berakhir. Ingin membuka halaman login di tab baru?')) {
                window.open('login', '_blank');
            }
            return;
        }

        const res = await response.json();
        if (res.success) {
            showToast(res.message, 'success');
            setTimeout(() => window.location.reload(), 800);
        } else {
            showToast(res.message || 'Gagal menghapus setoran', 'danger');
        }
    } catch (err) {
        showToast(err.message || 'Terjadi kesalahan jaringan', 'danger');
    }
}
</script>

