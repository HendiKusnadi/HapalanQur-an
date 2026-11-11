<?php
declare(strict_types=1);

use App\Database;
use App\Models\Santri;
use App\ProgressEngine;
use App\QuranData;

$db = Database::getConnection();
$santriModel = new Santri($db);
$allSantri = $santriModel->getAll();

$selectedSantriId = isset($_GET['id']) ? (int)$_GET['id'] : (int)($allSantri[0]['id'] ?? 0);
$filterJuz = isset($_GET['juz']) && $_GET['juz'] !== '' ? (int)$_GET['juz'] : null;
$filterStatus = $_GET['status'] ?? 'semua';

$engine = new ProgressEngine($db);
$report = $engine->getDetailedJuzSurahReport($selectedSantriId);

if (empty($report) || empty($report['santri'])) {
    echo '<div class="p-8 text-center text-rose-600 font-bold bg-white rounded-2xl shadow">Santri tidak ditemukan!</div>';
    return;
}

$santri = $report['santri'];
?>

<div class="space-y-6">

    <!-- Top Action & Selector Card -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200/80 space-y-4">
        
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <span class="inline-block text-[10px] font-extrabold uppercase tracking-widest px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 mb-1">
                    Laporan Rincian Per Juz & Surah
                </span>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice text-emerald-600"></i> Laporan Capaian Tahfidz Siswa
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Memantau rincian ayat yang sudah tuntas maupun yang masih dalam proses per juz dan surat.
                </p>
            </div>

            <!-- Tombol Export Excel & Cetak PDF -->
            <div class="flex flex-wrap items-center gap-2 self-start lg:self-auto">
                <a href="api/export_santri_excel.php?id=<?= $santri['id'] ?>" class="inline-flex items-center gap-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-300 font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs transition-all active:scale-95">
                    <i class="fa-solid fa-file-excel text-emerald-600 text-sm"></i>
                    <span>Export Excel (.csv)</span>
                </a>
                <a href="cetak-laporan?id=<?= $santri['id'] ?>" target="_blank" class="inline-flex items-center gap-1.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md transition-all active:scale-95">
                    <i class="fa-solid fa-print text-sm"></i>
                    <span>Cetak PDF Resmi (A4)</span>
                </a>
                <a href="setoran?santri_id=<?= $santri['id'] ?>" class="inline-flex items-center gap-1.5 bg-amber-400 hover:bg-amber-300 text-amber-950 font-extrabold text-xs px-4 py-2.5 rounded-xl shadow-md transition-all active:scale-95">
                    <i class="fa-solid fa-circle-plus"></i>
                    <span>+ Setoran</span>
                </a>
            </div>
        </div>

        <!-- Pencarian & Pilihan Siswa / Santri (Select dengan Filter) -->
        <div class="pt-4 border-t border-slate-100 grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            <div class="md:col-span-5">
                <label class="block text-xs font-bold text-slate-600 mb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-user-graduate text-emerald-600"></i> Pilih / Cari Santri:
                </label>
                <div class="relative">
                    <select id="select-santri-laporan" onchange="onSelectSantriChange(this.value)" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <?php foreach ($allSantri as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= $s['id'] === $selectedSantriId ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['nama']) ?> (NIS: <?= htmlspecialchars($s['nis']) ?>) - <?= htmlspecialchars($s['halaqah_nama'] ?? 'Halaqah Umum') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Filter Juz -->
            <div class="md:col-span-4">
                <label class="block text-xs font-bold text-slate-600 mb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-layer-group text-emerald-600"></i> Tampilkan Juz:
                </label>
                <select id="filter-juz" onchange="applyFilters()" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Semua Juz (1 - 30)</option>
                    <?php for ($j = 1; $j <= 30; $j++): 
                        $jInfo = QuranData::getJuz($j);
                    ?>
                        <option value="<?= $j ?>" <?= $filterJuz === $j ? 'selected' : '' ?>>
                            Juz <?= $j ?> (<?= htmlspecialchars($jInfo['nama']) ?>)
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Filter Status Hafalan -->
            <div class="md:col-span-3">
                <label class="block text-xs font-bold text-slate-600 mb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-filter text-emerald-600"></i> Status Surah:
                </label>
                <select id="filter-status" onchange="applyFilters()" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="semua" <?= $filterStatus === 'semua' ? 'selected' : '' ?>>Semua Status</option>
                    <option value="tuntas" <?= $filterStatus === 'tuntas' ? 'selected' : '' ?>>Hafal Tuntas Saja (✓)</option>
                    <option value="proses" <?= $filterStatus === 'proses' ? 'selected' : '' ?>>Sedang Proses Saja (⏳)</option>
                    <option value="belum" <?= $filterStatus === 'belum' ? 'selected' : '' ?>>Belum Dihafal</option>
                </select>
            </div>
        </div>

    </div>

    <!-- Santri Identity & Summary KPI Banner -->
    <div class="bg-gradient-to-br from-emerald-800 via-emerald-700 to-teal-800 rounded-3xl p-6 sm:p-7 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center text-3xl font-extrabold text-amber-300 shadow-inner flex-shrink-0">
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
                    <?php 
                        $surahPosisi = QuranData::getSurah((int)$santri['terakhir_surah_id']); 
                        $namaSurahPosisi = $surahPosisi['nama'] ?? 'Surah ' . $santri['terakhir_surah_id'];
                    ?>
                    <h3 class="text-2xl font-extrabold tracking-tight mt-1"><?= htmlspecialchars($santri['nama']) ?></h3>
                    <p class="text-xs text-emerald-100/90 mt-0.5 flex items-center gap-2">
                        <span><i class="fa-solid fa-user-tie text-emerald-300 mr-1"></i> Pembimbing: <?= htmlspecialchars($santri['halaqah_pembimbing'] ?? 'Musyrif') ?></span>
                        <span>•</span>
                        <span>Posisi: QS. <?= $namaSurahPosisi ?> : <?= $santri['terakhir_ayat'] ?> (Hal. <?= $santri['terakhir_halaman'] ?>)</span>
                    </p>
                </div>
            </div>

            <!-- Quick Metrics Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-white/10 backdrop-blur-md border border-white/20 p-4 rounded-2xl text-center">
                <div>
                    <span class="text-[10px] text-emerald-200 uppercase font-bold">Total Hafal</span>
                    <div class="text-xl font-extrabold text-white mt-0.5"><?= (float)$santri['total_juz_hafal'] ?> Juz</div>
                    <span class="text-[10px] text-emerald-200"><?= round((float)$santri['total_halaman_hafal']) ?> Hal</span>
                </div>
                <div>
                    <span class="text-[10px] text-amber-300 uppercase font-bold">Surah Tuntas</span>
                    <div class="text-xl font-extrabold text-amber-300 mt-0.5"><?= $report['total_tuntas_surah'] ?></div>
                    <span class="text-[10px] text-emerald-200">Surah</span>
                </div>
                <div>
                    <span class="text-[10px] text-teal-200 uppercase font-bold">Sedang Proses</span>
                    <div class="text-xl font-extrabold text-teal-200 mt-0.5"><?= $report['total_proses_surah'] ?></div>
                    <span class="text-[10px] text-emerald-200">Surah</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-300 uppercase font-bold">Belum Dihafal</span>
                    <div class="text-xl font-extrabold text-slate-200 mt-0.5"><?= $report['total_belum_surah'] ?></div>
                    <span class="text-[10px] text-emerald-200">Surah</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Rincian Per Juz & Surat -->
    <div class="space-y-6">
        <?php foreach ($report['juz_reports'] as $juz): 
            if ($filterJuz !== null && $juz['juz'] !== $filterJuz) {
                continue;
            }

            // Filter surah dalam juz sesuai status
            $filteredSurahs = array_filter($juz['surahs'], function($s) use ($filterStatus) {
                if ($filterStatus === 'tuntas') return $s['is_tuntas'];
                if ($filterStatus === 'proses') return $s['is_proses'];
                if ($filterStatus === 'belum') return (!$s['is_tuntas'] && !$s['is_proses']);
                return true;
            });

            if (empty($filteredSurahs)) {
                continue;
            }

            $badgeColor = match($juz['status']) {
                'HAPAL TUNTAS' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                'SEDANG PROSES' => 'bg-amber-100 text-amber-900 border-amber-300',
                default => 'bg-slate-100 text-slate-600 border-slate-200'
            };
        ?>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                
                <!-- Header Kartu Juz -->
                <div class="p-4 sm:p-5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-emerald-700 text-white font-extrabold flex items-center justify-center text-sm shadow-xs">
                            <?= $juz['juz'] ?>
                        </span>
                        <div>
                            <h4 class="font-extrabold text-slate-800 text-base flex items-center gap-2">
                                Juz <?= $juz['juz'] ?> (<?= htmlspecialchars($juz['nama']) ?>)
                                <span class="text-xs font-semibold text-slate-400">• <?= $juz['halaman'] ?></span>
                            </h4>
                            <div class="text-xs text-slate-500 mt-0.5">
                                Terdiri dari <b><?= $juz['total_surah'] ?> Surah</b> (Total <?= $juz['total_ayat'] ?> Ayat)
                            </div>
                        </div>
                    </div>

                    <!-- Indikator Capaian Juz -->
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <span class="text-[11px] font-bold text-slate-600">
                                Tuntas: <b><?= $juz['surah_tuntas'] ?></b> / <?= $juz['total_surah'] ?> Surah
                            </span>
                            <div class="w-32 bg-slate-200 h-2 rounded-full overflow-hidden mt-1">
                                <div class="bg-emerald-600 h-full rounded-full" style="width: <?= min(100, $juz['persentase']) ?>%"></div>
                            </div>
                        </div>
                        <span class="text-xs font-extrabold px-3 py-1 rounded-full border <?= $badgeColor ?>">
                            <?= $juz['status'] ?> (<?= $juz['persentase'] ?>%)
                        </span>
                    </div>
                </div>

                <!-- Tabel Rincian Surah -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider bg-white">
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-4">Nama Surah</th>
                                <th class="py-3 px-4">Status Hafalan</th>
                                <th class="py-3 px-4">Rincian Ayat yang Sudah Dihafal</th>
                                <th class="py-3 px-4 text-center">Nilai</th>
                                <th class="py-3 px-4 text-right">Tanggal Setoran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($filteredSurahs as $s): 
                                $statusBadge = match($s['status_badge']) {
                                    'HAPAL TUNTAS' => ['bg-emerald-50 text-emerald-800 border-emerald-300 font-extrabold', '✓ HAPAL TUNTAS'],
                                    'BELUM TUNTAS (SEDANG PROSES)' => ['bg-amber-50 text-amber-900 border-amber-300 font-bold', '⏳ BELUM TUNTAS (SEDANG PROSES)'],
                                    default => ['bg-slate-50 text-slate-500 border-slate-200', '— BELUM DIHAFAL']
                                };
                            ?>
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3.5 px-4 text-center font-mono text-slate-400 font-semibold">
                                        <?= $s['surah_id'] ?>
                                    </td>
                                    
                                    <!-- Nama Surah -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-extrabold text-slate-900 text-sm">
                                            <?= htmlspecialchars($s['nama']) ?>
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-quran mt-0.5">
                                            <?= $s['arab'] ?> • <?= $s['total_ayat_surah'] ?> Ayat (<?= $s['arti'] ?>)
                                        </div>
                                    </td>

                                    <!-- Status Hafalan -->
                                    <td class="py-3.5 px-4">
                                        <span class="inline-block px-2.5 py-1 rounded-lg border text-[11px] <?= $statusBadge[0] ?>">
                                            <?= $statusBadge[1] ?>
                                        </span>
                                    </td>

                                    <!-- Informasi Rincian Ayat (Ayat berapa s/d berapa) -->
                                    <td class="py-3.5 px-4 font-bold text-slate-800">
                                        <div class="text-xs">
                                            <?= htmlspecialchars($s['rincian_ayat']) ?>
                                        </div>
                                        <?php if ($s['is_proses']): ?>
                                            <div class="w-full max-w-xs bg-slate-100 h-1.5 rounded-full overflow-hidden mt-1.5">
                                                <div class="bg-amber-500 h-full rounded-full" style="width: <?= min(100, round(($s['ayat_hafal_count'] / max(1, $s['total_ayat_in_juz'])) * 100)) ?>%"></div>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Nilai Terakhir -->
                                    <td class="py-3.5 px-4 text-center uppercase font-extrabold text-slate-700">
                                        <?= $s['terakhir_penilaian'] !== '-' ? '<span class="bg-slate-100 px-2 py-0.5 rounded text-[11px] font-bold">'.$s['terakhir_penilaian'].'</span>' : '<span class="text-slate-300">—</span>' ?>
                                    </td>

                                    <!-- Tanggal Terakhir -->
                                    <td class="py-3.5 px-4 text-right text-slate-600 font-mono text-[11px]">
                                        <?= $s['terakhir_tanggal'] !== '-' ? date('d/m/Y', strtotime($s['terakhir_tanggal'])) : '<span class="text-slate-300">—</span>' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

</div>

<script>
function onSelectSantriChange(santriId) {
    const filterJuz = document.getElementById('filter-juz').value;
    const filterStatus = document.getElementById('filter-status').value;
    window.location.href = `laporan-santri?id=${santriId}&juz=${filterJuz}&status=${filterStatus}`;
}

function applyFilters() {
    const santriId = document.getElementById('select-santri-laporan').value;
    const filterJuz = document.getElementById('filter-juz').value;
    const filterStatus = document.getElementById('filter-status').value;
    window.location.href = `laporan-santri?id=${santriId}&juz=${filterJuz}&status=${filterStatus}`;
}
</script>
