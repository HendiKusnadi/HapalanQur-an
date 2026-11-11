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

$santriId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$santri = $santriModel->find($santriId);

if (!$santri) {
    echo '<div class="p-8 text-center text-rose-600 font-bold bg-white rounded-2xl shadow">Santri tidak ditemukan! <a href="santri" class="underline ml-2">Kembali ke Daftar Santri</a></div>';
    return;
}

$stats = $engine->getSantriStats($santriId);
$sabqiQueue = $engine->getMurojaahBaruQueue($santriId, 10);
$manzilRecs = $engine->getMurojaahSebelumnyaRecommendations($santriId);
$history = $setoranModel->getRecent(50, $santriId);

$totalJuzHafal = (float)$santri['total_juz_hafal'];
$arahHafalan = $santri['arah_hafalan'] ?? 'juz30_mundur';
?>

<!-- Santri Detail & Rapor Tahfidz -->
<div class="space-y-6">

    <!-- Top Action Bar (Back & Print) -->
    <div class="flex items-center justify-between no-print">
        <a href="santri" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-emerald-700 bg-white px-3.5 py-2 rounded-xl border border-slate-200 shadow-xs transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
        </a>
        <div class="flex items-center gap-2">
            <a href="laporan-santri?id=<?= $santri['id'] ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 px-3.5 py-2 rounded-xl border border-emerald-300 shadow-xs transition-colors">
                <i class="fa-solid fa-file-invoice text-emerald-600"></i> Laporan Juz & Surah (PDF/Excel)
            </a>
            <a href="cetak-rapor?id=<?= $santri['id'] ?>" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl border border-slate-200 shadow-xs transition-colors">
                <i class="fa-solid fa-print text-emerald-600"></i> Cetak Rapor
            </a>
            <a href="setoran?santri_id=<?= $santri['id'] ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-950 bg-amber-400 hover:bg-amber-300 px-4 py-2 rounded-xl shadow-md transition-all">
                <i class="fa-solid fa-plus-circle"></i> Setor Hafalan
            </a>
        </div>
    </div>

    <!-- Santri Profile Hero Card -->
    <div class="bg-gradient-to-br from-emerald-800 via-emerald-700 to-teal-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4 sm:gap-6">
                <!-- Avatar -->
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center text-3xl sm:text-4xl text-amber-300 font-bold shadow-inner flex-shrink-0">
                    <?= mb_substr($santri['nama'], 0, 1) ?>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="bg-emerald-500/40 text-emerald-100 text-xs px-2.5 py-0.5 rounded-full font-bold border border-emerald-400/30">
                            NIS: <?= htmlspecialchars($santri['nis']) ?>
                        </span>
                        <span class="bg-white/15 text-white text-xs px-2.5 py-0.5 rounded-full font-semibold">
                            <?= htmlspecialchars($santri['halaqah_nama'] ?? 'Halaqah Umum') ?>
                        </span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight mt-1.5"><?= htmlspecialchars($santri['nama']) ?></h2>
                    <p class="text-xs sm:text-sm text-emerald-100/90 flex items-center gap-3 mt-1">
                        <span><i class="fa-solid fa-user-tie text-emerald-300 mr-1"></i> Pembimbing: <?= htmlspecialchars($santri['halaqah_pembimbing'] ?? 'Musyrif') ?></span>
                        <span>•</span>
                        <span>Arah: <?= $arahHafalan === 'juz30_mundur' ? 'Juz 30 Mundur' : 'Juz 1 Maju' ?></span>
                    </p>
                </div>
            </div>

            <!-- Total Hafalan & Progress Bar Card -->
            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 sm:p-5 flex-shrink-0 min-w-[260px]">
                <div class="flex items-center justify-between text-xs text-emerald-100 mb-1">
                    <span>Capaian Hafalan:</span>
                    <span class="font-extrabold text-amber-300 text-sm"><?= $santri['persen_target'] ?>%</span>
                </div>
                <!-- Progress Bar -->
                <div class="w-full bg-black/20 h-3 rounded-full overflow-hidden p-0.5 border border-white/10 mb-2">
                    <div class="bg-gradient-to-r from-amber-400 to-amber-300 h-full rounded-full transition-all duration-500" style="width: <?= min(100, $santri['persen_target']) ?>%"></div>
                </div>
                <div class="flex items-center justify-between text-xs font-bold text-white">
                    <span><?= (float)$santri['total_juz_hafal'] ?> / <?= $santri['target_juz'] ?> Juz</span>
                    <span class="text-emerald-200"><?= round((float)$santri['total_halaman_hafal'], 1) ?> Halaman</span>
                </div>
            </div>
        </div>

        <!-- Watermark -->
        <div class="absolute -right-6 -bottom-8 text-white/5 text-[160px] pointer-events-none font-quran select-none">
            القرآن
        </div>
    </div>

    <!-- Widget Penyesuaian Otomatis Cepat (+/- Halaman) -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 no-print">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                    <i class="fa-solid fa-sliders text-emerald-600"></i> Koreksi / Penyesuaian Progres Otomatis
                </h4>
                <p class="text-xs text-slate-600 mt-0.5">
                    Gunakan tombol cepat di bawah untuk menambah atau mengurang hafalan santri secara otomatis:
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <!-- Tambah Cepat -->
                <button onclick="adjustSantri(<?= $santri['id'] ?>, 'tambah', 1.0)" class="inline-flex items-center gap-1 text-xs font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 px-3 py-1.5 rounded-xl transition-all active:scale-95">
                    <i class="fa-solid fa-plus text-emerald-600"></i> +1 Halaman
                </button>
                <button onclick="adjustSantri(<?= $santri['id'] ?>, 'tambah', 0.5)" class="inline-flex items-center gap-1 text-xs font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 px-3 py-1.5 rounded-xl transition-all active:scale-95">
                    <i class="fa-solid fa-plus text-emerald-600"></i> +0.5 Halaman
                </button>
                <!-- Kurang Cepat -->
                <button onclick="adjustSantri(<?= $santri['id'] ?>, 'kurang', 0.5)" class="inline-flex items-center gap-1 text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-300 px-3 py-1.5 rounded-xl transition-all active:scale-95">
                    <i class="fa-solid fa-minus text-rose-600"></i> -0.5 Halaman
                </button>
                <button onclick="adjustSantri(<?= $santri['id'] ?>, 'kurang', 1.0)" class="inline-flex items-center gap-1 text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-300 px-3 py-1.5 rounded-xl transition-all active:scale-95">
                    <i class="fa-solid fa-minus text-rose-600"></i> -1 Halaman (Mundur)
                </button>
            </div>
        </div>
    </div>

    <!-- Mushaf 30 Juz Visual Map -->
    <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-200/80">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-cubes text-emerald-600"></i> Visual Peta 30 Juz Al-Qur'an
            </h3>
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1 text-emerald-700 font-semibold"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Hafal</span>
                <span class="flex items-center gap-1 text-amber-700 font-semibold"><span class="w-3 h-3 rounded-full bg-amber-400"></span> Berjalan</span>
                <span class="flex items-center gap-1 text-slate-400"><span class="w-3 h-3 rounded-full bg-slate-200"></span> Belum</span>
            </div>
        </div>

        <!-- 30 Blocks Grid -->
        <div class="grid grid-cols-5 sm:grid-cols-6 md:grid-cols-10 gap-2 sm:gap-2.5">
            <?php 
            $hafalCount = (int)floor($totalJuzHafal);
            for ($j = 1; $j <= 30; $j++): 
                $isHafal = false;
                $isSedangProses = false;

                if ($arahHafalan === 'juz30_mundur') {
                    if ($j > (30 - $hafalCount)) {
                        $isHafal = true;
                    } elseif ($j === (30 - $hafalCount)) {
                        $isSedangProses = true;
                    }
                } else {
                    if ($j <= $hafalCount) {
                        $isHafal = true;
                    } elseif ($j === ($hafalCount + 1)) {
                        $isSedangProses = true;
                    }
                }

                $boxClass = $isHafal 
                    ? 'bg-emerald-600 text-white border-emerald-700 shadow-xs' 
                    : ($isSedangProses 
                        ? 'bg-amber-100 text-amber-900 border-amber-300 font-bold' 
                        : 'bg-slate-50 text-slate-400 border-slate-200');
            ?>
                <div class="p-2 sm:p-2.5 rounded-xl border text-center transition-transform hover:scale-105 <?= $boxClass ?>" title="Juz <?= $j ?>">
                    <div class="text-[10px] uppercase font-bold tracking-tight">Juz</div>
                    <div class="text-base sm:text-lg font-extrabold"><?= $j ?></div>
                    <div class="text-[9px] mt-0.5">
                        <?= $isHafal ? '<i class="fa-solid fa-check"></i> Mutqin' : ($isSedangProses ? 'Proses' : '—') ?>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Detail Tabs: Sabqi, Manzil, & Riwayat -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column 1: Murojaah Baru (Sabqi Queue) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center text-xs font-bold">
                        <i class="fa-solid fa-star-half-stroke"></i>
                    </div>
                    <h4 class="font-bold text-slate-800 text-sm">Murojaah Baru (Sabqi)</h4>
                </div>
                <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                    <?= count($sabqiQueue) ?> Bagian
                </span>
            </div>

            <?php if (empty($sabqiQueue)): ?>
                <p class="text-xs text-slate-400 py-6 text-center">Belum ada antrean murojaah baru.</p>
            <?php else: ?>
                <div class="space-y-2.5">
                    <?php foreach ($sabqiQueue as $sb): ?>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs flex items-center justify-between">
                            <div>
                                <div class="font-bold text-slate-800"><?= htmlspecialchars($sb['judul']) ?></div>
                                <div class="text-[10px] text-slate-500 mt-0.5">
                                    Juz <?= $sb['juz'] ?> • Telah disetor: <?= $sb['frekuensi'] ?>x
                                </div>
                            </div>
                            <a href="setoran?santri_id=<?= $santri['id'] ?>&jenis=murojaah_baru" class="bg-amber-400 hover:bg-amber-500 text-amber-950 font-bold px-2 py-1 rounded-md text-[10px]">
                                Setor
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Column 2: Murojaah Sebelumnya (Manzil Queue) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-800 flex items-center justify-center text-xs font-bold">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </div>
                    <h4 class="font-bold text-slate-800 text-sm">Rotasi Murojaah Lama</h4>
                </div>
                <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">
                    <?= count($manzilRecs) ?> Juz
                </span>
            </div>

            <?php if (empty($manzilRecs)): ?>
                <p class="text-xs text-slate-400 py-6 text-center">Belum ada juz tuntas yang tercatat.</p>
            <?php else: ?>
                <div class="space-y-2.5">
                    <?php foreach ($manzilRecs as $mz): 
                        $badgeUrg = $mz['hari_lalu'] >= 7 ? 'text-rose-600 font-bold bg-rose-50 border-rose-200' : 'text-slate-600 bg-slate-100 border-slate-200';
                    ?>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs flex items-center justify-between">
                            <div>
                                <div class="font-bold text-slate-800">Juz <?= $mz['juz'] ?> (<?= $mz['nama'] ?>)</div>
                                <div class="text-[10px] text-slate-500 mt-0.5"><?= $mz['halaman'] ?></div>
                                <span class="inline-block text-[9px] px-1.5 py-0.5 rounded border mt-1 <?= $badgeUrg ?>">
                                    <?= $mz['hari_lalu'] >= 999 ? 'Belum Pernah' : $mz['hari_lalu'] . ' hari lalu' ?>
                                </span>
                            </div>
                            <a href="setoran?santri_id=<?= $santri['id'] ?>&jenis=murojaah_sebelumnya&juz=<?= $mz['juz'] ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-2 py-1 rounded-md text-[10px]">
                                Setor
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Column 3: Ringkasan Rapor / Statistik -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-bold">
                    <i class="fa-solid fa-chart-simple"></i>
                </div>
                <h4 class="font-bold text-slate-800 text-sm">Statistik & Rapor</h4>
            </div>

            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-slate-600">Total Setoran Hafalan Baru:</span>
                    <span class="font-extrabold text-slate-800"><?= $stats['setoran_baru_count'] ?> kali (<?= $stats['setoran_baru_hal'] ?> Hal)</span>
                </div>
                <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-slate-600">Total Murojaah Baru:</span>
                    <span class="font-extrabold text-amber-700"><?= $stats['murojaah_baru_count'] ?> kali (<?= $stats['murojaah_baru_hal'] ?> Hal)</span>
                </div>
                <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-slate-600">Total Murojaah Sebelumnya:</span>
                    <span class="font-extrabold text-blue-700"><?= $stats['murojaah_lama_count'] ?> kali (<?= $stats['murojaah_lama_hal'] ?> Hal)</span>
                </div>
                <div class="flex items-center justify-between p-2.5 bg-emerald-50 rounded-xl border border-emerald-200">
                    <span class="text-emerald-900 font-bold">Total Akumulasi Hal:</span>
                    <span class="font-extrabold text-emerald-800 text-sm"><?= (float)$santri['total_halaman_hafal'] ?> Hal</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Riwayat Lengkap Setoran Santri -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-list text-emerald-600"></i> Riwayat Lengkap Setoran Santri
            </h3>
            <span class="text-xs text-slate-500"><?= count($history) ?> Catatan</span>
        </div>

        <?php if (empty($history)): ?>
            <p class="text-center py-8 text-xs text-slate-400">Belum ada riwayat setoran untuk santri ini.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="pb-3 pr-4">Tanggal</th>
                            <th class="pb-3 px-3">Jenis</th>
                            <th class="pb-3 px-3">Bagian Al-Qur'an</th>
                            <th class="pb-3 px-3">Nilai & Status</th>
                            <th class="pb-3 px-3">Dampak Otomatis</th>
                            <th class="pb-3 px-3">Catatan Tajwid</th>
                            <th class="pb-3 pl-3 text-right no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <?php foreach ($history as $h): 
                            $badgeJenis = match($h['jenis_setoran']) {
                                'hafalan_baru' => ['bg-teal-50 text-teal-800 border-teal-200', 'Hafalan Baru'],
                                'murojaah_baru' => ['bg-amber-50 text-amber-800 border-amber-200', 'Murojaah Baru'],
                                'murojaah_sebelumnya' => ['bg-blue-50 text-blue-800 border-blue-200', 'Murojaah Lama'],
                                default => ['bg-slate-50 text-slate-700', $h['jenis_setoran']]
                            };
                        ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 pr-4 font-semibold text-slate-800 whitespace-nowrap">
                                    <?= date('d/m/Y', strtotime($h['tanggal_setoran'])) ?>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-block px-2 py-0.5 rounded border text-[10px] font-bold uppercase <?= $badgeJenis[0] ?>">
                                        <?= $badgeJenis[1] ?>
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="font-bold text-slate-800"><?= htmlspecialchars($h['cakupan_teks']) ?></div>
                                    <div class="text-[10px] text-slate-400">Juz <?= $h['juz'] ?> (Hal. <?= $h['halaman_mulai'] ?> - <?= $h['halaman_selesai'] ?>)</div>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="font-bold text-slate-800 uppercase tracking-tight"><?= $h['penilaian'] ?></div>
                                    <span class="text-[10px] font-semibold <?= $h['status_kelulusan'] === 'lulus' ? 'text-emerald-600' : 'text-rose-600' ?>">
                                        <?= $h['status_kelulusan'] === 'lulus' ? 'Lulus' : 'Mengulang' ?>
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    <?php if ($h['perubahan_otomatis'] === 'tambah'): ?>
                                        <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                            +<?= $h['jumlah_halaman'] ?> Hal
                                        </span>
                                    <?php elseif ($h['perubahan_otomatis'] === 'kurang'): ?>
                                        <span class="text-rose-700 font-bold bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                            -<?= $h['jumlah_halaman'] ?> Hal
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-400 font-medium">Tetap</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3 text-slate-600 italic">
                                    <?= !empty($h['catatan']) ? htmlspecialchars($h['catatan']) : '-' ?>
                                </td>
                                <td class="py-3 pl-3 text-right no-print">
                                    <button onclick="hapusSetoran(<?= $h['id'] ?>)" class="text-slate-400 hover:text-rose-600 p-1 rounded" title="Batalkan & Rollback Otomatis">
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

