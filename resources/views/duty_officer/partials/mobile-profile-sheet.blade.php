<!-- ==================================================================== -->
<!-- MOBILE PROFILE & SOP BOTTOM SHEET / DRAWER -->
<!-- ==================================================================== -->
<div x-show="profileSheetOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="profile-sheet-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="profileSheetOpen"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="profileSheetOpen = false"
         class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"></div>

    <div class="flex min-h-full items-end justify-center text-center sm:items-center p-0 sm:p-4">
        <div x-show="profileSheetOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-t-3xl sm:rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-lg border border-slate-200 max-h-[90vh] flex flex-col">
            
            <!-- Drag Handle Bar for Mobile Touch Feel -->
            <div class="pt-3 pb-1 flex justify-center sm:hidden bg-slate-50 cursor-pointer" @click="profileSheetOpen = false">
                <div class="w-12 h-1.5 rounded-full bg-slate-300"></div>
            </div>

            <!-- Sheet Header -->
            <div class="p-4 sm:p-5 bg-gradient-to-br from-[#001A72] to-[#001250] text-white flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center font-black text-lg text-white shadow-inner shrink-0">
                        {{ strtoupper(substr(auth()->user()->full_name ?? 'D', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <span class="font-extrabold text-base tracking-tight text-white block truncate leading-tight">
                            {{ auth()->user()->full_name ?? 'Petugas Duty Officer' }}
                        </span>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Shift Aktif
                            </span>
                            <span class="text-[11px] text-slate-300 font-medium truncate font-mono">
                                {{ auth()->user()->phone_number ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                <button type="button" 
                        @click="profileSheetOpen = false"
                        class="p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition cursor-pointer shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <!-- Sheet Body (Scrollable) -->
            <div class="p-4 sm:p-5 space-y-4 overflow-y-auto custom-scrollbar flex-1 text-slate-800">
                
                <!-- Digital Clock & Shift Info Card -->
                <div class="p-3.5 rounded-2xl bg-[#EEF2FF] border border-indigo-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-black tracking-wider text-[#001A72] block">Waktu Operasional MCC</span>
                        <div class="text-xl font-black font-mono tracking-tight text-[#001A72] mt-0.5" x-text="currentTimeString">
                            --:--:-- WIB
                        </div>
                        <span class="text-[11px] font-medium text-slate-600">
                            {{ $target_date->translatedFormat('l, d F Y') }}
                        </span>
                    </div>

                    <div class="text-right space-y-1">
                        <span class="inline-block px-2.5 py-1 rounded-brand text-xs font-black bg-[#001A72] text-white shadow-2xs">
                            @if($filter->shift === 'morning')
                                Shift Pagi (08:00 - 16:00)
                            @elseif($filter->shift === 'evening')
                                Shift Sore (16:00 - 22:00)
                            @else
                                Semua Shift Operasional
                            @endif
                        </span>
                        <p class="text-[10px] text-slate-500 font-semibold">Gedung MCC Malang</p>
                    </div>
                </div>

                <!-- Ringkasan Metrik KPI Hari Ini -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">
                            Beban Tugas Hari Ini
                        </span>
                        <span class="text-[11px] font-bold text-[#001A72]">
                            Total: {{ $metrics['total'] ?? 0 }} Acara
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <!-- Perlu Buka -->
                        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-amber-800 block">Perlu Dibuka</span>
                                <span class="text-lg font-black text-amber-950">{{ $metrics['needs_opening'] ?? 0 }}</span>
                            </div>
                            <span class="w-3 h-3 rounded-full bg-amber-500 animate-ping"></span>
                        </div>

                        <!-- Berlangsung -->
                        <div class="p-3 rounded-xl bg-teal-50 border border-teal-200 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-teal-800 block">Berlangsung</span>
                                <span class="text-lg font-black text-teal-950">{{ $metrics['checked_in'] ?? 0 }}</span>
                            </div>
                            <span class="w-3 h-3 rounded-full bg-teal-500 animate-pulse"></span>
                        </div>

                        <!-- Akan Datang -->
                        <div class="p-3 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-[#001A72] block">Akan Datang</span>
                                <span class="text-lg font-black text-[#001A72]">{{ $metrics['upcoming'] ?? 0 }}</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>

                        <!-- Selesai -->
                        <div class="p-3 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-slate-600 block">Selesai / Ditutup</span>
                                <span class="text-lg font-black text-slate-800">{{ $metrics['completed'] ?? 0 }}</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                    </div>
                </div>

                <!-- SOP Lapangan Ringkas -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 block">
                        SOP Pendampingan &amp; Inspeksi
                    </span>
                    <div class="space-y-1.5 text-xs text-slate-600">
                        <div class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-[#001A72] text-white flex items-center justify-center text-[9px] font-black shrink-0 mt-0.5">1</span>
                            <span>Cek kesiapan fisik ruangan (AC, kabel HDMI, proyektor, audio) sebelum jam mulai.</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-[#001A72] text-white flex items-center justify-center text-[9px] font-black shrink-0 mt-0.5">2</span>
                            <span>Verifikasi alat bawaan mandiri pemohon dan lakukan serah terima ruangan via tombol Buka Akses.</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-[#001A72] text-white flex items-center justify-center text-[9px] font-black shrink-0 mt-0.5">3</span>
                            <span>Pantau bilah hitung mundur dan konfirmasi checkout fisik saat acara usai.</span>
                        </div>
                    </div>
                </div>

                <!-- Kontak Front Office (Resepsionis) -->
                <div class="p-3 rounded-xl bg-amber-50/80 border border-amber-200 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-900 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <div>
                            <span class="font-bold text-amber-950 block">Front Office Lt. 1</span>
                            <span class="text-[10px] text-amber-800">Koordinasi kustodian KTP &amp; check-in pemohon</span>
                        </div>
                    </div>
                </div>

                <!-- Modul Lain (Jika Super Admin / Front Office) -->
                @if(auth()->user()->isSuperAdmin() || auth()->user()->isFrontOffice() || auth()->user()->isMarketing())
                    <div class="pt-2 border-t border-slate-100 space-y-1.5">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block px-1">
                            Akses Modul Lain
                        </span>
                        
                        @if(auth()->user()->isFrontOffice() || auth()->user()->isSuperAdmin())
                            <a href="{{ route('front_office.manifest') }}" 
                               class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 hover:bg-[#EEF2FF] text-slate-700 hover:text-[#001A72] border border-slate-200 transition text-xs font-bold">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/></svg>
                                    <span>Front Office Manifes</span>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </a>
                        @endif

                        @if(auth()->user()->isMarketing() || auth()->user()->isSuperAdmin())
                            <a href="{{ route('marketing.dashboard') }}" 
                               class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 hover:bg-[#EEF2FF] text-slate-700 hover:text-[#001A72] border border-slate-200 transition text-xs font-bold">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                    <span>Portal Marketing</span>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </a>
                        @endif
                    </div>
                @endif

                <!-- Portal Publik -->
                <a href="{{ route('home') }}" 
                   target="_blank"
                   class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 hover:bg-[#EEF2FF] text-slate-700 hover:text-[#001A72] border border-slate-200 transition text-xs font-bold">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                        <span>Lihat Portal Publik MCC</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/></svg>
                </a>

            </div>

            <!-- Sheet Footer: Logout Button -->
            <div class="p-4 bg-slate-50 border-t border-slate-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 hover:border-rose-300 transition font-bold text-xs cursor-pointer shadow-xs active:scale-98"
                            style="min-height: 48px;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                        <span>Keluar dari Akun Duty Officer</span>
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
