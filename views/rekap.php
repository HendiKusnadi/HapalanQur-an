<?php
declare(strict_types=1);

use App\Database;
use App\Models\Santri;
use App\QuranData;

$db = Database::getConnection();

$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$filterJenis = $_GET['jenis'] ?? '';
$filterHalaqah = isset($_GET['halaqah_id']) && $_GET['halaqah_id'] !== '' ? (int)$_GET['halaqah_id'] : null;

// Query rekapitulasi
$sql = "
    SELECT st.*, s.nama as santri_nama, s.nis as santri_nis, h.nama as halaqah_nama
    FROM setoran st
    JOIN santri s ON st.santri_id = s.id
    LEFT JOIN halaqah h ON s.halaqah_id = h.id
    WHERE st.tanggal_setoran BETWEEN ? AND ?
";
$params = [$startDate, $endDate];

if (!empty($filterJenis)) {
    $sql .= " AND st.jenis_setoran = ?";
    $params[] = $filterJenis;
}

if ($filterHalaqah) {
    $sql .= " AND s.halaqah_id = ?";
    $params[] = $filterHalaqah;
}

$sql .= " ORDER BY st.tanggal_setoran DESC, st.id DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll();

// Hitung metrik ringkasan
$totalSetoran = count($data);
$totalHalaman = 0.0;
$totalBaru = 0;
$totalMurojaahBaru = 0;
$totalMurojaahLama = 0;
$totalLulus = 0;
$uniqueSantri = [];

foreach ($data as &$row) {
    $totalHalaman += (float)$row['jumlah_halaman'];
    $uniqueSantri[$row['santri_id']] = true;
    if ($row['jenis_setoran'] === 'hafalan_baru') $totalBaru++;
    if ($row['jenis_setoran'] === 'murojaah_baru') $totalMurojaahBaru++;
    if ($row['jenis_setoran'] === 'murojaah_sebelumnya') $totalMurojaahLama++;
    if ($row['status_kelulusan'] === 'lulus') $totalLulus++;

    $s1 = QuranData::getSurah((int)$row['surah_mulai_id']);
    $s2 = QuranData::getSurah((int)$row['surah_selesai_id']);
    $row['surah_mulai_nama'] = $s1['nama'] ?? '';
    $row['surah_selesai_nama'] = $s2['nama'] ?? '';
}

// Halaqah list for filter
$halaqahList = $db->query("SELECT * FROM halaqah ORDER BY nama ASC")->fetchAll();
?>

