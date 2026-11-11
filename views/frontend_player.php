<?php
declare(strict_types=1);

use App\Database;
use App\Models\Santri;
use App\QuranData;

$db = Database::getConnection();
$allSurah = QuranData::getAllSurah();
$allJuz = QuranData::getAllJuz();
$santriModel = new Santri($db);
$allSantri = $santriModel->getAll();

// Default query params
$selectedJuz = isset($_GET['juz']) ? (int)$_GET['juz'] : 30;
$selectedSurah = isset($_GET['surah']) ? (int)$_GET['surah'] : 78; // An-Naba
$selectedAyatMulai = isset($_GET['ayat_mulai']) ? (int)$_GET['ayat_mulai'] : 1;
$selectedAyatSelesai = isset($_GET['ayat_selesai']) ? (int)$_GET['ayat_selesai'] : 5;
$selectedSantriId = isset($_GET['santri_id']) ? (int)$_GET['santri_id'] : ($allSantri[0]['id'] ?? 0);

$activeSurahInfo = QuranData::getSurah($selectedSurah) ?? QuranData::getSurah(78);
?>

<div class="max-w-5xl mx-auto space-y-6">

    <!-- Top Banner: Mode Santri vs Ustadz Switcher -->
    <div class="bg-gradient-to-r from-emerald-800 via-teal-800 to-emerald-900 rounded-2xl p-4 sm:p-5 text-white shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center text-amber-300 text-2xl shadow-inner">
                <i class="fa-solid fa-headphones-simple"></i>
            </div>
            <div>
                <span class="inline-block text-[10px] font-extrabold uppercase tracking-widest px-2.5 py-0.5 rounded-full bg-amber-400 text-amber-950 mb-0.5">
                    Mode Santri: Menghafal & Tikrar
                </span>
                <h2 class="text-lg sm:text-xl font-extrabold text-white">Aplikasi Hafalan Berulang Al-Qur'an</h2>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="dashboard" class="inline-flex items-center gap-1.5 text-xs font-bold bg-white/15 hover:bg-white/25 text-white border border-white/20 px-3.5 py-2 rounded-xl transition-all shadow-xs">
                <i class="fa-solid fa-user-shield text-amber-300"></i> Masuk Mode Admin / Ustadz
            </a>
        </div>
    </div>

    <!-- Panel Pengaturan Menghafal (Juz, Surah, Rentang Ayat, Qari, Pengulangan) -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-5 sm:p-6 space-y-5">
        
        <!-- Baris 1: Filter Juz & Pilihan Surah -->
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
            
            <!-- Pilih Juz -->
            <div class="sm:col-span-4">
                <label class="block text-xs font-bold text-slate-600 mb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-layer-group text-emerald-600"></i> 1. Filter Juz
                </label>
                <select id="select-juz" onchange="onJuzFilterChange(this.value)" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <?php for ($j = 1; $j <= 30; $j++): 
                        $jInfo = QuranData::getJuz($j);
                    ?>
                        <option value="<?= $j ?>" <?= $j === $selectedJuz ? 'selected' : '' ?>>
                            Juz <?= $j ?> (<?= htmlspecialchars($jInfo['nama']) ?>)
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Pilih Surah -->
            <div class="sm:col-span-8">
                <label class="block text-xs font-bold text-slate-600 mb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-book-quran text-emerald-600"></i> 2. Pilih Surah
                </label>
                <select id="select-surah" onchange="onSurahChange(this.value)" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <?php foreach ($allSurah as $sId => $s): ?>
                        <option value="<?= $sId ?>" data-juz="<?= $s['juz'] ?>" data-ayat="<?= $s['ayat'] ?>" <?= $sId === $selectedSurah ? 'selected' : '' ?>>
                            <?= $sId ?>. <?= htmlspecialchars($s['nama']) ?> (<?= $s['ayat'] ?> Ayat) - <?= $s['arab'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>

        <!-- Baris 2: Rentang Ayat (Mulai s/d Selesai) & Tombol Preset Cepat -->
        <div class="bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <label class="text-xs font-bold text-emerald-950 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-arrows-left-right text-emerald-600"></i> 3. Tentukan Rentang Ayat yang Akan Dihafal
                </label>
                
                <!-- Indikator Total Ayat Terpilih -->
                <span id="label-total-ayat-selected" class="text-xs font-extrabold text-emerald-800 bg-emerald-100 px-3 py-1 rounded-full border border-emerald-300 self-start sm:self-auto">
                    5 Ayat Terpilih (1 - 5)
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 items-center">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Mulai Ayat Ke:</label>
                    <input type="number" id="input-ayat-mulai" value="<?= $selectedAyatMulai ?>" min="1" max="286" onchange="onAyatRangeChange()" class="w-full bg-white border border-slate-300 rounded-xl p-2 text-center text-sm font-extrabold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Sampai Ayat Ke:</label>
                    <input type="number" id="input-ayat-selesai" value="<?= $selectedAyatSelesai ?>" min="1" max="286" onchange="onAyatRangeChange()" class="w-full bg-white border border-slate-300 rounded-xl p-2 text-center text-sm font-extrabold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div class="col-span-2 flex flex-wrap items-center gap-1.5 pt-4 sm:pt-0">
                    <button type="button" onclick="setAyatPreset(1, 5)" class="px-2.5 py-1.5 bg-white hover:bg-emerald-100 text-emerald-900 border border-emerald-200 rounded-lg text-xs font-bold shadow-2xs">
                        Ayat 1-5
                    </button>
                    <button type="button" onclick="setAyatPreset(6, 10)" class="px-2.5 py-1.5 bg-white hover:bg-emerald-100 text-emerald-900 border border-emerald-200 rounded-lg text-xs font-bold shadow-2xs">
                        Ayat 6-10
                    </button>
                    <button type="button" onclick="setAyatPreset(1, 10)" class="px-2.5 py-1.5 bg-white hover:bg-emerald-100 text-emerald-900 border border-emerald-200 rounded-lg text-xs font-bold shadow-2xs">
                        Ayat 1-10
                    </button>
                    <button type="button" onclick="stepBlockAyat(-5)" class="px-2 py-1.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 rounded-lg text-xs font-bold" title="5 Ayat Sebelumnya">
                        <i class="fa-solid fa-backward-step"></i>
                    </button>
                    <button type="button" onclick="stepBlockAyat(5)" class="px-2 py-1.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 rounded-lg text-xs font-bold" title="5 Ayat Berikutnya">
                        <i class="fa-solid fa-forward-step"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Baris 3: Pengisi Suara (Qari) & Pengaturan Tikrar (Pengulangan) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            
            <!-- Pilihan Qari -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-600 mb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-microphone-lines text-emerald-600"></i> 4. Pengisi Suara (Murottal Qari)
                </label>
                <select id="select-qari" onchange="onQariChange(this.value)" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="Alafasy_128kbps">Misyari Rasyid Al-Afasy (Tartil & Sangat Jelas)</option>
                    <option value="Husary_128kbps">Mahmoud Khalil Al-Hushari (Standar Tajwid Internasional)</option>
                    <option value="Husary_Mujawwad_128kbps">Al-Hushari (Mujawwad - Langgam Tajwid)</option>
                    <option value="Minshawy_Murattal_128kbps">Muhammad Siddiq Al-Minshawi (Syahdu & Khusyuk)</option>
                    <option value="Abdurrahmaan_As-Sudais_192kbps">Abdurrahman As-Sudais (Imam Masjidil Haram, Tegas)</option>
                    <option value="Saood_ash-Shuraym_128kbps">Sa'ud Ash-Shuraim (Tempo Cepat & Ritmis)</option>
                    <option value="Ghamadi_40kbps">Saad Al-Ghamdi (Merdu & Lembut)</option>
                    <option value="MaherAlMuaiqly128kbps">Maher Al-Muaiqly (Imam Masjidil Haram, Tartil)</option>
                    <option value="Abu_Bakr_Ash-Shaatree_128kbps">Abu Bakr Asy-Syathiri (Halus & Khusyuk)</option>
                    <option value="Hani_Rifai_192kbps">Hani Ar-Rifai (Emosional & Menyentuh Hati)</option>
                    <option value="Yasser_Ad-Dussary_128kbps">Yasser Ad-Dussary (Nada Khas Hijazi Modern)</option>
                    <option value="Hudhaify_128kbps">Ali Al-Hudhaify (Imam Nabawi, Tempo Pelan & Teliti)</option>
                </select>
            </div>

            <!-- Ulang Per Ayat (Metode Tikrar) -->
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-repeat text-amber-500"></i> Ulang Tiap Ayat
                </label>
                <select id="select-repeat-verse" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="1">1 Kali (Tanpa Ulang)</option>
                    <option value="3" selected>3 Kali Diulang</option>
                    <option value="5">5 Kali Diulang</option>
                    <option value="7">7 Kali Diulang</option>
                    <option value="10">10 Kali Diulang</option>
                    <option value="20">20 Kali (Metode Tikrar Cepat)</option>
                    <option value="40">40 Kali (Metode Tikrar Mutqin)</option>
                </select>
            </div>

            <!-- Ulang Blok / Seluruh Rentang -->
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-arrows-spin text-teal-600"></i> Ulang Seluruh Rentang
                </label>
                <select id="select-repeat-block" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="1">1 Putaran</option>
                    <option value="3" selected>3 Putaran Blok</option>
                    <option value="5">5 Putaran Blok</option>
                    <option value="10">10 Putaran Blok</option>
                    <option value="999">Loop Terus (Tanpa Batas)</option>
                </select>
            </div>

        </div>

        <!-- Fitur Baru: Tikrar Sambung Akumulatif (Wasl / Berantai) -->
        <div class="p-4 rounded-2xl bg-gradient-to-r from-amber-50/80 via-emerald-50/60 to-teal-50/80 border-2 border-amber-300/80 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <label class="flex items-start sm:items-center gap-3 cursor-pointer select-none flex-1">
                    <input type="checkbox" id="check-accumulative" onchange="toggleAccumulativeMode(this.checked)" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 mt-0.5 sm:mt-0">
                    <div>
                        <div class="text-xs sm:text-sm font-extrabold text-slate-900 flex items-center gap-2 flex-wrap">
                            <i class="fa-solid fa-link text-emerald-700"></i>
                            <span>Metode Sambung Akumulatif (Tikrar Berantai / Tangga Hafalan)</span>
                            <span class="text-[10px] bg-amber-400 text-amber-950 px-2 py-0.5 rounded-full font-extrabold uppercase">Metode Pesantren</span>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Contoh: Ayat 1 (3x) ➔ Ayat 2 (3x) ➔ <b>Sambung Ayat 1 s/d 2</b> ➔ Ayat 3 (3x) ➔ <b>Sambung Ayat 1 s/d 3</b> ➔ Ayat 4 (3x) ➔ <b>Sambung Ayat 1 s/d 4</b>.
                        </p>
                    </div>
                </label>

                <!-- Status Aktif & Opsi Putaran Sambung -->
                <div class="flex items-center gap-2.5 self-start sm:self-auto flex-shrink-0">
                    <div id="box-link-repeat" class="hidden flex items-center gap-1.5 text-xs">
                        <span class="text-[11px] font-semibold text-slate-600">Putar Sambung:</span>
                        <select id="select-link-repeat" class="bg-white border border-slate-300 rounded-lg px-2 py-1 text-xs font-bold text-slate-800">
                            <option value="1" selected>1x Sambung</option>
                            <option value="2">2x Sambung</option>
                        </select>
                    </div>
                    <span id="badge-accumulative-status" onclick="document.getElementById('check-accumulative').click()" class="text-[11px] font-extrabold px-3 py-1 rounded-full bg-slate-200 text-slate-600 cursor-pointer select-none transition-all hover:opacity-90 active:scale-95" title="Klik untuk aktifkan / nonaktifkan">
                        Nonaktif
                    </span>
                </div>
            </div>
        </div>

        <!-- Baris 4: Pengaturan Tambahan (Jeda Antar Ayat, Kecepatan, Toggle Teks) -->
        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100 text-xs">
            <div class="flex flex-wrap items-center gap-4">
                
                <!-- Jeda / Pause Antar Ayat -->
                <div class="flex items-center gap-2">
                    <span class="text-slate-500 font-semibold">Jeda Menirukan:</span>
                    <select id="select-pause" class="bg-slate-100 border border-slate-200 rounded-lg px-2 py-1 text-xs font-semibold">
                        <option value="0">Tanpa Jeda</option>
                        <option value="1.5" selected>1.5 Detik</option>
                        <option value="3">3 Detik</option>
                        <option value="5">5 Detik</option>
                    </select>
                </div>

                <!-- Kecepatan Playback -->
                <div class="flex items-center gap-2">
                    <span class="text-slate-500 font-semibold">Kecepatan:</span>
                    <select id="select-speed" onchange="changePlaybackSpeed(this.value)" class="bg-slate-100 border border-slate-200 rounded-lg px-2 py-1 text-xs font-semibold">
                        <option value="0.75">0.75x (Pelan)</option>
                        <option value="1.0" selected>1.0x (Normal)</option>
                        <option value="1.25">1.25x (Cepat)</option>
                    </select>
                </div>
            </div>

            <!-- Toggles: Sembunyikan Teks Arab & Terjemahan + Panduan -->
            <div class="flex items-center gap-2 flex-wrap">
                <a href="panduan?tab=santri" class="bg-amber-100 hover:bg-amber-200 text-amber-900 px-3 py-1.5 rounded-xl border border-amber-300 font-bold transition-all flex items-center gap-1.5 shadow-xs" title="Buka Panduan Menghafal Tikrar">
                    <i class="fa-solid fa-book-open text-amber-700"></i> Panduan Cara Pakai
                </a>
                <button type="button" onclick="toggleBlindTest()" id="btn-blind-test" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl border border-slate-200 font-bold transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-eye-slash text-slate-500"></i> Uji Hafalan (Tutup Teks)
                </button>
                <button type="button" onclick="toggleTranslation()" id="btn-toggle-trans" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl border border-slate-200 font-bold transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-language text-slate-500"></i> Terjemahan: On
                </button>
            </div>
        </div>

    </div>

    <!-- Sticky Audio Player Control Card -->
    <div class="sticky top-18 z-30 bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 text-white rounded-3xl p-5 sm:p-6 shadow-2xl border border-white/10">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
            
            <!-- Info Status Pemutaran -->
            <div class="space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <span id="player-indicator-dot" class="w-3 h-3 rounded-full bg-slate-500"></span>
                    <span id="player-status-badge" class="text-[11px] font-extrabold uppercase tracking-widest text-emerald-400">
                        Siap Memutar
                    </span>
                    <span id="player-phase-badge" class="hidden text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                    </span>
                </div>
                <h3 id="player-current-title" class="text-lg sm:text-xl font-extrabold text-white flex items-center gap-2">
                    QS. <?= htmlspecialchars($activeSurahInfo['nama']) ?> : Ayat <span id="label-active-ayah"><?= $selectedAyatMulai ?></span>
                </h3>
                <div class="flex items-center gap-3 text-xs text-slate-300 flex-wrap">
                    <span>Pengulangan Ayat: <b id="counter-verse-rep" class="text-amber-400 font-bold">1</b> / <span id="max-verse-rep">3</span></span>
                    <span>•</span>
                    <span>Putaran Blok: <b id="counter-block-rep" class="text-teal-300 font-bold">1</b> / <span id="max-block-rep">3</span></span>
                    <span id="label-accumulative-hint" class="hidden text-[11px] text-amber-300 font-bold bg-amber-950/70 px-2 py-0.5 rounded-lg border border-amber-500/30"></span>
                </div>
            </div>

            <!-- Tombol Kontrol Audio Utama -->
            <div class="flex items-center gap-3 self-center sm:self-auto">
                
                <!-- Prev Verse -->
                <button type="button" onclick="playPreviousAyah()" class="w-11 h-11 rounded-2xl bg-white/10 hover:bg-white/20 active:scale-95 text-white flex items-center justify-center text-base transition-all" title="Ayat Sebelumnya">
                    <i class="fa-solid fa-backward-step"></i>
                </button>

                <!-- Big Play / Pause Button -->
                <button type="button" onclick="togglePlayPause()" id="btn-play-pause" class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 active:scale-95 text-slate-950 font-extrabold flex items-center justify-center text-2xl shadow-xl shadow-emerald-500/30 transition-all">
                    <i class="fa-solid fa-play ml-1" id="icon-play-pause"></i>
                </button>

                <!-- Next Verse -->
                <button type="button" onclick="playNextAyah()" class="w-11 h-11 rounded-2xl bg-white/10 hover:bg-white/20 active:scale-95 text-white flex items-center justify-center text-base transition-all" title="Ayat Berikutnya">
                    <i class="fa-solid fa-forward-step"></i>
                </button>

                <!-- Stop Button -->
                <button type="button" onclick="stopPlayback()" class="w-11 h-11 rounded-2xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 flex items-center justify-center text-base transition-all" title="Stop & Reset">
                    <i class="fa-solid fa-stop"></i>
                </button>
            </div>

            <!-- Tombol Cepat: Sudah Hafal -> Setor ke Ustadz! -->
            <div class="flex items-center">
                <button type="button" onclick="goToSetoran()" class="w-full sm:w-auto bg-amber-400 hover:bg-amber-300 text-amber-950 font-extrabold px-4 py-3 rounded-2xl text-xs flex items-center justify-center gap-2 shadow-lg transition-all active:scale-95">
                    <i class="fa-solid fa-circle-check text-base"></i>
                    <span>Sudah Hafal? Setorkan ke Ustadz</span>
                </button>
            </div>

        </div>

        <!-- Hidden Audio Element -->
        <audio id="quran-audio-player" preload="auto"></audio>
    </div>

    <!-- Kotak Rekam Suara Santri & Uji Mandiri -->
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200/80">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="fa-solid fa-microphone"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                        Rekam Suara Bacaan Saya (Uji Mandiri)
                        <span class="text-[10px] bg-rose-100 text-rose-800 px-2 py-0.5 rounded-full font-bold">Interaktif</span>
                    </h4>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekam bacaan Anda sendiri dan bandingkan makhraj serta tajwidnya dengan Qari.
                    </p>
                </div>
            </div>

            <!-- Kontrol Perekam -->
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <button type="button" id="btn-record" onclick="toggleRecording()" class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md transition-all active:scale-95">
                    <i class="fa-solid fa-circle" id="icon-record"></i>
                    <span id="text-record">Mulai Rekam</span>
                </button>
                <div id="recording-playback-box" class="hidden flex items-center gap-2">
                    <button type="button" onclick="playStudentRecording()" id="btn-play-student" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-3.5 py-2.5 rounded-xl shadow-sm transition-all active:scale-95">
                        <i class="fa-solid fa-play"></i> Putar Rekaman Saya
                    </button>
                    <span id="record-timer" class="text-xs font-mono font-bold text-slate-600 bg-slate-100 px-2.5 py-2 rounded-lg border border-slate-200">00:00</span>
                </div>
            </div>
        </div>
        <audio id="student-audio-playback" class="hidden"></audio>
    </div>

    <!-- Tampilan Teks Ayat Al-Qur'an (Mushaf Card Container) -->
    <div id="verses-container" class="space-y-4">
        <div class="text-center py-12 bg-white rounded-3xl border border-slate-200">
            <i class="fa-solid fa-spinner fa-spin text-2xl text-emerald-600 mb-2"></i>
            <p class="text-xs font-semibold text-slate-500">Memuat teks ayat Al-Qur'an...</p>
        </div>
    </div>

</div>

<!-- JavaScript Engine Player Tikrar & Audio Controller -->
<script>
let currentSurahId = <?= $selectedSurah ?>;
let currentAyatMulai = <?= $selectedAyatMulai ?>;
let currentAyatSelesai = <?= $selectedAyatSelesai ?>;
let currentQari = 'Alafasy_128kbps';
let versesData = [];
let currentAyahIndex = 0; // Index dalam array versesData
let currentVerseRepeatCount = 1;
let currentBlockRepeatCount = 1;
let isPlaying = false;
let isBlindTest = false;
let showTranslation = true;
let pauseTimeout = null;

// State untuk Metode Sambung Akumulatif (Tikrar Berantai / Tangga Hafalan)
let isAccumulativeMode = false;
let frontierIndex = 0;             // Indeks ayat baru yang sedang difokuskan
let accumulativePhase = 'drill';   // 'drill' (ulang target baru N kali) atau 'link' (sambung 0 s/d frontierIndex)
let linkCurrentIndex = 0;          // Indeks ayat yang sedang bersuara saat fase 'link'
let currentLinkPass = 1;           // Putaran fase sambung (jika diset > 1x)

const audioPlayer = document.getElementById('quran-audio-player');

// Saat dokumen selesai dimuat
document.addEventListener('DOMContentLoaded', () => {
    loadVerses();

    audioPlayer.addEventListener('ended', onAudioEnded);
    audioPlayer.addEventListener('error', onAudioError);

    // Event listener langsung pada checkbox akumulatif
    const checkAcc = document.getElementById('check-accumulative');
    if (checkAcc) {
        checkAcc.addEventListener('change', function() {
            toggleAccumulativeMode(this.checked);
        });
        // Sinkronisasi otomatis saat page load / refresh
        if (checkAcc.checked) {
            toggleAccumulativeMode(true);
        }
    }
});

// Load ayat dari API Backend
async function loadVerses() {
    const container = document.getElementById('verses-container');
    container.innerHTML = `
        <div class="text-center py-12 bg-white rounded-3xl border border-slate-200">
            <i class="fa-solid fa-spinner fa-spin text-2xl text-emerald-600 mb-2"></i>
            <p class="text-xs font-semibold text-slate-500">Memuat teks ayat dan audio...</p>
        </div>
    `;

    try {
        const base = window.BASE_URL || '/';
        const queryParams = `surah=${currentSurahId}&ayat_mulai=${currentAyatMulai}&ayat_selesai=${currentAyatSelesai}&qari=${encodeURIComponent(currentQari)}`;
        let resp = null;
        
        // Coba endpoint direct api/quran_ayat.php terlebih dahulu, jika gagal coba clean route api/quran-ayat
        try {
            resp = await fetch(`${base}api/quran_ayat.php?${queryParams}`);
            if (!resp.ok) throw new Error('Coba clean route');
        } catch (_) {
            resp = await fetch(`${base}api/quran-ayat?${queryParams}`);
        }

        if (!resp || !resp.ok) {
            throw new Error(`HTTP ${resp ? resp.status : 'offline'}`);
        }

        const data = await resp.json();

        if (data.success) {
            versesData = data.verses;
            currentAyahIndex = 0;

            // Update judul QS pada player
            if (data.surah && data.surah.nama) {
                const titleElem = document.getElementById('player-current-title');
                if (titleElem) {
                    titleElem.innerHTML = `QS. ${data.surah.nama} : Ayat <span id="label-active-ayah">${versesData[0] ? versesData[0].nomor_ayat : currentAyatMulai}</span>`;
                }
            }

            // Jika ada ayat yang belum memiliki teks Arab lokal, ambil dari API Alquran Cloud di browser
            if (data.need_fetch_text) {
                await fetchMissingTextsFromCloud(data.surah);
            } else {
                renderVerses();
            }

            updateTotalAyatLabel();
        } else {
            container.innerHTML = `<div class="p-6 bg-rose-50 text-rose-700 text-center rounded-2xl">${data.message || 'Gagal memuat data ayat'}</div>`;
        }
    } catch (err) {
        console.error('Error load verses:', err);
        container.innerHTML = `
            <div class="p-6 bg-rose-50 text-rose-700 text-center rounded-2xl">
                <div class="font-bold text-sm mb-1 text-rose-800"><i class="fa-solid fa-circle-exclamation mr-1"></i> Gagal memuat teks ayat</div>
                <p class="text-xs text-rose-600 mb-3">Pastikan server aplikasi aktif. Klik tombol di bawah untuk mencoba memuat ulang.</p>
                <button onclick="loadVerses()" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-xs transition-all active:scale-95">
                    <i class="fa-solid fa-arrows-rotate mr-1"></i> Coba Muat Ulang
                </button>
            </div>
        `;
    }
}

// Fallback fetch dari CDN Quran jika belum ada di SQLite
async function fetchMissingTextsFromCloud(surahInfo) {
    try {
        const cloudUrl = `https://api.alquran.cloud/v1/surah/${currentSurahId}/editions/quran-uthmani,id.indonesian`;
        const res = await fetch(cloudUrl);
        const cloudData = await res.json();

        if (cloudData.code === 200 && cloudData.data.length >= 2) {
            const arabAyahs = cloudData.data[0].ayahs;
            const indoAyahs = cloudData.data[1].ayahs;

            versesData.forEach(v => {
                const foundArab = arabAyahs.find(a => a.numberInSurah === v.nomor_ayat);
                const foundIndo = indoAyahs.find(a => a.numberInSurah === v.nomor_ayat);
                if (foundArab) v.teks_arab = foundArab.text;
                if (foundIndo) v.terjemahan = foundIndo.text;
            });

            // Kirim balik ke database lokal untuk di-cache permanen di SQLite!
            fetch(base + 'api/cache_quran.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ surah_id: currentSurahId, verses: versesData })
            }).catch(() => {
                fetch(base + 'api/cache-quran', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ surah_id: currentSurahId, verses: versesData })
                }).catch(() => {});
            });
        }
    } catch (e) {
        console.warn('Could not fetch external text:', e);
    }

    renderVerses();
}

