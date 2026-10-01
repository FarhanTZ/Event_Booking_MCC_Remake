<!-- ==================================================================== -->
<!-- SUPER ADMIN PANEL: SIDEBAR KANAN (PROFIL & RINGKASAN METRIK MASTER) -->
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
        <!-- 1. Profil Super Admin Snapshot Card -->
        <div class="bg-gradient-to-b from-slate-50 to-white rounded-brand border border-slate-200 p-3.5 shadow-2xs space-y-3">
            <div class="flex items-center justify-between gap-2.5">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-10 h-10 rounded-brand bg-[#001A72] text-white flex items-center justify-center shrink-0 shadow-xs border border-indigo-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="font-black text-slate-900 text-xs sm:text-sm block truncate leading-tight">
                            {{ auth()->user()->full_name ?? auth()->user()->name ?? 'Super Administrator' }}
                        </span>
                        <span class="text-slate-500 text-[11px] block truncate leading-tight mt-0.5">
                            {{ auth()->user()->email ?? 'admin@mcc.id' }}
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
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-50 text-[#001A72] border border-indigo-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <span>Super Admin</span>
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

        <!-- 2. Ringkasan Status Fisik Gedung -->
        <div class="flex items-center justify-between px-1 pt-1">
            <div class="flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                    Status Entitas Ruangan
                </span>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Realtime</span>
            </span>
        </div>

        <!-- 3. KPI Status Ruangan -->
        <div class="space-y-2.5">
            <!-- 1. Total Ruangan -->
            <div class="bg-white rounded-brand border border-slate-200 p-3 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-600">Total Ruangan Master</span>
                    <span class="text-base font-black text-[#001A72]">{{ $metrics['total'] ?? 0 }}</span>
                </div>
                <p class="text-[10px] text-slate-400 mt-0.5">Tercatat di Lantai 1 s/d Lantai 8</p>
            </div>

            <!-- 2. Ruangan Aktif -->
            <div class="bg-emerald-50/50 rounded-brand border border-emerald-200 p-3 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-emerald-900">Ruangan Aktif</span>
                    <span class="text-base font-black text-emerald-700">{{ $metrics['active'] ?? 0 }}</span>
                </div>
                <p class="text-[10px] text-emerald-700 mt-0.5">Tampil &amp; dapat dipesan di kalender publik</p>
            </div>

            <!-- 3. Maintenance / Nonaktif -->
            <div class="bg-rose-50/50 rounded-brand border border-rose-200 p-3 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-rose-900">Ruangan Maintenance</span>
                    <span class="text-base font-black text-rose-700">{{ $metrics['inactive'] ?? 0 }}</span>
                </div>
                <p class="text-[10px] text-rose-700 mt-0.5">Dinonaktifkan dari pemesanan publik</p>
            </div>

            <!-- 4. Izin Komersial -->
            <div class="bg-indigo-50/50 rounded-brand border border-indigo-200 p-3 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-[#001A72]">Skema Komersial</span>
                    <span class="text-base font-black text-[#001A72]">{{ $metrics['commercial'] ?? 0 }}</span>
                </div>
                <p class="text-[10px] text-indigo-700 mt-0.5">Mendukung tarif berbayar per jam</p>
            </div>

            <!-- 5. Total Aset Inventaris -->
            <div class="bg-slate-50 rounded-brand border border-slate-200 p-3 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-700">Total Fasilitas Terdata</span>
                    <span class="text-base font-black text-slate-900">{{ $metrics['total_facilities'] ?? 0 }}</span>
                </div>
                <p class="text-[10px] text-slate-500 mt-0.5">Acuan checklist inspeksi Duty Officer</p>
            </div>
        </div>
    </div>

    <!-- 4. Quick Help Box -->
    <div class="pt-3 border-t border-slate-100 text-center">
        <p class="text-[10px] text-slate-400 font-medium">
            Perubahan master langsung mempengaruhi kalender publik &amp; form inspeksi DO.
        </p>
    </div>
</aside>
