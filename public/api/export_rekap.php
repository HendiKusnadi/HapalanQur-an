<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/QuranData.php';
require_once dirname(__DIR__, 2) . '/app/Database.php';
require_once dirname(__DIR__, 2) . '/app/Auth.php';

use App\Database;
use App\Auth;
use App\QuranData;

if (!Auth::check()) {
    header('Location: ../index.php?page=login');
    exit;
}

$db = Database::getConnection();

$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$filterJenis = $_GET['jenis'] ?? '';
$filterHalaqah = isset($_GET['halaqah_id']) && $_GET['halaqah_id'] !== '' ? (int)$_GET['halaqah_id'] : null;

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
$rows = $stmt->fetchAll();

// Output CSV headers
$filename = "rekap_setoran_" . date('Ymd_His') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Microsoft Excel compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Header baris CSV
fputcsv($output, [
    'No',
    'Tanggal Setoran',
    'NIS',
    'Nama Santri',
    'Halaqah',
    'Kategori Setoran',
    'Surah Mulai',
    'Ayat Mulai',
    'Surah Selesai',
    'Ayat Selesai',
    'Juz',
    'Halaman Mulai',
    'Halaman Selesai',
    'Jumlah Halaman',
    'Penilaian',
    'Status Kelulusan',
    'Dampak Otomatis',
    'Catatan Tajwid',
    'Nama Musyrif'
]);

$no = 1;
foreach ($rows as $r) {
    $s1 = QuranData::getSurah((int)$r['surah_mulai_id']);
    $s2 = QuranData::getSurah((int)$r['surah_selesai_id']);

    $kategori = match($r['jenis_setoran']) {
        'hafalan_baru' => 'Hafalan Baru (Sabaq)',
        'murojaah_baru' => 'Murojaah Baru (Sabqi)',
        'murojaah_sebelumnya' => 'Murojaah Lama (Manzil)',
        default => $r['jenis_setoran']
    };

    fputcsv($output, [
        $no++,
        $r['tanggal_setoran'],
        $r['santri_nis'],
        $r['santri_nama'],
        $r['halaqah_nama'] ?? 'Halaqah Umum',
        $kategori,
        $s1['nama'] ?? 'Surah ' . $r['surah_mulai_id'],
        $r['ayat_mulai'],
        $s2['nama'] ?? 'Surah ' . $r['surah_selesai_id'],
        $r['ayat_selesai'],
        $r['juz'],
        $r['halaman_mulai'],
        $r['halaman_selesai'],
        $r['jumlah_halaman'],
        strtoupper($r['penilaian']),
        strtoupper($r['status_kelulusan']),
        $r['perubahan_otomatis'],
        $r['catatan'] ?? '',
        $r['musyrif_nama'] ?? ''
    ]);
}

fclose($output);
exit;

