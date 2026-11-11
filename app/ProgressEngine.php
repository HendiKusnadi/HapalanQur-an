<?php
declare(strict_types=1);

namespace App;

use PDO;

class ProgressEngine
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * Proses pencatatan setoran dengan logika otomatis menambah / mengurang
     *
     * @param array{
     *   santri_id: int,
     *   jenis_setoran: string,
     *   surah_mulai_id: int,
     *   ayat_mulai: int,
     *   surah_selesai_id: int,
     *   ayat_selesai: int,
     *   halaman_mulai: int,
     *   halaman_selesai: int,
     *   juz: int,
     *   penilaian: string,
     *   status_kelulusan: string,
     *   mode_otomatis: string, // 'tambah', 'kurang', 'tetap'
     *   catatan?: string,
     *   musyrif_nama?: string,
     *   tanggal_setoran?: string
     * } $data
     * @return array{success: bool, setoran_id: int, message: string, next_target?: array}
     */
    public function recordSetoran(array $data): array
    {
        $santriId = (int) $data['santri_id'];
        $santri = $this->getSantri($santriId);
        if (!$santri) {
            return ['success' => false, 'setoran_id' => 0, 'message' => 'Santri tidak ditemukan!'];
        }

        $jenis = $data['jenis_setoran'];
        $lulus = $data['status_kelulusan'] === 'lulus';
        $penilaian = $data['penilaian'];
        $halMulai = (int) $data['halaman_mulai'];
        $halSelesai = (int) $data['halaman_selesai'];
        $jumlahHal = QuranData::calculatePageCount($halMulai, $halSelesai);
        $juz = (int) ($data['juz'] ?: QuranData::getJuzByPage($halMulai));

        $tanggal = $data['tanggal_setoran'] ?? date('Y-m-d');
        $musyrif = $data['musyrif_nama'] ?? 'Musyrif Halaqah';
        $catatan = $data['catatan'] ?? '';
        $modeOtomatis = $data['mode_otomatis'] ?? ($lulus && $jenis === 'hafalan_baru' ? 'tambah' : 'tetap');

        // Mulai transaksi database
        $this->db->beginTransaction();

        try {
            // 1. Simpan riwayat setoran
            $stmt = $this->db->prepare("
                INSERT INTO setoran (
                    santri_id, jenis_setoran, surah_mulai_id, ayat_mulai,
                    surah_selesai_id, ayat_selesai, halaman_mulai, halaman_selesai,
                    juz, jumlah_halaman, penilaian, status_kelulusan,
                    perubahan_otomatis, catatan, musyrif_nama, tanggal_setoran
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $santriId,
                $jenis,
                (int) $data['surah_mulai_id'],
                (int) $data['ayat_mulai'],
                (int) $data['surah_selesai_id'],
                (int) $data['ayat_selesai'],
                $halMulai,
                $halSelesai,
                $juz,
                $jumlahHal,
                $penilaian,
                $data['status_kelulusan'],
                $modeOtomatis,
                $catatan,
                $musyrif,
                $tanggal
            ]);

            $setoranId = (int) $this->db->lastInsertId();

            // 2. Eksekusi Logika Otomatis Menambah / Mengurang
            $surahSelesai = QuranData::getSurah((int) $data['surah_selesai_id']);
            $namaSurahSelesai = $surahSelesai['nama'] ?? 'Surah';

            if ($modeOtomatis === 'tambah' && $lulus) {
                // TAMBAH OTOMATIS:
                // - Tingkatkan total hafalan santri
                // - Majukan pointer surah, ayat, halaman, dan juz terakhir
                $newTotalHal = max(0.0, ((float) $santri['total_halaman_hafal']) + $jumlahHal);
                // Hitung estimasi juz (total halaman dibagi 20.13 halaman per juz)
                $newTotalJuz = round($newTotalHal / 20.133, 2);

                $updateStmt = $this->db->prepare("
                    UPDATE santri SET
                        total_halaman_hafal = ?,
                        total_juz_hafal = ?,
                        terakhir_surah_id = ?,
                        terakhir_ayat = ?,
                        terakhir_halaman = ?,
                        terakhir_juz = ?,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");

                $updateStmt->execute([
                    $newTotalHal,
                    $newTotalJuz,
                    (int) $data['surah_selesai_id'],
                    (int) $data['ayat_selesai'],
                    $halSelesai,
                    $juz,
                    $santriId
                ]);

                // Masukkan hafalan baru yang lulus ke antrean "Murojaah Baru" (Sabqi)
                if ($jenis === 'hafalan_baru') {
                    $judul = "QS. {$namaSurahSelesai}: {$data['ayat_mulai']}-{$data['ayat_selesai']} (Hal. {$halMulai}-{$halSelesai})";
                    $this->pushToMurojaahAntrean($santriId, 'baru', $judul, [
                        'surah_mulai_id' => (int) $data['surah_mulai_id'],
                        'ayat_mulai' => (int) $data['ayat_mulai'],
                        'surah_selesai_id' => (int) $data['surah_selesai_id'],
                        'ayat_selesai' => (int) $data['ayat_selesai'],
                        'halaman_mulai' => $halMulai,
                        'halaman_selesai' => $halSelesai,
                        'juz' => $juz,
                    ]);
                }
            } elseif ($modeOtomatis === 'kurang') {
                // KURANG OTOMATIS:
                // - Jika santri dinyatakan mengulang/mundur target, kurangi total hafalan
                //   atau mundurkan posisi terakhir sebesar jumlah halaman/target tersebut
                $newTotalHal = max(0.0, ((float) $santri['total_halaman_hafal']) - $jumlahHal);
                $newTotalJuz = round($newTotalHal / 20.133, 2);

                // Mundurkan halaman aktif
                $prevHal = max(1, $halMulai - 1);
                $prevJuz = QuranData::getJuzByPage($prevHal);

                $updateStmt = $this->db->prepare("
                    UPDATE santri SET
                        total_halaman_hafal = ?,
                        total_juz_hafal = ?,
                        terakhir_halaman = ?,
                        terakhir_juz = ?,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");

                $updateStmt->execute([
                    $newTotalHal,
                    $newTotalJuz,
                    $prevHal,
                    $prevJuz,
                    $santriId
                ]);
            }

            // Jika ini adalah setoran murojaah, perbarui tanggal murojaah terakhir di antrean
            if ($jenis === 'murojaah_baru' || $jenis === 'murojaah_sebelumnya') {
                $this->updateMurojaahStatus($santriId, $halMulai, $halSelesai, $tanggal);
            }

            $this->db->commit();

            // Hitung saran target berikutnya untuk mempermudah ustadz
            $nextTarget = QuranData::calculateNextTarget(
                (int) $data['surah_selesai_id'],
                (int) $data['ayat_selesai'],
                $santri['arah_hafalan'] ?? 'juz30_mundur'
            );

            $msg = match ($modeOtomatis) {
                'tambah' => "Setoran berhasil disimpan! Hafalan santri OTOMATIS BERTAMBAH {$jumlahHal} halaman.",
                'kurang' => "Setoran berhasil disimpan! Posisi hafalan santri OTOMATIS DIKURANGI/DIMUNDURKAN {$jumlahHal} halaman.",
                default => "Setoran berhasil disimpan (Posisi hafalan santri tetap)."
            };

            return [
                'success' => true,
                'setoran_id' => $setoranId,
                'message' => $msg,
                'next_target' => $nextTarget,
            ];
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return [
                'success' => false,
                'setoran_id' => 0,
                'message' => 'Gagal memproses setoran: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Laporan Lengkap Rincian Per Juz dan Per Surah untuk Santri
     * Menampilkan status: Hapal Tuntas atau Belum Tuntas (Ayat berapa s/d berapa)
     *
     * @param int $santriId
     * @return array{
     *   santri: array,
     *   total_tuntas_surah: int,
     *   total_proses_surah: int,
     *   total_belum_surah: int,
     *   juz_reports: array<int, array>
     * }
     */
    public function getDetailedJuzSurahReport(int $santriId): array
    {
        $santri = $this->getSantri($santriId);
        if (!$santri) {
            return [];
        }

        $arah = $santri['arah_hafalan'] ?? 'juz30_mundur';
        $totalJuzHafal = (float)$santri['total_juz_hafal'];
        $terakhirSurah = (int)$santri['terakhir_surah_id'];
        $terakhirAyat = (int)$santri['terakhir_ayat'];
        $terakhirJuz = (int)$santri['terakhir_juz'];

        // Ambil semua setoran lulus santri ini
        $stmt = $this->db->prepare("
            SELECT * FROM setoran 
            WHERE santri_id = ? AND status_kelulusan = 'lulus'
            ORDER BY tanggal_setoran ASC, id ASC
        ");
        $stmt->execute([$santriId]);
        $setoranList = $stmt->fetchAll();

        // Peta capaian per surah dari rekaman setoran
        // $surahProgress[surah_id] = ['max_ayat' => int, 'min_ayat' => int, 'last_date' => string, 'last_grade' => string]
        $surahProgress = [];
        foreach ($setoranList as $st) {
            $sMul = (int)$st['surah_mulai_id'];
            $sSel = (int)$st['surah_selesai_id'];

            for ($s = $sMul; $s <= $sSel; $s++) {
                if (!isset($surahProgress[$s])) {
                    $surahProgress[$s] = [
                        'min_ayat' => 999,
                        'max_ayat' => 0,
                        'last_date' => $st['tanggal_setoran'],
                        'last_grade' => $st['penilaian'],
                        'catatan' => $st['catatan'],
                        'musyrif' => $st['musyrif_nama']
                    ];
                }

                $aMul = ($s === $sMul) ? (int)$st['ayat_mulai'] : 1;
                $sInfo = QuranData::getSurah($s);
                $aSel = ($s === $sSel) ? (int)$st['ayat_selesai'] : (int)($sInfo['ayat'] ?? 10);

                $surahProgress[$s]['min_ayat'] = min($surahProgress[$s]['min_ayat'], $aMul);
                $surahProgress[$s]['max_ayat'] = max($surahProgress[$s]['max_ayat'], $aSel);
                $surahProgress[$s]['last_date'] = $st['tanggal_setoran'];
                $surahProgress[$s]['last_grade'] = $st['penilaian'];
            }
        }

        // Urutan Juz: Jika juz30_mundur, tampilkan Juz 30 dulu sampai 1
        $juzOrder = [];
        if ($arah === 'juz30_mundur') {
            for ($j = 30; $j >= 1; $j--) $juzOrder[] = $j;
        } else {
            for ($j = 1; $j <= 30; $j++) $juzOrder[] = $j;
        }

        $juzReports = [];
        $totalTuntasSurah = 0;
        $totalProsesSurah = 0;
        $totalBelumSurah = 0;

        foreach ($juzOrder as $jNum) {
            $juzInfo = QuranData::getJuz($jNum);
            $surahsInJuz = QuranData::getSurahsInJuz($jNum);

            // Apakah Juz ini secara keseluruhan sudah tuntas penuh?
            $jumlahJuzPenuh = (int)floor($totalJuzHafal);
            $isJuzFullyTuntas = false;
            if ($arah === 'juz30_mundur') {
                if ($jNum > (30 - $jumlahJuzPenuh)) {
                    $isJuzFullyTuntas = true;
                }
            } else {
                if ($jNum <= $jumlahJuzPenuh) {
                    $isJuzFullyTuntas = true;
                }
            }

            $surahListReport = [];
            $surahTuntasInJuz = 0;
            $totalAyatInJuz = 0;
            $totalAyatHafalInJuz = 0;

            foreach ($surahsInJuz as $sItem) {
                $sId = $sItem['surah_id'];
                $totalAyatSurah = $sItem['total_ayat_surah'];
                $ayatMulaiJuz = $sItem['ayat_mulai_juz'];
                $ayatSelesaiJuz = $sItem['ayat_selesai_juz'];
                $totalAyatDiJuzIni = $sItem['total_ayat_in_juz'];
                $totalAyatInJuz += $totalAyatDiJuzIni;

                // Tentukan status hafalan surah ini
                $isTuntas = false;
                $isProses = false;
                $rincianAyat = 'Belum Dihafal';
                $ayatHafalCount = 0;
                $lastDate = '-';
                $lastGrade = '-';

                if ($isJuzFullyTuntas) {
                    // Jika satu juz sudah tercatat tuntas di profil
                    $isTuntas = true;
                    $rincianAyat = "Ayat {$ayatMulaiJuz} s/d {$ayatSelesaiJuz} (Tuntas 100%)";
                    $ayatHafalCount = $totalAyatDiJuzIni;
                    $lastGrade = 'Mumtaz';
                } elseif (isset($surahProgress[$sId])) {
                    $sp = $surahProgress[$sId];
                    $maxA = $sp['max_ayat'];
                    $minA = $sp['min_ayat'] == 999 ? 1 : $sp['min_ayat'];
                    $lastDate = $sp['last_date'];
                    $lastGrade = $sp['last_grade'];

                    if ($maxA >= $ayatSelesaiJuz && $minA <= $ayatMulaiJuz) {
                        $isTuntas = true;
                        $rincianAyat = "Ayat {$ayatMulaiJuz} s/d {$ayatSelesaiJuz} (Tuntas 100%)";
                        $ayatHafalCount = $totalAyatDiJuzIni;
                    } else {
                        $isProses = true;
                        $minRange = max($ayatMulaiJuz, $minA);
                        $maxRange = min($ayatSelesaiJuz, $maxA);
                        $rincianAyat = "Hafal Ayat {$minRange} s/d {$maxRange} (Belum Tuntas - Sisa " . ($ayatSelesaiJuz - $maxRange) . " ayat)";
                        $ayatHafalCount = max(0, ($maxRange - $minRange) + 1);
                    }
                } elseif ($sId === $terakhirSurah && $terakhirAyat > 0) {
                    // Posisi aktif santri saat ini
                    $isProses = true;
                    $rincianAyat = "Hafal Ayat 1 s/d {$terakhirAyat} (Belum Tuntas - Sisa " . ($totalAyatSurah - $terakhirAyat) . " ayat)";
                    $ayatHafalCount = min($totalAyatDiJuzIni, $terakhirAyat);
                } else {
                    $rincianAyat = "Belum Dihafal (0 dari {$totalAyatDiJuzIni} Ayat)";
                }

                if ($isTuntas) {
                    $statusBadge = 'HAPAL TUNTAS';
                    $statusColor = 'emerald';
                    $surahTuntasInJuz++;
                    $totalTuntasSurah++;
                } elseif ($isProses) {
                    $statusBadge = 'BELUM TUNTAS (SEDANG PROSES)';
                    $statusColor = 'amber';
                    $totalProsesSurah++;
                } else {
                    $statusBadge = 'BELUM DIHAFAL';
                    $statusColor = 'slate';
                    $totalBelumSurah++;
                }

                $totalAyatHafalInJuz += $ayatHafalCount;

                $surahListReport[] = [
                    'surah_id' => $sId,
                    'nama' => $sItem['nama'],
                    'arab' => $sItem['arab'],
                    'arti' => $sItem['arti'],
                    'total_ayat_surah' => $totalAyatSurah,
                    'ayat_mulai_juz' => $ayatMulaiJuz,
                    'ayat_selesai_juz' => $ayatSelesaiJuz,
                    'total_ayat_in_juz' => $totalAyatDiJuzIni,
                    'ayat_hafal_count' => $ayatHafalCount,
                    'is_tuntas' => $isTuntas,
                    'is_proses' => $isProses,
                    'status_badge' => $statusBadge,
                    'status_color' => $statusColor,
                    'rincian_ayat' => $rincianAyat,
                    'terakhir_tanggal' => $lastDate,
                    'terakhir_penilaian' => $lastGrade,
                ];
            }

            $totalSurahInJuz = count($surahsInJuz);
            $persenJuz = $totalAyatInJuz > 0 ? round(($totalAyatHafalInJuz / $totalAyatInJuz) * 100, 1) : 0;
            $juzStatus = ($surahTuntasInJuz === $totalSurahInJuz && $surahTuntasInJuz > 0) 
                ? 'HAPAL TUNTAS' 
                : ($totalAyatHafalInJuz > 0 ? 'SEDANG PROSES' : 'BELUM DIHAFAL');

            $juzReports[] = [
                'juz' => $jNum,
                'nama' => $juzInfo['nama'],
                'halaman' => "Hal. {$juzInfo['hal_mulai']} - {$juzInfo['hal_selesai']}",
                'total_surah' => $totalSurahInJuz,
                'surah_tuntas' => $surahTuntasInJuz,
                'total_ayat' => $totalAyatInJuz,
                'total_ayat_hafal' => $totalAyatHafalInJuz,
                'persentase' => $persenJuz,
                'status' => $juzStatus,
                'surahs' => $surahListReport
            ];
        }

        return [
            'santri' => $santri,
            'total_tuntas_surah' => $totalTuntasSurah,
            'total_proses_surah' => $totalProsesSurah,
            'total_belum_surah' => $totalBelumSurah,
            'juz_reports' => $juzReports
        ];
    }

    /**
     * Penyesuaian manual cepat (Tambah / Kurang Otomatis 1 Halaman/Ayat)
     */
    public function adjustSantriProgress(int $santriId, string $action, float $pages = 1.0): array
    {
        $santri = $this->getSantri($santriId);
        if (!$santri) {
            return ['success' => false, 'message' => 'Santri tidak ditemukan'];
        }

        $currentHal = (float) $santri['total_halaman_hafal'];
        $lastHalNum = (int) $santri['terakhir_halaman'];

        if ($action === 'tambah') {
            $newHal = $currentHal + $pages;
            $newLastHal = min(604, $lastHalNum + (int) ceil($pages));
            $verb = "ditambah";
        } elseif ($action === 'kurang') {
            $newHal = max(0.0, $currentHal - $pages);
            $newLastHal = max(1, $lastHalNum - (int) ceil($pages));
            $verb = "dikurangi";
        } else {
            return ['success' => false, 'message' => 'Aksi penyesuaian tidak valid'];
        }

        $newJuzTotal = round($newHal / 20.133, 2);
        $newJuzNum = QuranData::getJuzByPage($newLastHal);

        $stmt = $this->db->prepare("
            UPDATE santri SET
                total_halaman_hafal = ?,
                total_juz_hafal = ?,
                terakhir_halaman = ?,
                terakhir_juz = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        $stmt->execute([$newHal, $newJuzTotal, $newLastHal, $newJuzNum, $santriId]);

        return [
            'success' => true,
            'message' => "Progres hafalan berhasil {$verb} sebesar {$pages} halaman.",
            'total_halaman' => $newHal,
            'total_juz' => $newJuzTotal,
            'terakhir_halaman' => $newLastHal,
            'terakhir_juz' => $newJuzNum
        ];
    }

    /**
     * Hapus setoran dan batalkan dampaknya (Rollback / Kurang Otomatis jika setoran bertambah)
     */
    public function deleteSetoran(int $setoranId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM setoran WHERE id = ?");
        $stmt->execute([$setoranId]);
        $setoran = $stmt->fetch();

        if (!$setoran) {
            return ['success' => false, 'message' => 'Data setoran tidak ditemukan.'];
        }

        $this->db->beginTransaction();
        try {
            $santriId = (int) $setoran['santri_id'];
            $santri = $this->getSantri($santriId);

            // Jika setoran ini dahulu 'tambah' otomatis, maka hapusnya otomatis 'mengurang' kembali
            if ($setoran['perubahan_otomatis'] === 'tambah' && $santri) {
                $pages = (float) $setoran['jumlah_halaman'];
                $newHal = max(0.0, ((float) $santri['total_halaman_hafal']) - $pages);
                $newJuz = round($newHal / 20.133, 2);

                $update = $this->db->prepare("
                    UPDATE santri SET
                        total_halaman_hafal = ?,
                        total_juz_hafal = ?,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                $update->execute([$newHal, $newJuz, $santriId]);
            }

            // Hapus rekaman
            $del = $this->db->prepare("DELETE FROM setoran WHERE id = ?");
            $del->execute([$setoranId]);

            $this->db->commit();
            return ['success' => true, 'message' => 'Setoran berhasil dihapus dan akumulasi hafalan disesuaikan otomatis.'];
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Gagal menghapus: ' . $e->getMessage()];
        }
    }

    /**
     * Dapatkan antrean Murojaah Hafalan Baru (Sabqi)
     * Menampilkan bagian yang baru saja dihafal santri dan wajib dimurojaah
     */
    public function getMurojaahBaruQueue(int $santriId, int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM murojaah_antrean
            WHERE santri_id = ? AND jenis = 'baru'
            ORDER BY id DESC
            LIMIT ?
        ");
        $stmt->execute([$santriId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Dapatkan rekomendasi Murojaah Hafalan Sebelumnya (Manzil)
     * Memprioritaskan juz / bagian yang paling lama belum dimurojaah
     */
    public function getMurojaahSebelumnyaRecommendations(int $santriId): array
    {
        $santri = $this->getSantri($santriId);
        if (!$santri) {
            return [];
        }

        $totalJuz = (float) $santri['total_juz_hafal'];
        $arah = $santri['arah_hafalan'] ?? 'juz30_mundur';

        // Tentukan daftar juz yang sudah dihafal
        $juzHafal = [];
        $jumlahJuzPenuh = (int) floor($totalJuz);

        if ($arah === 'juz30_mundur') {
            for ($i = 0; $i < max(1, $jumlahJuzPenuh); $i++) {
                $juzHafal[] = 30 - $i;
            }
        } else {
            for ($i = 1; $i <= max(1, $jumlahJuzPenuh); $i++) {
                $juzHafal[] = $i;
            }
        }

        // Cek kapan terakhir kali tiap juz dimurojaah dalam setoran
        $recommendations = [];
        foreach ($juzHafal as $jNum) {
            $juzInfo = QuranData::getJuz($jNum);
            if (!$juzInfo) {
                continue;
            }

            $stmt = $this->db->prepare("
                SELECT tanggal_setoran, penilaian, status_kelulusan
                FROM setoran
                WHERE santri_id = ? AND juz = ? AND jenis_setoran = 'murojaah_sebelumnya'
                ORDER BY tanggal_setoran DESC, id DESC
                LIMIT 1
            ");
            $stmt->execute([$santriId, $jNum]);
            $lastMurojaah = $stmt->fetch();

            $tgl = $lastMurojaah['tanggal_setoran'] ?? null;
            $hariLalu = null;
            $urgensi = 'Normal';

            if ($tgl) {
                $diff = (new \DateTime())->diff(new \DateTime($tgl));
                $hariLalu = (int) $diff->days;
                if ($hariLalu > 7) {
                    $urgensi = 'Mendesak (Lebih dari 7 hari)';
                } elseif ($hariLalu > 3) {
                    $urgensi = 'Perlu Murojaah';
                }
            } else {
                $hariLalu = 999;
                $urgensi = 'Belum Pernah Murojaah';
            }

            $recommendations[] = [
                'juz' => $jNum,
                'nama' => $juzInfo['nama'],
                'halaman' => "Hal. {$juzInfo['hal_mulai']} - {$juzInfo['hal_selesai']}",
                'terakhir_murojaah' => $tgl,
                'hari_lalu' => $hariLalu,
                'urgensi' => $urgensi,
                'penilaian_terakhir' => $lastMurojaah['penilaian'] ?? '-'
            ];
        }

        // Urutkan berdasarkan yang paling lama belum dimurojaah (hari_lalu terbesar)
        usort($recommendations, fn($a, $b) => $b['hari_lalu'] <=> $a['hari_lalu']);

        return $recommendations;
    }

    /**
     * Hitung ringkasan statistik santri
     */
    public function getSantriStats(int $santriId): array
    {
        $santri = $this->getSantri($santriId);
        if (!$santri) {
            return [];
        }

        // Total setoran hafalan baru
        $stmtBaru = $this->db->prepare("
            SELECT COUNT(*) as total, SUM(jumlah_halaman) as total_hal
            FROM setoran
            WHERE santri_id = ? AND jenis_setoran = 'hafalan_baru' AND status_kelulusan = 'lulus'
        ");
        $stmtBaru->execute([$santriId]);
        $statsBaru = $stmtBaru->fetch();

        // Total murojaah baru
        $stmtMurBaru = $this->db->prepare("
            SELECT COUNT(*) as total, SUM(jumlah_halaman) as total_hal
            FROM setoran
            WHERE santri_id = ? AND jenis_setoran = 'murojaah_baru'
        ");
        $stmtMurBaru->execute([$santriId]);
        $statsMurBaru = $stmtMurBaru->fetch();

        // Total murojaah sebelumnya
        $stmtMurLama = $this->db->prepare("
            SELECT COUNT(*) as total, SUM(jumlah_halaman) as total_hal
            FROM setoran
            WHERE santri_id = ? AND jenis_setoran = 'murojaah_sebelumnya'
        ");
        $stmtMurLama->execute([$santriId]);
        $statsMurLama = $stmtMurLama->fetch();

        // Persentase capaian target (Target Juz santri)
        $targetJuz = (int) ($santri['target_juz'] ?: 30);
        $totalJuzHafal = (float) $santri['total_juz_hafal'];
        $persenTarget = min(100.0, round(($totalJuzHafal / max(1, $targetJuz)) * 100, 1));

        return [
            'santri' => $santri,
            'setoran_baru_count' => (int) ($statsBaru['total'] ?? 0),
            'setoran_baru_hal' => (float) ($statsBaru['total_hal'] ?? 0),
            'murojaah_baru_count' => (int) ($statsMurBaru['total'] ?? 0),
            'murojaah_baru_hal' => (float) ($statsMurBaru['total_hal'] ?? 0),
            'murojaah_lama_count' => (int) ($statsMurLama['total'] ?? 0),
            'murojaah_lama_hal' => (float) ($statsMurLama['total_hal'] ?? 0),
            'persen_target' => $persenTarget,
        ];
    }

    private function pushToMurojaahAntrean(int $santriId, string $jenis, string $judul, array $meta): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO murojaah_antrean (
                santri_id, jenis, judul, surah_mulai_id, ayat_mulai,
                surah_selesai_id, ayat_selesai, halaman_mulai, halaman_selesai,
                juz, status, tanggal_terakhir_setoran
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'menunggu', CURRENT_DATE)
        ");

        $stmt->execute([
            $santriId,
            $jenis,
            $judul,
            $meta['surah_mulai_id'] ?? null,
            $meta['ayat_mulai'] ?? null,
            $meta['surah_selesai_id'] ?? null,
            $meta['ayat_selesai'] ?? null,
            $meta['halaman_mulai'] ?? null,
            $meta['halaman_selesai'] ?? null,
            $meta['juz'] ?? null,
        ]);

        // Batasi antrean murojaah baru hanya menyimpan N item terbaru, sisanya diarsip ke 'selesai'
        $threshold = 5;
        $this->db->exec("
            UPDATE murojaah_antrean
            SET status = 'selesai'
            WHERE santri_id = {$santriId} AND jenis = 'baru' AND id NOT IN (
                SELECT id FROM murojaah_antrean
                WHERE santri_id = {$santriId} AND jenis = 'baru'
                ORDER BY id DESC LIMIT {$threshold}
            )
        ");
    }

    private function updateMurojaahStatus(int $santriId, int $halMulai, int $halSelesai, string $tanggal): void
    {
        $stmt = $this->db->prepare("
            UPDATE murojaah_antrean
            SET status = 'selesai',
                frekuensi = frekuensi + 1,
                tanggal_terakhir_setoran = ?
            WHERE santri_id = ?
              AND (
                  (halaman_mulai >= ? AND halaman_mulai <= ?)
                  OR (halaman_selesai >= ? AND halaman_selesai <= ?)
              )
        ");
        $stmt->execute([$tanggal, $santriId, $halMulai, $halSelesai, $halMulai, $halSelesai]);
    }

    public function getSantri(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, h.nama as halaqah_nama, h.pembimbing as halaqah_pembimbing
            FROM santri s
            LEFT JOIN halaqah h ON s.halaqah_id = h.id
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}

