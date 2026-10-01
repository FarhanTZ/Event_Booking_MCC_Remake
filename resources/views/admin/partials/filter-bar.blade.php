<!-- ==================================================================== -->
<!-- MULTI-PARAMETER INLINE SEARCH & FILTER BAR (SUPER ADMIN) -->
<!-- ==================================================================== -->
<div class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 space-y-3 w-full">
    <form method="GET" 
          action="{{ route('admin.rooms.index') }}" 
          x-ref="filterForm"
          class="space-y-3 w-full">

        <!-- Baris Utama: Search Bar (Kiri, Fleksibel Panjang) + Filter Lantai & Status (Sebelah Kanan) -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full">
            
            <!-- 1. Search Bar (Memanjang fleksibel memenuhi ruang sampai batas dropdown) -->
            <div class="relative flex-1 min-w-0">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       @input.debounce.400ms="$refs.filterForm.submit()"
                       placeholder="Cari nama ruangan, slug URL, atau fasilitas master..." 
                       class="block w-full pl-10 pr-3.5 py-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] placeholder:text-slate-400 bg-white transition">
            </div>

            <!-- 2. Filter Tingkat Lantai (Sebelah Kanan Searchbar) -->
            <div class="w-full sm:w-48 shrink-0">
                <select name="floor" 
                        @change="$refs.filterForm.submit()"
                        class="block w-full py-2.5 px-3 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-700 font-medium cursor-pointer bg-white">
                    <option value="">Semua Lantai</option>
                    @for($i = 1; $i <= 8; $i++)
                        <option value="{{ $i }}" {{ request('floor') == (string)$i ? 'selected' : '' }}>
                            Lantai {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- 3. Filter Status Operasional (Sebelah Kanan Lantai / Paling Kanan) -->
            <div class="w-full sm:w-48 shrink-0">
                <select name="status" 
                        @change="$refs.filterForm.submit()"
                        class="block w-full py-2.5 px-3 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 font-medium cursor-pointer bg-white">
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>
                        Semua Status ({{ $metrics['total'] ?? 0 }})
                    </option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                        Aktif / Tersedia ({{ $metrics['active'] ?? 0 }})
                    </option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                        Maintenance ({{ $metrics['inactive'] ?? 0 }})
                    </option>
                    <option value="commercial" {{ request('status') === 'commercial' ? 'selected' : '' }}>
                        Izin Komersial ({{ $metrics['commercial'] ?? 0 }})
                    </option>
                </select>
            </div>

            <!-- 4. Tombol Reset Cepat (Bila ada filter aktif) -->
            @if(request()->hasAny(['search', 'floor', 'status']) && (request('search') || request('floor') || (request('status') && request('status') !== 'all')))
                <div class="shrink-0">
                    <a href="{{ route('admin.rooms.index') }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-brand text-xs font-bold text-rose-600 hover:text-white hover:bg-rose-600 bg-rose-50 border border-rose-200 transition shadow-2xs"
                       title="Reset Semua Filter">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        <span>Reset</span>
                    </a>
                </div>
            @endif

        </div>

        <!-- Baris Status Filter Aktif (Chips) -->
        @if(request()->hasAny(['search', 'floor', 'status']) && (request('search') || request('floor') || (request('status') && request('status') !== 'all')))
            <div class="flex items-center gap-2 pt-2 border-t border-slate-100 text-xs flex-wrap">
                <span class="text-slate-400 text-[11px] font-bold">Filter Aktif:</span>
                @if(request('search'))
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100">
                        Kata Kunci: "{{ request('search') }}"
                    </span>
                @endif
                @if(request('floor'))
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100">
                        Lantai {{ request('floor') }}
                    </span>
                @endif
                @if(request('status') && request('status') !== 'all')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100 uppercase">
                        Status: {{ request('status') }}
                    </span>
                @endif
            </div>
        @endif

    </form>
</div>
