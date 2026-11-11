    </main>

    <!-- Desktop Footer Info ChatTutor -->
    <footer class="mt-auto py-5 bg-slate-900 text-slate-400 text-xs border-t border-slate-800 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-600/30 text-emerald-400 flex items-center justify-center font-bold text-sm shadow-inner">
                    <i class="fa-solid fa-book-quran"></i>
                </div>
                <div>
                    <div class="font-bold text-white flex items-center gap-2">
                        <span>SIM Tahfidz Qur'an & Tikrar</span>
                        <span class="text-[10px] bg-emerald-950 text-emerald-400 px-2 py-0.5 rounded-full border border-emerald-800 font-extrabold uppercase">100% Gratis</span>
                    </div>
                    <div class="text-[11px] text-slate-400 mt-0.5">
                        Wakaf Digital & Kontribusi Ummat oleh <a href="https://chattutor.id" target="_blank" class="text-emerald-400 font-bold hover:underline">ChatTutor.id</a> & <a href="https://webschool.id" target="_blank" class="text-teal-400 font-bold hover:underline">WebSchool.id</a>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-2.5 text-xs">
                <span class="text-rose-300 font-extrabold bg-rose-950/70 px-2.5 py-1 rounded-lg border border-rose-800/80 text-[10px] tracking-wide">
                    <i class="fa-solid fa-ban mr-1"></i> DILARANG MEMPERJUALBELIKAN
                </span>
                <button type="button" onclick="openModalInfoChatTutor()" class="text-amber-300 hover:text-amber-200 font-bold flex items-center gap-1.5 bg-amber-950/50 hover:bg-amber-900/60 px-3 py-1 rounded-lg border border-amber-600/40 transition-all shadow-xs">
                    <i class="fa-solid fa-circle-info text-amber-400"></i> Info & Kontak ChatTutor
                </button>
            </div>
        </div>
    </footer>

    <!-- Modal Informasi ChatTutor & Wakaf Aplikasi -->
    <div id="modal-info-chattutor" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-sm animate-fade-in no-print">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-xl max-h-[90vh] overflow-y-auto no-scrollbar relative flex flex-col">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 text-white p-5 sm:p-6 rounded-t-3xl relative overflow-hidden flex-shrink-0">
                <button type="button" onclick="closeModalInfoChatTutor()" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur border border-white/20 flex items-center justify-center text-amber-300 text-2xl shadow-inner flex-shrink-0">
                        <i class="fa-solid fa-book-quran"></i>
                    </div>
                    <div>
                        <span class="inline-block text-[10px] font-extrabold uppercase tracking-widest bg-amber-400 text-amber-950 px-2.5 py-0.5 rounded-full mb-1">
                            Aplikasi Wakaf & Dakwah Digital
                        </span>
                        <h3 class="text-xl font-extrabold tracking-tight">SIM Tahfidz Al-Qur'an & Tikrar</h3>
                        <p class="text-xs text-emerald-200 mt-0.5">Diberdayakan secara penuh oleh ChatTutor.id</p>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-5 sm:p-6 space-y-5 text-slate-700 text-xs sm:text-sm leading-relaxed">
                
                <!-- Doa Terbaik -->
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 relative overflow-hidden">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0 mt-0.5 shadow-sm">
                            <i class="fa-solid fa-heart"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-xs uppercase tracking-wider text-emerald-800">Doa Terbaik untuk Seluruh Santri & Penghafal Qur'an:</h4>
                            <p class="font-semibold text-emerald-900 mt-1 italic leading-snug">
                                "Doa terbaik semoga ananda dan seluruh santri menjadi Hafidz & Hafidzah Al-Qur'an yang mutqin, berakhlak mulia, cerdas, serta istiqomah. Lanjutkan dan teruskan perjuangan mulia menjaga kalamullah!"
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Peringatan Keras Dilarang Memperjualbelikan -->
                <div class="p-4 rounded-2xl bg-rose-50 border-2 border-rose-300 text-rose-950">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0 mt-0.5 shadow-sm">
                            <i class="fa-solid fa-ban text-base"></i>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-xs uppercase tracking-wider text-rose-700 flex items-center gap-1.5">
                                <span>Peringatan Hak Cipta & Lisensi:</span>
                            </h4>
                            <p class="font-extrabold text-rose-900 text-sm mt-1 leading-snug uppercase tracking-tight">
                                DI LARANG KERAS MEMPERJUALBELIKAN APLIKASI INI!
                            </p>
                            <p class="text-rose-800 text-xs mt-1 leading-relaxed">
                                <b>APLIKASI INI MURNI GRATIS DARI CHATTUTOR</b> untuk kemaslahatan pondok pesantren, rumah tahfidz, sekolah Islam, ustadz, dan para santri. Tidak diperkenankan untuk dikomersialkan dalam bentuk apa pun.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Informasi Resmi ChatTutor & WebSchool -->
                <div class="space-y-3">
                    <h4 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-globe text-emerald-600"></i> Kanal Resmi & Layanan ChatTutor:
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        
                        <!-- Website ChatTutor.id -->
                        <a href="https://chattutor.id" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-emerald-50/70 border border-slate-200 hover:border-emerald-300 transition-all group">
                            <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-base group-hover:scale-105 transition-transform flex-shrink-0 shadow-xs">
                                <i class="fa-solid fa-globe"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-extrabold text-xs text-slate-800 group-hover:text-emerald-700">chattutor.id</div>
                                <div class="text-[11px] text-slate-500 truncate">Website Resmi ChatTutor</div>
                            </div>
                        </a>

                        <!-- Website WebSchool.id -->
                        <a href="https://webschool.id" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-teal-50/70 border border-slate-200 hover:border-teal-300 transition-all group">
                            <div class="w-9 h-9 rounded-xl bg-teal-600 text-white flex items-center justify-center text-base group-hover:scale-105 transition-transform flex-shrink-0 shadow-xs">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-extrabold text-xs text-slate-800 group-hover:text-teal-700">webschool.id</div>
                                <div class="text-[11px] text-slate-500 truncate">Platform Sekolah Digital</div>
                            </div>
                        </a>

                        <!-- YouTube ChatTutor -->
                        <a href="https://www.youtube.com/results?search_query=chattutor" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-rose-50/70 border border-slate-200 hover:border-rose-300 transition-all group">
                            <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center text-base group-hover:scale-105 transition-transform flex-shrink-0 shadow-xs">
                                <i class="fa-brands fa-youtube"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-extrabold text-xs text-slate-800 group-hover:text-rose-700">YouTube ChatTutor</div>
                                <div class="text-[11px] text-slate-500 truncate">Video Tutorial & Edukasi</div>
                            </div>
                        </a>

                        <!-- Facebook ChatTutor -->
                        <a href="https://www.facebook.com/profile.php?id=61577896051895" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-blue-50/70 border border-slate-200 hover:border-blue-300 transition-all group">
                            <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center text-base group-hover:scale-105 transition-transform flex-shrink-0 shadow-xs">
                                <i class="fa-brands fa-facebook-f"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-extrabold text-xs text-slate-800 group-hover:text-blue-700">Facebook ChatTutor</div>
                                <div class="text-[11px] text-slate-500 truncate">Komunitas & Informasi</div>
                            </div>
                        </a>

                    </div>
                </div>

                <!-- Kontak Resmi WhatsApp -->
                <div class="p-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-md">
                    <div>
                        <div class="text-[10px] uppercase font-extrabold tracking-wider text-emerald-200">Layanan Kontak & Konsultasi Resmi:</div>
                        <div class="text-base sm:text-lg font-extrabold flex items-center gap-2 mt-0.5">
                            <i class="fa-brands fa-whatsapp text-2xl text-emerald-300"></i>
                            <span>0897 5775 046</span>
                        </div>
                        <p class="text-[11px] text-emerald-100 mt-0.5">Silakan hubungi kami untuk pertanyaan, konsultasi, atau masukan sistem.</p>
                    </div>
                    <a href="https://wa.me/628975775046?text=Halo%20ChatTutor,%20saya%20pengguna%20Aplikasi%20SIM%20Tahfidz%20Quran" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 bg-white text-emerald-900 hover:bg-emerald-50 px-4 py-2.5 rounded-xl font-extrabold text-xs shadow-md transition-all active:scale-95 whitespace-nowrap">
                        <i class="fa-brands fa-whatsapp text-base text-emerald-600"></i>
                        <span>Chat WhatsApp</span>
                    </a>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-slate-50 border-t border-slate-200 rounded-b-3xl flex items-center justify-between flex-shrink-0 text-xs">
                <span class="text-slate-500 font-semibold">Versi 2.0 • Portable & Offline Ready</span>
                <button type="button" onclick="closeModalInfoChatTutor()" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold transition-all cursor-pointer">
                    Tutup
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Bottom Navigation Bar (App Experience) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-slate-200/80 shadow-2xl no-print">
        <?php if (class_exists('App\Auth') && App\Auth::check()): ?>
            <div class="grid grid-cols-5 h-16 max-w-md mx-auto">
                <!-- Menghafal (Player Tikrar Frontend) -->
                <a href="player" class="flex flex-col items-center justify-center text-xs <?= $activeNav === 'player' ? 'text-emerald-700 font-extrabold' : 'text-slate-500 hover:text-emerald-600' ?>">
                    <i class="fa-solid fa-headphones-simple text-lg mb-0.5 <?= $activeNav === 'player' ? 'text-emerald-700' : '' ?>"></i>
                    <span class="text-[10px]">Hafalan</span>
                </a>

                <!-- Dashboard / Admin -->
                <a href="dashboard" class="flex flex-col items-center justify-center text-xs <?= $activeNav === 'dashboard' ? 'text-emerald-600 font-bold' : 'text-slate-500 hover:text-emerald-600' ?>">
                    <i class="fa-solid fa-chart-pie text-lg mb-0.5"></i>
                    <span class="text-[10px]">Admin</span>
                </a>

                <!-- Center Floating Action Button: Setoran -->
                <div class="flex items-center justify-center -mt-5">
                    <a href="setoran" class="w-13 h-13 p-3.5 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-500 text-white shadow-lg shadow-emerald-500/40 flex items-center justify-center text-xl hover:scale-105 active:scale-95 transition-all border-4 border-slate-50" title="Setor Hafalan">
                        <i class="fa-solid fa-plus"></i>
                    </a>
                </div>

                <!-- Murojaah -->
                <a href="murojaah" class="flex flex-col items-center justify-center text-xs <?= $activeNav === 'murojaah' ? 'text-emerald-600 font-bold' : 'text-slate-500 hover:text-emerald-600' ?>">
                    <i class="fa-solid fa-arrows-rotate text-lg mb-0.5"></i>
                    <span class="text-[10px]">Murojaah</span>
                </a>

                <!-- Santri -->
                <a href="santri" class="flex flex-col items-center justify-center text-xs <?= $activeNav === 'santri' ? 'text-emerald-600 font-bold' : 'text-slate-500 hover:text-emerald-600' ?>">
                    <i class="fa-solid fa-users text-lg mb-0.5"></i>
                    <span class="text-[10px]">Santri</span>
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 h-16 max-w-xs mx-auto">
                <!-- Menghafal (Player Tikrar Frontend) -->
                <a href="player" class="flex flex-col items-center justify-center text-xs text-emerald-700 font-extrabold border-r border-slate-100">
                    <i class="fa-solid fa-headphones-simple text-xl mb-0.5"></i>
                    <span class="text-[11px]">Hafalan Santri</span>
                </a>

                <!-- Login Ustadz -->
                <a href="login" class="flex flex-col items-center justify-center text-xs text-amber-800 font-extrabold bg-amber-50/50">
                    <i class="fa-solid fa-lock text-xl mb-0.5 text-amber-600"></i>
                    <span class="text-[11px]">Login Ustadz</span>
                </a>
            </div>
        <?php endif; ?>
    </nav>

    <!-- Global Toast Container -->
    <div id="toast-container" class="fixed bottom-20 md:bottom-6 right-4 md:right-6 z-50 flex flex-col space-y-2 max-w-sm pointer-events-none"></div>

    <!-- Scripts -->
    <script>
        // Modal Info ChatTutor Handler
        function openModalInfoChatTutor() {
            const modal = document.getElementById('modal-info-chattutor');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeModalInfoChatTutor() {
            const modal = document.getElementById('modal-info-chattutor');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        // Close on Esc key or click outside
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModalInfoChatTutor();
        });
        document.addEventListener('click', (e) => {
            const modal = document.getElementById('modal-info-chattutor');
            if (modal && e.target === modal) {
                closeModalInfoChatTutor();
            }
        });

        // Global Toast Notification Helper
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto transform transition-all duration-300 ease-out translate-y-2 opacity-0 flex items-center p-4 rounded-xl shadow-xl text-sm font-medium text-white ${
                type === 'success' ? 'bg-emerald-600 border border-emerald-500' :
                type === 'danger' ? 'bg-rose-600 border border-rose-500' :
                type === 'warning' ? 'bg-amber-600 border border-amber-500' :
                'bg-slate-800 border border-slate-700'
            }`;

            const iconClass = type === 'success' ? 'fa-circle-check' :
                              type === 'danger' ? 'fa-triangle-exclamation' :
                              type === 'warning' ? 'fa-circle-exclamation' : 'fa-circle-info';

            toast.innerHTML = `
                <i class="fa-solid ${iconClass} text-lg mr-3 flex-shrink-0"></i>
                <div class="flex-1 mr-2 leading-snug">${message}</div>
                <button onclick="this.parentElement.remove()" class="text-white/80 hover:text-white p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;

            container.appendChild(toast);

            // Animate In
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            });

            // Auto Remove
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 4500);
        }

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('./sw.js')
                    .then((reg) => console.log('SIM Tahfidz PWA Registered:', reg.scope))
                    .catch((err) => console.log('SIM Tahfidz PWA Registration Failed:', err));
            });
        }
    </script>
</body>
</html>

