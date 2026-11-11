    </main>

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

