/**
 * SIM Tahfidz Mobile - Local Database Engine
 * Penyimpanan Mandiri di Memori Internal HP Android (localStorage / IndexedDB)
 */

const STORAGE_KEY = 'sim_tahfidz_offline_db';

const DEFAULT_DATABASE = {
    pengaturan: {
        nama_aplikasi: 'SIM Tahfidz Quran',
        nama_pesantren: 'Rumah Tahfidz & Santri Mandiri',
        default_musyrif: 'Ust. Pembimbing',
        kontak_wa: '0897 5775 046'
    },
    halaqah: [
        { id: 1, nama: 'Halaqah Abu Bakar Ash-Shiddiq', pembimbing: 'Ust. Ahmad Fauzan, Al-Hafizh' },
        { id: 2, nama: 'Halaqah Umar bin Khattab', pembimbing: 'Ust. Ridwan Kamil, Lc' },
        { id: 3, nama: 'Halaqah Utsman bin Affan', pembimbing: 'Ust. M. Zaky Al-Bantani' }
    ],
    santri: [
        {
            id: 1,
            nis: '2026001',
            nama: 'Muhammad Zaky Al-Farisi',
            jenis_kelamin: 'L',
            halaqah_id: 1,
            target_juz: 30,
            arah_hafalan: 'juz30_mundur',
            terakhir_surah_id: 78,
            terakhir_ayat: 5,
            terakhir_halaman: 582,
            terakhir_juz: 30,
            total_halaman_hafal: 25.0,
            total_juz_hafal: 1.24,
            created_at: '2026-11-10 10:00:00'
        },
        {
            id: 2,
            nis: '2026002',
            nama: 'Ahmad Raihan Pratama',
            jenis_kelamin: 'L',
            halaqah_id: 1,
            target_juz: 30,
            arah_hafalan: 'juz30_mundur',
            terakhir_surah_id: 83,
            terakhir_ayat: 10,
            terakhir_halaman: 587,
            terakhir_juz: 30,
            total_halaman_hafal: 14.0,
            total_juz_hafal: 0.70,
            created_at: '2026-11-10 10:00:00'
        },
        {
            id: 3,
            nis: '2026003',
            nama: 'Fathir Abdurrahman',
            jenis_kelamin: 'L',
            halaqah_id: 2,
            target_juz: 30,
            arah_hafalan: 'juz1_maju',
            terakhir_surah_id: 2,
            terakhir_ayat: 141,
            terakhir_halaman: 21,
            terakhir_juz: 1,
            total_halaman_hafal: 21.0,
            total_juz_hafal: 1.04,
            created_at: '2026-11-10 10:00:00'
        }
    ],
    setoran: [
        {
            id: 1,
            santri_id: 1,
            jenis_setoran: 'hafalan_baru',
            surah_mulai_id: 78,
            ayat_mulai: 1,
            surah_selesai_id: 78,
            ayat_selesai: 5,
            halaman_mulai: 582,
            halaman_selesai: 582,
            juz: 30,
            jumlah_halaman: 1.0,
            penilaian: 'mumtaz',
            status_kelulusan: 'lulus',
            perubahan_otomatis: 'tambah',
            catatan: 'Makhraj huruf ra dan dho sangat lancar',
            musyrif_nama: 'Ust. Pembimbing',
            tanggal_setoran: '2026-11-10',
            created_at: '2026-11-10 10:30:00'
        }
    ]
};