<script>
// Penyesuaian Progres Langsung (+/-)
async function adjustSantri(santriId, action, pages) {
    const verb = action === 'tambah' ? 'MENAMBAH' : 'MENGURANGI';
    if (!confirm(`Apakah Anda yakin ingin ${verb} progres santri sebesar ${pages} halaman?`)) {
        return;
    }

    const base = window.BASE_URL || '/';
    try {
        let response = null;
        const payload = JSON.stringify({ santri_id: santriId, action: action, pages: pages });
        try {
            response = await fetch(base + 'api/adjust_progress.php', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: payload });
            if (!response.ok && response.status !== 401 && response.status !== 400) throw new Error();
        } catch (_) {
            response = await fetch(base + 'api/adjust-progress', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: payload });
        }

        if (response.status === 401) {
            showToast('Sesi login telah berakhir. Silakan login kembali di tab baru.', 'danger');
            if (confirm('Sesi login telah berakhir. Ingin membuka halaman login di tab baru?')) {
                window.open('login', '_blank');
            }
            return;
        }

        const res = await response.json();
        if (res.success) {
            showToast(res.message, 'success');
            setTimeout(() => window.location.reload(), 700);
        } else {
            showToast(res.message || 'Gagal memproses penyesuaian', 'danger');
        }
    } catch (e) {
        showToast(e.message || 'Gagal memproses penyesuaian', 'danger');
    }
}

// Hapus Setoran
async function hapusSetoran(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus data setoran ini? Akumulasi hafalan akan disesuaikan secara otomatis.')) {
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
            if (confirm('Sesi login telah berakhir. Ingin membuka halaman login di tab baru?')) {
                window.open('login', '_blank');
            }
            return;
        }

        const res = await response.json();
        if (res.success) {
            showToast(res.message, 'success');
            setTimeout(() => window.location.reload(), 700);
        } else {
            showToast(res.message || 'Gagal menghapus setoran', 'danger');
        }
    } catch (err) {
        showToast(err.message || 'Terjadi kesalahan jaringan', 'danger');
    }
}
</script>

