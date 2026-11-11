<?php
declare(strict_types=1);

use App\Database;
use App\Models\Santri;
use App\QuranData;

$db = Database::getConnection();
$santriModel = new Santri($db);
$allSantri = $santriModel->getAll();
$allSurah = QuranData::getAllSurah();
$allJuz = QuranData::getAllJuz();

$selectedSantriId = isset($_GET['santri_id']) ? (int)$_GET['santri_id'] : (int)($allSantri[0]['id'] ?? 0);
$prefillJenis = $_GET['jenis'] ?? 'hafalan_baru';
$prefillJuz = isset($_GET['juz']) ? (int)$_GET['juz'] : 30;
$prefillSurah = isset($_GET['surah_id']) ? (int)$_GET['surah_id'] : 0;
$prefillAyatMulai = isset($_GET['ayat_mulai']) ? (int)$_GET['ayat_mulai'] : 0;
$prefillAyatSelesai = isset($_GET['ayat_selesai']) ? (int)$_GET['ayat_selesai'] : 0;

// Ambil default musyrif dari pengaturan
$defaultMusyrif = 'Ust. Pembimbing';
try {
    $stmtM = $db->query("SELECT nilai FROM pengaturan WHERE kunci = 'default_musyrif'");
    $mVal = $stmtM->fetchColumn();
    if ($mVal) $defaultMusyrif = $mVal;
} catch (\Throwable $e) {}
?>

