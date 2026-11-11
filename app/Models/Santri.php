<?php
declare(strict_types=1);

namespace App\Models;

use App\Database;
use App\QuranData;
use PDO;

class Santri
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function getAll(string $search = '', ?int $halaqahId = null, string $status = 'aktif'): array
    {
        $sql = "
            SELECT s.*, h.nama as halaqah_nama, h.pembimbing as halaqah_pembimbing
            FROM santri s
            LEFT JOIN halaqah h ON s.halaqah_id = h.id
            WHERE 1=1
        ";
        $params = [];

        if ($status === 'aktif') {
            $sql .= " AND s.status_aktif = 1";
        } elseif ($status === 'nonaktif') {
            $sql .= " AND s.status_aktif = 0";
        }

        if ($halaqahId) {
            $sql .= " AND s.halaqah_id = ?";
            $params[] = $halaqahId;
        }

        if (!empty($search)) {
            $sql .= " AND (s.nama LIKE ? OR s.nis LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY s.nama ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $santriList = $stmt->fetchAll();

        foreach ($santriList as &$s) {
            $surah = QuranData::getSurah((int)$s['terakhir_surah_id']);
            $s['surah_nama'] = $surah['nama'] ?? 'Surah ' . $s['terakhir_surah_id'];
            $targetJuz = (int)($s['target_juz'] ?: 30);
            $s['persen_target'] = min(100.0, round(((float)$s['total_juz_hafal'] / max(1, $targetJuz)) * 100, 1));
        }

        return $santriList;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, h.nama as halaqah_nama, h.pembimbing as halaqah_pembimbing
            FROM santri s
            LEFT JOIN halaqah h ON s.halaqah_id = h.id
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        $s = $stmt->fetch();
        if (!$s) {
            return null;
        }

        $surah = QuranData::getSurah((int)$s['terakhir_surah_id']);
        $s['surah_nama'] = $surah['nama'] ?? 'Surah ' . $s['terakhir_surah_id'];
        $targetJuz = (int)($s['target_juz'] ?: 30);
        $s['persen_target'] = min(100.0, round(((float)$s['total_juz_hafal'] / max(1, $targetJuz)) * 100, 1));

        return $s;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO santri (
                nis, nama, jenis_kelamin, halaqah_id, target_juz, arah_hafalan,
                terakhir_surah_id, terakhir_ayat, terakhir_halaman, terakhir_juz,
                total_halaman_hafal, total_juz_hafal, status_aktif
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ");

        $halMulai = QuranData::estimatePageBySurahAyah((int)$data['terakhir_surah_id'], (int)$data['terakhir_ayat']);
        $juz = QuranData::getJuzByPage($halMulai);
        $totalHal = (float)($data['total_halaman_hafal'] ?? 0);
        $totalJuz = (float)($data['total_juz_hafal'] ?? round($totalHal / 20.133, 2));

        $stmt->execute([
            $data['nis'],
            $data['nama'],
            $data['jenis_kelamin'] ?? 'L',
            $data['halaqah_id'] ?? null,
            (int)($data['target_juz'] ?? 30),
            $data['arah_hafalan'] ?? 'juz30_mundur',
            (int)($data['terakhir_surah_id'] ?? 78),
            (int)($data['terakhir_ayat'] ?? 0),
            $halMulai,
            $juz,
            $totalHal,
            $totalJuz
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE santri SET
                nis = ?,
                nama = ?,
                jenis_kelamin = ?,
                halaqah_id = ?,
                target_juz = ?,
                arah_hafalan = ?,
                status_aktif = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['nis'],
            $data['nama'],
            $data['jenis_kelamin'],
            $data['halaqah_id'] ?: null,
            (int)$data['target_juz'],
            $data['arah_hafalan'],
            (int)($data['status_aktif'] ?? 1),
            $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM santri WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