// Render kartu ayat Al-Qur'an
function renderVerses() {
    const container = document.getElementById('verses-container');
    if (!versesData || versesData.length === 0) {
        container.innerHTML = `<div class="p-8 text-center bg-white rounded-3xl text-slate-400">Tidak ada ayat untuk ditampilkan.</div>`;
        return;
    }

    let html = '';

    // Basmalah Header (Kecuali Surah 9 At-Taubah dan Surah 1 Al-Fatihah yang sudah punya basmalah di ayat 1)
    if (currentSurahId !== 9 && currentSurahId !== 1 && currentAyatMulai === 1) {
        html += `
            <div class="text-center py-4 bg-emerald-50/50 border border-emerald-100 rounded-2xl mb-4">
                <span class="font-quran text-2xl sm:text-3xl text-emerald-900 select-none">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</span>
            </div>
        `;
    }

    versesData.forEach((v, idx) => {
        const isActive = idx === currentAyahIndex;
        const activeClass = isActive 
            ? 'ring-2 ring-emerald-500 bg-emerald-50/70 border-emerald-300 shadow-md scale-[1.01]' 
            : 'bg-white border-slate-200/90 hover:border-emerald-300 shadow-xs';

        html += `
            <div id="verse-card-${idx}" onclick="jumpToAyah(${idx})" class="verse-card p-5 sm:p-6 rounded-3xl border transition-all duration-300 cursor-pointer ${activeClass}">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <!-- Nomor Ayat & Info Halaman -->
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-xs flex items-center justify-center border border-emerald-200">
                            ${v.nomor_ayat}
                        </span>
                        <span class="text-[11px] font-semibold text-slate-400">
                            Juz ${v.juz} • Hal. ${v.halaman}
                        </span>
                    </div>

                    <!-- Tombol Play Manual Ayat Ini -->
                    <button type="button" onclick="event.stopPropagation(); jumpToAyah(${idx}, true)" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-600 flex items-center justify-center text-xs transition-colors" title="Putar Ayat Ini Saja">
                        <i class="fa-solid fa-play"></i>
                    </button>
                </div>

                <!-- Teks Arab Utsmani -->
                <div class="text-right py-3">
                    <p class="font-quran text-2xl sm:text-3xl leading-[2.3] sm:leading-[2.5] text-slate-900 ${isBlindTest ? 'blur-md select-none' : ''} transition-all">
                        ${v.teks_arab || 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ'}
                        <span class="inline-flex items-center justify-center w-7 h-7 text-xs font-bold font-sans text-emerald-700 bg-emerald-100 rounded-full ml-2 border border-emerald-300">
                            ${v.nomor_ayat}
                        </span>
                    </p>
                </div>

                <!-- Terjemahan Bahasa Indonesia -->
                ${showTranslation && v.terjemahan ? `
                    <div class="mt-3 pt-3 border-t border-slate-100/80 text-xs text-slate-600 leading-relaxed">
                        <span class="font-bold text-slate-500 mr-1.5">${v.nomor_ayat}.</span>${v.terjemahan}
                    </div>
                ` : ''}
            </div>
        `;
    });

    container.innerHTML = html;
}