<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-circle-plus text-emerald-600"></i> Form Setoran Hafalan
            </h2>
            <p class="text-sm text-slate-500">Pilih santri, pilih jenis setoran, dan sistem otomatis menghitung capaian.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="murojaah" class="text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-2 rounded-xl border border-emerald-200 transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-arrows-rotate"></i> Lihat Antrean Murojaah
            </a>
        </div>
    </div>

    <!-- Step 1: Pilih Santri & Info Capaian -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 transition-all">
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">1. Pilih Santri</label>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
            <div>
                <select id="select-santri" onchange="onSantriChange(this.value)" class="w-full text-base font-semibold text-slate-800 bg-slate-50 border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-shadow">
                    <?php foreach ($allSantri as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $s['id'] === $selectedSantriId ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nama']) ?> (NIS: <?= htmlspecialchars($s['nis']) ?>) - <?= htmlspecialchars($s['halaqah_nama'] ?? 'Halaqah Umum') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Mini Santri Badge & Progress (Live Updated) -->
            <div id="santri-badge" class="bg-emerald-50/60 border border-emerald-200/80 rounded-xl p-3.5 flex items-center justify-between">
                <div>
                    <div class="text-xs text-emerald-800 font-bold flex items-center gap-1.5">
                        <i class="fa-solid fa-bookmark text-emerald-600"></i> Posisi Terakhir:
                        <span id="badge-posisi" class="font-extrabold text-slate-800">-</span>
                    </div>
                    <div class="text-[11px] text-slate-600 mt-1 flex items-center gap-2">
                        <span>Total: <b id="badge-total-hal">0</b> Hal (<b id="badge-total-juz">0</b> Juz)</span>
                        <span>• Target: <b id="badge-target-juz">30</b> Juz</span>
                    </div>
                </div>
                <div class="text-right">
                    <span id="badge-persen" class="text-sm font-extrabold text-emerald-700 bg-white px-2 py-1 rounded-lg border border-emerald-200 shadow-xs">0%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: Tab Kategori Setoran (Hafalan Baru, Murojaah Baru, Murojaah Lama) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 space-y-6">
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">2. Pilih Kategori Setoran</label>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                
                <!-- Tab: Hafalan Baru (Ziyadah) -->
                <button type="button" onclick="switchCategory('hafalan_baru')" id="btn-cat-hafalan_baru" class="cat-tab-btn relative p-4 rounded-xl border-2 text-left transition-all flex flex-col justify-between border-emerald-600 bg-emerald-50/50 text-emerald-950">
                    <div class="flex items-center justify-between mb-2">
                        <span class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-sm font-bold shadow-xs">
                            <i class="fa-solid fa-star"></i>
                        </span>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-900">+ Auto Tambah</span>
                    </div>
                    <div>
                        <div class="font-bold text-sm">Hafalan Baru</div>
                        <p class="text-[11px] text-slate-600 mt-0.5">Ziyadah / Sabaq yang baru pertama kali disetor</p>
                    </div>
                </button>

                <!-- Tab: Murojaah Hafalan Baru (Sabqi) -->
                <button type="button" onclick="switchCategory('murojaah_baru')" id="btn-cat-murojaah_baru" class="cat-tab-btn relative p-4 rounded-xl border-2 text-left transition-all flex flex-col justify-between border-slate-200 hover:border-amber-400 bg-white text-slate-700">
                    <div class="flex items-center justify-between mb-2">
                        <span class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center text-sm font-bold shadow-xs">
                            <i class="fa-solid fa-arrows-rotate"></i>
                        </span>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full bg-amber-100 text-amber-900">Sabqi</span>
                    </div>
                    <div>
                        <div class="font-bold text-sm">Murojaah Baru</div>
                        <p class="text-[11px] text-slate-600 mt-0.5">Ulangi hafalan 5 setoran terakhir agar mutqin</p>
                    </div>
                </button>

                <!-- Tab: Murojaah Hafalan Sebelumnya (Manzil) -->
                <button type="button" onclick="switchCategory('murojaah_sebelumnya')" id="btn-cat-murojaah_sebelumnya" class="cat-tab-btn relative p-4 rounded-xl border-2 text-left transition-all flex flex-col justify-between border-slate-200 hover:border-blue-400 bg-white text-slate-700">
                    <div class="flex items-center justify-between mb-2">
                        <span class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-sm font-bold shadow-xs">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </span>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full bg-blue-100 text-blue-900">Manzil</span>
                    </div>
                    <div>
                        <div class="font-bold text-sm">Murojaah Sebelumnya</div>
                        <p class="text-[11px] text-slate-600 mt-0.5">Rotasi juz/surah yang telah diselesaikan</p>
                    </div>
                </button>

            </div>
        </div>

        <!-- Suggestion Widget (Berubah sesuai Tab yang aktif) -->
        <div id="suggestion-box" class="p-4 rounded-xl bg-slate-50 border border-slate-200/90 text-sm">
            <!-- Dynamically populated via JS -->
        </div>

        <!-- Step 3: Form Detail Surah, Ayat, & Halaman -->
        <form id="form-setoran" onsubmit="submitSetoran(event)" class="space-y-6">
            <input type="hidden" name="santri_id" id="input-santri-id" value="<?= $selectedSantriId ?>">
            <input type="hidden" name="jenis_setoran" id="input-jenis-setoran" value="<?= htmlspecialchars($prefillJenis) ?>">

            <div class="border-t border-slate-200 pt-5">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">3. Cakupan Hafalan / Setoran</label>

                <!-- Rentang Surah & Ayat -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    <!-- Dari Surah & Ayat -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3">
                        <span class="text-xs font-bold text-emerald-800 flex items-center gap-1.5">
                            <i class="fa-solid fa-play text-[10px]"></i> Mulai Dari:
                        </span>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Surah</label>
                            <select name="surah_mulai_id" id="surah-mulai" onchange="onSurahMulaiChange(this.value)" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                <?php foreach ($allSurah as $sId => $sData): ?>
                                    <option value="<?= $sId ?>" <?= $sId == 78 ? 'selected' : '' ?>>
                                        <?= $sId ?>. <?= htmlspecialchars($sData['nama']) ?> (<?= $sData['ayat'] ?> Ayat) - <?= $sData['arab'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Ayat Mulai</label>
                            <input type="number" name="ayat_mulai" id="ayat-mulai" value="1" min="1" max="286" oninput="syncPageEstimations()" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-sm font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                    </div>

                    <!-- Sampai Surah & Ayat -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3">
                        <span class="text-xs font-bold text-emerald-800 flex items-center gap-1.5">
                            <i class="fa-solid fa-flag-checkered text-[10px]"></i> Sampai Dengan:
                        </span>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Surah</label>
                            <select name="surah_selesai_id" id="surah-selesai" onchange="onSurahSelesaiChange(this.value)" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                <?php foreach ($allSurah as $sId => $sData): ?>
                                    <option value="<?= $sId ?>" <?= $sId == 78 ? 'selected' : '' ?>>
                                        <?= $sId ?>. <?= htmlspecialchars($sData['nama']) ?> (<?= $sData['ayat'] ?> Ayat) - <?= $sData['arab'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Ayat Selesai</label>
                            <input type="number" name="ayat_selesai" id="ayat-selesai" value="10" min="1" max="286" oninput="syncPageEstimations()" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-sm font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                    </div>

                </div>

                <!-- Info Halaman & Juz (Auto-Calculated) -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
                    <div class="bg-slate-100/80 p-3 rounded-xl border border-slate-200">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Halaman Mulai</label>
                        <input type="number" name="halaman_mulai" id="hal-mulai" value="582" min="1" max="604" class="w-full bg-transparent font-extrabold text-slate-800 text-base focus:outline-none">
                    </div>
                    <div class="bg-slate-100/80 p-3 rounded-xl border border-slate-200">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Halaman Selesai</label>
                        <input type="number" name="halaman_selesai" id="hal-selesai" value="582" min="1" max="604" class="w-full bg-transparent font-extrabold text-slate-800 text-base focus:outline-none">
                    </div>
                    <div class="bg-slate-100/80 p-3 rounded-xl border border-slate-200">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Juz</label>
                        <input type="number" name="juz" id="input-juz" value="30" min="1" max="30" class="w-full bg-transparent font-extrabold text-slate-800 text-base focus:outline-none">
                    </div>
                    <div class="bg-emerald-50 p-3 rounded-xl border border-emerald-200">
                        <label class="block text-[10px] font-bold text-emerald-700 uppercase">Jumlah Halaman</label>
                        <div id="display-total-hal" class="font-extrabold text-emerald-800 text-base">1.0 Hal</div>
                    </div>
                </div>

                <!-- Quick Button Adjusters -->
                <div class="flex flex-wrap items-center gap-2 mt-3 text-xs">
                    <span class="text-slate-500 text-[11px] font-semibold">Tombol Cepat:</span>
                    <button type="button" onclick="quickStepAyat(10)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1 rounded-lg border border-slate-300 font-semibold transition-colors">
                        +10 Ayat
                    </button>
                    <button type="button" onclick="quickStepAyat(20)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1 rounded-lg border border-slate-300 font-semibold transition-colors">
                        +20 Ayat
                    </button>
                    <button type="button" onclick="quickStepPage(1)" class="bg-emerald-100 hover:bg-emerald-200 text-emerald-900 px-2.5 py-1 rounded-lg border border-emerald-300 font-bold transition-colors">
                        +1 Halaman Penuh
                    </button>
                    <button type="button" onclick="quickStepPage(-1)" class="bg-rose-100 hover:bg-rose-200 text-rose-900 px-2.5 py-1 rounded-lg border border-rose-300 font-bold transition-colors">
                        -1 Halaman (Mundur)
                    </button>
                </div>
            </div>

            <!-- Step 4: Penilaian & Status Kelulusan -->
            <div class="border-t border-slate-200 pt-5 space-y-4">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">4. Penilaian & Kelulusan</label>

                <!-- Pilihan Nilai Bintang -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                    
                    <label class="cursor-pointer">
                        <input type="radio" name="penilaian" value="mumtaz" checked onchange="onPenilaianChange(this.value)" class="peer sr-only">
                        <div class="p-3 text-center rounded-xl border-2 peer-checked:border-emerald-600 peer-checked:bg-emerald-50/70 border-slate-200 hover:border-emerald-300 transition-all">
                            <div class="text-amber-500 text-xs mb-1">★★★★★</div>
                            <div class="font-extrabold text-xs text-slate-800">Mumtaz</div>
                            <span class="text-[10px] text-emerald-700 font-semibold">Sangat Lancar</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="penilaian" value="jayyid_jiddan" onchange="onPenilaianChange(this.value)" class="peer sr-only">
                        <div class="p-3 text-center rounded-xl border-2 peer-checked:border-teal-600 peer-checked:bg-teal-50/70 border-slate-200 hover:border-teal-300 transition-all">
                            <div class="text-amber-500 text-xs mb-1">★★★★☆</div>
                            <div class="font-extrabold text-xs text-slate-800">Jayyid Jiddan</div>
                            <span class="text-[10px] text-teal-700 font-semibold">Lancar</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="penilaian" value="jayyid" onchange="onPenilaianChange(this.value)" class="peer sr-only">
                        <div class="p-3 text-center rounded-xl border-2 peer-checked:border-sky-600 peer-checked:bg-sky-50/70 border-slate-200 hover:border-sky-300 transition-all">
                            <div class="text-amber-500 text-xs mb-1">★★★☆☆</div>
                            <div class="font-extrabold text-xs text-slate-800">Jayyid</div>
                            <span class="text-[10px] text-sky-700 font-semibold">Cukup</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="penilaian" value="maqbul" onchange="onPenilaianChange(this.value)" class="peer sr-only">
                        <div class="p-3 text-center rounded-xl border-2 peer-checked:border-amber-600 peer-checked:bg-amber-50/70 border-slate-200 hover:border-amber-300 transition-all">
                            <div class="text-amber-500 text-xs mb-1">★★☆☆☆</div>
                            <div class="font-extrabold text-xs text-slate-800">Maqbul</div>
                            <span class="text-[10px] text-amber-700 font-semibold">Kurang Lancar</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="penilaian" value="rasib" onchange="onPenilaianChange(this.value)" class="peer sr-only">
                        <div class="p-3 text-center rounded-xl border-2 peer-checked:border-rose-600 peer-checked:bg-rose-50/70 border-slate-200 hover:border-rose-300 transition-all">
                            <div class="text-rose-500 text-xs mb-1">★☆☆☆☆</div>
                            <div class="font-extrabold text-xs text-slate-800">Rasib</div>
                            <span class="text-[10px] text-rose-700 font-semibold">Mengulang</span>
                        </div>
                    </label>

                </div>

                <!-- Status Kelulusan Radio -->
                <div class="flex items-center gap-6 text-sm font-semibold">
                    <span class="text-xs text-slate-500">Status Kelulusan:</span>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status_kelulusan" id="status-lulus" value="lulus" checked onchange="onStatusKelulusanChange(this.value)" class="text-emerald-600 focus:ring-emerald-500">
                        <span class="text-emerald-700 font-bold">Lulus (Tuntas)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status_kelulusan" id="status-ulang" value="ulang" onchange="onStatusKelulusanChange(this.value)" class="text-rose-600 focus:ring-rose-500">
                        <span class="text-rose-700 font-bold">Mengulang</span>
                    </label>
                </div>
            </div>

            <!-- Step 5: Fitur Utama: Mode Otomatis Menambah & Mengurang -->
            <div class="border-t border-slate-200 pt-5 space-y-3 bg-emerald-50/40 p-4 rounded-xl border border-emerald-200">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-wand-magic-sparkles text-emerald-600"></i> 5. Otomatis Menambah / Mengurang Hafalan
                    </label>
                    <span class="text-[11px] bg-emerald-600 text-white font-bold px-2 py-0.5 rounded-full">Fitur Utama</span>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Tentukan bagaimana sistem menyesuaikan akumulasi total halaman & posisi terakhir santri saat setoran ini disimpan:
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                    
                    <!-- Tambah Otomatis -->
                    <label class="cursor-pointer">
                        <input type="radio" name="mode_otomatis" id="mode-tambah" value="tambah" checked class="peer sr-only">
                        <div class="p-3 rounded-xl border-2 peer-checked:border-emerald-600 peer-checked:bg-white border-slate-200/80 bg-slate-50 hover:bg-white transition-all">
                            <div class="font-extrabold text-xs text-emerald-800 flex items-center gap-1.5 mb-1">
                                <i class="fa-solid fa-circle-arrow-up text-emerald-600 text-sm"></i> + Tambah Otomatis
                            </div>
                            <p class="text-[11px] text-slate-500 leading-tight">
                                Majukan posisi & tambah total hafalan sebesar jumlah halaman ini.
                            </p>
                        </div>
                    </label>

                    <!-- Posisi Tetap -->
                    <label class="cursor-pointer">
                        <input type="radio" name="mode_otomatis" id="mode-tetap" value="tetap" class="peer sr-only">
                        <div class="p-3 rounded-xl border-2 peer-checked:border-slate-700 peer-checked:bg-white border-slate-200/80 bg-slate-50 hover:bg-white transition-all">
                            <div class="font-extrabold text-xs text-slate-800 flex items-center gap-1.5 mb-1">
                                <i class="fa-solid fa-circle-pause text-slate-500 text-sm"></i> = Posisi Tetap
                            </div>
                            <p class="text-[11px] text-slate-500 leading-tight">
                                Akumulasi dan posisi tidak berubah (Sesuai untuk Murojaah rutin).
                            </p>
                        </div>
                    </label>

                    <!-- Kurang Otomatis -->
                    <label class="cursor-pointer">
                        <input type="radio" name="mode_otomatis" id="mode-kurang" value="kurang" class="peer sr-only">
                        <div class="p-3 rounded-xl border-2 peer-checked:border-rose-600 peer-checked:bg-white border-slate-200/80 bg-slate-50 hover:bg-white transition-all">
                            <div class="font-extrabold text-xs text-rose-800 flex items-center gap-1.5 mb-1">
                                <i class="fa-solid fa-circle-arrow-down text-rose-600 text-sm"></i> - Kurang Otomatis
                            </div>
                            <p class="text-[11px] text-slate-500 leading-tight">
                                Mundurkan posisi atau kurangi total jika santri menurunkan target/lupa.
                            </p>
                        </div>
                    </label>

                </div>
            </div>

            <!-- Catatan & Pembimbing -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Catatan Tajwid / Makhraj</label>
                    <input type="text" name="catatan" id="input-catatan" placeholder="Contoh: Makhraj shod dan dho perlu diperjelas, waqaf tartil" class="w-full bg-slate-50 border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Musyrif / Pembimbing</label>
                    <input type="text" name="musyrif_nama" id="input-musyrif" value="<?= htmlspecialchars($defaultMusyrif) ?>" class="w-full bg-slate-50 border border-slate-300 rounded-lg p-2.5 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <!-- Tanggal Setoran -->
            <div class="flex items-center gap-4 text-xs font-semibold text-slate-600">
                <span>Tanggal Setoran:</span>
                <input type="date" name="tanggal_setoran" value="<?= date('Y-m-d') ?>" class="bg-slate-50 border border-slate-300 rounded-lg px-3 py-1.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Submit Button & Loader -->
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-end gap-3">
                <button type="submit" id="btn-submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold px-8 py-3.5 rounded-xl shadow-lg shadow-emerald-600/30 active:scale-95 transition-all text-sm">
                    <i class="fa-solid fa-floppy-disk text-base"></i>
                    <span>Simpan & Proses Otomatis</span>
                </button>
            </div>

        </form>
    </div>

</div>

<!-- JavaScript Engine untuk Interaktivitas & Perhitungan Otomatis -->
<script>
let currentSantriData = null;
let currentCategory = '<?= htmlspecialchars($prefillJenis) ?>';

// Saat halaman dimuat
document.addEventListener('DOMContentLoaded', () => {
    const defaultSantriId = document.getElementById('select-santri').value;
    if (defaultSantriId) {
        onSantriChange(defaultSantriId);
    }
});

// Ganti Santri & Ambil data via API
async function onSantriChange(santriId) {
    document.getElementById('input-santri-id').value = santriId;
    const base = window.BASE_URL || '/';

    try {
        let resp = null;
        try {
            resp = await fetch(`${base}api/santri_info.php?id=${santriId}`);
            if (!resp.ok) throw new Error();
        } catch (_) {
            resp = await fetch(`${base}api/santri-info?id=${santriId}`);
        }
        const data = await resp.json();

        if (data.success) {
            currentSantriData = data;
            updateSantriBadge(data.santri);
            renderSuggestionBox(data);
            
            // Jika kategori hafalan baru, terapkan target berikutnya
            if (currentCategory === 'hafalan_baru' && data.next_target) {
                applyTarget(data.next_target);
            }

            // Jika ada prefill dari frontend player (shortcut "Setor ke Ustadz")
            const prefillSurah = <?= (int)$prefillSurah ?>;
            const prefillAyatMulai = <?= (int)$prefillAyatMulai ?>;
            const prefillAyatSelesai = <?= (int)$prefillAyatSelesai ?>;
            if (prefillSurah > 0) {
                document.getElementById('surah-mulai').value = prefillSurah;
                document.getElementById('surah-selesai').value = prefillSurah;
                if (prefillAyatMulai > 0) document.getElementById('ayat-mulai').value = prefillAyatMulai;
                if (prefillAyatSelesai > 0) document.getElementById('ayat-selesai').value = prefillAyatSelesai;
                onSurahMulaiChange(prefillSurah);
            }
        }
    } catch (e) {
        console.error('Gagal mengambil data santri:', e);
    }
}

// Update Badge Info Santri
function updateSantriBadge(santri) {
    document.getElementById('badge-posisi').textContent = `QS. ${santri.terakhir_surah_nama} : ${santri.terakhir_ayat} (Hal. ${santri.terakhir_halaman})`;
    document.getElementById('badge-total-hal').textContent = parseFloat(santri.total_halaman_hafal).toFixed(1);
    document.getElementById('badge-total-juz').textContent = parseFloat(santri.total_juz_hafal).toFixed(1);
    document.getElementById('badge-target-juz').textContent = santri.target_juz || 30;

    const target = santri.target_juz || 30;
    const persen = Math.min(100, Math.round(((santri.total_juz_hafal || 0) / target) * 100));
    document.getElementById('badge-persen').textContent = `${persen}% Target`;
}

// Ganti Kategori Setoran
function switchCategory(cat) {
    currentCategory = cat;
    document.getElementById('input-jenis-setoran').value = cat;

    // Update style tabs
    document.querySelectorAll('.cat-tab-btn').forEach(btn => {
        btn.classList.remove('border-emerald-600', 'bg-emerald-50/50', 'border-amber-500', 'bg-amber-50/50', 'border-blue-600', 'bg-blue-50/50');
        btn.classList.add('border-slate-200', 'bg-white');
    });

    const activeBtn = document.getElementById(`btn-cat-${cat}`);
    if (cat === 'hafalan_baru') {
        activeBtn.classList.remove('border-slate-200', 'bg-white');
        activeBtn.classList.add('border-emerald-600', 'bg-emerald-50/50');
        // Default mode: Tambah
        document.getElementById('mode-tambah').checked = true;
    } else if (cat === 'murojaah_baru') {
        activeBtn.classList.remove('border-slate-200', 'bg-white');
        activeBtn.classList.add('border-amber-500', 'bg-amber-50/50');
        // Default mode: Tetap
        document.getElementById('mode-tetap').checked = true;
    } else {
        activeBtn.classList.remove('border-slate-200', 'bg-white');
        activeBtn.classList.add('border-blue-600', 'bg-blue-50/50');
        // Default mode: Tetap
        document.getElementById('mode-tetap').checked = true;
    }

    if (currentSantriData) {
        renderSuggestionBox(currentSantriData);
        if (cat === 'hafalan_baru' && currentSantriData.next_target) {
            applyTarget(currentSantriData.next_target);
        }
    }
}

// Render Kotak Saran Cepat sesuai Kategori
function renderSuggestionBox(data) {
    const box = document.getElementById('suggestion-box');
    if (!box) return;

    if (currentCategory === 'hafalan_baru') {
        const nt = data.next_target;
        box.innerHTML = `
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-sparkles text-amber-500"></i> Rekomendasi Target Hafalan Baru Berikutnya:
                    </span>
                    <div class="text-sm font-extrabold text-slate-800 mt-1">
                        QS. ${nt.surah_nama} (${nt.surah_arab}) Ayat ${nt.next_ayat_mulai} s/d ${nt.next_ayat_selesai}
                        <span class="text-xs font-normal text-slate-500 ml-1.5">• Halaman ${nt.next_hal_mulai} (Juz ${nt.juz})</span>
                    </div>
                </div>
                <button type="button" onclick='applyTarget(${JSON.stringify(nt)})' class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-3.5 py-2 rounded-lg shadow-sm whitespace-nowrap">
                    <i class="fa-solid fa-check mr-1"></i> Terapkan Target Ini
                </button>
            </div>
        `;
    } else if (currentCategory === 'murojaah_baru') {
        // Antrean Sabqi
        const sabqi = data.murojaah_baru || [];
        if (sabqi.length === 0) {
            box.innerHTML = `
                <div class="text-slate-500 text-xs">
                    <i class="fa-solid fa-circle-info text-amber-500 mr-1"></i> Belum ada antrean murojaah baru tersimpan. Santri dapat langsung memilih surah di bawah.
                </div>
            `;
        } else {
            let html = `
                <div class="text-xs font-bold text-amber-900 mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-list-check text-amber-600"></i> Pilih Bagian Murojaah Baru (Sabqi):
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            `;
            sabqi.forEach(item => {
                const itemJson = JSON.stringify({
                    next_surah_id: item.surah_mulai_id,
                    next_ayat_mulai: item.ayat_mulai,
                    surah_selesai_id: item.surah_selesai_id,
                    next_ayat_selesai: item.ayat_selesai,
                    next_hal_mulai: item.halaman_mulai,
                    next_hal_selesai: item.halaman_selesai,
                    juz: item.juz
                });
                html += `
                    <button type="button" onclick='applyTarget(${itemJson})' class="text-left p-2.5 rounded-lg bg-white border border-slate-200 hover:border-amber-400 hover:bg-amber-50/50 transition-all text-xs">
                        <div class="font-bold text-slate-800">${item.judul}</div>
                        <div class="text-[10px] text-slate-500">Juz ${item.juz} • Frekuensi: ${item.frekuensi}x</div>
                    </button>
                `;
            });
            html += `</div>`;
            box.innerHTML = html;
        }
    } else {
        // Murojaah Sebelumnya (Manzil)
        const manzil = data.murojaah_sebelumnya || [];
        if (manzil.length === 0) {
            box.innerHTML = `
                <div class="text-slate-500 text-xs">
                    <i class="fa-solid fa-circle-info text-blue-500 mr-1"></i> Santri belum memiliki rekaman juz tuntas untuk murojaah sebelumnya.
                </div>
            `;
        } else {
            let html = `
                <div class="text-xs font-bold text-blue-900 mb-2 flex items-center justify-between">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-bell text-blue-600"></i> Rekomendasi Murojaah Sebelumnya (Prioritas Paling Lama):</span>
                    <span class="text-[11px] font-normal text-slate-500">Paling lama belum disetor</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            `;
            manzil.slice(0, 4).forEach(item => {
                const urgensiBadge = item.hari_lalu >= 7 ? 'text-rose-600 font-bold bg-rose-50 border-rose-200' : 'text-slate-600 bg-slate-100 border-slate-200';
                html += `
                    <div class="p-2.5 rounded-lg bg-white border border-slate-200 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-bold text-slate-800">Juz ${item.juz} (${item.nama})</div>
                            <div class="text-[10px] text-slate-500">${item.halaman}</div>
                            <span class="inline-block text-[9px] px-1.5 py-0.5 rounded border mt-0.5 ${urgensiBadge}">
                                ${item.hari_lalu >= 999 ? 'Belum Pernah' : item.hari_lalu + ' hari lalu'}
                            </span>
                        </div>
                        <button type="button" onclick="setJuzRange(${item.juz})" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-2.5 py-1.5 rounded-lg shadow-sm">
                            Pilih Juz
                        </button>
                    </div>
                `;
            });
            html += `</div>`;
            box.innerHTML = html;
        }
    }
}

// Terapkan Target ke Form Input
function applyTarget(target) {
    if (target.next_surah_id) {
        document.getElementById('surah-mulai').value = target.next_surah_id;
        document.getElementById('surah-selesai').value = target.surah_selesai_id || target.next_surah_id;
    }
    if (target.next_ayat_mulai) {
        document.getElementById('ayat-mulai').value = target.next_ayat_mulai;
    }
    if (target.next_ayat_selesai) {
        document.getElementById('ayat-selesai').value = target.next_ayat_selesai;
    }
    if (target.next_hal_mulai) {
        document.getElementById('hal-mulai').value = target.next_hal_mulai;
    }
    if (target.next_hal_selesai) {
        document.getElementById('hal-selesai').value = target.next_hal_selesai;
    }
    if (target.juz) {
        document.getElementById('input-juz').value = target.juz;
    }

    calculateTotalPages();
}

// Set rentang Juz penuh (untuk Murojaah Sebelumnya)
function setJuzRange(juzNumber) {
    document.getElementById('input-juz').value = juzNumber;
    // Estimasi halaman berdasarkan juz
    const halAwal = Math.max(1, (juzNumber - 1) * 20 + 2);
    const halAkhir = Math.min(604, juzNumber * 20 + 1);

    document.getElementById('hal-mulai').value = (juzNumber === 1) ? 1 : (juzNumber === 30 ? 582 : halAwal);
    document.getElementById('hal-selesai').value = (juzNumber === 30) ? 604 : halAkhir;

    calculateTotalPages();
    showToast(`Target Murojaah Juz ${juzNumber} diterapkan`, 'info');
}

// Hitung total halaman
function calculateTotalPages() {
    const h1 = parseInt(document.getElementById('hal-mulai').value) || 1;
    const h2 = parseInt(document.getElementById('hal-selesai').value) || 1;
    const total = Math.max(0.5, (h2 >= h1 ? (h2 - h1 + 1) : (h1 - h2 + 1)));
    document.getElementById('display-total-hal').textContent = `${total.toFixed(1)} Hal`;
}

// Tombol cepat langkah halaman
function quickStepPage(step) {
    const h1 = parseInt(document.getElementById('hal-mulai').value) || 1;
    let h2 = parseInt(document.getElementById('hal-selesai').value) || h1;

    h2 = Math.max(1, Math.min(604, h2 + step));
    document.getElementById('hal-selesai').value = h2;
    calculateTotalPages();
}

// Tombol cepat langkah ayat
function quickStepAyat(step) {
    let a2 = parseInt(document.getElementById('ayat-selesai').value) || 1;
    a2 += step;
    document.getElementById('ayat-selesai').value = a2;
    syncPageEstimations();
}

// Event handler saat surah berubah
async function onSurahMulaiChange(surahId) {
    const surahSelesai = document.getElementById('surah-selesai');
    if (parseInt(surahSelesai.value) < parseInt(surahId)) {
        surahSelesai.value = surahId;
    }
    syncPageEstimations();
}

async function onSurahSelesaiChange(surahId) {
    syncPageEstimations();
}

async function syncPageEstimations() {
    calculateTotalPages();
}

// Event Penilaian & Otomatisasi
function onPenilaianChange(nilai) {
    const statusLulus = document.getElementById('status-lulus');
    const statusUlang = document.getElementById('status-ulang');
    const modeTambah = document.getElementById('mode-tambah');
    const modeTetap = document.getElementById('mode-tetap');
    const modeKurang = document.getElementById('mode-kurang');

    if (['mumtaz', 'jayyid_jiddan', 'jayyid'].includes(nilai)) {
        statusLulus.checked = true;
        if (currentCategory === 'hafalan_baru') {
            modeTambah.checked = true;
        } else {
            modeTetap.checked = true;
        }
    } else if (nilai === 'rasib') {
        statusUlang.checked = true;
        // Jika rasib (mengulang), tawarkan posisi tetap atau kurangi target
        modeTetap.checked = true;
    }
}

function onStatusKelulusanChange(status) {
    const modeTambah = document.getElementById('mode-tambah');
    const modeTetap = document.getElementById('mode-tetap');

    if (status === 'ulang') {
        modeTetap.checked = true;
    } else {
        if (currentCategory === 'hafalan_baru') {
            modeTambah.checked = true;
        }
    }
}

// Submit Setoran via AJAX
async function submitSetoran(e) {
    e.preventDefault();

    const btn = document.getElementById('btn-submit');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-2"></i> Menyimpan...`;

    const form = document.getElementById('form-setoran');
    const formData = new FormData(form);

    // Pastikan ID santri dan kategori aktif selalu terkirim dengan benar
    const currentSantriId = document.getElementById('select-santri').value || document.getElementById('input-santri-id').value;
    formData.set('santri_id', currentSantriId);
    formData.set('jenis_setoran', currentCategory);

    const base = window.BASE_URL || '/';

    try {
        let response = null;
        try {
            response = await fetch(base + 'api/save_setoran.php', {
                method: 'POST',
                body: formData
            });
            if (!response.ok && response.status !== 401 && response.status !== 400) {
                throw new Error();
            }
        } catch (_) {
            response = await fetch(base + 'api/save-setoran', {
                method: 'POST',
                body: formData
            });
        }

        // Tangani jika sesi login kedaluwarsa
        if (response.status === 401) {
            showToast('Sesi login ustadz/admin telah berakhir. Silakan login kembali.', 'danger');
            if (confirm('Sesi login Anda telah berakhir. Buka halaman login di tab baru? (Data pada form ini tetap aman dan tidak akan hilang)')) {
                window.open('login', '_blank');
            }
            return;
        }

        let result = null;
        try {
            result = await response.json();
        } catch (jsonErr) {
            throw new Error('Gagal memproses respons server (format respons tidak sesuai)');
        }

        if (result.success) {
            showToast(result.message, 'success');
            
            // Perbarui data lokal santri
            if (result.santri) {
                currentSantriData.santri = result.santri;
                updateSantriBadge(result.santri);
            }
            if (result.next_target) {
                currentSantriData.next_target = result.next_target;
            }

            // Reset catatan
            document.getElementById('input-catatan').value = '';

            // Terapkan target berikutnya secara instan
            if (currentCategory === 'hafalan_baru' && result.next_target) {
                applyTarget(result.next_target);
                renderSuggestionBox(currentSantriData);
            }
        } else {
            showToast(result.message || 'Gagal menyimpan setoran', 'danger');
        }
    } catch (err) {
        showToast(err.message || 'Terjadi kesalahan koneksi server', 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}
</script>

