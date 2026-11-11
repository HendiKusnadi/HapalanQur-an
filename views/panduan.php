<?php
declare(strict_types=1);

use App\Auth;

$activeTab = $_GET['tab'] ?? (Auth::check() ? 'admin' : 'santri');
if (!in_array($activeTab, ['santri', 'admin', 'laporan', 'teknis', 'faq'], true)) {
    $activeTab = 'santri';
}
?>

<div class="space-y-6 max-w-6xl mx-auto">

    <!-- Header Panduan Hero -->
    <div class="bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 max-w-3xl">
            <div class="inline-flex items-center gap-2 bg-emerald-600/40 border border-emerald-400/30 px-3 py-1 rounded-full text-xs font-bold text-amber-300 mb-3">
                <i class="fa-solid fa-book-open"></i> Buku Panduan Lengkap SIM Tahfidz Al-Qur'an
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                Buku Panduan Penggunaan Lengkap Aplikasi
            </h1>
            <p class="text-emerald-100/90 text-xs sm:text-sm mt-2 leading-relaxed">
                Panduan komprehensif untuk Santri (Frontend Menghafal Tikrar, Metode Sambung Berantai, Uji Mandiri) serta Ustadz / Musyrif (Otomatisasi Setoran, Pusat Murojaah, Laporan Rinci Juz & Surah, Cetak PDF Resmi, dan Pengaturan Sistem).
            </p>
        </div>
        <div class="absolute -right-6 -bottom-8 text-white/5 text-[150px] font-quran select-none pointer-events-none">
            📖
        </div>
    </div>

    <!-- Tab Switcher Navigation (5 Tab Lengkap) -->
    <div class="flex items-center gap-1.5 p-1.5 bg-slate-200/80 rounded-2xl max-w-4xl mx-auto shadow-inner overflow-x-auto no-scrollbar">
        <button type="button" onclick="switchGuideTab('santri')" id="tab-btn-santri" class="flex-1 min-w-[120px] py-3 px-3 sm:px-4 rounded-xl transition-all flex items-center justify-center gap-1.5 <?= $activeTab === 'santri' ? 'bg-white text-emerald-800 shadow-md font-extrabold' : 'text-slate-600 hover:text-slate-900 font-bold' ?> text-xs sm:text-sm whitespace-nowrap">
            <i class="fa-solid fa-headphones-simple text-emerald-600"></i>
            <span>1. Menghafal (Santri)</span>
        </button>

        <button type="button" onclick="switchGuideTab('admin')" id="tab-btn-admin" class="flex-1 min-w-[120px] py-3 px-3 sm:px-4 rounded-xl transition-all flex items-center justify-center gap-1.5 <?= $activeTab === 'admin' ? 'bg-white text-emerald-800 shadow-md font-extrabold' : 'text-slate-600 hover:text-slate-900 font-bold' ?> text-xs sm:text-sm whitespace-nowrap">
            <i class="fa-solid fa-user-tie text-emerald-600"></i>
            <span>2. Setoran & Murojaah</span>
        </button>

        <button type="button" onclick="switchGuideTab('laporan')" id="tab-btn-laporan" class="flex-1 min-w-[120px] py-3 px-3 sm:px-4 rounded-xl transition-all flex items-center justify-center gap-1.5 <?= $activeTab === 'laporan' ? 'bg-white text-emerald-800 shadow-md font-extrabold' : 'text-slate-600 hover:text-slate-900 font-bold' ?> text-xs sm:text-sm whitespace-nowrap">
            <i class="fa-solid fa-file-invoice text-emerald-600"></i>
            <span>3. Laporan & Rapor</span>
        </button>

        <button type="button" onclick="switchGuideTab('teknis')" id="tab-btn-teknis" class="flex-1 min-w-[120px] py-3 px-3 sm:px-4 rounded-xl transition-all flex items-center justify-center gap-1.5 <?= $activeTab === 'teknis' ? 'bg-white text-emerald-800 shadow-md font-extrabold' : 'text-slate-600 hover:text-slate-900 font-bold' ?> text-xs sm:text-sm whitespace-nowrap">
            <i class="fa-solid fa-mobile-screen-button text-emerald-600"></i>
            <span>4. Teknis & APK HP</span>
        </button>

        <button type="button" onclick="switchGuideTab('faq')" id="tab-btn-faq" class="flex-1 min-w-[120px] py-3 px-3 sm:px-4 rounded-xl transition-all flex items-center justify-center gap-1.5 <?= $activeTab === 'faq' ? 'bg-white text-emerald-800 shadow-md font-extrabold' : 'text-slate-600 hover:text-slate-900 font-bold' ?> text-xs sm:text-sm whitespace-nowrap">
            <i class="fa-solid fa-circle-question text-amber-600"></i>
            <span>5. Tips & FAQ</span>
        </button>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 1: PANDUAN FRONTEND SANTRI (APLIKASI MENGHAFAL TIKRAR) -->
    <!-- ============================================================== -->
    <div id="guide-content-santri" class="<?= $activeTab === 'santri' ? 'block' : 'hidden' ?> space-y-6">

        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="w-11 h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-lg flex-shrink-0 shadow-sm">
                    <i class="fa-solid fa-headphones-simple"></i>
                </span>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Frontend Menghafal Al-Qur'an (Mode Santri / Umum)</h3>
                    <p class="text-xs text-slate-600 mt-0.5">Dapat diakses langsung oleh santri, wali santri, maupun masyarakat umum tanpa login pada alamat <code>/player</code>.</p>
                </div>
            </div>
            <a href="player" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-sm flex items-center gap-2 self-start sm:self-auto flex-shrink-0">
                <span>Buka Player Sekarang</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <!-- Grid Panduan Santri -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <!-- Card 1: Memilih Juz, Surah & Rentang Ayat -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center text-lg font-bold border border-teal-200">
                        1
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-teal-700 uppercase tracking-wider">Langkah Awal</span>
                        <h4 class="font-extrabold text-slate-800 text-base">Memilih Juz, Surah & Rentang Ayat</h4>
                    </div>
                </div>
                <ul class="text-xs text-slate-600 space-y-2.5 leading-relaxed">
                    <li>• <b>Filter Berdasarkan Juz</b>: Klik dropdown <i>"Filter Juz"</i> (Juz 1 s/d 30). Sistem secara otomatis menyaring pilihan surah yang hanya berada di juz tersebut. Contoh: Memilih <i>Juz 30</i> akan menampilkan An-Naba' s/d An-Nas.</li>
                    <li>• <b>Pilih Surah</b>: Pilih salah satu dari 114 Surah Al-Qur'an lengkap dengan nama latin, tulisan Arab, arti, dan total ayatnya.</li>
                    <li>• <b>Tentukan Rentang Ayat</b>: Masukkan ayat mulai dan ayat selesai pada kolom yang tersedia (misal: <code>Ayat 1 s/d 5</code> atau <code>Ayat 11 s/d 15</code>).</li>
                    <li>• <b>Tombol Pintas Navigasi Cepat</b>:
                        <div class="flex flex-wrap gap-1.5 mt-1.5">
                            <span class="bg-slate-100 border border-slate-200 px-2 py-0.5 rounded text-[11px] font-mono">Ayat 1-5</span>
                            <span class="bg-slate-100 border border-slate-200 px-2 py-0.5 rounded text-[11px] font-mono">Ayat 6-10</span>
                            <span class="bg-slate-100 border border-slate-200 px-2 py-0.5 rounded text-[11px] font-mono">⏮ 5 Ayat Sebelumnya</span>
                            <span class="bg-slate-100 border border-slate-200 px-2 py-0.5 rounded text-[11px] font-mono">⏭ 5 Ayat Berikutnya</span>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Card 2: Pengaturan Tikrar Terstruktur -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center text-lg font-bold border border-teal-200">
                        2
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-teal-700 uppercase tracking-wider">Konfigurasi Player</span>
                        <h4 class="font-extrabold text-slate-800 text-base">Atur Pengulangan Tikrar & Jeda Audio</h4>
                    </div>
                </div>
                <ul class="text-xs text-slate-600 space-y-2.5 leading-relaxed">
                    <li>• <b>Ulang Tiap Ayat (Per-Ayat Repetition)</b>: Tentukan berapa kali sebuah ayat diulang sebelum pindah ke ayat berikutnya (Pilihan: 1x, 3x, 5x, 7x, 10x, 20x, atau <b>40x Mutqin</b>).</li>
                    <li>• <b>Ulang Seluruh Rentang (Range Loop)</b>: Tentukan berapa kali seluruh paket ayat (misal ayat 1-5) diputar kembali dari awal (1x, 3x, 5x, 10x, atau <i>Loop Tanpa Batas</i>).</li>
                    <li>• <b>Jeda Menirukan (Echo Pause)</b>: Aktifkan jeda waktu (1.5 detik s/d 5 detik) setelah ayat berbunyi agar santri dapat melafalkan dan menirukan bacaan Qari secara runtut.</li>
                    <li>• <b>Kecepatan Audio</b>: Pilih <code>0.75x</code> untuk tartil lambat bertajwid, <code>1.0x</code> normal, atau <code>1.25x</code> untuk tempo sedang.</li>
                </ul>
            </div>

            <!-- Card 3: Metode Sambung Akumulatif (FITUR UNGGULAN) -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border-2 border-amber-300 md:col-span-2 space-y-4 bg-gradient-to-br from-amber-50/50 via-white to-emerald-50/40">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-amber-400 text-amber-950 flex items-center justify-center text-xl font-extrabold shadow-sm">
                        <i class="fa-solid fa-stairs"></i>
                    </div>
                    <div>
                        <span class="text-[10px] bg-amber-400 text-amber-950 px-2 py-0.5 rounded-full font-extrabold uppercase">Metode Tahfidz Unggulan</span>
                        <h4 class="font-extrabold text-slate-900 text-lg">Metode Sambung Akumulatif (Tikrar Berantai / Tangga Hafalan)</h4>
                    </div>
                </div>

                <p class="text-xs text-slate-700 leading-relaxed">
                    Seringkali santri hafal ayat 1, ayat 2, dan ayat 3 secara terpisah, namun saat membaca bersambung santri lupa sambungan ayat berikutnya. Fitur <b>Metode Sambung Akumulatif</b> ini didesain khusus melatih kekuatan sambungan hafalan (*wasl*) antar ayat layaknya menaiki anak tangga:
                </p>

                <!-- Skema Visual Tangga Interaktif -->
                <div class="p-4 bg-white rounded-2xl border border-amber-200 text-xs space-y-3 shadow-inner">
                    <div class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-route text-amber-600"></i>
                        <span>Ilustrasi Alur Kerja Sistem (Contoh: Menghafal Ayat 1 s/d 4 dengan Pengulangan 3x):</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs font-mono">
                        <div class="p-3 rounded-xl bg-amber-50 border border-amber-300">
                            <span class="text-[10px] font-bold text-amber-800 block mb-1">LANGKAH 1 (Fokus)</span>
                            <b>Ayat 1</b> diputar <b>3x</b>
                            <div class="text-[10px] text-amber-700 mt-1 font-sans">Santri menghafal matang ayat pertama.</div>
                        </div>
                        <div class="p-3 rounded-xl bg-teal-50 border border-teal-300">
                            <span class="text-[10px] font-bold text-teal-800 block mb-1">LANGKAH 2 (Fokus + Sambung)</span>
                            <b>Ayat 2</b> diputar <b>3x</b><br>
                            <span class="text-emerald-700 font-bold block mt-1 font-sans">🔗 Putar Sambung: Ayat 1 &rarr; Ayat 2</span>
                        </div>
                        <div class="p-3 rounded-xl bg-amber-50 border border-amber-300">
                            <span class="text-[10px] font-bold text-amber-800 block mb-1">LANGKAH 3 (Fokus + Sambung)</span>
                            <b>Ayat 3</b> diputar <b>3x</b><br>
                            <span class="text-emerald-700 font-bold block mt-1 font-sans">🔗 Putar Sambung: Ayat 1 &rarr; Ayat 2 &rarr; Ayat 3</span>
                        </div>
                        <div class="p-3 rounded-xl bg-teal-50 border border-teal-300">
                            <span class="text-[10px] font-bold text-teal-800 block mb-1">LANGKAH 4 (Tuntas Sempurna)</span>
                            <b>Ayat 4</b> diputar <b>3x</b><br>
                            <span class="text-emerald-700 font-bold block mt-1 font-sans">🔗 Putar Sambung: Ayat 1 &rarr; Ayat 2 &rarr; Ayat 3 &rarr; Ayat 4</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs text-slate-600">
                    <div class="p-3 bg-white/80 rounded-xl border border-slate-200">
                        <b class="text-slate-800">Cara Mengaktifkan:</b><br>
                        Beri tanda centang pada kotak <b>[✓] Metode Sambung Akumulatif</b> di bar kontrol player, atau klik tombol status hingga menjadi hijau menyala bertuliskan <b>`Aktif ✓`</b>.
                    </div>
                    <div class="p-3 bg-white/80 rounded-xl border border-slate-200">
                        <b class="text-slate-800">Membaca Indikator Visual di Layar:</b><br>
                        Saat ayat baru dihafal akan berstatus <span class="bg-amber-100 text-amber-900 font-bold px-1.5 py-0.5 rounded">🎯 Fokus Target</span>, dan saat menyambung berstatus <span class="bg-teal-100 text-teal-900 font-bold px-1.5 py-0.5 rounded">🔗 Fase Sambung</span> dengan kartu ayat tersorot toska secara berurutan.
                    </div>
                </div>
            </div>

            <!-- Card 4: 12 Pilihan Qari Masyhur -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center text-lg font-bold border border-teal-200">
                        4
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-teal-700 uppercase tracking-wider">Pilihan Suara Audio</span>
                        <h4 class="font-extrabold text-slate-800 text-base">12 Pilihan Pengisi Suara (Qari Ternama)</h4>
                    </div>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Setiap santri memiliki karakter suara dan kenyamanan irama yang berbeda. Aplikasi menyediakan audio resolusi tinggi resmi dari para Imam Masjidil Haram, Masjid Nabawi, dan pakar tajwid dunia:
                </p>
                <div class="grid grid-cols-2 gap-2 text-xs text-slate-700 font-medium">
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-200">• Misyari Rasyid Al-Afasy</div>
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-200">• Mahmoud Khalil Al-Hushari</div>
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-200">• Muhammad Siddiq Al-Minshawi</div>
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-200">• Abdurrahman As-Sudais</div>
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-200">• Sa'ud Ash-Shuraim</div>
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-200">• Saad Al-Ghamdi</div>
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-200">• Maher Al-Muaiqly</div>
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-200">• Abu Bakr Asy-Syathiri</div>
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-200">• Hani Ar-Rifai</div>
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-200">• Yasser Ad-Dussary</div>
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-200">• Ali Al-Hudhaify</div>
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-200">• Al-Hushari (Mujawwad)</div>
                </div>
            </div>

            <!-- Card 5: Uji Hafalan (Blind Test) & Rekam Suara -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center text-lg font-bold border border-teal-200">
                        5
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-teal-700 uppercase tracking-wider">Evaluasi Mandiri</span>
                        <h4 class="font-extrabold text-slate-800 text-base">Uji Hafalan (Tutup Teks) & Rekam Mandiri</h4>
                    </div>
                </div>
                <ul class="text-xs text-slate-600 space-y-2.5 leading-relaxed">
                    <li>• <b>Uji Hafalan (Blind Test / Tutup Teks)</b>: Klik tombol <code>[Uji Hafalan (Tutup Teks)]</code>. Teks mushaf akan disamarkan/diburamkan secara otomatis sehingga santri ditantang membaca murni dari ingatan. Klik pada ayat terkait jika ingin melihat bocoran lafadz.</li>
                    <li>• <b>Perekam Suara Mandiri (Voice Recorder)</b>: Klik tombol <code>[Mulai Rekam]</code> untuk merekam bacaan hafalan santri menggunakan mikrofon HP atau laptop. Setelah selesai, klik <code>[Hentikan Rekam]</code> lalu tekan <code>[Putar Rekaman Saya]</code> untuk mengevaluasi fashohah dan tajwid sendiri.</li>
                    <li>• <b>Tombol Pintas "Setor ke Ustadz"</b>: Begitu santri merasa sudah lancar dan mutqin, klik tombol emas <code>[Sudah Lancar? Setor ke Ustadz]</code> di bar pemutar. Sistem akan mengarahkan ke form setoran dengan nama surah dan rentang ayat yang terisi otomatis.</li>
                </ul>
            </div>

        </div>

    </div>

    <!-- ============================================================== -->
    <!-- TAB 2: PANDUAN BACKEND USTADZ / ADMIN (SETORAN & MUROJAAH) -->
    <!-- ============================================================== -->
    <div id="guide-content-admin" class="<?= $activeTab === 'admin' ? 'block' : 'hidden' ?> space-y-6">

        <div class="p-4 bg-slate-900 text-white rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="w-11 h-11 rounded-2xl bg-amber-400 text-amber-950 flex items-center justify-center text-xl flex-shrink-0 font-bold shadow-sm">
                    <i class="fa-solid fa-user-shield"></i>
                </span>
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base">Portal Administrasi Ustadz, Musyrif & Pengasuh Tahfidz</h3>
                    <p class="text-xs text-slate-300 mt-0.5">Memerlukan hak akses login ustadz. Akun bawaan: Username <code>admin</code> | Password <code>admin123</code>.</p>
                </div>
            </div>
            <?php if (!Auth::check()): ?>
                <a href="login" class="bg-amber-400 hover:bg-amber-300 text-amber-950 text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-sm flex items-center gap-2 self-start sm:self-auto flex-shrink-0">
                    <i class="fa-solid fa-lock"></i>
                    <span>Login Ustadz Sekarang</span>
                </a>
            <?php else: ?>
                <span class="text-xs font-bold text-emerald-400 flex items-center gap-2 bg-emerald-950/80 px-3 py-1.5 rounded-xl border border-emerald-500/30">
                    <i class="fa-solid fa-circle-check"></i> Sesi Anda Sedang Aktif
                </span>
            <?php endif; ?>
        </div>

        <!-- Grid Panduan Admin -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <!-- Card 1: 3 Kategori Setoran -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-bold border border-emerald-200">
                        1
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-emerald-700 uppercase tracking-wider">Standar Tahfidz Pesantren</span>
                        <h4 class="font-extrabold text-slate-800 text-base">3 Kategori Setoran (Sabaq, Sabqi, Manzil)</h4>
                    </div>
                </div>
                <ul class="text-xs text-slate-600 space-y-2.5 leading-relaxed">
                    <li>• <b>1. Hafalan Baru (Sabaq / Ziyadah)</b>: Menambah ayat atau halaman baru yang belum pernah disetorkan. Sistem secara cerdas mendeteksi posisi terakhir santri dan langsung menyodorkan <i>Target Rekomendasi Berikutnya</i> (misal: jika terakhir An-Naba 1-10, sistem otomatis menyiapkan An-Naba 11-20).</li>
                    <li>• <b>2. Murojaah Baru (Sabqi)</b>: Mengulang 5 bagian hafalan baru terakhir. Bagian ini wajib disetorkan setiap hari sebelum santri menambah hafalan baru agar pondasi hafalan baru tidak cepat hilang.</li>
                    <li>• <b>3. Murojaah Lama (Manzil)</b>: Rotasi pengulangan juz-juz yang sudah tuntas dihafal. Sistem memantau tanggal terakhir setiap juz diulang dan memberi peringatan darurat jika ada juz yang lebih dari 7 hari belum pernah dimurojaah.</li>
                </ul>
            </div>

            <!-- Card 2: Logika Otomatisasi Tambah, Tetap & Kurang -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-bold border border-emerald-200">
                        2
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-emerald-700 uppercase tracking-wider">Kecerdasan Sistem</span>
                        <h4 class="font-extrabold text-slate-800 text-base">Otomatisasi Penambahan, Pengurangan & Rollback</h4>
                    </div>
                </div>
                <ul class="text-xs text-slate-600 space-y-2.5 leading-relaxed">
                    <li>• <b>Otomatis Tambah (Saat Lulus)</b>: Ketika nilai santri Mumtaz, Jayyid Jiddan, Jayyid, atau Maqbul pada kategori Hafalan Baru, sistem otomatis memajukan posisi terakhir santri (surah, ayat, juz, halaman) dan mengkalkulasi akumulasi capaian total juz.</li>
                    <li>• <b>Otomatis Kurang / Mundur (Saat Rasib / Mengulang)</b>: Jika hafalan santri dinilai <i>Rasib (Ulang)</i> atau santri menurunkan target, sistem memberikan opsi untuk memundurkan posisi dan mengurangkan capaian halaman secara otomatis.</li>
                    <li>• <b>Posisi Tetap (Murojaah)</b>: Untuk setoran murojaah Sabqi maupun Manzil, akumulasi juz tidak bertambah, namun riwayat tanggal dan kualitas nilai tersimpan rapi.</li>
                    <li>• <b>Fitur Hapus & Auto-Rollback</b>: Jika ustadz keliru memasukkan data, cukup klik tombol hapus di Kartu Kendali. Sistem otomatis mengembalikan posisi dan akumulasi hafalan santri ke kondisi semula secara matematis.</li>
                </ul>
            </div>

            <!-- Card 3: Kartu Kendali & Peta Visual 30 Juz -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-bold border border-emerald-200">
                        3
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-emerald-700 uppercase tracking-wider">Pemantauan Individual</span>
                        <h4 class="font-extrabold text-slate-800 text-base">Kartu Kendali & Peta Visual 30 Juz Santri</h4>
                    </div>
                </div>
                <ul class="text-xs text-slate-600 space-y-2.5 leading-relaxed">
                    <li>• <b>Peta Visual 30 Kotak Juz</b>: Tampilan grid 30 kotak interaktif dengan kode warna yang jelas:
                        <br>&bull; <span class="bg-emerald-100 text-emerald-800 font-bold px-1.5 py-0.5 rounded text-[11px]">Hijau</span> : Juz telah selesai disetorkan tuntas.
                        <br>&bull; <span class="bg-blue-100 text-blue-800 font-bold px-1.5 py-0.5 rounded text-[11px]">Biru</span> : Juz sedang berjalan (posisi aktif santri saat ini).
                        <br>&bull; <span class="bg-slate-100 text-slate-700 font-bold px-1.5 py-0.5 rounded text-[11px]">Abu-abu</span> : Juz belum pernah disentuh.
                    </li>
                    <li>• <b>Tombol Penyesuaian Cepat (Quick Adjustment)</b>: Tersedia tombol pintas <code>[+1 Hal]</code>, <code>[+0.5 Hal]</code>, <code>[-0.5 Hal]</code>, dan <code>[-1 Hal]</code> untuk melakukan koreksi capaian secara instan tanpa perlu menghapus riwayat setoran.</li>
                    <li>• <b>Riwayat Kronologis & Catatan Makhraj</b>: Menampilkan tanggal setoran, musyrif penguji, penilaian bintang, dan catatan catatan tajwid yang perlu diperbaiki santri.</li>
                </ul>
            </div>

            <!-- Card 4: Pusat Kendali Murojaah -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-bold border border-emerald-200">
                        4
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-emerald-700 uppercase tracking-wider">Sentralisasi Mutqin</span>
                        <h4 class="font-extrabold text-slate-800 text-base">Pusat Kendali Murojaah Santri</h4>
                    </div>
                </div>
                <ul class="text-xs text-slate-600 space-y-2.5 leading-relaxed">
                    <li>• <b>Menu Terpusat <code>/murojaah</code></b>: Ustadz dapat memantau antrean murojaah seluruh santri sekaligus dalam satu layar tanpa perlu membuka satu demi satu profil santri.</li>
                    <li>• <b>Kolom Sabqi (Murojaah Baru)</b>: Daftar santri dengan paket 5 setoran terakhir yang harus disetor hari ini. Terdapat tombol <code>[Setorkan]</code> yang langsung mengisi formulir setoran.</li>
                    <li>• <b>Kolom Manzil (Rotasi Murojaah Lama)</b>: Sistem mengurutkan santri berdasarkan juz yang paling darurat belum dimurojaah. Juz dengan label merah menandakan telah lewat dari 7 hari belum diulang.</li>
                </ul>
            </div>

        </div>

    </div>

    <!-- ============================================================== -->
    <!-- TAB 3: PANDUAN LAPORAN SISWA, REKAP & RAPOR (PDF & EXCEL) -->
    <!-- ============================================================== -->
    <div id="guide-content-laporan" class="<?= $activeTab === 'laporan' ? 'block' : 'hidden' ?> space-y-6">

        <div class="p-4 bg-teal-50 border border-teal-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="w-11 h-11 rounded-2xl bg-teal-700 text-white flex items-center justify-center text-xl flex-shrink-0 shadow-sm">
                    <i class="fa-solid fa-file-invoice"></i>
                </span>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Laporan Detail Santri Per Juz & Surah + Ekspor Excel & PDF</h3>
                    <p class="text-xs text-slate-600 mt-0.5">Laporan transparan yang merinci status setiap surat di bawah setiap juz untuk wali santri, ustadz, dan arsip pesantren.</p>
                </div>
            </div>
            <a href="laporan-santri" class="bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-sm flex items-center gap-2 self-start sm:self-auto flex-shrink-0">
                <span>Buka Laporan Siswa</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <!-- Card 1: Cara Membaca Status Surat -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center text-lg font-bold border border-teal-200">
                        1
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-teal-700 uppercase tracking-wider">Detail Per Surah</span>
                        <h4 class="font-extrabold text-slate-800 text-base">3 Klasifikasi Status Surat di Bawah Setiap Juz</h4>
                    </div>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Setiap Juz (1 s/d 30) dibedah seluruh surat yang tercakup di dalamnya, dengan 3 indikator status yang sangat jelas:
                </p>
                <div class="space-y-2.5 text-xs">
                    <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-300">
                        <span class="inline-flex items-center gap-1 bg-emerald-700 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full mb-1">
                            <i class="fa-solid fa-check-circle"></i> HAPAL TUNTAS
                        </span>
                        <div class="font-bold text-emerald-950">Seluruh ayat pada surat tersebut telah tuntas disetorkan.</div>
                        <p class="text-[11px] text-emerald-800 mt-0.5">Ditampilkan progress bar 100% dan keterangan jumlah ayat lengkap (contoh: QS. An-Naba' : 1-40).</p>
                    </div>

                    <div class="p-3 rounded-2xl bg-amber-50 border border-amber-300">
                        <span class="inline-flex items-center gap-1 bg-amber-500 text-amber-950 text-[10px] font-extrabold px-2 py-0.5 rounded-full mb-1">
                            <i class="fa-solid fa-clock"></i> BELUM TUNTAS - Ayat X s/d Y
                        </span>
                        <div class="font-bold text-amber-950">Santri baru menghafal sebagian ayat dari surat tersebut.</div>
                        <p class="text-[11px] text-amber-800 mt-0.5">Sistem menginformasikan ayat berapa sampai berapa yang sudah disetorkan (misal: Ayat 1-15 dari 40 ayat) beserta persentase capaian.</p>
                    </div>

                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-300">
                        <span class="inline-flex items-center gap-1 bg-slate-300 text-slate-700 text-[10px] font-extrabold px-2 py-0.5 rounded-full mb-1">
                            <i class="fa-solid fa-circle-minus"></i> BELUM DIHAFAL
                        </span>
                        <div class="font-bold text-slate-700">Surat belum pernah disetorkan sama sekali oleh santri.</div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Kotak Pencarian & Filter Cepat -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center text-lg font-bold border border-teal-200">
                        2
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-teal-700 uppercase tracking-wider">Pencarian Fleksibel</span>
                        <h4 class="font-extrabold text-slate-800 text-base">Pencarian Siswa, Filter Juz & Status</h4>
                    </div>
                </div>
                <ul class="text-xs text-slate-600 space-y-2.5 leading-relaxed">
                    <li>• <b>Pilih / Cari Siswa Cepat</b>: Pada menu <code>/laporan-santri</code>, tersedia kotak select dropdown santri. Cukup pilih nama santri atau cari berdasarkan NIS, halaman laporan langsung diperbarui seketika.</li>
                    <li>• <b>Filter Per Juz Tertentu</b>: Ingin memeriksa capaian Juz Amma saja? Pilih opsi <code>Juz 30</code> pada filter juz. Sistem hanya akan membedah surat-surat di Juz 30.</li>
                    <li>• <b>Filter Berdasarkan Status</b>:
                        <br>&bull; Opsi <i>"Semua Status"</i> : Menampilkan seluruh surat 30 juz.
                        <br>&bull; Opsi <i>"Hapal Tuntas Saja"</i> : Menampilkan daftar surat yang sudah tuntas (berguna untuk laporan kelulusan juz).
                        <br>&bull; Opsi <i>"Belum Tuntas Saja"</i> : Menampilkan surat yang sedang berlangsung dan belum hafal (berguna untuk bimbingan target).
                    </li>
                </ul>
            </div>

            <!-- Card 3: Cetak PDF Resmi Siap Cetak A4 -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center text-lg font-bold border border-teal-200">
                        3
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-teal-700 uppercase tracking-wider">Dokumen Cetak</span>
                        <h4 class="font-extrabold text-slate-800 text-base">Cetak Laporan Rinci Format PDF Resmi (A4)</h4>
                    </div>
                </div>
                <ul class="text-xs text-slate-600 space-y-2.5 leading-relaxed">
                    <li>• <b>Buka Format Cetak</b>: Klik tombol hijau <code>[Cetak PDF Resmi (A4)]</code> pada halaman laporan siswa atau buka rute <code>/cetak-laporan?id=X</code>.</li>
                    <li>• <b>Desain Standar Pesantren</b>: Dokumen telah disesuaikan dengan standar A4 cetak rapi, dilengkapi bingkai ornamen Islam ganda, kop nama pesantren, data profil santri, tabel breakdown juz dan surat lengkap, rekap mutqin, serta kolom tanda tangan musyrif dan wali santri.</li>
                    <li>• <b>Simpan Sebagai PDF</b>: Di jendela cetak peramban, pilih printer <i>"Save as PDF" (Simpan sebagai PDF)</i> untuk menghasilkan berkas digital yang siap dikirim melalui WhatsApp ke wali santri.</li>
                </ul>
            </div>

            <!-- Card 4: Ekspor Spreadsheet Excel (.csv) -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center text-lg font-bold border border-teal-200">
                        4
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-teal-700 uppercase tracking-wider">Integrasi Data</span>
                        <h4 class="font-extrabold text-slate-800 text-base">Unduh Format Spreadsheet Excel (.csv)</h4>
                    </div>
                </div>
                <ul class="text-xs text-slate-600 space-y-2.5 leading-relaxed">
                    <li>• <b>1-Klik Unduh Excel Siswa</b>: Klik tombol <code>[Export Excel (.csv)]</code>. Sistem langsung menghasilkan berkas spreadsheet lengkap berisi data santri, seluruh juz, nama surat arab dan latin, ayat yang sudah dihafal, dan persentase tuntas.</li>
                    <li>• <b>Kompatibel 100%</b>: File dapat langsung dibuka dengan Microsoft Excel, Google Sheets, LibreOffice Calc, maupun WPS Office untuk kebutuhan arsip dinas, evaluasi kurikulum, atau rekapitulasi semester.</li>
                    <li>• <b>Ekspor Rekap Global</b>: Pada menu <code>/rekap</code>, Anda juga dapat mengekspor rekapitulasi seluruh riwayat setoran seluruh santri dalam rentang tanggal tertentu ke format Excel.</li>
                </ul>
            </div>

        </div>

    </div>

    <!-- ============================================================== -->
    <!-- TAB 4: PANDUAN TEKNIS, INSTALASI APK HP & KEAMANAN -->
    <!-- ============================================================== -->
    <div id="guide-content-teknis" class="<?= $activeTab === 'teknis' ? 'block' : 'hidden' ?> space-y-6">

        <div class="p-4 bg-slate-900 text-white rounded-2xl flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="w-11 h-11 rounded-2xl bg-emerald-500 text-emerald-950 flex items-center justify-center text-xl flex-shrink-0 font-bold shadow-sm">
                    <i class="fa-solid fa-server"></i>
                </span>
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base">Panduan Teknis, Instalasi APK HP & Keamanan Sistem</h3>
                    <p class="text-xs text-slate-300 mt-0.5">Tata cara menjalankan aplikasi, instalasi di HP santri, arsitektur URL bersih, dan keamanan basis data SQLite.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <!-- Card 1: Cara Menjalankan Server Lokal -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-bold border border-emerald-200">
                        1
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-emerald-700 uppercase tracking-wider">Menjalankan Aplikasi</span>
                        <h4 class="font-extrabold text-slate-800 text-base">Cara Menjalankan via File `run.bat`</h4>
                    </div>
                </div>
                <ul class="text-xs text-slate-600 space-y-2.5 leading-relaxed">
                    <li>• <b>Klik Ganda `run.bat`</b>: Aplikasi cukup dijalankan dengan mengeklik ganda file <code>run.bat</code> di folder utama proyek. File batch ini secara otomatis mencari PHP 8.3 di sistem (Laragon, XAMPP, atau PATH sistem), menyalakan server lokal, dan membuka peramban secara otomatis di alamat <code>http://localhost:8080</code>.</li>
                    <li>• <b>Akses dari HP / Tablet Santri (1 Wi-Fi)</b>: Pastikan HP santri dan laptop server terhubung ke jaringan Wi-Fi yang sama. Cek alamat IP lokal komputer Anda (misal <code>192.168.1.50</code>). Santri dapat membuka di HP mereka dengan mengetikkan:
                        <div class="p-2.5 rounded-xl bg-slate-100 font-mono text-emerald-800 font-bold mt-1 text-[11px]">
                            http://192.168.1.50:8080/player
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Card 2: Install Jadi Aplikasi HP (PWA / APK) -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-bold border border-emerald-200">
                        2
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-emerald-700 uppercase tracking-wider">Aplikasi HP Mandiri</span>
                        <h4 class="font-extrabold text-slate-800 text-base">Cara Install Jadi Aplikasi HP (PWA / APK)</h4>
                    </div>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Aplikasi telah dilengkapi Service Worker dan Web App Manifest sehingga dapat langsung di-install di HP Android maupun iPhone tanpa perlu Google Play Store:
                </p>
                <ul class="text-xs text-slate-600 space-y-2 leading-relaxed">
                    <li>• <b>Di Android (Google Chrome / Edge)</b>: Buka alamat web player di HP ➔ Ketuk ikon titik tiga di pojok kanan atas ➔ Pilih <b>"Tambahkan ke Layar Utama" (Install App)</b>. Ikon SIM Tahfidz akan muncul di layar utama HP seperti aplikasi APK native tanpa bilah URL peramban.</li>
                    <li>• <b>Di iPhone / iPad (Safari)</b>: Buka alamat web di Safari ➔ Ketuk tombol <i>Share (Bagikan)</i> di bagian bawah ➔ Pilih <b>"Add to Home Screen" (Tambahkan ke Layar Utama)</b>.</li>
                </ul>
            </div>

            <!-- Card 3: Arsitektur URL Bersih (Clean URLs) -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-bold border border-emerald-200">
                        3
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-emerald-700 uppercase tracking-wider">Arsitektur URL Bersih</span>
                        <h4 class="font-extrabold text-slate-800 text-base">Rute Bersih Tanpa .php dan Tanpa ?page=</h4>
                    </div>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Seluruh sistem menggunakan arsitektur URL bersih (*Pretty Path-Based Routing*). Tidak ada lagi ekstensi file `.php` maupun parameter panjang yang membingungkan:
                </p>
                <div class="grid grid-cols-2 gap-2 text-[11px] font-mono text-slate-700">
                    <div class="p-2 rounded bg-slate-50 border">/player</div>
                    <div class="p-2 rounded bg-slate-50 border">/dashboard</div>
                    <div class="p-2 rounded bg-slate-50 border">/setoran</div>
                    <div class="p-2 rounded bg-slate-50 border">/murojaah</div>
                    <div class="p-2 rounded bg-slate-50 border">/santri</div>
                    <div class="p-2 rounded bg-slate-50 border">/santri/5</div>
                    <div class="p-2 rounded bg-slate-50 border">/laporan-santri</div>
                    <div class="p-2 rounded bg-slate-50 border">/rekap</div>
                    <div class="p-2 rounded bg-slate-50 border">/panduan</div>
                    <div class="p-2 rounded bg-slate-50 border">/login</div>
                </div>
                <p class="text-[11px] text-slate-500 italic">
                    * Catatan: Jika ada pengguna yang membuka bookmark lama dengan format <code>index.php?page=...</code>, sistem otomatis melakukan redirect 301 ke URL bersih yang baru.
                </p>
            </div>

            <!-- Card 4: Keamanan Sistem & Backup Basis Data -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-bold border border-emerald-200">
                        4
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold text-emerald-700 uppercase tracking-wider">Perlindungan Siber</span>
                        <h4 class="font-extrabold text-slate-800 text-base">Keamanan Siber, Brute-Force & Cadangan Data</h4>
                    </div>
                </div>
                <ul class="text-xs text-slate-600 space-y-2.5 leading-relaxed">
                    <li>• <b>Proteksi Brute-Force Login</b>: Sistem membatasi percobaan masuk admin maksimal 5 kali gagal. Jika terlampaui, akun otomatis terkunci selama 10 menit untuk mencegah pembobolan sandi.</li>
                    <li>• <b>Proteksi File Basis Data SQLite</b>: Folder <code>database/</code> dilindungi file <code>.htaccess</code> dengan aturan <code>Require all denied</code>, sehingga berkas basis data tidak dapat diunduh langsung dari web oleh pihak luar.</li>
                    <li>• <b>HTTP Security Headers & Anti-CSRF</b>: Dilengkapi proteksi Clickjacking (`X-Frame-Options`), MIME Sniffing (`X-Content-Type-Options`), XSS, dan token validasi CSRF pada setiap form mutasi data.</li>
                    <li>• <b>Backup Database 1-Klik</b>: Masuk ke menu <code>/pengaturan</code> ➔ klik tombol <b>[Unduh / Backup File Database (.sqlite)]</b> untuk mengunduh seluruh data dalam satu file cadangan instan.</li>
                </ul>
            </div>

        </div>

    </div>

    <!-- ============================================================== -->
    <!-- TAB 5: TIPS METODOLOGI MENGHAFAL MUTQIN & FAQ -->
    <!-- ============================================================== -->
    <div id="guide-content-faq" class="<?= $activeTab === 'faq' ? 'block' : 'hidden' ?> space-y-6">

        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-4">
            <h3 class="font-extrabold text-slate-800 text-base sm:text-lg flex items-center gap-2">
                <i class="fa-solid fa-lightbulb text-amber-500"></i>
                <span>Metodologi Menghafal Al-Qur'an Berulang (Tikrar Mutqin)</span>
            </h3>
            <p class="text-xs text-slate-600 leading-relaxed">
                Metode Tikrar adalah metode pengulangan bacaan yang terbukti paling kokoh dan diwariskan turun-temurun dalam tradisi tahfidz pesantren di Timur Tengah dan Nusantara:
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200">
                    <div class="font-extrabold text-emerald-900 mb-1 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px]">1</span>
                        <span>Dengar (Talaqqi)</span>
                    </div>
                    <p class="text-slate-600 text-[11px] leading-relaxed">Dengarkan lantunan Qari 3 s/d 5 kali terlebih dahulu sambil memperhatikan makhraj, tajwid, dan panjang pendek harakat sebelum mulai menghafal.</p>
                </div>
                <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200">
                    <div class="font-extrabold text-amber-950 mb-1 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-amber-500 text-amber-950 flex items-center justify-center text-[10px]">2</span>
                        <span>Ulang Ayat (Drill)</span>
                    </div>
                    <p class="text-slate-600 text-[11px] leading-relaxed">Ulangi satu ayat sebanyak 20 s/d 40 kali hingga meluncur lancar di lisan tanpa perlu memikirkan urutan huruf dan harakatnya.</p>
                </div>
                <div class="p-4 rounded-2xl bg-teal-50/70 border border-teal-200">
                    <div class="font-extrabold text-teal-950 mb-1 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-teal-600 text-white flex items-center justify-center text-[10px]">3</span>
                        <span>Sambung (Wasl)</span>
                    </div>
                    <p class="text-slate-600 text-[11px] leading-relaxed">Gunakan <b>Metode Sambung Akumulatif</b> untuk mengaitkan ayat 1 ke 2, lalu ayat 1-3, lalu 1-4 agar sambungan hafalan tidak lepas saat diuji.</p>
                </div>
            </div>
        </div>

        <!-- FAQ Accordion Komprehensif -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-4">
            <h3 class="font-extrabold text-slate-800 text-base sm:text-lg flex items-center gap-2">
                <i class="fa-solid fa-circle-question text-emerald-600"></i>
                <span>Pertanyaan yang Sering Diajukan (FAQ)</span>
            </h3>

            <div class="space-y-3 text-xs">
                
                <details class="group p-4 rounded-2xl bg-slate-50 border border-slate-200 transition-all">
                    <summary class="font-bold text-slate-800 cursor-pointer flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-wifi text-emerald-600"></i>
                            <span>Apakah aplikasi ini memerlukan koneksi internet untuk memutar suara qari?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-slate-600 mt-3 leading-relaxed border-t border-slate-200/60 pt-2.5">
                        Aplikasi mengambil streaming audio Qari resmi dari CDN EveryAyah. Audio memerlukan koneksi internet saat pertama kali diputar. Namun, peramban web dan Service Worker aplikasi akan otomatis menyimpan (*cache*) audio yang sering diputar sehingga pemutaran berikutnya menjadi lebih hemat kuota dan lebih cepat. Teks Arab dan seluruh data santri tersimpan secara lokal dan permanen di basis data SQLite komputer server Anda.
                    </p>
                </details>

                <details class="group p-4 rounded-2xl bg-slate-50 border border-slate-200 transition-all">
                    <summary class="font-bold text-slate-800 cursor-pointer flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-microphone text-emerald-600"></i>
                            <span>Bagaimana jika rekaman suara mikrofon santri tidak berfungsi atau hening?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-slate-600 mt-3 leading-relaxed border-t border-slate-200/60 pt-2.5">
                        Pastikan izin mikrofon telah disetujui (*Allow*) saat peramban web memunculkan jendela konfirmasi izin mikrofon. Jika tidak sengaja tertolak, klik ikon gembok di bilah alamat peramban web ➔ ubah izin Mikrofon menjadi <i>"Izinkan" (Allow)</i>, lalu segarkan halaman. Fitur ini memanfaatkan standar HTML5 MediaStream yang didukung di Google Chrome, Edge, Safari, dan Firefox.
                    </p>
                </details>

                <details class="group p-4 rounded-2xl bg-slate-50 border border-slate-200 transition-all">
                    <summary class="font-bold text-slate-800 cursor-pointer flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-rotate-left text-emerald-600"></i>
                            <span>Bagaimana cara mengoreksi jika ustadz keliru memasukkan data setoran santri?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-slate-600 mt-3 leading-relaxed border-t border-slate-200/60 pt-2.5">
                        Buka menu <b>Santri</b> (`/santri`), klik nama santri terkait untuk membuka Kartu Kendali (`/santri/X`). Pada tabel riwayat setoran di bawah, klik tombol tempat sampah merah (*Hapus*). Sistem akan meminta konfirmasi dan secara otomatis melakukan <b>Auto-Rollback</b> (mengembalikan posisi surah terakhir dan mengurangi akumulasi capaian halaman santri ke posisi sebelum setoran tersebut dimasukkan).
                    </p>
                </details>

                <details class="group p-4 rounded-2xl bg-slate-50 border border-slate-200 transition-all">
                    <summary class="font-bold text-slate-800 cursor-pointer flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                            <span>Bagaimana jika ustadz lupa kata sandi login admin?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-slate-600 mt-3 leading-relaxed border-t border-slate-200/60 pt-2.5">
                        Akun bawaan sistem adalah Username: <code>admin</code> dan Password: <code>admin123</code>. Jika Anda telah mengubah kata sandi dan lupa, pengelola server dapat mereset kata sandi dengan membuka basis data SQLite di <code>database/hafalan.sqlite</code> atau memperbarui nilai hash password pada tabel <code>users</code> menggunakan fungsi <code>password_hash()</code> bawaan PHP.
                    </p>
                </details>

                <details class="group p-4 rounded-2xl bg-slate-50 border border-slate-200 transition-all">
                    <summary class="font-bold text-slate-800 cursor-pointer flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-users text-emerald-600"></i>
                            <span>Berapa batas kapasitas santri yang dapat ditampung dalam aplikasi ini?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="text-slate-600 mt-3 leading-relaxed border-t border-slate-200/60 pt-2.5">
                        Basis data SQLite 3 dengan mode <b>WAL (Write-Ahead Logging)</b> yang digunakan aplikasi ini mampu menampung puluhan ribu data santri dan jutaan riwayat setoran secara sangat cepat dan ringan tanpa memerlukan instalasi server database MySQL/MariaDB yang berat.
                    </p>
                </details>

            </div>
        </div>

    </div>

    <!-- Kartu Informasi Pengembang, Doa Hafidz & Lisensi ChatTutor -->
    <div class="bg-gradient-to-br from-emerald-900 via-slate-900 to-teal-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-emerald-500/20 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-white/10 pb-5">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center text-amber-300 text-2xl shadow-inner flex-shrink-0">
                    <i class="fa-solid fa-book-quran"></i>
                </div>
                <div>
                    <span class="inline-block text-[10px] font-extrabold uppercase tracking-widest bg-amber-400 text-amber-950 px-2.5 py-0.5 rounded-full mb-1">
                        Wakaf Digital & Amal Jariyah
                    </span>
                    <h3 class="text-xl font-extrabold tracking-tight">SIM Tahfidz Qur'an & Player Tikrar</h3>
                    <p class="text-xs text-emerald-200 mt-0.5">Diberdayakan secara penuh oleh ChatTutor.id & WebSchool.id</p>
                </div>
            </div>
            <button type="button" onclick="openModalInfoChatTutor()" class="self-start md:self-auto bg-amber-400 hover:bg-amber-300 text-amber-950 font-extrabold px-4 py-2 rounded-xl text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-circle-info"></i> Buka Informasi Lengkap
            </button>
        </div>

        <!-- Doa Terbaik & Larangan Jual Beli -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 rounded-2xl bg-white/10 border border-white/10 text-emerald-100">
                <div class="flex items-center gap-2 text-xs font-bold text-amber-300 uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-heart text-rose-400"></i> Doa Terbaik untuk Santri
                </div>
                <p class="text-xs leading-relaxed italic">
                    "Doa terbaik semoga ananda dan seluruh santri menjadi Hafidz & Hafidzah Al-Qur'an yang mutqin, berakhlak mulia, cerdas, dan istiqomah. Lanjutkan perjuangan mulia menjaga kalamullah!"
                </p>
            </div>
            <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-500/40 text-rose-200">
                <div class="flex items-center gap-2 text-xs font-extrabold text-rose-300 uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-ban text-rose-400"></i> Lisensi Wakaf Gratis
                </div>
                <p class="text-xs leading-relaxed font-semibold">
                    <b class="text-white">DILARANG KERAS MEMPERJUALBELIKAN APLIKASI INI!</b><br>
                    Aplikasi ini adalah <b>MURNI GRATIS DARI CHATTUTOR</b> untuk pondok pesantren, rumah tahfidz, masjid, sekolah, dan seluruh kaum muslimin.
                </p>
            </div>
        </div>

        <!-- Kanal ChatTutor & Kontak WhatsApp -->
        <div class="flex flex-wrap items-center justify-between gap-4 pt-2">
            <div class="flex flex-wrap items-center gap-3 text-xs">
                <a href="https://chattutor.id" target="_blank" class="text-emerald-300 hover:underline flex items-center gap-1 font-bold">
                    <i class="fa-solid fa-globe"></i> chattutor.id
                </a>
                <span>•</span>
                <a href="https://webschool.id" target="_blank" class="text-teal-300 hover:underline flex items-center gap-1 font-bold">
                    <i class="fa-solid fa-graduation-cap"></i> webschool.id
                </a>
                <span>•</span>
                <a href="https://www.youtube.com/results?search_query=chattutor" target="_blank" class="text-rose-300 hover:underline flex items-center gap-1 font-bold">
                    <i class="fa-brands fa-youtube"></i> YouTube ChatTutor
                </a>
                <span>•</span>
                <a href="https://www.facebook.com/search/top?q=chattutor" target="_blank" class="text-blue-300 hover:underline flex items-center gap-1 font-bold">
                    <i class="fa-brands fa-facebook"></i> Facebook ChatTutor
                </a>
            </div>
            <a href="https://wa.me/628975775046?text=Halo%20ChatTutor,%20saya%20pengguna%20Aplikasi%20SIM%20Tahfidz%20Quran" target="_blank" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold px-3.5 py-1.5 rounded-xl text-xs transition-all shadow-sm">
                <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp: 0897 5775 046
            </a>
        </div>
    </div>

</div>

<!-- JavaScript untuk Penggantian Tab Panduan -->
<script>
function switchGuideTab(tabName) {
    const tabs = ['santri', 'admin', 'laporan', 'teknis', 'faq'];
    tabs.forEach(t => {
        const btn = document.getElementById(`tab-btn-${t}`);
        const content = document.getElementById(`guide-content-${t}`);
        if (!btn || !content) return;
        if (t === tabName) {
            btn.className = 'flex-1 min-w-[120px] py-3 px-3 sm:px-4 rounded-xl transition-all flex items-center justify-center gap-1.5 bg-white text-emerald-800 shadow-md font-extrabold text-xs sm:text-sm whitespace-nowrap';
            content.classList.remove('hidden');
            content.classList.add('block');
        } else {
            btn.className = 'flex-1 min-w-[120px] py-3 px-3 sm:px-4 rounded-xl transition-all flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-900 font-bold text-xs sm:text-sm whitespace-nowrap';
            content.classList.remove('block');
            content.classList.add('hidden');
        }
    });

    // Update URL parameter tanpa reload halaman
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.replaceState({}, '', url);
}
</script>
