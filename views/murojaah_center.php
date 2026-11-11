<?php
declare(strict_types=1);

use App\Database;
use App\Models\Santri;
use App\ProgressEngine;
use App\QuranData;

$db = Database::getConnection();
$santriModel = new Santri($db);
$engine = new ProgressEngine($db);

$allSantri = $santriModel->getAll();

// Kumpulkan antrean Sabqi seluruh santri
$stmtSabqiAll = $db->query("
    SELECT ma.*, s.nama as santri_nama, s.nis as santri_nis, h.nama as halaqah_nama
    FROM murojaah_antrean ma
    JOIN santri s ON ma.santri_id = s.id
    LEFT JOIN halaqah h ON s.halaqah_id = h.id
    WHERE ma.jenis = 'baru' AND ma.status = 'menunggu'
    ORDER BY ma.id DESC
");
$allSabqi = $stmtSabqiAll->fetchAll();

// Kumpulkan santri yang butuh rotasi Manzil
$manzilAlerts = [];
foreach ($allSantri as $s) {
    $recs = $engine->getMurojaahSebelumnyaRecommendations((int)$s['id']);
    if (!empty($recs)) {
        // Ambil top 2 rekomendasi
        foreach (array_slice($recs, 0, 2) as $r) {
            $manzilAlerts[] = [
                'santri' => $s,
                'juz' => $r['juz'],
                'nama' => $r['nama'],
                'halaman' => $r['halaman'],
                'hari_lalu' => $r['hari_lalu'],
                'urgensi' => $r['urgensi'],
            ];
        }
    }
}

// Urutkan alerts berdasarkan hari_lalu descending (paling darurat)
usort($manzilAlerts, fn($a, $b) => $b['hari_lalu'] <=> $a['hari_lalu']);
?>

<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-arrows-rotate text-emerald-600"></i> Pusat Kendali Murojaah Santri
            </h2>
            <p class="text-sm text-slate-500">
                Memantau Murojaah Hafalan Baru (Sabqi) dan rotasi Murojaah Hafalan Sebelumnya (Manzil) agar hafalan santri mutqin.
            </p>
        </div>
        <a href="setoran" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-950 bg-amber-400 hover:bg-amber-300 px-4 py-2.5 rounded-xl shadow-md transition-all self-start sm:self-auto">
            <i class="fa-solid fa-plus-circle"></i> Input Setoran Murojaah
        </a>
    </div>

    <!-- Alert Banner -->
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 sm:p-5 flex items-start gap-3.5 text-amber-950">
        <div class="w-9 h-9 rounded-xl bg-amber-200/80 text-amber-800 flex items-center justify-center flex-shrink-0 text-base">
            <i class="fa-solid fa-lightbulb"></i>
        </div>
        <div class="text-xs sm:text-sm">
            <h4 class="font-bold">Prinsip Tahfidz Mutqin:</h4>
            <p class="text-slate-700 mt-0.5 leading-relaxed">
                <b>1. Sabqi (Murojaah Baru)</b>: Wajib disetorkan setiap hari mengulang 5 setoran terakhir.<br>
                <b>2. Manzil (Murojaah Lama)</b>: Seluruh juz yang sudah tuntas harus diulang berkala maksimal setiap <b>7 hari sekali</b> agar tidak pudar.
            </p>
        </div>
    </div>

    <!-- Grid 2 Kolom: Sabqi & Manzil -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Kolom Kiri: Antrean Murojaah Baru (Sabqi) -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-200/80">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-star-half-stroke"></i>
                    </span>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Antrean Murojaah Baru (Sabqi)</h3>
                        <p class="text-[11px] text-slate-500">Setoran hafalan baru terkini yang wajib diulang</p>
                    </div>
                </div>
                <span class="text-xs font-extrabold text-amber-800 bg-amber-100 px-2.5 py-1 rounded-full">
                    <?= count($allSabqi) ?> Antrean
                </span>
            </div>

            <?php if (empty($allSabqi)): ?>
                <div class="text-center py-10 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                    <p class="text-xs text-slate-400">Tidak ada antrean sabqi yang menunggu saat ini.</p>
                </div>
            <?php else: ?>
                <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                    <?php foreach ($allSabqi as $sq): ?>
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 hover:border-amber-400 transition-all flex items-center justify-between gap-3 text-xs">
                            <div>
                                <a href="santri-detail?id=<?= $sq['santri_id'] ?>" class="font-bold text-slate-800 hover:text-emerald-700 text-sm block">
                                    <?= htmlspecialchars($sq['santri_nama']) ?>
                                </a>
                                <div class="text-slate-600 font-semibold mt-1">
                                    <?= htmlspecialchars($sq['judul']) ?>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Juz <?= $sq['juz'] ?> • <?= htmlspecialchars($sq['halaqah_nama'] ?? 'Halaqah Umum') ?> • Riwayat Diulang: <?= $sq['frekuensi'] ?>x
                                </div>
                            </div>
                            <a href="setoran?santri_id=<?= $sq['santri_id'] ?>&jenis=murojaah_baru" class="bg-amber-400 hover:bg-amber-500 text-amber-950 font-extrabold px-3 py-1.5 rounded-lg shadow-sm whitespace-nowrap text-xs">
                                Setorkan
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Kolom Kanan: Peringatan & Rotasi Murojaah Lama (Manzil) -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-200/80">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-800 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </span>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Rotasi Murojaah Sebelumnya (Manzil)</h3>
                        <p class="text-[11px] text-slate-500">Prioritas juz yang paling lama belum dimurojaah</p>
                    </div>
                </div>
                <span class="text-xs font-extrabold text-blue-800 bg-blue-100 px-2.5 py-1 rounded-full">
                    <?= count($manzilAlerts) ?> Target
                </span>
            </div>

            <?php if (empty($manzilAlerts)): ?>
                <div class="text-center py-10 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                    <p class="text-xs text-slate-400">Belum ada catatan target manzil.</p>
                </div>
            <?php else: ?>
                <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                    <?php foreach ($manzilAlerts as $ma): 
                        $santri = $ma['santri'];
                        $isUrgent = $ma['hari_lalu'] >= 7;
                        $urgClass = $isUrgent ? 'border-rose-300 bg-rose-50/40' : 'border-slate-200 bg-slate-50';
                        $urgBadge = $isUrgent ? 'bg-rose-100 text-rose-800 font-bold border-rose-300' : 'bg-slate-200 text-slate-700 border-slate-300';
                    ?>
                        <div class="p-3.5 rounded-xl border <?= $urgClass ?> hover:border-blue-400 transition-all flex items-center justify-between gap-3 text-xs">
                            <div>
                                <div class="flex items-center gap-2">
                                    <a href="santri-detail?id=<?= $santri['id'] ?>" class="font-bold text-slate-800 hover:text-emerald-700 text-sm">
                                        <?= htmlspecialchars($santri['nama']) ?>
                                    </a>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded border <?= $urgBadge ?>">
                                        <?= $ma['hari_lalu'] >= 999 ? 'Belum Pernah' : $ma['hari_lalu'] . ' hari lalu' ?>
                                    </span>
                                </div>
                                <div class="font-bold text-blue-900 mt-1">
                                    Juz <?= $ma['juz'] ?> (<?= $ma['nama'] ?>) • <?= $ma['halaman'] ?>
                                </div>
                                <div class="text-[10px] text-slate-500 mt-0.5">
                                    Total Capaian: <?= (float)$santri['total_juz_hafal'] ?> Juz • <?= htmlspecialchars($santri['halaqah_nama'] ?? 'Halaqah Umum') ?>
                                </div>
                            </div>
                            <a href="setoran?santri_id=<?= $santri['id'] ?>&jenis=murojaah_sebelumnya&juz=<?= $ma['juz'] ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-3 py-1.5 rounded-lg shadow-sm whitespace-nowrap text-xs">
                                Setor Juz <?= $ma['juz'] ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>

