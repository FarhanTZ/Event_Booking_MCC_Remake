<footer class="bg-slate-900 text-slate-300 border-t border-slate-800 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand & Info -->
            <div class="md:col-span-2 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-md">
                        <i data-lucide="building-2" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="font-bold text-lg text-white block leading-tight">Malang Creative Center</span>
                        <span class="text-xs text-slate-400">Pusat Inkubasi & Kolaborasi Industri Kreatif</span>
                    </div>
                </div>
                <p class="text-sm text-slate-400 max-w-md leading-relaxed">
                    Sistem portal reservasi mandiri (self-service) terintegrasi untuk peminjaman ruangan, studio, auditorium, dan fasilitas kreatif di gedung MCC Malang.
                </p>
                <div class="flex items-center gap-3 text-slate-400 text-xs">
                    <span class="inline-flex items-center gap-1"><i data-lucide="map-pin" class="w-3.5 h-3.5 text-indigo-400"></i> Jl. A. Yani No.53, Blimbing, Kota Malang</span>
                </div>
            </div>

            <!-- Navigasi Cepat -->
            <div>
                <h4 class="text-sm font-semibold text-white tracking-wider uppercase mb-4">Navigasi</h4>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li><a href="/" class="hover:text-white transition">Katalog Ruangan</a></li>
                    <li><a href="/jadwal" class="hover:text-white transition">Kalender Jadwal</a></li>
                    <li><a href="/panduan" class="hover:text-white transition">Alur & Syarat Reservasi</a></li>
                    <li><a href="/faq" class="hover:text-white transition">FAQ & Bantuan</a></li>
                </ul>
            </div>

            <!-- Jam Operasional & Kontak -->
            <div>
                <h4 class="text-sm font-semibold text-white tracking-wider uppercase mb-4">Operasional</h4>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li class="flex items-center gap-2">
                        <i data-lucide="clock" class="w-4 h-4 text-emerald-400"></i>
                        <span>08:00 – 22:00 WIB (Setiap Hari)</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4 text-indigo-400"></i>
                        <span>Wajib Jaminan KTP Asli Saat Check-in</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i data-lucide="phone" class="w-4 h-4 text-amber-400"></i>
                        <span>Helpdesk: +62 812-3456-7890</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-slate-800 mt-10 pt-6 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 gap-4">
            <p>&copy; {{ date('Y') }} Malang Creative Center. All rights reserved.</p>
            <div class="flex items-center gap-6">
                <a href="#" class="hover:text-slate-400">Syarat & Ketentuan</a>
                <a href="#" class="hover:text-slate-400">Kebijakan Privasi</a>
                <span class="text-indigo-400 font-medium">Zero Double-Booking Engine</span>
            </div>
        </div>
    </div>
</footer>