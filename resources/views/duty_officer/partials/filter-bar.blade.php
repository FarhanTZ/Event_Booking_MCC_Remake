<!-- ==================================================================== -->
<!-- MULTI-PARAMETER INSTANT SEARCH & FILTER BAR (DUTY OFFICER) -->
<!-- ==================================================================== -->
<div class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 space-y-4">
    <form method="GET" 
          action="{{ route('duty_officer.dashboard') }}" 
          x-ref="filterForm"
          class="space-y-3.5">

        <!-- Row 1: Search, Status, Floor & Shift Selects -->
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5">
            
            <!-- 1. Text Search Input (Instant search: Booking Code, Event Name, PIC, Room, Equipment) -->
            <div class="sm:col-span-4 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ $filter->search }}" 
                       @input.debounce.400ms="$refs.filterForm.submit()"
                       placeholder="Cari Kode Booking, Acara, PIC, Alat..." 
                       class="block w-full pl-9 pr-2.5 py-2 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] placeholder:text-slate-400">
            </div>

            <!-- 2. Scope Filter: Tugas Saya vs Semua Penugasan -->
            <div class="sm:col-span-2">
                <select name="scope" 
                        @change="$refs.filterForm.submit()"
                        class="block w-full py-2 px-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 font-bold bg-slate-50 cursor-pointer">
                    <option value="assigned" {{ ($filter->scope === 'assigned' || empty($filter->scope)) ? 'selected' : '' }}>
                        Tugas Saya
                    </option>
                    <option value="all" {{ $filter->scope === 'all' ? 'selected' : '' }}>
                        Semua Penugasan
                    </option>
                </select>
            </div>

            <!-- 3. Status Dropdown Filter -->
            <div class="sm:col-span-2">
                <select name="status" 
                        @change="$refs.filterForm.submit()"
                        class="block w-full py-2 px-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 font-medium cursor-pointer">
                    <option value="all" {{ ($filter->status === 'all' || empty($filter->status)) ? 'selected' : '' }}>
                        Semua Status ({{ $metrics['total'] ?? 0 }})
                    </option>
                    <option value="needs_opening" {{ $filter->status === 'needs_opening' ? 'selected' : '' }}>
                        Perlu Dibuka / Check-in Baru ({{ $metrics['needs_opening'] ?? 0 }})
                    </option>
                    <option value="checked_in" {{ $filter->status === 'checked_in' ? 'selected' : '' }}>
                        Sedang Berlangsung ({{ $metrics['checked_in'] ?? 0 }})
                    </option>
                    <option value="upcoming" {{ $filter->status === 'upcoming' ? 'selected' : '' }}>
                        Akan Datang ({{ $metrics['upcoming'] ?? 0 }})
                    </option>
                    <option value="completed" {{ $filter->status === 'completed' ? 'selected' : '' }}>
                        Selesai Digunakan ({{ $metrics['completed'] ?? 0 }})
                    </option>
                </select>
            </div>

            <!-- 4. Floor Filter Level -->
            <div class="sm:col-span-2">
                <select name="floor" 
                        @change="$refs.filterForm.submit()"
                        class="block w-full py-2 px-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-700 font-medium cursor-pointer">
                    <option value="" {{ empty($filter->floor) ? 'selected' : '' }}>Semua Lantai</option>
                    @foreach($floors as $f)
                        <option value="{{ $f }}" {{ $filter->floor == $f ? 'selected' : '' }}>
                            Lantai {{ $f }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 5. Shift Filter -->
            <div class="sm:col-span-2">
                <select name="shift" 
                        @change="$refs.filterForm.submit()"
                        class="block w-full py-2 px-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-700 font-medium cursor-pointer">
                    <option value="all" {{ ($filter->shift === 'all' || empty($filter->shift)) ? 'selected' : '' }}>Semua Shift</option>
                    <option value="morning" {{ $filter->shift === 'morning' ? 'selected' : '' }}>Shift Pagi (08-16)</option>
                    <option value="evening" {{ $filter->shift === 'evening' ? 'selected' : '' }}>Shift Sore (16-22)</option>
                </select>
            </div>

        </div>

        <!-- Row 2: Tanggal Operasional, Quick Floor Pills & Reset Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100 text-xs">
            <!-- Tanggal Lapangan -->
            <div class="flex items-center gap-2 flex-wrap">
                <label class="text-slate-500 font-bold text-[11px] whitespace-nowrap">Tanggal Tugas:</label>
                <input type="date" 
                       name="date" 
                       value="{{ $filter->date ?? now()->format('Y-m-d') }}" 
                       @change="$refs.filterForm.submit()"
                       class="py-1.5 px-3 text-xs rounded-brand border border-slate-300 text-slate-800 font-semibold focus:border-[#001A72] focus:ring-[#001A72] shadow-2xs cursor-pointer">
                
                @if($filter->date && $filter->date !== now()->format('Y-m-d'))
                    <a href="{{ route('duty_officer.dashboard', array_merge(request()->query(), ['date' => now()->format('Y-m-d')])) }}" 
                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-brand bg-[#EEF2FF] text-[#001A72] hover:bg-indigo-100 font-bold text-xs transition shadow-2xs">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg>
                        <span>Kembali ke Hari Ini</span>
                    </a>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Hari Ini</span>
                    </span>
                @endif
            </div>

            <!-- Active filter chips & Reset button -->
            <div class="flex items-center gap-2 flex-wrap">
                @if($filter->search || ($filter->status && $filter->status !== 'all') || $filter->floor || ($filter->shift && $filter->shift !== 'all') || ($filter->scope && $filter->scope !== 'assigned') || ($filter->date && $filter->date !== now()->format('Y-m-d')))
                    <a href="{{ route('duty_officer.dashboard') }}" 
                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-brand text-xs font-semibold text-rose-600 hover:text-white hover:bg-rose-600 bg-rose-50 border border-rose-200 transition shadow-2xs">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        <span>Reset Semua Filter</span>
                    </a>
                @endif
            </div>
        </div>

    </form>
</div>