// Navigasi & Kontrol Audio Player
function togglePlayPause() {
    if (isPlaying) {
        pausePlayback();
    } else {
        startPlayback();
    }
}

function startPlayback() {
    if (!versesData || versesData.length === 0) return;

    isPlaying = true;
    updatePlayerUI();
    playCurrentAyah();
}

function pausePlayback() {
    isPlaying = false;
    clearTimeout(pauseTimeout);
    audioPlayer.pause();
    updatePlayerUI();
}

// Toggle Mode Sambung Akumulatif (Tikrar Berantai)
function toggleAccumulativeMode(checked) {
    isAccumulativeMode = Boolean(checked);
    const badge = document.getElementById('badge-accumulative-status');
    const boxLinkRepeat = document.getElementById('box-link-repeat');
    const checkboxElem = document.getElementById('check-accumulative');

    if (checkboxElem) {
        checkboxElem.checked = isAccumulativeMode;
    }

    if (isAccumulativeMode) {
        if (badge) {
            badge.className = 'text-[11px] font-extrabold px-3 py-1 rounded-full bg-emerald-600 text-white shadow-xs';
            badge.textContent = 'Aktif ✓';
        }
        if (boxLinkRepeat) boxLinkRepeat.classList.remove('hidden');
        showToast('Metode Sambung Akumulatif Aktif: Setiap ayat baru diulang lalu otomatis disambung dari ayat awal.', 'info');
    } else {
        if (badge) {
            badge.className = 'text-[11px] font-extrabold px-3 py-1 rounded-full bg-slate-200 text-slate-600';
            badge.textContent = 'Nonaktif';
        }
        if (boxLinkRepeat) boxLinkRepeat.classList.add('hidden');
        showToast('Mode Sambung Akumulatif dinonaktifkan (kembali ke pengulangan standar).', 'info');
    }

    // Reset posisi akumulatif
    frontierIndex = currentAyahIndex;
    accumulativePhase = 'drill';
    currentVerseRepeatCount = 1;
    currentLinkPass = 1;

    updatePlayerUI();
    updateCounters();
    highlightActiveCard();
}