<div class="space-y-6">

    <!-- Header & Print Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list text-emerald-600"></i> Rekapitulasi & Laporan Tahfidz
            </h2>
            <p class="text-sm text-slate-500">Rekap hafalan baru, murojaah sabaq & manzil, filter tanggal dan cetak laporan resmi.</p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto no-print">
            <a href="laporan-santri" class="inline-flex items-center gap-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-300 font-bold text-xs px-3.5 py-2.5 rounded-xl shadow-xs transition-all">
                <i class="fa-solid fa-file-invoice text-emerald-600"></i> Laporan Per Siswa (Juz & Surah)
            </a>
            <a href="api/export_rekap.php?start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>&jenis=<?= urlencode($filterJenis) ?>&halaqah_id=<?= $filterHalaqah ?>" class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 shadow-xs transition-all">
                <i class="fa-solid fa-file-excel text-emerald-600"></i> Export Excel
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md transition-all">
                <i class="fa-solid fa-print"></i> Cetak Laporan (PDF)
            </button>
        </div>
    </div>

    <!-- Filter Bar (Hidden on Print) -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-200/80 no-print">
        <form method="GET" action="rekap" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Kategori Setoran</label>
                <select name="jenis" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Kategori</option>
                    <option value="hafalan_baru" <?= $filterJenis === 'hafalan_baru' ? 'selected' : '' ?>>Hafalan Baru (Sabaq)</option>
                    <option value="murojaah_baru" <?= $filterJenis === 'murojaah_baru' ? 'selected' : '' ?>>Murojaah Baru (Sabqi)</option>
                    <option value="murojaah_sebelumnya" <?= $filterJenis === 'murojaah_sebelumnya' ? 'selected' : '' ?>>Murojaah Lama (Manzil)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Halaqah</label>
                <select name="halaqah_id" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Halaqah</option>
                    <?php foreach ($halaqahList as $hl): ?>
                        <option value="<?= $hl['id'] ?>" <?= $filterHalaqah === (int)$hl['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($hl['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs p-2.5 rounded-xl shadow-xs transition-colors">
                    <i class="fa-solid fa-filter mr-1"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Ringkasan Kartu Metrik -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Setoran</span>
            <div class="text-2xl font-extrabold text-slate-800 mt-1"><?= $totalSetoran ?></div>
            <span class="text-[10px] text-slate-400"><?= count($uniqueSantri) ?> Santri Menyetor</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-teal-600">Hafalan Baru</span>
            <div class="text-2xl font-extrabold text-teal-700 mt-1"><?= $totalBaru ?></div>
            <span class="text-[10px] text-slate-400">+ Otomatis Bertambah</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Total Murojaah</span>
            <div class="text-2xl font-extrabold text-amber-700 mt-1"><?= $totalMurojaahBaru + $totalMurojaahLama ?></div>
            <span class="text-[10px] text-slate-400">Baru: <?= $totalMurojaahBaru ?> • Lama: <?= $totalMurojaahLama ?></span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Total Halaman Disetor</span>
            <div class="text-2xl font-extrabold text-emerald-700 mt-1"><?= round($totalHalaman, 1) ?> Hal</div>
            <span class="text-[10px] text-slate-400">Rasio Lulus: <?= $totalSetoran > 0 ? round(($totalLulus / $totalSetoran) * 100) : 0 ?>%</span>
        </div>
    </div>

    <!-- Tabel Rekapitulasi (Siap Cetak) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6">
        <div class="border-b border-slate-100 pb-4 mb-4 flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-slate-800 text-base">Laporan Rincian Setoran Santri</h3>
                <p class="text-xs text-slate-500">Periode: <?= date('d M Y', strtotime($startDate)) ?> s/d <?= date('d M Y', strtotime($endDate)) ?></p>
            </div>
            <span class="text-xs font-bold text-slate-500"><?= $totalSetoran ?> Rekaman</span>
        </div>

        <?php if (empty($data)): ?>
            <div class="text-center py-12 text-slate-400 text-xs">
                Tidak ada data setoran yang cocok dengan filter yang dipilih.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                            <th class="pb-2.5 pr-2">No</th>
                            <th class="pb-2.5 px-2">Tanggal</th>
                            <th class="pb-2.5 px-2">Santri / NIS</th>
                            <th class="pb-2.5 px-2">Kategori</th>
                            <th class="pb-2.5 px-2">Cakupan Setoran</th>
                            <th class="pb-2.5 px-2">Juz / Hal</th>
                            <th class="pb-2.5 px-2">Nilai</th>
                            <th class="pb-2.5 px-2">Otomatis</th>
                            <th class="pb-2.5 px-2">Catatan</th>
                            <th class="pb-2.5 pl-2">Musyrif</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($data as $i => $row): 
                            $badgeKategori = match($row['jenis_setoran']) {
                                'hafalan_baru' => ['bg-teal-50 text-teal-800 border-teal-200', 'Hafalan Baru'],
                                'murojaah_baru' => ['bg-amber-50 text-amber-800 border-amber-200', 'Murojaah Baru'],
                                'murojaah_sebelumnya' => ['bg-blue-50 text-blue-800 border-blue-200', 'Murojaah Lama'],
                                default => ['bg-slate-50 text-slate-700', $row['jenis_setoran']]
                            };
                            $autoText = match($row['perubahan_otomatis']) {
                                'tambah' => '<span class="text-emerald-700 font-bold">+'.$row['jumlah_halaman'].' Hal</span>',
                                'kurang' => '<span class="text-rose-700 font-bold">-'.$row['jumlah_halaman'].' Hal</span>',
                                default => '<span class="text-slate-400">Tetap</span>'
                            };
                        ?>
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-2.5 pr-2 text-slate-400"><?= $i + 1 ?></td>
                                <td class="py-2.5 px-2 font-semibold text-slate-700 whitespace-nowrap">
                                    <?= date('d/m/Y', strtotime($row['tanggal_setoran'])) ?>
                                </td>
                                <td class="py-2.5 px-2 font-bold text-slate-800">
                                    <?= htmlspecialchars($row['santri_nama']) ?>
                                    <div class="text-[10px] text-slate-400 font-normal">NIS: <?= htmlspecialchars($row['santri_nis']) ?></div>
                                </td>
                                <td class="py-2.5 px-2">
                                    <span class="inline-block px-1.5 py-0.5 rounded border text-[10px] font-bold <?= $badgeKategori[0] ?>">
                                        <?= $badgeKategori[1] ?>
                                    </span>
                                </td>
                                <td class="py-2.5 px-2 font-semibold text-slate-800">
                                    QS. <?= $row['surah_mulai_nama'] ?>: <?= $row['ayat_mulai'] ?> - <?= $row['ayat_selesai'] ?>
                                </td>
                                <td class="py-2.5 px-2 text-slate-600 whitespace-nowrap">
                                    Juz <?= $row['juz'] ?> (Hal. <?= $row['halaman_mulai'] ?>-<?= $row['halaman_selesai'] ?>)
                                </td>
                                <td class="py-2.5 px-2 uppercase font-bold text-slate-700">
                                    <?= $row['penilaian'] ?>
                                </td>
                                <td class="py-2.5 px-2 whitespace-nowrap">
                                    <?= $autoText ?>
                                </td>
                                <td class="py-2.5 px-2 text-slate-500 italic max-w-xs truncate">
                                    <?= !empty($row['catatan']) ? htmlspecialchars($row['catatan']) : '-' ?>
                                </td>
                                <td class="py-2.5 pl-2 text-slate-600 whitespace-nowrap">
                                    <?= htmlspecialchars($row['musyrif_nama'] ?? 'Musyrif') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div>

