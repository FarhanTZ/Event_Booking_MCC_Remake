<div class="space-y-3.5" x-data="{ advancedFilterOpen: false }">
    <!-- 1. KPI & Quick Status Metrics Bar (Touch-friendly horizontal scroll on mobile) -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 sm:gap-3">
        <!-- Tab: Semua -->
        <a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}" 
           class="p-3 rounded-brand border transition shadow-card flex items-center justify-between {{ ($filter->status === 'all' || empty($filter->status)) ? 'bg-[#001A72] text-white border-[#001A72] ring-2 ring-[#001A72]/20' : 'bg-white text-slate-800 border-slate-200 hover:border-slate-300' }}"
           style="min-height: 52px;">
            <div class="min-w-0">
                <span class="block text-[11px] font-bold uppercase tracking-wider {{ ($filter->status === 'all' || empty($filter->status)) ? 'text-slate-200' : 'text-slate-500' }}">Total Tugas</span>
                <span class="text-xl font-black">{{ $metrics['total'] }}</span>
            </div>
            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ ($filter->status === 'all' || empty($filter->status)) ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>
            </div>
        </a>

        <!-- Tab: Perlu Dibuka / Check-in Baru (FR-DO-02) -->
        <a href="{{ request()->fullUrlWithQuery(['status' => 'needs_opening']) }}" 
           class="p-3 rounded-brand border transition shadow-card flex items-center justify-between {{ $filter->status === 'needs_opening' ? 'bg-amber-600 text-white border-amber-600 ring-2 ring-amber-500/40' : 'bg-white text-slate-800 border-slate-200 hover:border-amber-300' }}"
           style="min-height: 52px;">
            <div class="min-w-0">
                <span class="block text-[11px] font-bold uppercase tracking-wider {{ $filter->status === 'needs_opening' ? 'text-amber-100' : 'text-amber-600 font-extrabold' }}">Perlu Buka</span>
                <div class="flex items-center gap-1.5">
                    <span class="text-xl font-black">{{ $metrics['needs_opening'] ?? 0 }}</span>
                    @if(($metrics['needs_opening'] ?? 0) > 0)
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                    @endif
                </div>
            </div>
            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $filter->status === 'needs_opening' ? 'bg-white/20 text-white' : 'bg-amber-50 text-amber-700' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21 2-2 2m-6 6 7 7-4 4-7-7"/><path d="m3 21 6-6"/><path d="m15 9-6 6"/></svg>
            </div>
        </a>

        <!-- Tab: Berlangsung (Checked-in) -->
        <a href="{{ request()->fullUrlWithQuery(['status' => 'checked_in']) }}" 
           class="p-3 rounded-brand border transition shadow-card flex items-center justify-between {{ $filter->status === 'checked_in' ? 'bg-teal-700 text-white border-teal-700 ring-2 ring-teal-600/30' : 'bg-white text-slate-800 border-slate-200 hover:border-teal-300' }}"
           style="min-height: 52px;">
            <div class="min-w-0">
                <span class="block text-[11px] font-bold uppercase tracking-wider {{ $filter->status === 'checked_in' ? 'text-teal-100' : 'text-teal-600' }}">Berlangsung</span>
                <div class="flex items-center gap-1.5">
                    <span class="text-xl font-black">{{ $metrics['checked_in'] }}</span>
                    @if($metrics['checked_in'] > 0)
                        <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                    @endif
                </div>
            </div>
            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $filter->status === 'checked_in' ? 'bg-white/20 text-white' : 'bg-teal-50 text-teal-700' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
        </a>

        <!-- Tab: Akan Datang (Upcoming) -->
        <a href="{{ request()->fullUrlWithQuery(['status' => 'upcoming']) }}" 
           class="p-3 rounded-brand border transition shadow-card flex items-center justify-between {{ $filter->status === 'upcoming' ? 'bg-emerald-700 text-white border-emerald-700 ring-2 ring-emerald-600/30' : 'bg-white text-slate-800 border-slate-200 hover:border-emerald-300' }}"
           style="min-height: 52px;">
            <div class="min-w-0">
                <span class="block text-[11px] font-bold uppercase tracking-wider {{ $filter->status === 'upcoming' ? 'text-emerald-100' : 'text-emerald-600' }}">Akan Datang</span>
                <span class="text-xl font-black">{{ $metrics['upcoming'] }}</span>
            </div>
            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $filter->status === 'upcoming' ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-700' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
            </div>
        </a>

        <!-- Tab: Selesai (Completed) -->
        <a href="{{ request()->fullUrlWithQuery(['status' => 'completed']) }}" 
           class="p-3 rounded-brand border transition shadow-card flex items-center justify-between {{ $filter->status === 'completed' ? 'bg-slate-800 text-white border-slate-800 ring-2 ring-slate-700/30' : 'bg-white text-slate-800 border-slate-200 hover:border-slate-300' }}"
           style="min-height: 52px;">
            <div class="min-w-0">
                <span class="block text-[11px] font-bold uppercase tracking-wider {{ $filter->status === 'completed' ? 'text-slate-300' : 'text-slate-500' }}">Selesai</span>
                <span class="text-xl font-black">{{ $metrics['completed'] }}</span>
            </div>
            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $filter->status === 'completed' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
        </a>
    </div>

    <!-- 2. Search & Main Filter Control Bar -->
    <div class="bg-white p-3.5 sm:p-4 rounded-brand border border-slate-200 shadow-card space-y-3">
        <!-- Search & Date Bar -->
        <form method="GET" action="{{ route('duty_officer.dashboard') }}" class="space-y-3">
            <!-- Retain active filters in hidden inputs -->
            @if($filter->status && $filter->status !== 'all')
                <input type="hidden" name="status" value="{{ $filter->status }}">
            @endif
            @if($filter->scope && $filter->scope !== 'assigned')
                <input type="hidden" name="scope" value="{{ $filter->scope }}">
            @endif
            @if($filter->floor)
                <input type="hidden" name="floor" value="{{ $filter->floor }}">
            @endif
            @if($filter->shift && $filter->shift !== 'all')
                <input type="hidden" name="shift" value="{{ $filter->shift }}">
            @endif

            <div class="flex flex-col sm:flex-row gap-2.5">
                <!-- Search Input Field -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ $filter->search }}" 
                           placeholder="Cari acara, ruangan, PIC, atau alat..." 
                           class="w-full pl-9 pr-8 py-2.5 bg-slate-50 border border-slate-300 rounded-brand text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#001A72] focus:border-transparent transition"
                           style="min-height: 44px;">
                    @if($filter->search)
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" 
                           class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </a>
                    @endif
                </div>

                <!-- Date Filter Input -->
                <div class="flex items-center gap-2">
                    <input type="date" 
                           name="date" 
                           value="{{ $filter->date }}" 
                           class="w-full sm:w-auto px-3 py-2 bg-slate-50 border border-slate-300 rounded-brand text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#001A72]"
                           style="min-height: 44px;">
                    
                    <button type="submit" 
                            class="px-4 py-2 bg-[#001A72] hover:bg-[#001250] text-white font-bold text-xs sm:text-sm rounded-brand shadow-xs transition flex items-center gap-1.5 shrink-0 cursor-pointer active:scale-95"
                            style="min-height: 44px;">
                        <span>Terapkan</span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Filter Buttons: Scope & Shift Switcher -->
        <div class="flex flex-wrap items-center justify-between gap-2.5 pt-2 border-t border-slate-100">
            <!-- Scope Switcher: Tugas Saya vs Semua Tugas Lapangan -->
            <div class="inline-flex rounded-brand bg-slate-100 p-1 border border-slate-200">
                <a href="{{ request()->fullUrlWithQuery(['scope' => 'assigned']) }}" 
                   class="px-3 py-1.5 text-xs font-bold rounded-[8px] transition {{ ($filter->scope === 'assigned' || empty($filter->scope)) ? 'bg-[#001A72] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"
                   style="min-height: 36px; display: inline-flex; items-center;">
                    Tugas Saya
                </a>
                <a href="{{ request()->fullUrlWithQuery(['scope' => 'all']) }}" 
                   class="px-3 py-1.5 text-xs font-bold rounded-[8px] transition {{ $filter->scope === 'all' ? 'bg-[#001A72] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"
                   style="min-height: 36px; display: inline-flex; items-center;">
                    Semua Penugasan
                </a>
            </div>

            <!-- Shift Switcher: Semua, Shift Pagi, Shift Sore -->
            <div class="inline-flex rounded-brand bg-slate-100 p-1 border border-slate-200">
                <a href="{{ request()->fullUrlWithQuery(['shift' => 'all']) }}" 
                   class="px-2.5 py-1.5 text-[11px] font-bold rounded-[8px] transition {{ ($filter->shift === 'all' || empty($filter->shift)) ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500 hover:text-slate-900' }}"
                   style="min-height: 36px; display: inline-flex; items-center;">
                    Semua Shift
                </a>
                <a href="{{ request()->fullUrlWithQuery(['shift' => 'morning']) }}" 
                   class="px-2.5 py-1.5 text-[11px] font-bold rounded-[8px] transition {{ $filter->shift === 'morning' ? 'bg-white text-[#001A72] shadow-2xs font-extrabold' : 'text-slate-500 hover:text-slate-900' }}"
                   style="min-height: 36px; display: inline-flex; items-center;">
                    Pagi (08-16)
                </a>
                <a href="{{ request()->fullUrlWithQuery(['shift' => 'evening']) }}" 
                   class="px-2.5 py-1.5 text-[11px] font-bold rounded-[8px] transition {{ $filter->shift === 'evening' ? 'bg-white text-[#001A72] shadow-2xs font-extrabold' : 'text-slate-500 hover:text-slate-900' }}"
                   style="min-height: 36px; display: inline-flex; items-center;">
                    Sore (16-22)
                </a>
            </div>
        </div>

        <!-- 3. Floor Level Filter Pills (Horizontal Scrollable) -->
        <div class="pt-2 border-t border-slate-100">
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 custom-scrollbar">
                <span class="text-[11px] font-bold uppercase text-slate-400 tracking-wider shrink-0 mr-1">Lantai:</span>
                
                <!-- All Floors Pill -->
                <a href="{{ request()->fullUrlWithQuery(['floor' => null]) }}" 
                   class="px-3 py-1 text-xs font-bold rounded-full transition shrink-0 {{ empty($filter->floor) ? 'bg-[#001A72] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
                   style="min-height: 34px; display: inline-flex; align-items: center;">
                    Semua Lantai
                </a>

                @foreach($floors as $floorNum)
                    <a href="{{ request()->fullUrlWithQuery(['floor' => $floorNum]) }}" 
                       class="px-3 py-1 text-xs font-bold rounded-full transition shrink-0 {{ $filter->floor === $floorNum ? 'bg-[#001A72] text-white shadow-2xs' : 'bg-slate-100 text-slate-700 hover:bg-[#EEF2FF] hover:text-[#001A72]' }}"
                       style="min-height: 34px; display: inline-flex; align-items: center;">
                        Lt. {{ $floorNum }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