function stopPlayback() {
    isPlaying = false;
    clearTimeout(pauseTimeout);
    audioPlayer.pause();
    audioPlayer.currentTime = 0;
    currentAyahIndex = 0;
    frontierIndex = 0;
    accumulativePhase = 'drill';
    linkCurrentIndex = 0;
    currentLinkPass = 1;
    currentVerseRepeatCount = 1;
    currentBlockRepeatCount = 1;
    updatePlayerUI();
    updateCounters();
    highlightActiveCard();
}

// Putar Ayat Aktif
function playCurrentAyah() {
    if (!isPlaying) return;
    if (currentAyahIndex >= versesData.length) {
        onBlockCompleted();
        return;
    }

    const currentVerse = versesData[currentAyahIndex];
    audioPlayer.src = currentVerse.audio_url;
    audioPlayer.playbackRate = parseFloat(document.getElementById('select-speed').value) || 1.0;

    highlightActiveCard();
    updateCounters();

    audioPlayer.play().catch(err => {
        console.warn('Audio play error:', err);
    });
}

// Callback saat satu ayat selesai diputar
function onAudioEnded() {
    const maxVerseRepeat = parseInt(document.getElementById('select-repeat-verse').value) || 1;
    const pauseDuration = parseFloat(document.getElementById('select-pause').value) || 0;

    // Helper jeda audio sebelum ayat berikutnya
    const scheduleNext = (delayMs = pauseDuration * 1000) => {
        if (!isPlaying) return;
        if (delayMs > 0) {
            pauseTimeout = setTimeout(() => {
                if (isPlaying) playCurrentAyah();
            }, delayMs);
        } else {
            playCurrentAyah();
        }
    };

    // ==============================================================
    // 1. MODE STANDAR (Bukan Mode Sambung Akumulatif)
    // ==============================================================
    if (!isAccumulativeMode) {
        if (currentVerseRepeatCount < maxVerseRepeat) {
            // Ulangi ayat yang sama
            currentVerseRepeatCount++;
            updateCounters();
            scheduleNext();
        } else {
            // Lanjut ke ayat berikutnya dalam rentang
            currentVerseRepeatCount = 1;
            currentAyahIndex++;

            if (currentAyahIndex < versesData.length) {
                updateCounters();
                scheduleNext();
            } else {
                // Seluruh rentang ayat selesai
                onBlockCompleted();
            }
        }
        return;
    }

    // ==============================================================
    // 2. MODE SAMBUNG AKUMULATIF (Tikrar Berantai / Tangga Hafalan)
    // ==============================================================
    const maxLinkRepeat = parseInt(document.getElementById('select-link-repeat')?.value) || 1;

    if (accumulativePhase === 'drill') {
        // Sedang mengulang ayat target baru (frontierIndex)
        if (currentVerseRepeatCount < maxVerseRepeat) {
            currentVerseRepeatCount++;
            updateCounters();
            scheduleNext();
        } else {
            // Pengulangan ayat baru selesai!
            currentVerseRepeatCount = 1;

            if (frontierIndex === 0) {
                // Ayat pertama selesai di-drill. Jika ada ayat ke-2 dst, lanjut drill ayat ke-2
                if (versesData.length > 1) {
                    frontierIndex = 1;
                    accumulativePhase = 'drill';
                    currentAyahIndex = frontierIndex;
                    updateCounters();
                    showToast(`Ayat ${versesData[0].nomor_ayat} selesai (${maxVerseRepeat}x). Lanjut fokus Ayat ${versesData[frontierIndex].nomor_ayat}!`, 'info');
                    scheduleNext();
                } else {
                    onBlockCompleted();
                }
            } else {
                // Ayat target (misal ayat 2, 3, dst) sudah diulang N kali.
                // Masuk ke FASE SAMBUNG: Baca berurutan dari ayat 1 s/d ayat target ini!
                accumulativePhase = 'link';
                linkCurrentIndex = 0;
                currentAyahIndex = 0;
                currentLinkPass = 1;
                updateCounters();
                showToast(`🔗 Fase Sambung: Mengulang sambungan Ayat ${versesData[0].nomor_ayat} s/d ${versesData[frontierIndex].nomor_ayat}`, 'info');
                scheduleNext();
            }
        }
    } else if (accumulativePhase === 'link') {
        // Sedang menyambung dari indeks 0 s/d frontierIndex
        if (linkCurrentIndex < frontierIndex) {
            linkCurrentIndex++;
            currentAyahIndex = linkCurrentIndex;
            updateCounters();
            scheduleNext();
        } else {
            // Sudah sampai di ujung sambung (linkCurrentIndex === frontierIndex)
            if (currentLinkPass < maxLinkRepeat) {
                currentLinkPass++;
                linkCurrentIndex = 0;
                currentAyahIndex = 0;
                updateCounters();
                scheduleNext();
            } else {
                // Selesai satu siklus sambung!
                currentLinkPass = 1;

                if (frontierIndex < versesData.length - 1) {
                    // Masih ada ayat berikutnya yang belum dihafal
                    frontierIndex++;
                    accumulativePhase = 'drill';
                    currentAyahIndex = frontierIndex;
                    currentVerseRepeatCount = 1;
                    updateCounters();
                    showToast(`🎯 Lanjut fokus hafal Ayat baru: Ayat ${versesData[frontierIndex].nomor_ayat} (${maxVerseRepeat}x)`, 'success');
                    scheduleNext(Math.max(1000, pauseDuration * 1000));
                } else {
                    // Seluruh ayat dari awal s/d akhir sudah dihafal dan disambung!
                    onBlockCompleted();
                }
            }
        }
    }
}

