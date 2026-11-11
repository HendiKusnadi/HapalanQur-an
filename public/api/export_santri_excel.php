<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/QuranData.php';
require_once dirname(__DIR__, 2) . '/app/Database.php';
require_once dirname(__DIR__, 2) . '/app/Auth.php';
require_once dirname(__DIR__, 2) . '/app/ProgressEngine.php';

use App\Database;
use App\Auth;
use App\ProgressEngine;

if (!Auth::check()) {
    header('Location: ../index.php?page=login');
    exit;
}

$santriId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($santriId <= 0) {
    die('ID Santri tidak valid');
}

$db = Database::getConnection();
$engine = new ProgressEngine($db);
$report = $engine->getDetailedJuzSurahReport($santriId);

if (empty($report) || empty($report['santri'])) {
    die('Santri tidak ditemukan');
}

$santri = $report['santri'];
$safeNama = preg_replace('/[^A-Za-z0-9_\-]/', '_', $santri['nama']);
$filename = "Laporan_Hafalan_{$safeNama}_{$santri['nis']}.csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// UTF-8 BOM untuk Microsoft Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Header Biodata Santri
fputcsv($output, ['LAPORAN CAPAIAN TAHFIDZ AL-QUR\'AN PER JUZ & SURAH']);
fputcsv($output, ['Nama Santri', $santri['nama']]);
fputcsv($output, ['NIS', "'" . $santri['nis']]);
fputcsv($output, ['Halaqah', $santri['halaqah_nama'] ?? 'Halaqah Umum']);
fputcsv($output, ['Pembimbing', $santri['halaqah_pembimbing'] ?? 'Musyrif']);
fputcsv($output, ['Total Capaian', (float)$santri['total_juz_hafal'] . ' Juz (' . round((float)$santri['total_halaman_hafal']) . ' Halaman)']);
fputcsv($output, ['Target Santri', $santri['target_juz'] . ' Juz']);
fputcsv($output, ['Surah Hafal Tuntas', $report['total_tuntas_surah'] . ' Surah']);
fputcsv($output, ['Surah Sedang Proses', $report['total_proses_surah'] . ' Surah']);
fputcsv($output, ['Tanggal Unduh', date('d/m/Y H:i:s')]);
fputcsv($output, []); // Baris Kosong

// Header Tabel Rincian
fputcsv($output, [
    'No',
    'Juz',
    'Nama Juz',
    'Status Juz',
    'No Surah',
    'Nama Surah',
    'Nama Arab',
    'Rentang Ayat di Juz',
    'Total Ayat di Juz',
    'Status Hafalan',
    'Rincian Ayat yang Sudah Dihafal',
    'Jumlah Ayat Dihafal',
    'Nilai Terakhir',
    'Tanggal Setoran Terakhir'
]);

$no = 1;
foreach ($report['juz_reports'] as $juzItem) {
    $juzNum = $juzItem['juz'];
    $juzName = $juzItem['nama'];
    $juzStatus = $juzItem['status'];

    foreach ($juzItem['surahs'] as $sItem) {
        fputcsv($output, [
            $no++,
            $juzNum,
            $juzName,
            $juzStatus,
            $sItem['surah_id'],
            $sItem['nama'],
            $sItem['arab'],
            "Ayat {$sItem['ayat_mulai_juz']} - {$sItem['ayat_selesai_juz']}",
            $sItem['total_ayat_in_juz'],
            $sItem['status_badge'],
            $sItem['rincian_ayat'],
            $sItem['ayat_hafal_count'],
            strtoupper($sItem['terakhir_penilaian']),
            $sItem['terakhir_tanggal']
        ]);
    }
}

fclose($output);
exit;

