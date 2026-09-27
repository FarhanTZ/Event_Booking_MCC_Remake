<!-- ==================================================================== -->
<!-- SISI KIRI: NAVBAR MENU & FITUR FRONT OFFICE (FIXED / STICKY FULL-HEIGHT) -->
<!-- ==================================================================== -->
<aside x-show="sidebarLeft" 
       x-transition:enter="transition-all ease-out duration-200"
       x-transition:enter-start="opacity-0 -translate-x-4"
       x-transition:enter-end="opacity-100 translate-x-0"
       x-transition:leave="transition-all ease-in duration-150"
       x-transition:leave-start="opacity-100 translate-x-0"
       x-transition:leave-end="opacity-0 -translate-x-4"
       class="w-full lg:w-72 xl:w-80 shrink-0 bg-white rounded-brand border border-slate-200 shadow-card flex flex-col justify-between p-4 sm:p-5 lg:sticky lg:top-4 lg:h-[calc(100vh-2rem)] overflow-y-auto custom-scrollbar z-30 space-y-5">
    
    <div class="space-y-5">
        <!-- 1. Header Brand & Close Button -->
        <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-brand bg-[#001A72] flex items-center justify-center p-2 shadow-sm text-white shrink-0">
                    <img src="{{ asset('assets/images/media/branding/mcc-aset-logo blue 1.png') }}" 
                         alt="MCC Logo" 
                         class="h-full w-auto object-contain brightness-0 invert">
                </div>
                <div class="min-w-0">
                    <span class="font-extrabold text-sm tracking-tight text-slate-900 block truncate leading-tight">
                        MCC Front Office
                    </span>
                    <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider block leading-tight mt-0.5">
                        Meja Resepsionis &amp; Kustodian
                    </span>
                </div>
            </div>

            <!-- Close Sidebar Button (Mobile / Toggle) -->
            <button type="button" 
                    @click="sidebarLeft = false" 
                    class="p-1.5 rounded-brand text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                    title="Tutup Menu Kiri">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </button>
        </div>

        <!-- 2. Tombol Cepat Kunjungi Landing Page / Web Publik -->
        <a href="/" 
           target="_blank"
           class="flex items-center justify-between px-3.5 py-2.5 rounded-brand bg-slate-50 hover:bg-[#EEF2FF] text-slate-700 hover:text-[#001A72] border border-slate-200 hover:border-indigo-200 transition group shadow-2xs">
            <div class="flex items-center gap-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 group-hover:text-[#001A72] transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                <span class="text-xs font-bold">Kunjungi Landing Page</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400 group-hover:text-[#001A72] group-hover:translate-x-0.5 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
        </a>

        <!-- 3. Section: Fitur & Menu Navigasi Utama -->
        <div class="space-y-1.5">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-2.5 block mb-1.5">
                Menu Front Office
            </span>

            <!-- Manifes Acara Harian -->
            <a href="{{ route('front_office.manifest') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-brand font-bold text-xs {{ request()->routeIs('front_office.manifest') ? 'bg-[#001A72] text-white shadow-xs' : 'text-slate-600 hover:text-[#001A72] hover:bg-slate-50' }} transition">
                <div class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ request()->routeIs('front_office.manifest') ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="m9 16 2 2 4-4"/></svg>
                    <span>Manifes Acara Hari Ini</span>
                </div>
                @if($metrics['total_slots_today'] > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ request()->routeIs('front_office.manifest') ? 'bg-white text-[#001A72]' : 'bg-slate-100 text-slate-700' }} shadow-2xs">
                        {{ $metrics['total_slots_today'] }}
                    </span>
                @endif
            </a>

            <!-- Validasi Check-In (Opens New Tab) -->
            <a href="{{ route('front_office.checkin') }}" 
               target="_blank"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-brand font-bold text-xs {{ request()->routeIs('front_office.checkin') ? 'bg-[#001A72] text-white shadow-xs' : 'text-slate-700 hover:text-[#001A72] hover:bg-[#EEF2FF] bg-slate-50 border border-slate-200' }} transition group shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ request()->routeIs('front_office.checkin') ? 'text-white' : 'text-emerald-600' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect width="10" height="10" x="7" y="7" rx="1"/></svg>
                    <span>Validasi Check-In</span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400 group-hover:text-[#001A72] group-hover:translate-x-0.5 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
            </a>

            <!-- Kalender Slot Gedung -->
            <a href="/jadwal" 
               class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-brand font-semibold text-xs text-slate-600 hover:text-[#001A72] hover:bg-slate-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                <span>Cek Kalender Seluruh Ruangan</span>
            </a>

            <!-- Katalog Ruangan -->
            <a href="/katalog" 
               class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-brand font-semibold text-xs text-slate-600 hover:text-[#001A72] hover:bg-slate-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span>Katalog Ruangan MCC</span>
            </a>
        </div>

        <!-- 4. Section: Filter Cepat Status Manifes Hari Ini -->
        <div class="space-y-1 pt-2 border-t border-slate-100">
            <div class="flex items-center justify-between px-2.5 mb-1.5">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                    Status Sesi Hari Ini
                </span>
                <span class="text-[10px] font-mono text-slate-400 font-bold">Total: {{ $metrics['total_slots_today'] }}</span>
            </div>

            <!-- Semua Sesi Hari Ini -->
            <a href="{{ route('front_office.manifest', ['status' => 'all', 'date' => request('date')]) }}"
               class="flex items-center justify-between px-3 py-2 rounded-brand text-xs font-semibold {{ $currentStatus === 'all' ? 'bg-[#EEF2FF] text-[#001A72] font-bold border border-indigo-100 shadow-2xs' : 'text-slate-600 hover:bg-slate-50' }}">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    <span>Semua Sesi Hari Ini</span>
                </div>
                <span class="text-[11px] font-mono font-bold">{{ $metrics['total_slots_today'] }}</span>
            </a>

            <!-- Menunggu Kedatangan -->
            <a href="{{ route('front_office.manifest', ['status' => 'confirmed', 'date' => request('date')]) }}"
               class="flex items-center justify-between px-3 py-2 rounded-brand text-xs font-semibold {{ $currentStatus === 'confirmed' ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200 shadow-2xs' : 'text-slate-600 hover:bg-slate-50' }}">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Menunggu Kedatangan</span>
                </div>
                <span class="text-[11px] font-mono font-bold text-amber-700 bg-amber-100/80 px-1.5 py-0.5 rounded">
                    {{ $metrics['confirmed_slots'] }}
                </span>
            </a>

            <!-- Sedang Berlangsung -->
            <a href="{{ route('front_office.manifest', ['status' => 'checked_in', 'date' => request('date')]) }}"
               class="flex items-center justify-between px-3 py-2 rounded-brand text-xs font-semibold {{ $currentStatus === 'checked_in' ? 'bg-teal-50 text-teal-900 font-bold border border-teal-200 shadow-2xs' : 'text-slate-600 hover:bg-slate-50' }}">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                    <span>Sedang Berlangsung</span>
                </div>
                <span class="text-[11px] font-mono font-bold text-teal-700 bg-teal-100/80 px-1.5 py-0.5 rounded">
                    {{ $metrics['checked_in_slots'] }}
                </span>
            </a>

            <!-- Selesai Digunakan -->
            <a href="{{ route('front_office.manifest', ['status' => 'completed', 'date' => request('date')]) }}"
               class="flex items-center justify-between px-3 py-2 rounded-brand text-xs font-semibold {{ $currentStatus === 'completed' ? 'bg-slate-100 text-slate-900 font-bold border border-slate-300 shadow-2xs' : 'text-slate-600 hover:bg-slate-50' }}">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                    <span>Selesai Digunakan</span>
                </div>
                <span class="text-[11px] font-mono font-bold text-slate-600">{{ $metrics['completed_slots'] }}</span>
            </a>
        </div>
    </div>
</aside>