// Callback saat satu putaran blok selesai
function onBlockCompleted() {
    const maxBlockRepeat = parseInt(document.getElementById('select-repeat-block').value) || 1;

    if (currentBlockRepeatCount < maxBlockRepeat) {
        currentBlockRepeatCount++;
        currentAyahIndex = 0;
        frontierIndex = 0;
        accumulativePhase = 'drill';
        linkCurrentIndex = 0;
        currentLinkPass = 1;
        currentVerseRepeatCount = 1;
        updateCounters();
        showToast(`Putaran Blok ke-${currentBlockRepeatCount} dimulai!`, 'info');

        const pauseDuration = parseFloat(document.getElementById('select-pause').value) || 0;
        setTimeout(() => {
            if (isPlaying) playCurrentAyah();
        }, Math.max(1000, pauseDuration * 1000));
    } else {
        // Seluruh pengulangan selesai sempurna!
        stopPlayback();
        showToast('Alhamdulillah! Seluruh target pengulangan hafalan selesai.', 'success');
    }
}

function onAudioError(e) {
    console.warn('Audio stream error, trying fallback:', e);
    showToast('Sedang memuat audio Qari...', 'warning');
}

// Lompat langsung ke ayat tertentu
function jumpToAyah(index, autoPlay = false) {
    currentAyahIndex = index;
    frontierIndex = index;
    accumulativePhase = 'drill';
    linkCurrentIndex = index;
    currentVerseRepeatCount = 1;
    currentLinkPass = 1;
    highlightActiveCard();
    updateCounters();

    if (autoPlay || isPlaying) {
        isPlaying = true;
        updatePlayerUI();
        playCurrentAyah();
    }
}

