<header class="bg-[#001A72] text-white shadow-md sticky top-0 z-40 border-b border-white/10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between gap-3">
        <!-- Brand & DO Status -->
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('duty_officer.dashboard') }}" class="shrink-0 flex items-center gap-2">
                <img src="{{ asset('assets/images/media/branding/mcc-aset-logo blue 1.png') }}" 
                     alt="MCC Logo" 
                     class="h-8 w-auto brightness-0 invert object-contain">
            </a>
            <div class="border-l border-white/20 pl-3 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black uppercase tracking-wider text-indigo-200">Duty Officer</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500 text-white shadow-xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse mr-1"></span>
                        Shift Aktif
                    </span>
                </div>
                <h1 class="text-sm sm:text-base font-black truncate text-white tracking-tight leading-tight">
                    {{ auth()->user()->full_name ?? 'Petugas Lapangan' }}
                </h1>
            </div>
        </div>

        <!-- Real-time Clock & Actions -->
        <div class="flex items-center gap-2 shrink-0">
            <!-- Digital Clock Badge (Mobile-optimized) -->
            <div class="hidden sm:flex flex-col items-end text-right px-2.5 py-1 rounded-brand bg-white/10 border border-white/15">
                <span class="text-[10px] uppercase font-semibold text-slate-200 tracking-wider">Waktu Lapangan</span>
                <span class="text-xs font-mono font-bold text-white" x-text="currentTimeString">--:--:-- WIB</span>
            </div>

            <!-- Reload / Quick Sync -->
            <button type="button" 
                    @click="window.location.reload()" 
                    title="Segarkan Data"
                    class="w-11 h-11 inline-flex items-center justify-center rounded-brand bg-white/10 hover:bg-white/20 text-white border border-white/15 transition cursor-pointer active:scale-95"
                    style="min-width: 44px; min-height: 44px;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>
            </button>

            <!-- User Menu / Logout -->
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button type="button" 
                        @click="open = !open"
                        class="w-11 h-11 inline-flex items-center justify-center rounded-brand bg-white text-[#001A72] font-black text-sm shadow-xs transition hover:bg-slate-100 cursor-pointer active:scale-95"
                        style="min-width: 44px; min-height: 44px;">
                    {{ strtoupper(substr(auth()->user()->full_name ?? 'D', 0, 1)) }}
                </button>

                <div x-show="open" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-56 bg-white rounded-brand shadow-xl border border-slate-200 py-1.5 z-50 text-slate-900">
                    <div class="px-4 py-2 border-b border-slate-100">
                        <p class="text-xs text-slate-500 font-medium">Masuk sebagai</p>
                        <p class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->full_name }}</p>
                        <span class="inline-block mt-0.5 px-2 py-0.5 text-[10px] font-semibold bg-indigo-50 text-[#001A72] rounded-full">
                            Duty Officer MCC
                        </span>
                    </div>

                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isFrontOffice())
                        <a href="{{ route('front_office.manifest') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-[#EEF2FF] hover:text-[#001A72]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/></svg>
                            <span>Portal Front Office</span>
                        </a>
                    @endif

                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-[#EEF2FF] hover:text-[#001A72]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <span>Halaman Publik</span>
                    </a>

                    <div class="border-t border-slate-100 my-1"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 text-left cursor-pointer"
                                style="min-height: 44px;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                            <span>Keluar Akun</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
