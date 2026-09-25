<footer class="bg-slate-900 text-slate-300 border-t border-slate-800 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand & Info -->
            <div class="md:col-span-2 space-y-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-brand bg-[#001A72] border border-white/20 flex items-center justify-center text-white shadow-xs">
                        <i data-lucide="building-2" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="font-bold text-base text-white block leading-tight">Malang Creative Center</span>
                        <span class="text-xs text-slate-400">Pusat Kolaborasi & Inkubasi Industri Kreatif</span>
                    </div>
                </div>
                <p class="text-xs text-slate-400 max-w-md leading-relaxed">
                    Sistem portal reservasi mandiri (self-service) terintegrasi untuk peminjaman ruangan, studio, auditorium, dan fasilitas kreatif di gedung MCC Malang.
                </p>
                <div class="flex items-center gap-2 text-slate-400 text-xs">
                    <img src="{{ asset('assets/icons/pin.png') }}" class="w-3.5 h-3.5 opacity-80" alt="Alamat">
                    <span>Jl. A. Yani No.53, Blimbing, Kota Malang, Jawa Timur</span>
                </div>
                <!-- Social Media Icons from public/assets/icons -->
                <div class="flex items-center gap-3 pt-2">
                    <a href="https://instagram.com" target="_blank" class="w-7 h-7 rounded-md bg-slate-800 hover:bg-slate-700 flex items-center justify-center transition-colors">
                        <img src="{{ asset('assets/icons/instagram.png') }}" class="w-3.5 h-3.5" alt="Instagram">
                    </a>
                    <a href="https://youtube.com" target="_blank" class="w-7 h-7 rounded-md bg-slate-800 hover:bg-slate-700 flex items-center justify-center transition-colors">
                        <img src="{{ asset('assets/icons/youtube.png') }}" class="w-3.5 h-3.5" alt="YouTube">
                    </a>
                    <a href="https://tiktok.com" target="_blank" class="w-7 h-7 rounded-md bg-slate-800 hover:bg-slate-700 flex items-center justify-center transition-colors">
                        <img src="{{ asset('assets/icons/tiktok.png') }}" class="w-3.5 h-3.5" alt="TikTok">
                    </a>
                    <a href="https://facebook.com" target="_blank" class="w-7 h-7 rounded-md bg-slate-800 hover:bg-slate-700 flex items-center justify-center transition-colors">
                        <img src="{{ asset('assets/icons/facebook.png') }}" class="w-3.5 h-3.5" alt="Facebook">
                    </a>
                </div>
            </div>

            <!-- Navigasi Cepat -->
            <div>
                <h4 class="text-xs font-bold text-white tracking-wider uppercase mb-3">Navigasi</h4>
                <ul class="space-y-2 text-xs text-slate-400">
                    <li><a href="/" class="hover:text-white transition-colors">Katalog Ruangan</a></li>
                    <li><a href="/jadwal" class="hover:text-white transition-colors">Kalender Jadwal</a></li>
                    <li><a href="/panduan" class="hover:text-white transition-colors">Alur & Syarat Reservasi</a></li>
                    <li><a href="/faq" class="hover:text-white transition-colors">FAQ & Bantuan</a></li>
                </ul>
            </div>

            <!-- Jam Operasional & Kontak -->
            <div>
                <h4 class="text-xs font-bold text-white tracking-wider uppercase mb-3">Operasional</h4>
                <ul class="space-y-2.5 text-xs text-slate-400">
                    <li class="flex items-center gap-2">
                        <img src="{{ asset('assets/icons/waktu.png') }}" class="w-3.5 h-3.5" alt="Waktu">
                        <span>08:00 – 22:00 WIB (Setiap Hari)</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-400"></i>
                        <span>Wajib Jaminan KTP Asli Saat Check-in</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <img src="{{ asset('assets/icons/telp.png') }}" class="w-3.5 h-3.5" alt="Telp">
                        <span>Helpdesk: +62 812-3456-7890</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-slate-800 mt-8 pt-5 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 gap-3">
            <p>&copy; {{ date('Y') }} Malang Creative Center. All rights reserved.</p>
            <div class="flex items-center gap-4">
                <span class="text-white font-semibold flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Zero Double-Booking Engine</span>
                </span>
            </div>
        </div>
    </div>
</footer>