function playNextAyah() {
    if (currentAyahIndex < versesData.length - 1) {
        currentAyahIndex++;
        frontierIndex = currentAyahIndex;
        accumulativePhase = 'drill';
        currentVerseRepeatCount = 1;
        currentLinkPass = 1;
        playCurrentAyah();
    }
}

function playPreviousAyah() {
    if (currentAyahIndex > 0) {
        currentAyahIndex--;
        frontierIndex = currentAyahIndex;
        accumulativePhase = 'drill';
        currentVerseRepeatCount = 1;
        currentLinkPass = 1;
        playCurrentAyah();
    }
}

// Highlight kartu ayat aktif dan auto-scroll
function highlightActiveCard() {
    document.querySelectorAll('.verse-card').forEach((card, idx) => {
        const isActive = (idx === currentAyahIndex);
        const isInLinkChain = isAccumulativeMode && accumulativePhase === 'link' && (idx <= frontierIndex);

        if (isActive) {
            card.classList.add('ring-2', 'ring-emerald-500', 'bg-emerald-50/80', 'border-emerald-400', 'shadow-md', 'scale-[1.01]');
            card.classList.remove('bg-white', 'border-slate-200/90', 'shadow-xs', 'bg-teal-50/40', 'border-teal-300');
            // Auto scroll smoothly to active ayah
            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else if (isInLinkChain) {
            // Berada di dalam rantai sambung
            card.classList.add('bg-teal-50/40', 'border-teal-300', 'shadow-xs');
            card.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-50/80', 'border-emerald-400', 'scale-[1.01]', 'bg-white');
        } else {
            card.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-50/80', 'border-emerald-400', 'shadow-md', 'scale-[1.01]', 'bg-teal-50/40', 'border-teal-300');
            card.classList.add('bg-white', 'border-slate-200/90', 'shadow-xs');
        }
    });

    if (versesData[currentAyahIndex]) {
        document.getElementById('label-active-ayah').textContent = versesData[currentAyahIndex].nomor_ayat;
    }
}

// Update tampilan teks counter pengulangan
function updateCounters() {
    const maxVerse = parseInt(document.getElementById('select-repeat-verse').value) || 1;
    const maxBlock = document.getElementById('select-repeat-block').value;

    document.getElementById('counter-verse-rep').textContent = currentVerseRepeatCount;
    document.getElementById('max-verse-rep').textContent = maxVerse;

    document.getElementById('counter-block-rep').textContent = currentBlockRepeatCount;
    document.getElementById('max-block-rep').textContent = maxBlock == 999 ? '∞' : maxBlock;

    const accumHint = document.getElementById('label-accumulative-hint');
    const phaseBadge = document.getElementById('player-phase-badge');

    if (isAccumulativeMode && versesData.length > 0 && versesData[frontierIndex]) {
        if (accumHint) accumHint.classList.remove('hidden');
        if (phaseBadge) phaseBadge.classList.remove('hidden');

        const activeVerseNum = versesData[currentAyahIndex] ? versesData[currentAyahIndex].nomor_ayat : '-';
        const frontierVerseNum = versesData[frontierIndex].nomor_ayat;
        const startVerseNum = versesData[0].nomor_ayat;

        if (accumulativePhase === 'drill') {
            if (phaseBadge) {
                phaseBadge.className = 'text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wider bg-amber-400 text-amber-950 border border-amber-300';
                phaseBadge.innerHTML = `<i class="fa-solid fa-crosshairs mr-1"></i> Fokus Baru: Ayat ${frontierVerseNum}`;
            }
            if (accumHint) {
                accumHint.innerHTML = `<i class="fa-solid fa-bullseye mr-1 text-amber-400"></i> Ulang Ayat Baru (${currentVerseRepeatCount}/${maxVerse})`;
            }
        } else {
            // Fase Sambung ('link')
            if (phaseBadge) {
                phaseBadge.className = 'text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wider bg-teal-400 text-teal-950 border border-teal-300';
                phaseBadge.innerHTML = `<i class="fa-solid fa-link mr-1"></i> Sambung: Ayat ${startVerseNum} s/d ${frontierVerseNum}`;
            }
            if (accumHint) {
                accumHint.innerHTML = `<i class="fa-solid fa-link mr-1 text-teal-300"></i> Rantai: Ayat ${activeVerseNum} (dari ${startVerseNum} s/d ${frontierVerseNum})`;
            }
        }
    } else {
        if (accumHint) accumHint.classList.add('hidden');
        if (phaseBadge) phaseBadge.classList.add('hidden');
    }
}

// Update UI tombol Play / Pause
function updatePlayerUI() {
    const btn = document.getElementById('btn-play-pause');
    const icon = document.getElementById('icon-play-pause');
    const dot = document.getElementById('player-indicator-dot');
    const badge = document.getElementById('player-status-badge');

    if (isPlaying) {
        icon.className = 'fa-solid fa-pause';
        dot.className = 'w-3 h-3 rounded-full bg-emerald-400 animate-ping';
        badge.textContent = 'Sedang Memutar...';
        badge.className = 'text-[11px] font-extrabold uppercase tracking-widest text-emerald-400';
    } else {
        icon.className = 'fa-solid fa-play ml-1';
        dot.className = 'w-3 h-3 rounded-full bg-slate-500';
        badge.textContent = 'Jeda / Siap';
        badge.className = 'text-[11px] font-extrabold uppercase tracking-widest text-slate-400';
    }
}

// Event Handlers Pilihan Filter
function onJuzFilterChange(juzNumber) {
    // Cari surah pertama yang ada di juz ini
    const selectSurah = document.getElementById('select-surah');
    for (let opt of selectSurah.options) {
        if (opt.getAttribute('data-juz') == juzNumber) {
            selectSurah.value = opt.value;
            onSurahChange(opt.value);
            break;
        }
    }
}

function onSurahChange(surahId) {
    stopPlayback();
    currentSurahId = parseInt(surahId);

    const selectSurah = document.getElementById('select-surah');
    const opt = selectSurah.options[selectSurah.selectedIndex];
    const totalAyat = parseInt(opt.getAttribute('data-ayat')) || 10;

    // Reset rentang ayat default: 1 sampai min(5, totalAyat)
    currentAyatMulai = 1;
    currentAyatSelesai = Math.min(5, totalAyat);

    document.getElementById('input-ayat-mulai').value = currentAyatMulai;
    document.getElementById('input-ayat-selesai').value = currentAyatSelesai;
    document.getElementById('input-ayat-mulai').max = totalAyat;
    document.getElementById('input-ayat-selesai').max = totalAyat;

    loadVerses();
}

function onAyatRangeChange() {
    stopPlayback();
    currentAyatMulai = parseInt(document.getElementById('input-ayat-mulai').value) || 1;
    currentAyatSelesai = parseInt(document.getElementById('input-ayat-selesai').value) || currentAyatMulai;

    if (currentAyatSelesai < currentAyatMulai) {
        currentAyatSelesai = currentAyatMulai;
        document.getElementById('input-ayat-selesai').value = currentAyatSelesai;
    }

    loadVerses();
}

function setAyatPreset(mulai, selesai) {
    stopPlayback();
    const selectSurah = document.getElementById('select-surah');
    const totalAyat = parseInt(selectSurah.options[selectSurah.selectedIndex].getAttribute('data-ayat')) || 100;

    currentAyatMulai = Math.max(1, Math.min(totalAyat, mulai));
    currentAyatSelesai = Math.max(currentAyatMulai, Math.min(totalAyat, selesai));

    document.getElementById('input-ayat-mulai').value = currentAyatMulai;
    document.getElementById('input-ayat-selesai').value = currentAyatSelesai;

    loadVerses();
}

function stepBlockAyat(step) {
    stopPlayback();
    const rangeSize = (currentAyatSelesai - currentAyatMulai) + 1;
    const selectSurah = document.getElementById('select-surah');
    const totalAyat = parseInt(selectSurah.options[selectSurah.selectedIndex].getAttribute('data-ayat')) || 100;

    let newMulai = currentAyatMulai + step;
    let newSelesai = newMulai + rangeSize - 1;

    if (newMulai < 1) {
        newMulai = 1;
        newSelesai = Math.min(totalAyat, rangeSize);
    }
    if (newSelesai > totalAyat) {
        newSelesai = totalAyat;
        newMulai = Math.max(1, newSelesai - rangeSize + 1);
    }

    currentAyatMulai = newMulai;
    currentAyatSelesai = newSelesai;

    document.getElementById('input-ayat-mulai').value = currentAyatMulai;
    document.getElementById('input-ayat-selesai').value = currentAyatSelesai;

    loadVerses();
}

function onQariChange(qariId) {
    currentQari = qariId;
    loadVerses();
}

function changePlaybackSpeed(speed) {
    audioPlayer.playbackRate = parseFloat(speed) || 1.0;
}

// Mode Uji Hafalan (Tutup Teks Arab)
function toggleBlindTest() {
    isBlindTest = !isBlindTest;
    const btn = document.getElementById('btn-blind-test');
    if (isBlindTest) {
        btn.classList.add('bg-amber-100', 'text-amber-900', 'border-amber-300');
        btn.innerHTML = `<i class="fa-solid fa-eye text-amber-600"></i> Buka Teks Arab`;
    } else {
        btn.classList.remove('bg-amber-100', 'text-amber-900', 'border-amber-300');
        btn.innerHTML = `<i class="fa-solid fa-eye-slash text-slate-500"></i> Uji Hafalan (Tutup Teks)`;
    }
    renderVerses();
}

function toggleTranslation() {
    showTranslation = !showTranslation;
    const btn = document.getElementById('btn-toggle-trans');
    btn.innerHTML = `<i class="fa-solid fa-language text-slate-500"></i> Terjemahan: ${showTranslation ? 'On' : 'Off'}`;
    renderVerses();
}

function updateTotalAyatLabel() {
    const count = (currentAyatSelesai - currentAyatMulai) + 1;
    document.getElementById('label-total-ayat-selected').textContent = `${count} Ayat Terpilih (${currentAyatMulai} - ${currentAyatSelesai})`;
}

// Arahkan langsung ke form Setoran Ustadz
function goToSetoran() {
    window.location.href = `setoran?surah_id=${currentSurahId}&ayat_mulai=${currentAyatMulai}&ayat_selesai=${currentAyatSelesai}&santri_id=<?= $selectedSantriId ?>`;
}

// Audio Recording & Self Evaluation Feature
let mediaRecorder = null;
let audioChunks = [];
let isRecording = false;
let recordTimerInterval = null;
let recordSeconds = 0;
let studentAudioBlob = null;
const studentAudioPlayer = document.getElementById('student-audio-playback');

async function toggleRecording() {
    if (!isRecording) {
        startRecording();
    } else {
        stopRecording();
    }
}

async function startRecording() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        audioChunks = [];

        mediaRecorder.ondataavailable = e => {
            if (e.data.size > 0) audioChunks.push(e.data);
        };

        mediaRecorder.onstop = () => {
            studentAudioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            studentAudioPlayer.src = URL.createObjectURL(studentAudioBlob);
            document.getElementById('recording-playback-box').classList.remove('hidden');
            stream.getTracks().forEach(track => track.stop());
        };

        mediaRecorder.start();
        isRecording = true;
        recordSeconds = 0;

        document.getElementById('icon-record').className = 'fa-solid fa-square';
        document.getElementById('text-record').textContent = 'Selesai Rekam';
        document.getElementById('btn-record').className = 'inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md transition-all animate-pulse';

        recordTimerInterval = setInterval(() => {
            recordSeconds++;
            const m = String(Math.floor(recordSeconds / 60)).padStart(2, '0');
            const s = String(recordSeconds % 60).padStart(2, '0');
            document.getElementById('record-timer').textContent = `${m}:${s}`;
        }, 1000);

        showToast('Perekaman dimulai, silakan bacakan ayatnya...', 'info');
    } catch (err) {
        console.error('Mikrofon error:', err);
        showToast('Izin mikrofon diperlukan untuk merekam bacaan Anda.', 'danger');
    }
}

function stopRecording() {
    if (mediaRecorder && isRecording) {
        mediaRecorder.stop();
        isRecording = false;
        clearInterval(recordTimerInterval);

        document.getElementById('icon-record').className = 'fa-solid fa-circle';
        document.getElementById('text-record').textContent = 'Rekam Ulang';
        document.getElementById('btn-record').className = 'inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md transition-all active:scale-95';

        showToast('Rekaman tersimpan! Klik "Putar Rekaman Saya" untuk mendengarkan.', 'success');
    }
}

function playStudentRecording() {
    if (studentAudioBlob && studentAudioPlayer.src) {
        if (isPlaying) {
            pausePlayback();
        }
        studentAudioPlayer.play();
        const btn = document.getElementById('btn-play-student');
        btn.innerHTML = `<i class="fa-solid fa-volume-high animate-bounce"></i> Memutar...`;
        studentAudioPlayer.onended = () => {
            btn.innerHTML = `<i class="fa-solid fa-play"></i> Putar Rekaman Saya`;
        };
    }
}
</script>

