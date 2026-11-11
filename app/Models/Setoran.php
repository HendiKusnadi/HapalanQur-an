<?php
declare(strict_types=1);

namespace App\Models;

use App\Database;
use App\QuranData;
use PDO;

class Setoran
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function getRecent(int $limit = 20, ?int $santriId = null, ?string $jenis = null, ?string $tanggal = null): array
    {
        $sql = "
            SELECT st.*, s.nama as santri_nama, s.nis as santri_nis, h.nama as halaqah_nama
            FROM setoran st
            JOIN santri s ON st.santri_id = s.id
            LEFT JOIN halaqah h ON s.halaqah_id = h.id
            WHERE 1=1
        ";
        $params = [];

        if ($santriId) {
            $sql .= " AND st.santri_id = ?";
            $params[] = $santriId;
        }

        if ($jenis) {
            $sql .= " AND st.jenis_setoran = ?";
            $params[] = $jenis;
        }

        if ($tanggal) {
            $sql .= " AND st.tanggal_setoran = ?";
            $params[] = $tanggal;
        }

        $sql .= " ORDER BY st.tanggal_setoran DESC, st.id DESC LIMIT ?";
        $params[] = $limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll();

        foreach ($records as &$r) {
            $sMulai = QuranData::getSurah((int)$r['surah_mulai_id']);
            $sSelesai = QuranData::getSurah((int)$r['surah_selesai_id']);

            $r['surah_mulai_nama'] = $sMulai['nama'] ?? 'Surah ' . $r['surah_mulai_id'];
            $r['surah_selesai_nama'] = $sSelesai['nama'] ?? 'Surah ' . $r['surah_selesai_id'];

            if ($r['surah_mulai_id'] === $r['surah_selesai_id']) {
                $r['cakupan_teks'] = "QS. {$r['surah_mulai_nama']}: {$r['ayat_mulai']} - {$r['ayat_selesai']}";
            } else {
                $r['cakupan_teks'] = "QS. {$r['surah_mulai_nama']}:{$r['ayat_mulai']} s/d QS. {$r['surah_selesai_nama']}:{$r['ayat_selesai']}";
            }
        }

        return $records;
    }

    public function getTodaySummary(): array
    {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_setoran,
                SUM(CASE WHEN jenis_setoran = 'hafalan_baru' THEN 1 ELSE 0 END) as total_baru,
                SUM(CASE WHEN jenis_setoran = 'murojaah_baru' THEN 1 ELSE 0 END) as total_murojaah_baru,
                SUM(CASE WHEN jenis_setoran = 'murojaah_sebelumnya' THEN 1 ELSE 0 END) as total_murojaah_lama,
                SUM(jumlah_halaman) as total_halaman,
                COUNT(DISTINCT santri_id) as total_santri_setor
            FROM setoran
            WHERE tanggal_setoran = ?
        ");
        $stmt->execute([$today]);
        $row = $stmt->fetch();

        return [
            'total_setoran' => (int)($row['total_setoran'] ?? 0),
            'total_baru' => (int)($row['total_baru'] ?? 0),
            'total_murojaah_baru' => (int)($row['total_murojaah_baru'] ?? 0),
            'total_murojaah_lama' => (int)($row['total_murojaah_lama'] ?? 0),
            'total_halaman' => (float)($row['total_halaman'] ?? 0.0),
            'total_santri_setor' => (int)($row['total_santri_setor'] ?? 0),
        ];
    }
}

