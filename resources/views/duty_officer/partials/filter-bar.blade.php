<!-- ==================================================================== -->
<!-- MOBILE-FIRST INSTANT SEARCH & HORIZONTAL DRAG/SWIPE FILTER CHIPS -->
<!-- ==================================================================== -->
<div class="space-y-3.5">
    <form method="GET" 
          action="{{ route('duty_officer.dashboard') }}" 
          x-ref="filterForm"
          class="space-y-3">
        
        <!-- Keep Scope Parameter -->
        <input type="hidden" name="scope" value="{{ $filter->scope ?? 'all' }}">

        <!-- 1. Search Bar & Date Trigger -->
        <div class="flex items-center gap-2">
            <!-- Search Input Box -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
                
                <input type="text" 
                       name="search" 
                       value="{{ $filter->search }}" 
                       @input.debounce.400ms="$refs.filterForm.submit()"
                       placeholder="Cari acara, ruangan, PIC, alat..." 
                       class="block w-full pl-9 pr-8 py-2.5 text-xs bg-white rounded-2xl border border-slate-200 shadow-xs focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72]/20 placeholder:text-slate-400 transition"
                       style="min-height: 46px;">
                
                @if($filter->search)
                    <button type="button" 
                            @click="$el.closest('div').querySelector('input').value = ''; $refs.filterForm.submit()"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
                    </button>
                @endif
            </div>

            <!-- Date Picker Button / Input -->
            <div class="relative shrink-0">
                <input type="date" 
                       id="filter-date-input"
                       name="date" 
                       value="{{ $filter->date ?? now()->format('Y-m-d') }}" 
                       @change="$refs.filterForm.submit()"
                       class="opacity-0 absolute inset-0 w-full h-full cursor-pointer z-10">
                <button type="button" 
                        class="px-4 py-2.5 rounded-2xl bg-white border border-slate-200 shadow-2xs text-xs font-extrabold text-slate-800 flex items-center gap-2 hover:bg-slate-50 transition pointer-events-none"
                        style="min-height: 44px;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    <span class="whitespace-nowrap">{{ $target_date->isToday() ? 'Hari Ini' : $target_date->translatedFormat('d M') }}</span>
                </button>
            </div>
        </div>

        <!-- 2. Status Filter Group with Title -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between px-0.5">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">
                    Filter Status Tugas
                </span>
                <span class="text-[10px] text-slate-400 font-medium">Geser &rarr;</span>
            </div>

            <!-- Swipeable & Mouse-Draggable Status Filter Chips -->
            <div class="relative" 
                 x-data="horizontalScrollDrag()" 
                 @mousedown="startDrag($event)" 
                 @mouseleave="stopDrag()" 
                 @mouseup="stopDrag()" 
                 @mousemove="onDrag($event)">
                
                <div x-ref="scrollContainer"
                     class="flex items-center gap-2 overflow-x-auto no-scrollbar scroll-smooth snap-x touch-pan-x -mx-3.5 px-3.5 py-1 whitespace-nowrap select-none cursor-grab active:cursor-grabbing">
                    
                    <!-- All Status -->
                    <a href="{{ route('duty_officer.dashboard', array_merge(request()->except(['status']), ['status' => 'all'])) }}"
                       class="shrink-0 snap-start px-3.5 py-2 rounded-2xl text-xs font-extrabold transition-all flex items-center gap-1.5 shadow-2xs {{ empty($filter->status) || $filter->status === 'all' ? 'bg-[#001A72] text-white ring-2 ring-[#001A72]/30' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                        <span>Semua Status</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ empty($filter->status) || $filter->status === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">
                            {{ $metrics['total'] ?? 0 }}
                        </span>
                    </a>

                    <!-- Needs Opening (FR-DO-02) -->
                    <a href="{{ route('duty_officer.dashboard', array_merge(request()->except(['status']), ['status' => 'needs_opening'])) }}"
                       class="shrink-0 snap-start px-3.5 py-2 rounded-2xl text-xs font-extrabold transition-all flex items-center gap-1.5 shadow-2xs {{ $filter->status === 'needs_opening' ? 'bg-amber-500 text-slate-950 ring-2 ring-amber-500/40' : 'bg-amber-50 text-amber-900 border border-amber-300/80 hover:bg-amber-100' }}">
                        <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse"></span>
                        <span>Perlu Dibuka</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $filter->status === 'needs_opening' ? 'bg-slate-950 text-amber-300' : 'bg-amber-200 text-amber-950' }}">
                            {{ $metrics['needs_opening'] ?? 0 }}
                        </span>
                    </a>

                    <!-- Checked In / Ongoing -->
                    <a href="{{ route('duty_officer.dashboard', array_merge(request()->except(['status']), ['status' => 'checked_in'])) }}"
                       class="shrink-0 snap-start px-3.5 py-2 rounded-2xl text-xs font-extrabold transition-all flex items-center gap-1.5 shadow-2xs {{ $filter->status === 'checked_in' ? 'bg-teal-600 text-white ring-2 ring-teal-600/30' : 'bg-teal-50 text-teal-800 border border-teal-200 hover:bg-teal-100' }}">
                        <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                        <span>Sedang Berlangsung</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $filter->status === 'checked_in' ? 'bg-white/20 text-white' : 'bg-teal-100 text-teal-900' }}">
                            {{ $metrics['checked_in'] ?? 0 }}
                        </span>
                    </a>

                    <!-- Upcoming -->
                    <a href="{{ route('duty_officer.dashboard', array_merge(request()->except(['status']), ['status' => 'upcoming'])) }}"
                       class="shrink-0 snap-start px-3.5 py-2 rounded-2xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs {{ $filter->status === 'upcoming' ? 'bg-[#001A72] text-white ring-2 ring-[#001A72]/30' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                        <span>Akan Datang</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold {{ $filter->status === 'upcoming' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">
                            {{ $metrics['upcoming'] ?? 0 }}
                        </span>
                    </a>

                    <!-- Completed -->
                    <a href="{{ route('duty_officer.dashboard', array_merge(request()->except(['status']), ['status' => 'completed'])) }}"
                       class="shrink-0 snap-start px-3.5 py-2 rounded-2xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs {{ $filter->status === 'completed' ? 'bg-slate-700 text-white ring-2 ring-slate-700/30' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                        <span>Selesai</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold {{ $filter->status === 'completed' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">
                            {{ $metrics['completed'] ?? 0 }}
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 3. Floor Filter Group with Title -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between px-0.5">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">
                    Filter Lantai Ruangan
                </span>
                <span class="text-[10px] text-slate-400 font-medium">Geser &rarr;</span>
            </div>

            <!-- Swipeable & Mouse-Draggable Floor Chips -->
            <div class="relative" 
                 x-data="horizontalScrollDrag()" 
                 @mousedown="startDrag($event)" 
                 @mouseleave="stopDrag()" 
                 @mouseup="stopDrag()" 
                 @mousemove="onDrag($event)">
                
                <div x-ref="scrollContainer"
                     class="flex items-center gap-1.5 overflow-x-auto no-scrollbar scroll-smooth snap-x touch-pan-x -mx-3.5 px-3.5 py-0.5 text-xs select-none whitespace-nowrap cursor-grab active:cursor-grabbing">
                    
                    <!-- All Floors -->
                    <a href="{{ route('duty_officer.dashboard', array_merge(request()->except(['floor']), ['floor' => ''])) }}"
                       class="shrink-0 snap-start px-3 py-1.5 rounded-xl font-bold text-xs transition-all {{ empty($filter->floor) ? 'bg-slate-800 text-white shadow-2xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                        Semua Lantai
                    </a>

                    @foreach($floors as $floorNum)
                        <a href="{{ route('duty_officer.dashboard', array_merge(request()->except(['floor']), ['floor' => $floorNum])) }}"
                           class="shrink-0 snap-start px-3 py-1.5 rounded-xl font-bold text-xs transition-all {{ $filter->floor == $floorNum ? 'bg-[#001A72] text-white shadow-2xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100' }}">
                            Lantai {{ $floorNum }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Active Filter Indicator & Reset Button -->
        @if($filter->search || ($filter->status && $filter->status !== 'all') || $filter->floor || ($filter->date && $filter->date !== now()->format('Y-m-d')))
            <div class="pt-1 flex items-center justify-between gap-2 text-xs">
                <span class="text-[11px] text-slate-500 font-semibold flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#001A72]"></span>
                    Filter aktif diterapkan
                </span>
                <a href="{{ route('duty_officer.dashboard', ['scope' => $filter->scope ?? 'all']) }}" 
                   class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-rose-600 hover:text-white hover:bg-rose-600 bg-rose-50 px-3 py-1.5 rounded-xl border border-rose-200 shadow-2xs transition active:scale-95 cursor-pointer"
                   style="min-height: 36px;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    <span>Reset Filter</span>
                </a>
            </div>
        @endif

    </form>
</div>
