<!-- ==================================================================== -->
<!-- SISI KIRI: NAVBAR MENU & FITUR MARKETING (FIXED / STICKY FULL-HEIGHT) -->
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
                        MCC Marketing
                    </span>
                    <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider block leading-tight mt-0.5">
                        Portal Kurasi v2.0
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
                Navigasi Kurasi
            </span>

            <!-- Antrean Kurasi (Active) -->
            <a href="{{ route('marketing.dashboard') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-brand font-bold text-xs bg-[#001A72] text-white shadow-xs transition">
                <div class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="m9 12 2 2 4-4"/></svg>
                    <span>Antrean Kurasi</span>
                </div>
                @if($metrics['awaiting_curation'] > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-400 text-slate-950 shadow-2xs">
                        {{ $metrics['awaiting_curation'] }}
                    </span>
                @endif
            </a>

            <!-- Kalender Slot Jadwal -->
            <a href="/jadwal" 
               class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-brand font-semibold text-xs text-slate-600 hover:text-[#001A72] hover:bg-slate-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                <span>Cek Slot &amp; Jadwal Gedung</span>
            </a>

            <!-- Katalog Ruangan -->
            <a href="/katalog" 
               class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-brand font-semibold text-xs text-slate-600 hover:text-[#001A72] hover:bg-slate-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span>Katalog Ruangan MCC</span>
            </a>
        </div>

        <!-- 4. Section: Filter Cepat Status Antrean -->
        <div class="space-y-1 pt-2 border-t border-slate-100">
            <div class="flex items-center justify-between px-2.5 mb-1.5">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                    Filter Cepat Status
                </span>
                <span class="text-[10px] font-mono text-slate-400 font-bold">Total: {{ $metrics['total_all'] }}</span>
            </div>

            <!-- Semua -->
            <a href="{{ route('marketing.dashboard', ['status' => 'all']) }}"
               class="flex items-center justify-between px-3 py-2 rounded-brand text-xs font-semibold {{ $currentStatus === 'all' ? 'bg-[#EEF2FF] text-[#001A72] font-bold border border-indigo-100 shadow-2xs' : 'text-slate-600 hover:bg-slate-50' }}">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    <span>Semua Proposal</span>
                </div>
                <span class="text-[11px] font-mono font-bold">{{ $metrics['total_all'] }}</span>
            </a>

            <!-- Butuh Kurasi -->
            <a href="{{ route('marketing.dashboard', ['status' => 'awaiting_marketing']) }}"
               class="flex items-center justify-between px-3 py-2 rounded-brand text-xs font-semibold {{ $currentStatus === 'awaiting_marketing' ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200 shadow-2xs' : 'text-slate-600 hover:bg-slate-50' }}">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Butuh Kurasi Segera</span>
                </div>
                <span class="text-[11px] font-mono font-bold {{ $metrics['awaiting_curation'] > 0 ? 'text-amber-700 bg-amber-100/80 px-1.5 py-0.2 rounded' : 'text-slate-400' }}">
                    {{ $metrics['awaiting_curation'] }}
                </span>
            </a>

            <!-- Toleransi Revisi -->
            <a href="{{ route('marketing.dashboard', ['status' => 'revised']) }}"
               class="flex items-center justify-between px-3 py-2 rounded-brand text-xs font-semibold {{ $currentStatus === 'revised' ? 'bg-indigo-50 text-indigo-900 font-bold border border-indigo-200 shadow-2xs' : 'text-slate-600 hover:bg-slate-50' }}">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <span>Toleransi Revisi (24J)</span>
                </div>
                <span class="text-[11px] font-mono font-bold {{ $metrics['revised'] > 0 ? 'text-indigo-700 bg-indigo-100/80 px-1.5 py-0.2 rounded' : 'text-slate-400' }}">
                    {{ $metrics['revised'] }}
                </span>
            </a>

            <!-- Verifikasi Bayar -->
            <a href="{{ route('marketing.dashboard', ['status' => 'awaiting_payment']) }}"
               class="flex items-center justify-between px-3 py-2 rounded-brand text-xs font-semibold {{ $currentStatus === 'awaiting_payment' ? 'bg-purple-50 text-purple-900 font-bold border border-purple-200 shadow-2xs' : 'text-slate-600 hover:bg-slate-50' }}">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                    <span>Verifikasi Bayar</span>
                </div>
                <span class="text-[11px] font-mono font-bold text-slate-500">{{ $metrics['awaiting_payment'] }}</span>
            </a>

            <!-- Disetujui Sah -->
            <a href="{{ route('marketing.dashboard', ['status' => 'confirmed']) }}"
               class="flex items-center justify-between px-3 py-2 rounded-brand text-xs font-semibold {{ $currentStatus === 'confirmed' ? 'bg-emerald-50 text-emerald-900 font-bold border border-emerald-200 shadow-2xs' : 'text-slate-600 hover:bg-slate-50' }}">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Disetujui Sah (Bulan Ini)</span>
                </div>
                <span class="text-[11px] font-mono font-bold text-emerald-700">{{ $metrics['approved_this_month'] }}</span>
            </a>
        </div>
    </div>

    <!-- 5. Footer Sidebar Kiri: Status Operasional -->
    <div class="pt-3 border-t border-slate-100 mt-auto">
        <div class="bg-slate-50 rounded-brand p-3 border border-slate-200/80 space-y-1">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5 text-slate-800 font-bold text-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Sistem Kurasi Aktif</span>
                </div>
                <span class="text-[9px] font-mono font-bold text-slate-400">MCC v2.0</span>
            </div>
            <p class="text-[10px] text-slate-500 leading-tight">
                Jam Operasional: 08:00 – 22:00 WIB
            </p>
        </div>
    </div>
</aside>
