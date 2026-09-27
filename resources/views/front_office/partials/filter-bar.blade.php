<!-- ==================================================================== -->
<!-- MULTI-PARAMETER INSTANT SEARCH & FILTER BAR (FRONT OFFICE) -->
<!-- ==================================================================== -->
<div class="bg-white rounded-brand border border-slate-200 shadow-card p-5 space-y-4">
    <form method="GET" 
          action="{{ route('front_office.manifest') }}" 
          x-ref="filterForm"
          class="space-y-3.5">

        <!-- Row 1: Search, Status Dropdown, Lantai & Tanggal Operasional -->
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5">
            
            <!-- 1. Text Search Input (Instant search: Booking Code, Event Name, PIC, Room) -->
            <div class="sm:col-span-4 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
                <input type="text" 
                       name="q" 
                       value="{{ $currentSearch }}" 
                       @input.debounce.400ms="$refs.filterForm.submit()"
                       placeholder="Cari Kode Booking, Acara, PIC, Ruang..." 
                       class="block w-full pl-9 pr-2.5 py-2 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] placeholder:text-slate-400">
            </div>

            <!-- 2. Status Dropdown Filter -->
            <div class="sm:col-span-3">
                <select name="status" 
                        @change="$refs.filterForm.submit()"
                        class="block w-full py-2 px-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 font-medium">
                    <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>
                        Semua Status ({{ $metrics['total_slots_today'] }})
                    </option>
                    <option value="confirmed" {{ $currentStatus === 'confirmed' ? 'selected' : '' }}>
                        Menunggu Kedatangan ({{ $metrics['confirmed_slots'] }})
                    </option>
                    <option value="checked_in" {{ $currentStatus === 'checked_in' ? 'selected' : '' }}>
                        Sedang Berlangsung ({{ $metrics['checked_in_slots'] }})
                    </option>
                    <option value="completed" {{ $currentStatus === 'completed' ? 'selected' : '' }}>
                        Selesai Digunakan ({{ $metrics['completed_slots'] }})
                    </option>
                </select>
            </div>

            <!-- 3. Filter Lantai Gedung (Diberi porsi lebar yang cukup dan padding kompak agar teks tidak terpotong) -->
            <div class="sm:col-span-2">
                <select name="floor_level" 
                        @change="$refs.filterForm.submit()"
                        class="block w-full py-2 pl-2.5 pr-6 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-700 font-medium cursor-pointer">
                    <option value="all" {{ $currentFloor === 'all' ? 'selected' : '' }}>Semua Lantai</option>
                    @for($f = 1; $f <= 8; $f++)
                        <option value="{{ $f }}" {{ $currentFloor == $f ? 'selected' : '' }}>
                            Lantai {{ $f }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- 4. Filter Spesifik Ruangan -->
            <div class="sm:col-span-3">
                <select name="room_id" 
                        @change="$refs.filterForm.submit()"
                        class="block w-full py-2 px-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-700 font-medium truncate">
                    <option value="all" {{ $currentRoom === 'all' ? 'selected' : '' }}>Semua Ruangan</option>
                    @foreach($rooms as $r)
                        <option value="{{ $r->id }}" {{ $currentRoom == $r->id ? 'selected' : '' }}>
                            Lt. {{ $r->floor_level }} - {{ $r->name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <!-- Row 2: Tanggal Manifes & Reset Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100 text-xs">
            <div class="flex items-center gap-2">
                <label class="text-slate-500 font-bold text-[11px] whitespace-nowrap">Tanggal Manifes:</label>
                <div class="relative">
                    <input type="date" 
                           name="date" 
                           value="{{ $targetDate->format('Y-m-d') }}" 
                           @change="$refs.filterForm.submit()"
                           class="py-1.5 px-3 text-xs rounded-brand border border-slate-300 text-slate-800 font-semibold focus:border-[#001A72] focus:ring-[#001A72] shadow-2xs">
                </div>
                @if(!$targetDate->isToday())
                    <a href="{{ route('front_office.manifest') }}" 
                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-brand bg-[#EEF2FF] text-[#001A72] hover:bg-indigo-100 font-bold text-xs transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                        <span>Kembali ke Hari Ini</span>
                    </a>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Hari Berjalan</span>
                    </span>
                @endif
            </div>

            <!-- Active filter chips & Reset button -->
            <div class="flex items-center gap-2 flex-wrap">
                @if($currentSearch || $currentStatus !== 'all' || $currentFloor !== 'all' || $currentRoom !== 'all' || !$targetDate->isToday())
                    <a href="{{ route('front_office.manifest') }}" 
                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-brand text-xs font-semibold text-rose-600 hover:text-white hover:bg-rose-600 bg-rose-50 border border-rose-200 transition shadow-2xs">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        <span>Reset Filter</span>
                    </a>
                @endif
            </div>
        </div>

    </form>
</div>
