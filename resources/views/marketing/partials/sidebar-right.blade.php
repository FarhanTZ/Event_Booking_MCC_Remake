<!-- ==================================================================== -->
<!-- SISI KANAN: PROFIL USER & STACK 4 KARTU KPI METRIK (FIXED / STICKY FULL-HEIGHT) -->
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
        <!-- 1. Profil Petugas Marketing Snapshot Card (Direct Top) -->
        <div class="bg-gradient-to-b from-slate-50 to-white rounded-brand border border-slate-200 p-3.5 shadow-2xs space-y-3">
            <!-- Profile Info with Close Button -->
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
                            {{ auth()->user()->full_name ?? auth()->user()->name ?? 'Petugas Marketing' }}
                        </span>
                        <span class="text-slate-500 text-[11px] block truncate leading-tight mt-0.5">
                            {{ auth()->user()->email ?? 'marketing@mcc.id' }}
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
                    <span>Kurator MCC</span>
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
                    Beban Kerja Kurasi
                </span>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Realtime</span>
            </span>
        </div>

        <!-- 3. 4 Stacked KPI Cards (White Cards, Colored Titles, Dark Text) -->
        <div class="space-y-2.5">
            
            <!-- 1. Permohonan -->
            <a href="{{ route('marketing.dashboard', ['status' => 'awaiting_marketing']) }}" 
               class="block p-3 rounded-brand border border-slate-200 bg-white hover:border-amber-400 hover:shadow-xs transition group {{ $currentStatus === 'awaiting_marketing' ? 'ring-2 ring-amber-500 border-amber-400' : '' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-amber-600 group-hover:text-amber-700 transition">Permohonan</span>
                    <div class="w-6 h-6 rounded-brand bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                </div>
                <div class="mt-1">
                    <span class="text-2xl font-black text-slate-900">{{ $metrics['awaiting_curation'] }}</span>
                </div>
                <p class="mt-0.5 text-[10px] text-slate-500 leading-tight">Antrean baru belum dinilai</p>
            </a>

            <!-- 2. Revisi Aktif -->
            <a href="{{ route('marketing.dashboard', ['status' => 'revised']) }}" 
               class="block p-3 rounded-brand border border-slate-200 bg-white hover:border-indigo-400 hover:shadow-xs transition group {{ $currentStatus === 'revised' ? 'ring-2 ring-indigo-500 border-indigo-400' : '' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-indigo-600 group-hover:text-indigo-700 transition">Revisi Aktif</span>
                    <div class="w-6 h-6 rounded-brand bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>
                    </div>
                </div>
                <div class="mt-1">
                    <span class="text-2xl font-black text-slate-900">{{ $metrics['revised'] }}</span>
                </div>
                <p class="mt-0.5 text-[10px] text-slate-500 leading-tight">Masa perbaikan pemohon (24 Jam)</p>
            </a>

            <!-- 3. Verifikasi Bayar -->
            <a href="{{ route('marketing.dashboard', ['status' => 'awaiting_payment']) }}" 
               class="block p-3 rounded-brand border border-slate-200 bg-white hover:border-purple-400 hover:shadow-xs transition group {{ $currentStatus === 'awaiting_payment' ? 'ring-2 ring-purple-500 border-purple-400' : '' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-purple-600 group-hover:text-purple-700 transition">Verifikasi Bayar</span>
                    <div class="w-6 h-6 rounded-brand bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                    </div>
                </div>
                <div class="mt-1">
                    <span class="text-2xl font-black text-slate-900">{{ $metrics['awaiting_payment'] }}</span>
                </div>
                <p class="mt-0.5 text-[10px] text-slate-500 leading-tight">Menunggu bukti transfer tarif</p>
            </a>

            <!-- 4. Acara Sah -->
            <a href="{{ route('marketing.dashboard', ['status' => 'confirmed']) }}" 
               class="block p-3 rounded-brand border border-slate-200 bg-white hover:border-emerald-400 hover:shadow-xs transition group {{ $currentStatus === 'confirmed' ? 'ring-2 ring-emerald-500 border-emerald-400' : '' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-600 group-hover:text-emerald-700 transition">Acara Sah</span>
                    <div class="w-6 h-6 rounded-brand bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                </div>
                <div class="mt-1">
                    <span class="text-2xl font-black text-slate-900">{{ $metrics['approved_this_month'] }}</span>
                </div>
                <p class="mt-0.5 text-[10px] text-slate-500 leading-tight">Total Sah Keseluruhan: <strong class="font-bold text-slate-800">{{ $metrics['total_confirmed'] }}</strong></p>
            </a>

        </div>
    </div>
</aside>