const MobileDB = {
    init: () => {
        if (!localStorage.getItem(STORAGE_KEY)) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(DEFAULT_DATABASE));
        }
    },

    getRaw: () => {
        MobileDB.init();
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY)) || DEFAULT_DATABASE;
        } catch (_) {
            return DEFAULT_DATABASE;
        }
    },

    saveRaw: (data) => {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    },

    // Santri CRUD
    getSantriList: () => {
        const db = MobileDB.getRaw();
        const halaqahMap = {};
        (db.halaqah || []).forEach(h => halaqahMap[h.id] = h.nama);

        return (db.santri || []).map(s => {
            const surah = QuranHelper.getSurah(s.terakhir_surah_id);
            const persen = Math.min(100, Math.round(((s.total_juz_hafal || 0) / (s.target_juz || 30)) * 100));
            return {
                ...s,
                halaqah_nama: halaqahMap[s.halaqah_id] || 'Umum',
                terakhir_surah_nama: surah ? surah.nama : `Surah ${s.terakhir_surah_id}`,
                terakhir_surah_arab: surah ? surah.arab : '',
                persen_target: persen
            };
        });
    },

    getSantri: (id) => {
        id = parseInt(id);
        const list = MobileDB.getSantriList();
        return list.find(s => s.id === id) || null;
    },

    addSantri: (data) => {
        const db = MobileDB.getRaw();
        const nextId = (db.santri.length > 0 ? Math.max(...db.santri.map(s => s.id)) : 0) + 1;
        const halAwal = QuranHelper.estimatePageBySurahAyah(data.terakhir_surah_id || 78, 1);
        const juzAwal = QuranHelper.getJuzByPage(halAwal);

        const newSantri = {
            id: nextId,
            nis: data.nis,
            nama: data.nama,
            jenis_kelamin: data.jenis_kelamin || 'L',
            halaqah_id: parseInt(data.halaqah_id) || 1,
            target_juz: parseInt(data.target_juz) || 30,
            arah_hafalan: data.arah_hafalan || 'juz30_mundur',
            terakhir_surah_id: parseInt(data.terakhir_surah_id) || 78,
            terakhir_ayat: 0,
            terakhir_halaman: halAwal,
            terakhir_juz: juzAwal,
            total_halaman_hafal: 0.0,
            total_juz_hafal: 0.0,
            created_at: new Date().toISOString().replace('T', ' ').substring(0, 19)
        };

        db.santri.push(newSantri);
        MobileDB.saveRaw(db);
        return newSantri;
    },

    deleteSantri: (id) => {
        id = parseInt(id);
        const db = MobileDB.getRaw();
        db.santri = db.santri.filter(s => s.id !== id);
        db.setoran = db.setoran.filter(s => s.santri_id !== id);
        MobileDB.saveRaw(db);
    },

    // Setoran Engine
    getSetoranList: (santriId = null) => {
        const db = MobileDB.getRaw();
        let list = db.setoran || [];
        if (santriId) {
            santriId = parseInt(santriId);
            list = list.filter(s => s.santri_id === santriId);
        }
        return list.sort((a, b) => b.id - a.id);
    },

    recordSetoran: (input) => {
        const db = MobileDB.getRaw();
        const santri = db.santri.find(s => s.id === parseInt(input.santri_id));
        if (!santri) return { success: false, message: 'Santri tidak ditemukan!' };

        const halMulai = parseInt(input.halaman_mulai) || QuranHelper.estimatePageBySurahAyah(input.surah_mulai_id, input.ayat_mulai);
        const halSelesai = parseInt(input.halaman_selesai) || QuranHelper.estimatePageBySurahAyah(input.surah_selesai_id, input.ayat_selesai);
        const jumlahHal = QuranHelper.calculatePageCount(halMulai, halSelesai);
        const juz = parseInt(input.juz) || QuranHelper.getJuzByPage(halMulai);
        const nextId = (db.setoran.length > 0 ? Math.max(...db.setoran.map(s => s.id)) : 0) + 1;

        const setoranItem = {
            id: nextId,
            santri_id: santri.id,
            jenis_setoran: input.jenis_setoran || 'hafalan_baru',
            surah_mulai_id: parseInt(input.surah_mulai_id),
            ayat_mulai: parseInt(input.ayat_mulai),
            surah_selesai_id: parseInt(input.surah_selesai_id),
            ayat_selesai: parseInt(input.ayat_selesai),
            halaman_mulai: halMulai,
            halaman_selesai: halSelesai,
            juz: juz,
            jumlah_halaman: jumlahHal,
            penilaian: input.penilaian || 'mumtaz',
            status_kelulusan: input.status_kelulusan || 'lulus',
            perubahan_otomatis: input.mode_otomatis || 'tambah',
            catatan: input.catatan || '',
            musyrif_nama: input.musyrif_nama || 'Ust. Pembimbing',
            tanggal_setoran: input.tanggal_setoran || new Date().toISOString().substring(0, 10),
            created_at: new Date().toISOString().replace('T', ' ').substring(0, 19)
        };

        db.setoran.push(setoranItem);

        // Eksekusi otomatisasi bertambah / berkurang
        const lulus = setoranItem.status_kelulusan === 'lulus';
        if (setoranItem.perubahan_otomatis === 'tambah' && lulus) {
            santri.total_halaman_hafal = Math.max(0.0, (parseFloat(santri.total_halaman_hafal) || 0) + jumlahHal);
            santri.total_juz_hafal = parseFloat((santri.total_halaman_hafal / 20.133).toFixed(2));
            santri.terakhir_surah_id = setoranItem.surah_selesai_id;
            santri.terakhir_ayat = setoranItem.ayat_selesai;
            santri.terakhir_halaman = halSelesai;
            santri.terakhir_juz = juz;
        } else if (setoranItem.perubahan_otomatis === 'kurang') {
            santri.total_halaman_hafal = Math.max(0.0, (parseFloat(santri.total_halaman_hafal) || 0) - jumlahHal);
            santri.total_juz_hafal = parseFloat((santri.total_halaman_hafal / 20.133).toFixed(2));
            santri.terakhir_halaman = Math.max(1, halMulai - 1);
            santri.terakhir_juz = QuranHelper.getJuzByPage(santri.terakhir_halaman);
        }

        MobileDB.saveRaw(db);

        const nextTarget = QuranHelper.calculateNextTarget(santri.terakhir_surah_id, santri.terakhir_ayat, santri.arah_hafalan);
        const msg = setoranItem.perubahan_otomatis === 'tambah'
            ? `Setoran tersimpan! Hafalan santri OTOMATIS BERTAMBAH ${jumlahHal} halaman.`
            : `Setoran berhasil disimpan di HP!`;

        return {
            success: true,
            setoran_id: nextId,
            message: msg,
            santri: MobileDB.getSantri(santri.id),
            next_target: nextTarget
        };
    },

    adjustProgress: (santriId, action, pages = 1.0) => {
        const db = MobileDB.getRaw();
        const santri = db.santri.find(s => s.id === parseInt(santriId));
        if (!santri) return { success: false, message: 'Santri tidak ditemukan!' };

        pages = parseFloat(pages) || 1.0;
        if (action === 'tambah') {
            santri.total_halaman_hafal = Math.max(0.0, (parseFloat(santri.total_halaman_hafal) || 0) + pages);
            santri.total_juz_hafal = parseFloat((santri.total_halaman_hafal / 20.133).toFixed(2));
            santri.terakhir_halaman = Math.min(604, (parseInt(santri.terakhir_halaman) || 1) + Math.ceil(pages));
        } else {
            santri.total_halaman_hafal = Math.max(0.0, (parseFloat(santri.total_halaman_hafal) || 0) - pages);
            santri.total_juz_hafal = parseFloat((santri.total_halaman_hafal / 20.133).toFixed(2));
            santri.terakhir_halaman = Math.max(1, (parseInt(santri.terakhir_halaman) || 1) - Math.ceil(pages));
        }
        santri.terakhir_juz = QuranHelper.getJuzByPage(santri.terakhir_halaman);

        MobileDB.saveRaw(db);
        return {
            success: true,
            message: `Progres santri berhasil di-${action === 'tambah' ? 'tambah' : 'kurang'} sebesar ${pages} halaman.`,
            santri: MobileDB.getSantri(santri.id)
        };
    },

    deleteSetoran: (setoranId) => {
        const db = MobileDB.getRaw();
        const item = db.setoran.find(s => s.id === parseInt(setoranId));
        if (!item) return { success: false, message: 'Data setoran tidak ditemukan!' };

        // Auto rollback jika setoran menambah
        if (item.perubahan_otomatis === 'tambah' && item.status_kelulusan === 'lulus') {
            const santri = db.santri.find(s => s.id === item.santri_id);
            if (santri) {
                santri.total_halaman_hafal = Math.max(0.0, (parseFloat(santri.total_halaman_hafal) || 0) - item.jumlah_halaman);
                santri.total_juz_hafal = parseFloat((santri.total_halaman_hafal / 20.133).toFixed(2));
            }
        }

        db.setoran = db.setoran.filter(s => s.id !== parseInt(setoranId));
        MobileDB.saveRaw(db);
        return { success: true, message: 'Data setoran dihapus dan akumulasi hafalan disesuaikan.' };
    },

    // Antrean Murojaah Baru (Sabqi)
    getSabqiQueue: (santriId, limit = 5) => {
        const setoran = MobileDB.getSetoranList(santriId)
            .filter(s => s.jenis_setoran === 'hafalan_baru' && s.status_kelulusan === 'lulus')
            .slice(0, limit);

        return setoran.map(s => {
            const surah = QuranHelper.getSurah(s.surah_selesai_id);
            return {
                id: s.id,
                judul: `QS. ${surah ? surah.nama : 'Surah'}: ${s.ayat_mulai}-${s.ayat_selesai} (Hal. ${s.halaman_mulai}-${s.halaman_selesai})`,
                surah_mulai_id: s.surah_mulai_id,
                ayat_mulai: s.ayat_mulai,
                surah_selesai_id: s.surah_selesai_id,
                ayat_selesai: s.ayat_selesai,
                halaman_mulai: s.halaman_mulai,
                halaman_selesai: s.halaman_selesai,
                juz: s.juz
            };
        });
    },

    // Rekomendasi Manzil (Murojaah Lama)
    getManzilRecs: (santriId) => {
        const santri = MobileDB.getSantri(santriId);
        if (!santri || santri.total_juz_hafal < 1) return [];

        const tuntasCount = Math.floor(santri.total_juz_hafal);
        const recs = [];
        const isMundur = santri.arah_hafalan === 'juz30_mundur';

        for (let i = 0; i < tuntasCount; i++) {
            const juzNum = isMundur ? (30 - i) : (1 + i);
            const juzInfo = QuranHelper.getAllJuz()[juzNum];
            recs.push({
                juz: juzNum,
                nama: juzInfo ? juzInfo.nama_awal : `Juz ${juzNum}`,
                halaman: juzInfo ? `Hal. ${juzInfo.hal_mulai} - ${juzInfo.hal_selesai}` : ''
            });
        }
        return recs;
    },

    // Backup & Restore
    exportBackupJSON: () => {
        return JSON.stringify(MobileDB.getRaw(), null, 2);
    },

    importBackupJSON: (jsonStr) => {
        try {
            const parsed = JSON.parse(jsonStr);
            if (!parsed.santri || !parsed.setoran) {
                return { success: false, message: 'Format file cadangan tidak valid!' };
            }
            MobileDB.saveRaw(parsed);
            return { success: true, message: 'Data cadangan berhasil dipulihkan!' };
        } catch (e) {
            return { success: false, message: 'Gagal membaca file JSON: ' + e.message };
        }
    }
};

// Inisialisasi awal
MobileDB.init();

