<header class="bg-[#001A72] text-white border-b border-indigo-900 sticky top-0 z-40 shadow-md">
    <div class="max-w-7xl 2xl:max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            
            <!-- SISI KIRI: Brand Logo, Badge Portal Marketing & Tombol Ke Landing Page -->
            <div class="flex items-center gap-4 sm:gap-6">
                <!-- MCC Logo -->
                <a href="{{ route('marketing.dashboard') }}" class="flex items-center gap-3 group shrink-0">
                    <img src="{{ asset('assets/images/media/branding/mcc-aset-logo blue 1.png') }}" 
                         alt="Malang Creative Center Logo" 
                         class="h-8 sm:h-9 w-auto object-contain brightness-0 invert group-hover:opacity-90 transition-opacity">
                </a>

                <div class="h-6 w-px bg-white/20 hidden sm:block"></div>

                <!-- Portal Badge -->
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-brand bg-white/10 text-amber-300 text-xs font-extrabold border border-white/15 tracking-wide uppercase">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span>Portal Kurasi Marketing</span>
                    </span>
                </div>

                <!-- Tombol Kembali / Kunjungi Landing Page -->
                <a href="/" 
                   target="_blank"
                   title="Buka Website Publik / Landing Page MCC di Tab Baru"
                   class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-brand bg-white/10 hover:bg-white/20 text-xs font-semibold text-slate-100 hover:text-white border border-white/20 transition-colors shadow-2xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-indigo-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                    <span>Kunjungi Landing Page</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-slate-300 ml-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                </a>
            </div>

            <!-- SISI KANAN: Tanggal, Profil Petugas Marketing & Tombol Keluar (Logout) -->
            <div class="flex items-center gap-3 sm:gap-4 shrink-0">
                
                <!-- Link Mobile ke Landing Page -->
                <a href="/" 
                   target="_blank"
                   title="Buka Landing Page"
                   class="md:hidden p-2 rounded-brand bg-white/10 text-white hover:bg-white/20 text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                </a>

                <!-- Tanggal Hari Ini -->
                <div class="hidden lg:flex items-center gap-1.5 px-3 py-1.5 rounded-brand bg-white/10 text-xs text-indigo-100 font-semibold border border-white/15">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>

                <!-- User Profile Snapshot -->
                <div class="flex items-center gap-2.5 pl-2 sm:pl-3 sm:border-l border-white/20">
                    <div class="w-8 h-8 rounded-brand bg-amber-400 text-slate-950 font-black text-xs flex items-center justify-center shadow-xs">
                        {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->name ?? 'M', 0, 1)) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <span class="font-bold text-white text-xs block leading-tight truncate max-w-[130px]">
                            {{ auth()->user()->full_name ?? auth()->user()->name ?? 'Petugas Marketing' }}
                        </span>
                        <span class="text-amber-300 text-[10px] font-semibold block leading-tight">
                            Kurator MCC
                        </span>
                    </div>
                </div>

                <!-- Tombol Logout -->
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" 
                            title="Keluar dari Portal Marketing"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-brand bg-rose-600/90 hover:bg-rose-600 text-white text-xs font-bold transition shadow-xs cursor-pointer border border-rose-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>

            </div>
        </div>
    </div>
</header>
