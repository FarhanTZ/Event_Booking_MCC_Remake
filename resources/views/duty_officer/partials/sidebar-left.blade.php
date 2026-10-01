<!-- ==================================================================== -->
<!-- SISI KIRI: NAVBAR MENU & FITUR DUTY OFFICER (FIXED / STICKY FULL-HEIGHT) -->
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
                        MCC Duty Officer
                    </span>
                    <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider block leading-tight mt-0.5">
                        Operasional &amp; Inspeksi
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
        <a href="{{ route('home') }}" 
           target="_blank"
           class="flex items-center justify-between px-3.5 py-2.5 rounded-brand bg-slate-50 hover:bg-[#EEF2FF] text-slate-700 hover:text-[#001A72] border border-slate-200 hover:border-indigo-200 transition group shadow-2xs">
            <div class="flex items-center gap-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 group-hover:text-[#001A72] transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                <span class="text-xs font-bold">Kunjungi Portal Publik</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400 group-hover:text-[#001A72] group-hover:translate-x-0.5 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
        </a>

        <!-- 3. Section: Fitur & Menu Navigasi Utama -->
        <div class="space-y-1.5">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-2.5 block mb-1.5">
                Menu Utama DO
            </span>

            <!-- Dasbor Tugas Lapangan -->
            <a href="{{ route('duty_officer.dashboard') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-brand font-bold text-xs {{ request()->routeIs('duty_officer.dashboard') && empty(request('floor')) && empty(request('shift')) ? 'bg-[#001A72] text-white shadow-xs' : 'text-slate-600 hover:text-[#001A72] hover:bg-slate-50' }} transition">
                <div class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ request()->routeIs('duty_officer.dashboard') && empty(request('floor')) && empty(request('shift')) ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    <span>Dasbor Tugas Lapangan</span>
                </div>
                <span class="px-1.5 py-0.5 text-[10px] rounded font-mono font-bold {{ request()->routeIs('duty_officer.dashboard') && empty(request('floor')) && empty(request('shift')) ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                    {{ $metrics['total'] ?? 0 }}
                </span>
            </a>

            <!-- Tugas Saya (Quick Scope) -->
            <a href="{{ route('duty_officer.dashboard', ['scope' => 'assigned']) }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-brand font-bold text-xs {{ $filter->scope === 'assigned' ? 'bg-[#EEF2FF] text-[#001A72] border border-indigo-200' : 'text-slate-600 hover:text-[#001A72] hover:bg-slate-50' }} transition">
                <div class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Penugasan Saya</span>
                </div>
                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
            </a>

            <!-- Jadwal Kalender Gedung -->
            <a href="{{ route('calendar.index') }}" 
               target="_blank"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-brand font-bold text-xs text-slate-600 hover:text-[#001A72] hover:bg-slate-50 transition">
                <div class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                    <span>Matriks Kalender Gedung</span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/></svg>
            </a>
        </div>

        <!-- 4. Section: Filter Cepat Lantai Operasional -->
        <div class="space-y-1.5 pt-2 border-t border-slate-100">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-2.5 block mb-1">
                Navigasi Lantai MCC
            </span>

            <div class="grid grid-cols-2 gap-1.5 px-1">
                @foreach($floors as $floorNum)
                    <a href="{{ route('duty_officer.dashboard', array_merge(request()->query(), ['floor' => $floorNum])) }}" 
                       class="flex items-center justify-between px-2.5 py-2 rounded-brand text-xs font-bold transition {{ $filter->floor === $floorNum ? 'bg-[#001A72] text-white shadow-2xs' : 'bg-slate-50 text-slate-700 hover:bg-[#EEF2FF] hover:text-[#001A72] border border-slate-200' }}">
                        <span>Lantai {{ $floorNum }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 {{ $filter->floor === $floorNum ? 'text-white' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- 5. Cross Portal Links (Jika Super Admin atau Front Office) -->
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isFrontOffice() || auth()->user()->isMarketing())
            <div class="space-y-1.5 pt-2 border-t border-slate-100">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-2.5 block mb-1">
                    Modul Lainnya
                </span>

                @if(auth()->user()->isFrontOffice() || auth()->user()->isSuperAdmin())
                    <a href="{{ route('front_office.manifest') }}" 
                       class="flex items-center justify-between px-3.5 py-2 rounded-brand font-bold text-xs text-slate-600 hover:text-[#001A72] hover:bg-slate-50 transition">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/></svg>
                            <span>Front Office Manifes</span>
                        </div>
                    </a>
                @endif

                @if(auth()->user()->isMarketing() || auth()->user()->isSuperAdmin())
                    <a href="{{ route('marketing.dashboard') }}" 
                       class="flex items-center justify-between px-3.5 py-2 rounded-brand font-bold text-xs text-slate-600 hover:text-[#001A72] hover:bg-slate-50 transition">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <span>Portal Kurasi Marketing</span>
                        </div>
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- 6. Footer Profil & Tombol Keluar -->
    <div class="pt-4 border-t border-slate-100 space-y-3">
        <div class="flex items-center gap-3 px-1">
            <div class="w-9 h-9 rounded-full bg-[#001A72] text-white flex items-center justify-center font-bold text-xs shadow-2xs shrink-0">
                {{ strtoupper(substr(auth()->user()->full_name ?? 'D', 0, 1)) }}
            </div>
            <div class="min-w-0">
                <span class="text-xs font-bold text-slate-900 block truncate">
                    {{ auth()->user()->full_name ?? 'Duty Officer' }}
                </span>
                <span class="text-[10px] text-emerald-600 font-semibold block flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Shift Aktif
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                    class="w-full flex items-center justify-center gap-2 px-3.5 py-2 rounded-brand bg-slate-50 hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200 hover:border-rose-200 transition font-bold text-xs cursor-pointer shadow-2xs"
                    style="min-height: 40px;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                <span>Keluar dari Akun</span>
            </button>
        </form>
    </div>
</aside>
