<!-- ==================================================================== -->
<!-- SUPER ADMIN PANEL: SIDEBAR NAVIGASI KIRI (STICKY / COLLAPSIBLE) -->
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
                        Super Admin
                    </span>
                    <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider block leading-tight mt-0.5">
                        Sistem Kontrol MCC
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
                <span class="text-xs font-bold">Kunjungi Web Publik</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400 group-hover:text-[#001A72] group-hover:translate-x-0.5 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
        </a>

        <!-- 3. Section: Modul Master & Fasilitas -->
        <div class="space-y-1.5">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-2.5 block mb-1.5">
                Manajemen Gedung (L1 - L8)
            </span>

            <!-- Master Ruangan -->
            <a href="{{ route('admin.rooms.index') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-brand font-bold text-xs {{ request()->routeIs('admin.rooms.*') ? 'bg-[#001A72] text-white shadow-xs' : 'text-slate-600 hover:text-[#001A72] hover:bg-slate-50' }} transition">
                <div class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ request()->routeIs('admin.rooms.*') ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <span>Master Ruangan &amp; Layout</span>
                </div>
                @if(isset($metrics['total']) && request()->routeIs('admin.rooms.*'))
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-white text-[#001A72] shadow-2xs">
                        {{ $metrics['total'] }}
                    </span>
                @endif
            </a>

            <!-- Manajemen Akun Staf (FR-ADM-03) -->
            <a href="{{ route('admin.staff.index') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-brand font-bold text-xs {{ request()->routeIs('admin.staff.*') ? 'bg-[#001A72] text-white shadow-xs' : 'text-slate-600 hover:text-[#001A72] hover:bg-slate-50' }} transition">
                <div class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ request()->routeIs('admin.staff.*') ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <span>Akun Staf Lintas Divisi</span>
                </div>
                @if(isset($metrics['total']) && request()->routeIs('admin.staff.*'))
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-white text-[#001A72] shadow-2xs">
                        {{ $metrics['total'] }}
                    </span>
                @endif
            </a>

            <!-- Penegakan Sanksi & Blacklist Pemohon (FR-ADM-04) -->
            <a href="{{ route('admin.sanctions.index') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-brand font-bold text-xs {{ request()->routeIs('admin.sanctions.*') ? 'bg-[#001A72] text-white shadow-xs' : 'text-slate-600 hover:text-[#001A72] hover:bg-slate-50' }} transition">
                <div class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ request()->routeIs('admin.sanctions.*') ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                    <span>Sanksi &amp; Blacklist Pemohon</span>
                </div>
                @if(isset($metrics['total_suspended']) && request()->routeIs('admin.sanctions.*'))
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-600 text-white shadow-2xs">
                        {{ $metrics['total_suspended'] }}
                    </span>
                @endif
            </a>

            <!-- Intervensi & Pembatalan Darurat (FR-ADM-05) -->
            <a href="{{ route('admin.interventions.index') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-brand font-bold text-xs {{ request()->routeIs('admin.interventions.*') ? 'bg-[#001A72] text-white shadow-xs' : 'text-slate-600 hover:text-[#001A72] hover:bg-slate-50' }} transition">
                <div class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ request()->routeIs('admin.interventions.*') ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>Intervensi Darurat Gedung</span>
                </div>
                @if(isset($metrics['total_active']) && request()->routeIs('admin.interventions.*'))
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-white text-[#001A72] shadow-2xs">
                        {{ $metrics['total_active'] }}
                    </span>
                @endif
            </a>
        </div>

        <!-- 4. Section: Modul Operasional Lainnya -->
        <div class="space-y-1.5 pt-2 border-t border-slate-100">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-2.5 block mb-1.5">
                Portal Operasional Tim
            </span>

            <!-- Front Office Manifes -->
            <a href="{{ route('front_office.manifest') }}" 
               class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-brand font-semibold text-xs text-slate-600 hover:text-[#001A72] hover:bg-slate-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="m9 16 2 2 4-4"/></svg>
                <span>Front Office Manifes</span>
            </a>

            <!-- Marketing Kurasi -->
            <a href="{{ route('marketing.dashboard') }}" 
               class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-brand font-semibold text-xs text-slate-600 hover:text-[#001A72] hover:bg-slate-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="m9 12 2 2 4-4"/></svg>
                <span>Marketing &amp; Kurasi</span>
            </a>

            <!-- Duty Officer Dashboard -->
            <a href="{{ route('duty_officer.dashboard') }}" 
               class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-brand font-semibold text-xs text-slate-600 hover:text-[#001A72] hover:bg-slate-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span>Duty Officer Mobile</span>
            </a>
        </div>
    </div>

    <!-- 5. Footer Versi & Info Status Sistem -->
    <div class="pt-3 border-t border-slate-100 text-center">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-[10px] font-bold text-slate-600">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>MCC Server v2.4 (Active)</span>
        </div>
    </div>
</aside>
