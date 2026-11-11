<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/QuranData.php';
require_once dirname(__DIR__, 2) . '/app/Database.php';
require_once dirname(__DIR__, 2) . '/app/Auth.php';
require_once dirname(__DIR__, 2) . '/app/Models/Santri.php';

use App\Database;
use App\Auth;
use App\Models\Santri;

if (!Auth::check()) {
    header('Location: ../index.php?page=login');
    exit;
}

$db = Database::getConnection();
$santriModel = new Santri($db);
$santriList = $santriModel->getAll();

$filename = "data_santri_tahfidz_" . date('Ymd_His') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, [
    'No',
    'NIS',
    'Nama Santri',
    'Jenis Kelamin',
    'Halaqah',
    'Pembimbing',
    'Target (Juz)',
    'Total Hafalan (Juz)',
    'Total Hafalan (Halaman)',
    'Persentase Capaian (%)',
    'Posisi Terakhir Surah',
    'Posisi Terakhir Ayat',
    'Posisi Terakhir Halaman',
    'Posisi Terakhir Juz',
    'Arah Hafalan'
]);

$no = 1;
foreach ($santriList as $s) {
    fputcsv($output, [
        $no++,
        $s['nis'],
        $s['nama'],
        $s['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan',
        $s['halaqah_nama'] ?? 'Umum',
        $s['halaqah_pembimbing'] ?? '',
        $s['target_juz'],
        (float)$s['total_juz_hafal'],
        (float)$s['total_halaman_hafal'],
        $s['persen_target'] . '%',
        $s['surah_nama'] ?? '',
        $s['terakhir_ayat'],
        $s['terakhir_halaman'],
        $s['terakhir_juz'],
        $s['arah_hafalan']
    ]);
}

fclose($output);
exit;

