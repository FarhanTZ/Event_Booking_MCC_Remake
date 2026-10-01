<!-- ==================================================================== -->
<!-- BOTTOM NAVBAR: [ SEMUA TUGAS (KIRI) | TUGAS SENDIRI (KANAN) ] -->
<!-- ==================================================================== -->
<nav class="fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/80 shadow-[0_-4px_25px_rgba(0,0,0,0.08)] md:max-w-lg md:mx-auto pb-safe">
    <div class="grid grid-cols-2 h-16 items-center p-2 gap-2">
        
        <!-- Tab Kiri: Semua Tugas (Gedung MCC) -->
        <a href="{{ route('duty_officer.dashboard', array_merge(request()->except(['scope']), ['scope' => 'all'])) }}"
           class="h-12 flex items-center justify-center gap-2 rounded-2xl font-black text-xs transition-all active:scale-98 shadow-2xs {{ $filter->scope !== 'assigned' ? 'bg-[#001A72] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            <span>Semua Tugas</span>
            @if(($metrics['needs_opening'] ?? 0) > 0)
                <span class="px-2 py-0.5 rounded-full bg-white text-[#001A72] font-black text-[11px] leading-tight shadow-xs flex items-center justify-center shrink-0">
                    {{ $metrics['needs_opening'] }}
                </span>
            @endif
        </a>

        <!-- Tab Kanan: Tugas Sendiri -->
        <a href="{{ route('duty_officer.dashboard', array_merge(request()->except(['scope']), ['scope' => 'assigned'])) }}"
           class="h-12 flex items-center justify-center gap-2 rounded-2xl font-black text-xs transition-all active:scale-98 shadow-2xs {{ $filter->scope === 'assigned' ? 'bg-[#001A72] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>Tugas Sendiri</span>
        </a>

    </div>
</nav>
