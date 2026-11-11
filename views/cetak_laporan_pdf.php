<?php
declare(strict_types=1);

use App\Database;
use App\Models\Santri;
use App\ProgressEngine;
use App\QuranData;

$db = Database::getConnection();
$santriId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$filterJuz = isset($_GET['juz']) && $_GET['juz'] !== '' ? (int)$_GET['juz'] : null;

$engine = new ProgressEngine($db);
$report = $engine->getDetailedJuzSurahReport($santriId);

if (empty($report) || empty($report['santri'])) {
    echo '<div style="padding:40px; text-align:center; font-family:sans-serif;">Santri tidak ditemukan!</div>';
    exit;
}

$santri = $report['santri'];

// Nama Pesantren
$namaPesantren = 'Pondok Pesantren & Rumah Tahfidz Al-Qur\'an';
try {
    $stmtPes = $db->query("SELECT nilai FROM pengaturan WHERE kunci = 'nama_pesantren'");
    $valPes = $stmtPes->fetchColumn();
    if ($valPes) $namaPesantren = $valPes;
} catch (\Throwable $e) {}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Lengkap Hafalan - <?= htmlspecialchars($santri['nama']) ?> (<?= htmlspecialchars($santri['nis']) ?>)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }
        .font-quran {
            font-family: 'Amiri', serif;
        }
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    <!-- Action Bar (Hidden on Print) -->
    <div class="max-w-5xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="laporan-santri?id=<?= $santri['id'] ?>" class="text-xs font-bold text-slate-700 bg-white border border-slate-300 px-4 py-2 rounded-xl shadow-xs hover:bg-slate-50 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Tampilan Layar
        </a>
        <div class="flex items-center gap-2">
            <a href="api/export_santri_excel.php?id=<?= $santri['id'] ?>" class="text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 px-4 py-2 rounded-xl shadow-xs flex items-center gap-1.5">
                <i class="fa-solid fa-file-excel text-emerald-600"></i> Unduh Format Excel
            </a>
            <button onclick="window.print()" class="text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 px-5 py-2.5 rounded-xl shadow-md transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF (A4)
            </button>
        </div>
    </div>

    <!-- Container Dokumen Resmi A4 -->
    <div class="max-w-5xl mx-auto bg-white border-2 border-emerald-800 p-8 sm:p-12 rounded-2xl shadow-xl relative">
        <div class="border border-emerald-600/30 p-6 sm:p-8 rounded-xl">

            <!-- Kop Lembaga -->
            <div class="text-center border-b-2 border-emerald-800 pb-4 mb-6">
                <div class="text-emerald-800 text-2xl font-quran mb-1">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
                <h1 class="text-xl sm:text-2xl font-extrabold uppercase tracking-wide text-slate-900"><?= htmlspecialchars($namaPesantren) ?></h1>
                <h2 class="text-xs sm:text-sm font-bold text-emerald-800 tracking-wider mt-0.5 uppercase">Laporan Evaluasi & Rincian Capaian Tahfidz Al-Qur'an</h2>
                <p class="text-[11px] text-slate-500 mt-1">Status Hafalan Tuntas & Sedang Proses Per Juz dan Per Surah</p>
            </div>

            <!-- Judul Dokumen -->
            <div class="text-center my-4">
                <span class="inline-block bg-emerald-800 text-white text-xs font-extrabold uppercase tracking-widest px-5 py-1.5 rounded-full shadow-xs">
                    LAPORAN CAPAIAN HAFALAN SANTRI
                </span>
            </div>

            <!-- Biodata Santri -->
            <div class="grid grid-cols-2 gap-4 text-xs mb-6 bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div class="space-y-1">
                    <div class="flex"><span class="w-32 text-slate-500 font-semibold">Nama Santri</span>: <b class="text-slate-900 ml-1"><?= htmlspecialchars($santri['nama']) ?></b></div>
                    <div class="flex"><span class="w-32 text-slate-500 font-semibold">Nomor Induk (NIS)</span>: <span class="font-mono ml-1"><?= htmlspecialchars($santri['nis']) ?></span></div>
                    <div class="flex"><span class="w-32 text-slate-500 font-semibold">Jenis Kelamin</span>: <span class="ml-1"><?= $santri['jenis_kelamin'] === 'L' ? 'Laki-laki (Ikhwan)' : 'Perempuan (Akhwat)' ?></span></div>
                </div>
                <div class="space-y-1">
                    <div class="flex"><span class="w-36 text-slate-500 font-semibold">Halaqah / Kelompok</span>: <b class="text-slate-900 ml-1"><?= htmlspecialchars($santri['halaqah_nama'] ?? 'Halaqah Umum') ?></b></div>
                    <div class="flex"><span class="w-36 text-slate-500 font-semibold">Musyrif Pembimbing</span>: <span class="ml-1"><?= htmlspecialchars($santri['halaqah_pembimbing'] ?? 'Musyrif') ?></span></div>
                    <div class="flex"><span class="w-36 text-slate-500 font-semibold">Total Hafalan Santri</span>: <b class="text-emerald-800 ml-1"><?= (float)$santri['total_juz_hafal'] ?> Juz (<?= round((float)$santri['total_halaman_hafal']) ?> Hal)</b></div>
                </div>
            </div>

            <!-- Ringkasan Statistik -->
            <div class="grid grid-cols-4 gap-3 text-center mb-6 text-xs">
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                    <span class="text-[10px] uppercase font-bold text-emerald-800">Surah Hafal Tuntas</span>
                    <div class="text-xl font-extrabold text-emerald-900 mt-0.5"><?= $report['total_tuntas_surah'] ?> Surah</div>
                </div>
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl">
                    <span class="text-[10px] uppercase font-bold text-amber-800">Sedang Proses</span>
                    <div class="text-xl font-extrabold text-amber-900 mt-0.5"><?= $report['total_proses_surah'] ?> Surah</div>
                </div>
                <div class="p-3 bg-slate-100 border border-slate-200 rounded-xl">
                    <span class="text-[10px] uppercase font-bold text-slate-600">Belum Dihafal</span>
                    <div class="text-xl font-extrabold text-slate-800 mt-0.5"><?= $report['total_belum_surah'] ?> Surah</div>
                </div>
                <div class="p-3 bg-teal-50 border border-teal-200 rounded-xl">
                    <span class="text-[10px] uppercase font-bold text-teal-800">Target Kelulusan</span>
                    <div class="text-xl font-extrabold text-teal-900 mt-0.5"><?= $santri['target_juz'] ?> Juz</div>
                </div>
            </div>

            <!-- Tabel Rincian Per Juz & Surah -->
            <div class="space-y-6">
                <?php foreach ($report['juz_reports'] as $juzItem): 
                    // Jika ada filter juz tertentu
                    if ($filterJuz !== null && $juzItem['juz'] !== $filterJuz) {
                        continue;
                    }

                    $juzBadgeColor = match($juzItem['status']) {
                        'HAPAL TUNTAS' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                        'SEDANG PROSES' => 'bg-amber-100 text-amber-900 border-amber-300',
                        default => 'bg-slate-100 text-slate-600 border-slate-200'
                    };
                ?>
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        
                        <!-- Header Juz -->
                        <div class="bg-slate-100/90 px-4 py-2.5 border-b border-slate-200 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-md bg-emerald-800 text-white font-extrabold flex items-center justify-center text-[11px]">
                                    <?= $juzItem['juz'] ?>
                                </span>
                                <b class="text-slate-800 text-sm">Juz <?= $juzItem['juz'] ?> (<?= htmlspecialchars($juzItem['nama']) ?>)</b>
                                <span class="text-slate-500 text-[11px]">• <?= $juzItem['halaman'] ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-semibold text-slate-600">
                                    Tuntas: <b><?= $juzItem['surah_tuntas'] ?></b> dari <?= $juzItem['total_surah'] ?> Surah
                                </span>
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded border <?= $juzBadgeColor ?>">
                                    <?= $juzItem['status'] ?> (<?= $juzItem['persentase'] ?>%)
                                </span>
                            </div>
                        </div>

                        <!-- Tabel Surah di Bawah Juz Ini -->
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold border-b border-slate-200">
                                <tr>
                                    <th class="py-2 px-3 w-8">No</th>
                                    <th class="py-2 px-3">Surah</th>
                                    <th class="py-2 px-3">Status Hafalan</th>
                                    <th class="py-2 px-3">Rincian Ayat yang Dihafal</th>
                                    <th class="py-2 px-3 text-center">Nilai</th>
                                    <th class="py-2 px-3 text-right">Tanggal Setor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($juzItem['surahs'] as $s): 
                                    $rowBadge = match($s['status_badge']) {
                                        'HAPAL TUNTAS' => 'text-emerald-800 font-extrabold bg-emerald-50 border-emerald-200',
                                        'BELUM TUNTAS (SEDANG PROSES)' => 'text-amber-800 font-bold bg-amber-50 border-amber-200',
                                        default => 'text-slate-400 bg-slate-50 border-slate-200'
                                    };
                                ?>
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="py-2 px-3 text-slate-400 font-mono"><?= $s['surah_id'] ?></td>
                                        <td class="py-2 px-3">
                                            <div class="font-bold text-slate-900"><?= htmlspecialchars($s['nama']) ?></div>
                                            <div class="text-[10px] text-slate-500 font-quran"><?= $s['arab'] ?> • <?= $s['total_ayat_surah'] ?> Ayat</div>
                                        </td>
                                        <td class="py-2 px-3">
                                            <span class="inline-block px-2 py-0.5 rounded border text-[10px] <?= $rowBadge ?>">
                                                <?= $s['is_tuntas'] ? '✓ HAPAL TUNTAS' : ($s['is_proses'] ? '⏳ SEDANG PROSES' : '— BELUM DIHAFAL') ?>
                                            </span>
                                        </td>
                                        <td class="py-2 px-3 font-semibold text-slate-800">
                                            <?= htmlspecialchars($s['rincian_ayat']) ?>
                                        </td>
                                        <td class="py-2 px-3 text-center uppercase font-bold text-slate-700">
                                            <?= htmlspecialchars($s['terakhir_penilaian']) ?>
                                        </td>
                                        <td class="py-2 px-3 text-right text-slate-600 font-mono text-[11px]">
                                            <?= $s['terakhir_tanggal'] !== '-' ? date('d/m/Y', strtotime($s['terakhir_tanggal'])) : '-' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Tanda Tangan Resmi -->
            <div class="grid grid-cols-3 gap-4 text-center text-xs mt-12 pt-6 border-t-2 border-slate-200">
                <div>
                    <p class="text-slate-500 mb-16">Wali Santri,</p>
                    <p class="font-bold border-b border-slate-400 pb-1 inline-block min-w-[140px]">( ................................ )</p>
                </div>
                <div>
                    <p class="text-slate-500 mb-16">Musyrif Pembimbing,</p>
                    <p class="font-bold border-b border-slate-400 pb-1 inline-block min-w-[140px]">
                        <?= htmlspecialchars($santri['halaqah_pembimbing'] ?? 'Musyrif Tahfidz') ?>
                    </p>
                </div>
                <div>
                    <p class="text-slate-500 mb-16">Pimpinan / Mudir Tahfidz,</p>
                    <p class="font-bold border-b border-slate-400 pb-1 inline-block min-w-[140px]">( ................................ )</p>
                </div>
            </div>

            <!-- Footer Dokumen -->
            <div class="text-[9px] text-slate-400 text-center mt-6">
                Laporan resmi dicetak melalui Sistem Informasi Tahfidz Al-Qur'an pada <?= date('d F Y, H:i') ?> WIB.
            </div>

        </div>
    </div>

</body>
</html>

