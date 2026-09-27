<!-- ==================================================================== -->
<!-- SISI KANAN: PROFIL USER & STACK 4 KARTU KPI METRIK MANIFES -->
<!-- ==================================================================== -->
<aside x-show="sidebarRight" 
       x-transition:enter="transition-all ease-out duration-200"
       x-transition:enter-start="opacity-0 translate-x-4"
       x-transition:enter-end="opacity-100 translate-x-0"
       x-transition:leave="transition-all ease-in duration-150"
       x-transition:leave-start="opacity-100 translate-x-0"
       x-transition:leave-end="opacity-0 translate-x-4"
       class="w-full lg:w-72 xl:w-80 shrink-0 bg-white rounded-brand border border-slate-200 shadow-card flex flex-col justify-between p-4 sm:p-5 lg:sticky lg:top-4 lg:h-[calc(100vh-2rem)] overflow-y-auto custom-scrollbar z-20 space-y-5">
    
    <div class="space-y-4">
        <!-- 1. Profil Petugas Front Office Snapshot Card -->
        <div class="bg-gradient-to-b from-slate-50 to-white rounded-brand border border-slate-200 p-3.5 shadow-2xs space-y-3">
            <div class="flex items-center justify-between gap-2.5">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-10 h-10 rounded-brand bg-[#001A72] text-white flex items-center justify-center shrink-0 shadow-xs border border-indigo-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="font-black text-slate-900 text-xs sm:text-sm block truncate leading-tight">
                            {{ auth()->user()->full_name ?? auth()->user()->name ?? 'Petugas Front Office' }}
                        </span>
                        <span class="text-slate-500 text-[11px] block truncate leading-tight mt-0.5">
                            {{ auth()->user()->email ?? 'frontoffice@mcc.id' }}
                        </span>
                    </div>
                </div>

                <!-- Close Sidebar Button (Mobile / Toggle) -->
                <button type="button" 
                        @click="sidebarRight = false" 
                        class="p-1 rounded-brand text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer shrink-0 self-start"
                        title="Tutup Panel Kanan">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            </div>

            <!-- Action: Role Badge & Logout -->
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-[#EEF2FF] text-[#001A72] border border-indigo-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <span>Front Office MCC</span>
                </span>

                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" 
                            title="Keluar dari Akun"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-brand text-[11px] font-bold text-rose-600 hover:text-white hover:bg-rose-600 bg-rose-50 border border-rose-200 transition cursor-pointer shadow-2xs">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- 2. KPI Section Header & Live Badge -->
        <div class="flex items-center justify-between px-1 pt-1">
            <div class="flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                    Ringkasan Hari Ini
                </span>
            </div>
            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Live</span>
            </span>
        </div>

        <!-- 3. STACK 4 KARTU METRIK OPERASIONAL HARIAN -->
        <div class="space-y-3">
            
            <!-- Kartu 1: Total Sesi Hari Ini -->
            <div class="bg-white rounded-brand border border-slate-200 p-3.5 shadow-card hover:border-indigo-300 transition group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-600">Total Sesi Hari Ini</span>
                    <div class="w-8 h-8 rounded-brand bg-[#EEF2FF] text-[#001A72] flex items-center justify-center font-bold text-xs shadow-2xs group-hover:scale-105 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-black text-slate-900 font-mono tracking-tight">
                        {{ $metrics['total_slots_today'] }}
                    </span>
                    <span class="text-[10px] font-bold text-slate-500">
                        {{ $metrics['active_rooms_count'] }} Ruangan Aktif
                    </span>
                </div>
            </div>

            <!-- Kartu 2: Siap Check-In (Confirmed) -->
            <div class="bg-white rounded-brand border border-slate-200 p-3.5 shadow-card hover:border-emerald-300 transition group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-600">Menunggu Kedatangan</span>
                    <div class="w-8 h-8 rounded-brand bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs shadow-2xs group-hover:scale-105 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-black text-emerald-600 font-mono tracking-tight">
                        {{ $metrics['confirmed_slots'] }}
                    </span>
                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                        Siap Validasi
                    </span>
                </div>
            </div>

            <!-- Kartu 3: Sedang Berlangsung (Checked-In) -->
            <div class="bg-white rounded-brand border border-slate-200 p-3.5 shadow-card hover:border-teal-300 transition group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-600">Sedang Berlangsung</span>
                    <div class="w-8 h-8 rounded-brand bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-xs shadow-2xs group-hover:scale-105 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-black text-teal-600 font-mono tracking-tight">
                        {{ $metrics['checked_in_slots'] }}
                    </span>
                    <span class="text-[10px] font-bold text-teal-700 bg-teal-50 px-1.5 py-0.5 rounded border border-teal-200">
                        Gedung Terpakai
                    </span>
                </div>
            </div>

            <!-- Kartu 4: Selesai (Completed) -->
            <div class="bg-white rounded-brand border border-slate-200 p-3.5 shadow-card hover:border-slate-400 transition group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-600">Selesai Hari Ini</span>
                    <div class="w-8 h-8 rounded-brand bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs shadow-2xs group-hover:scale-105 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-black text-slate-700 font-mono tracking-tight">
                        {{ $metrics['completed_slots'] }}
                    </span>
                    <span class="text-[10px] font-bold text-slate-500 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200">
                        Inspeksi DO
                    </span>
                </div>
            </div>

        </div>
    </div>
</aside>
