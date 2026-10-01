<!-- ==================================================================== -->
<!-- BOTTOM NAVIGATION BAR (MOBILE APP FEEL) -->
<!-- ==================================================================== -->
<nav class="fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/80 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] md:max-w-lg md:mx-auto pb-safe">
    <div class="grid grid-cols-4 h-16 items-center px-2">
        
        <!-- Tab 1: Dasbor Tugas (All Tasks) -->
        <a href="{{ route('duty_officer.dashboard', array_merge(request()->except(['scope']), ['scope' => 'all'])) }}"
           class="flex flex-col items-center justify-center py-1 group transition {{ $filter->scope !== 'assigned' ? 'text-[#001A72]' : 'text-slate-600 hover:text-slate-800' }}">
            <div class="relative flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform group-active:scale-90 {{ $filter->scope !== 'assigned' ? 'stroke-[2.5]' : 'stroke-2' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                @if(($metrics['needs_opening'] ?? 0) > 0)
                    <span class="absolute -top-1 -right-2 min-w-[16px] h-4 px-1 rounded-full bg-amber-500 text-slate-950 font-black text-[9px] flex items-center justify-center animate-pulse">
                        {{ $metrics['needs_opening'] }}
                    </span>
                @endif
            </div>
            <span class="text-[10px] font-bold mt-0.5 tracking-tight {{ $filter->scope !== 'assigned' ? 'text-[#001A72] font-extrabold' : '' }}">
                Semua Tugas
            </span>
        </a>

        <!-- Tab 2: Penugasan Saya (My Tasks) -->
        <a href="{{ route('duty_officer.dashboard', array_merge(request()->except(['scope']), ['scope' => 'assigned'])) }}"
           class="flex flex-col items-center justify-center py-1 group transition {{ $filter->scope === 'assigned' ? 'text-[#001A72]' : 'text-slate-600 hover:text-slate-800' }}">
            <div class="relative flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform group-active:scale-90 {{ $filter->scope === 'assigned' ? 'stroke-[2.5]' : 'stroke-2' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span class="w-2 h-2 rounded-full bg-teal-500 absolute -top-0.5 -right-1"></span>
            </div>
            <span class="text-[10px] font-bold mt-0.5 tracking-tight {{ $filter->scope === 'assigned' ? 'text-[#001A72] font-extrabold' : '' }}">
                Tugas Saya
            </span>
        </a>

        <!-- Tab 3: Kalender Gedung -->
        <a href="{{ route('calendar.index') }}"
           target="_blank"
           class="flex flex-col items-center justify-center py-1 group text-slate-600 hover:text-[#001A72] transition">
            <div class="relative flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-2 transition-transform group-active:scale-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
            </div>
            <span class="text-[10px] font-bold mt-0.5 tracking-tight">
                Kalender
            </span>
        </a>

        <!-- Tab 4: Profil & SOP (Bottom Sheet Trigger) -->
        <button type="button" 
                @click="profileSheetOpen = true"
                class="flex flex-col items-center justify-center py-1 group text-slate-600 hover:text-[#001A72] transition cursor-pointer">
            <div class="relative flex items-center justify-center">
                <div class="w-6 h-6 rounded-full bg-[#001A72] text-white flex items-center justify-center text-[10px] font-black shadow-2xs">
                    {{ strtoupper(substr(auth()->user()->full_name ?? 'D', 0, 1)) }}
                </div>
                <span class="w-2 h-2 rounded-full bg-emerald-500 border border-white absolute -bottom-0.5 -right-0.5"></span>
            </div>
            <span class="text-[10px] font-bold mt-0.5 tracking-tight">
                Profil &amp; SOP
            </span>
        </button>

    </div>
</nav>
