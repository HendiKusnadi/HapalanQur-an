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
    echo 'Santri tidak ditemukan!';
    exit;
}

$stats = $engine->getSantriStats($santriId);
$history = $setoranModel->getRecent(20, $santriId);
$totalJuzHafal = (float)$santri['total_juz_hafal'];
$arahHafalan = $santri['arah_hafalan'] ?? 'juz30_mundur';

// Pengaturan Pesantren
$namaAplikasi = 'SIM Tahfidz Quran';
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
    <title>Kartu Rapor Tahfidz - <?= htmlspecialchars($santri['nama']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
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
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    <!-- Floating Action Bar -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="santri-detail?id=<?= $santri['id'] ?>" class="text-xs font-bold text-slate-600 bg-white border border-slate-300 px-4 py-2 rounded-xl shadow-xs hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Profil Santri
        </a>
        <button onclick="window.print()" class="text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 px-5 py-2.5 rounded-xl shadow-md transition-all">
            <i class="fa-solid fa-print mr-1"></i> Cetak Dokumen / Simpan PDF
        </button>
    </div>

    <!-- Official Certificate Container (A4 Printable Layout) -->
    <div class="max-w-4xl mx-auto bg-white border-2 border-emerald-800 p-8 sm:p-12 rounded-2xl shadow-lg relative">
        
        <!-- Double Border Islamic Ornament -->
        <div class="border border-emerald-600/40 p-6 sm:p-8 rounded-xl relative">

            <!-- Kop Lembaga -->
            <div class="text-center border-b-2 border-emerald-800 pb-4 mb-6 relative">
                <div class="text-emerald-800 text-2xl font-quran mb-1">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
                <h1 class="text-xl sm:text-2xl font-extrabold uppercase tracking-wide text-slate-900"><?= htmlspecialchars($namaPesantren) ?></h1>
                <h2 class="text-sm sm:text-base font-bold text-emerald-800 tracking-wider mt-0.5">LEMBAGA PENDIDIKAN TAHFIDZ AL-QUR'AN</h2>
                <p class="text-xs text-slate-500 mt-1">Laporan Capaian Setoran Hafalan Baru & Murojaah Santri</p>
            </div>

            <!-- Judul Dokumen -->
            <div class="text-center my-6">
                <span class="inline-block bg-emerald-800 text-white text-xs sm:text-sm font-extrabold uppercase tracking-widest px-6 py-1.5 rounded-full shadow-xs">
                    KARTU KENDALI & RAPOR TAHFIDZ SANTRI
                </span>
            </div>

            <!-- Biodata Santri Table -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-6 bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div class="space-y-1.5">
                    <div class="flex"><span class="w-32 text-slate-500 font-semibold">Nama Santri</span>: <b class="text-slate-900 ml-1"><?= htmlspecialchars($santri['nama']) ?></b></div>
                    <div class="flex"><span class="w-32 text-slate-500 font-semibold">Nomor Induk (NIS)</span>: <span class="font-mono ml-1"><?= htmlspecialchars($santri['nis']) ?></span></div>
                    <div class="flex"><span class="w-32 text-slate-500 font-semibold">Jenis Kelamin</span>: <span class="ml-1"><?= $santri['jenis_kelamin'] === 'L' ? 'Laki-laki (Ikhwan)' : 'Perempuan (Akhwat)' ?></span></div>
                </div>
                <div class="space-y-1.5">
                    <div class="flex"><span class="w-36 text-slate-500 font-semibold">Halaqah / Kelompok</span>: <b class="text-slate-900 ml-1"><?= htmlspecialchars($santri['halaqah_nama'] ?? 'Halaqah Umum') ?></b></div>
                    <div class="flex"><span class="w-36 text-slate-500 font-semibold">Musyrif Pembimbing</span>: <span class="ml-1"><?= htmlspecialchars($santri['halaqah_pembimbing'] ?? 'Musyrif') ?></span></div>
                    <div class="flex"><span class="w-36 text-slate-500 font-semibold">Target Kelulusan</span>: <b class="text-emerald-800 ml-1"><?= $santri['target_juz'] ?> Juz Al-Qur'an</b></div>
                </div>
            </div>

            <!-- Ringkasan Capaian Hero -->
            <div class="grid grid-cols-3 gap-3 text-center mb-6">
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                    <div class="text-[10px] uppercase font-bold text-emerald-800">Total Juz Mutqin</div>
                    <div class="text-2xl font-extrabold text-emerald-900 mt-1"><?= (float)$santri['total_juz_hafal'] ?> Juz</div>
                    <div class="text-[10px] text-emerald-700"><?= round((float)$santri['total_halaman_hafal']) ?> Halaman Mushaf</div>
                </div>
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl">
                    <div class="text-[10px] uppercase font-bold text-amber-800">Persentase Target</div>
                    <div class="text-2xl font-extrabold text-amber-900 mt-1"><?= $santri['persen_target'] ?>%</div>
                    <div class="text-[10px] text-amber-700">Dari Target <?= $santri['target_juz'] ?> Juz</div>
                </div>
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl">
                    <div class="text-[10px] uppercase font-bold text-blue-800">Posisi Aktif</div>
                    <div class="text-sm font-extrabold text-blue-950 mt-1">QS. <?= $santri['surah_nama'] ?></div>
                    <div class="text-[10px] text-blue-700">Ayat <?= $santri['terakhir_ayat'] ?> (Hal. <?= $santri['terakhir_halaman'] ?>)</div>
                </div>
            </div>

            <!-- Peta 30 Juz Checklist (Print Layout) -->
            <div class="mb-6">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2 border-b border-slate-200 pb-1 flex items-center justify-between">
                    <span>Pemetaan Capaian 30 Juz Al-Qur'an:</span>
                    <span class="text-[10px] font-normal text-slate-500">[✓] = Mutqin / Telah Dihafal</span>
                </h4>
                <div class="grid grid-cols-10 gap-1.5 text-center text-xs">
                    <?php 
                    $hafalCount = (int)floor($totalJuzHafal);
                    for ($j = 1; $j <= 30; $j++): 
                        $isHafal = false;
                        if ($arahHafalan === 'juz30_mundur') {
                            if ($j > (30 - $hafalCount)) $isHafal = true;
                        } else {
                            if ($j <= $hafalCount) $isHafal = true;
                        }
                    ?>
                        <div class="p-1.5 rounded border text-[11px] <?= $isHafal ? 'bg-emerald-100 border-emerald-400 font-bold text-emerald-950' : 'bg-slate-50 border-slate-200 text-slate-400' ?>">
                            <div class="text-[9px] uppercase">Juz</div>
                            <div><?= $j ?></div>
                            <div class="text-[9px]"><?= $isHafal ? '✓' : '—' ?></div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Rekap Setoran Terakhir -->
            <div class="mb-8">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2 border-b border-slate-200 pb-1">
                    Catatan Setoran & Murojaah Terakhir:
                </h4>
                <table class="w-full text-left text-xs border border-slate-200">
                    <thead class="bg-slate-100 text-slate-700 uppercase text-[10px] font-bold">
                        <tr>
                            <th class="p-2 border-b">Tanggal</th>
                            <th class="p-2 border-b">Kategori</th>
                            <th class="p-2 border-b">Bagian Al-Qur'an</th>
                            <th class="p-2 border-b">Halaman</th>
                            <th class="p-2 border-b">Penilaian</th>
                            <th class="p-2 border-b">Catatan Tajwid</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach (array_slice($history, 0, 8) as $row): ?>
                            <tr>
                                <td class="p-2 font-mono text-[11px]"><?= date('d/m/Y', strtotime($row['tanggal_setoran'])) ?></td>
                                <td class="p-2 font-semibold">
                                    <?= match($row['jenis_setoran']) {
                                        'hafalan_baru' => 'Hafalan Baru',
                                        'murojaah_baru' => 'Murojaah Baru',
                                        'murojaah_sebelumnya' => 'Murojaah Lama',
                                        default => $row['jenis_setoran']
                                    } ?>
                                </td>
                                <td class="p-2 font-bold"><?= htmlspecialchars($row['cakupan_teks']) ?></td>
                                <td class="p-2 text-slate-600">Juz <?= $row['juz'] ?> (Hal. <?= $row['halaman_mulai'] ?>-<?= $row['halaman_selesai'] ?>)</td>
                                <td class="p-2 uppercase font-extrabold text-emerald-800"><?= $row['penilaian'] ?></td>
                                <td class="p-2 italic text-slate-500"><?= !empty($row['catatan']) ? htmlspecialchars($row['catatan']) : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tanda Tangan Resmi -->
            <div class="grid grid-cols-3 gap-4 text-center text-xs mt-12 pt-6 border-t border-slate-200">
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
                    <p class="text-slate-500 mb-16">Pimpinan / Koordinator Tahfidz,</p>
                    <p class="font-bold border-b border-slate-400 pb-1 inline-block min-w-[140px]">( ................................ )</p>
                </div>
            </div>

            <!-- Cetak Timestamp Footer -->
            <div class="text-[9px] text-slate-400 text-center mt-6">
                Dokumen resmi dicetak otomatis melalui SIM Tahfidz Al-Qur'an pada <?= date('d F Y, H:i') ?> WIB.
            </div>

        </div>
    </div>

</body>
</html>

