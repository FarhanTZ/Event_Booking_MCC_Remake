@extends('layouts.admin')

@section('title', 'Dasbor Eksekutif & Analitik Utilisasi Gedung - Super Admin MCC')
@section('page_title', 'Dasbor Eksekutif & Analitik')

@section('content')
<div class="space-y-5">
    
    <!-- Top Action Banner: Info & Export Buttons -->
    <div class="bg-[#001A72] text-white p-4 sm:p-5 rounded-brand border border-indigo-900 shadow-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-white/20 text-white">
                    FR-ADM-06
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-400 text-slate-950">
                    Eksekutif &amp; KPI Gedung
                </span>
            </div>
            <h2 class="text-base sm:text-lg font-black tracking-tight">
                Dasbor Eksekutif &amp; Analitik Utilisasi Gedung MCC
            </h2>
            <p class="text-indigo-200 text-xs mt-0.5 max-w-2xl">
                Visualisasi tingkat okupansi ruangan (<span class="font-mono text-white">Occupancy Rate</span>), distribusi 17 sub-sektor ekonomi kreatif, rasio pembiayaan subsidi vs komersial, dan statistik insiden fasilitas.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <!-- Export PDF -->
            <a href="{{ route('admin.analytics.export.pdf', request()->query()) }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-brand bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs shadow-sm transition active:scale-95 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                <span>Unduh PDF</span>
            </a>

            <!-- Export CSV / Excel -->
            <a href="{{ route('admin.analytics.export.csv', request()->query()) }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-brand bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow-sm transition active:scale-95 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span>Ekspor Excel / CSV</span>
            </a>
        </div>
    </div>

    <!-- Multi-Parameter Realtime Filter Panel (Harmonized Single-Row Layout) -->
    <div class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 space-y-3 w-full">
        <form method="GET" action="{{ route('admin.analytics.index') }}" x-ref="filterForm" class="space-y-3 w-full">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full">
                
                <!-- 1. Rentang Tanggal (Flex-1) -->
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <div class="relative flex-1 min-w-0">
                        <input type="date" 
                               name="date_from" 
                               value="{{ request('date_from', $filter['date_from']) }}" 
                               @change="$refs.filterForm.submit()"
                               title="Periode Dari"
                               class="block w-full py-2.5 px-3 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 bg-white">
                    </div>
                    <span class="text-slate-400 text-xs font-bold shrink-0">s/d</span>
                    <div class="relative flex-1 min-w-0">
                        <input type="date" 
                               name="date_to" 
                               value="{{ request('date_to', $filter['date_to']) }}" 
                               @change="$refs.filterForm.submit()"
                               title="Periode Sampai"
                               class="block w-full py-2.5 px-3 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 bg-white">
                    </div>
                </div>

                <!-- 2. Filter Tingkat Lantai (Sebelah Kanan Rentang Tanggal) -->
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

                <!-- 3. Filter Sub-Sektor Ekraf -->
                <div class="w-full sm:w-48 shrink-0">
                    <select name="subsector" 
                            @change="$refs.filterForm.submit()"
                            class="block w-full py-2.5 px-3 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 font-medium cursor-pointer bg-white">
                        <option value="all">Semua Sub-Sektor</option>
                        @foreach($ekrafSubsectors as $sub)
                            <option value="{{ $sub }}" {{ request('subsector') === $sub ? 'selected' : '' }}>
                                {{ $sub }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Filter Skema Biaya (Paling Kanan) -->
                <div class="w-full sm:w-48 shrink-0">
                    <select name="scheme" 
                            @change="$refs.filterForm.submit()"
                            class="block w-full py-2.5 px-3 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 font-medium cursor-pointer bg-white">
                        <option value="all">Semua Skema</option>
                        <option value="subsidy" {{ request('scheme') === 'subsidy' ? 'selected' : '' }}>Subsidi Gratis</option>
                        <option value="commercial" {{ request('scheme') === 'commercial' ? 'selected' : '' }}>Sewa Komersial</option>
                    </select>
                </div>

                <!-- 5. Tombol Reset Cepat (Bila ada filter aktif) -->
                @if(request()->hasAny(['date_from', 'date_to', 'floor', 'subsector', 'scheme']) && (request('floor') || (request('subsector') && request('subsector') !== 'all') || (request('scheme') && request('scheme') !== 'all') || request('date_from') || request('date_to')))
                    <div class="shrink-0">
                        <a href="{{ route('admin.analytics.index') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-brand text-xs font-bold text-rose-600 hover:text-white hover:bg-rose-600 bg-rose-50 border border-rose-200 transition shadow-2xs"
                           title="Reset Semua Filter">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            <span>Reset</span>
                        </a>
                    </div>
                @endif

            </div>

            <!-- Baris Status Filter Aktif (Chips) -->
            @if(request()->hasAny(['floor', 'subsector', 'scheme']) && (request('floor') || (request('subsector') && request('subsector') !== 'all') || (request('scheme') && request('scheme') !== 'all')))
                <div class="flex items-center gap-2 pt-2 border-t border-slate-100 text-xs flex-wrap">
                    <span class="text-slate-400 text-[11px] font-bold">Filter Aktif:</span>
                    @if(request('floor'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100">
                            Lantai {{ request('floor') }}
                        </span>
                    @endif
                    @if(request('subsector') && request('subsector') !== 'all')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100">
                            Sub-Sektor: {{ request('subsector') }}
                        </span>
                    @endif
                    @if(request('scheme') && request('scheme') !== 'all')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100 uppercase">
                            Skema: {{ request('scheme') }}
                        </span>
                    @endif
                </div>
            @endif
        </form>
    </div>

    <!-- 4 Summary KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- KPI 1: Occupancy Rate -->
        <div class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Occupancy Rate</span>
                <span class="p-1.5 rounded-full bg-indigo-50 text-[#001A72]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                </span>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">
                {{ $kpis['occupancy_rate'] }}%
            </div>
            <p class="text-xs text-slate-500 mt-1">
                {{ $kpis['total_utilized_hours'] }} jam dari {{ $kpis['total_available_hours'] }} jam kapasitas gedung
            </p>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-3 overflow-hidden">
                <div class="bg-[#001A72] h-1.5 rounded-full" style="width: {{ min(100, $kpis['occupancy_rate']) }}%"></div>
            </div>
        </div>

        <!-- KPI 2: Total Bookings & Sessions -->
        <div class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Volume Kegiatan</span>
                <span class="p-1.5 rounded-full bg-emerald-50 text-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </span>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">
                {{ $kpis['total_bookings'] }} Acara
            </div>
            <p class="text-xs text-slate-500 mt-1">
                {{ $kpis['total_sessions'] }} sesi terlaksana di {{ $kpis['active_rooms_count'] }} ruangan aktif
            </p>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-3 overflow-hidden">
                <div class="bg-emerald-600 h-1.5 rounded-full" style="width: 100%"></div>
            </div>
        </div>

        <!-- KPI 3: Rasio Subsidi Pemkot -->
        <div class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Rasio Subsidi Pemkot</span>
                <span class="p-1.5 rounded-full bg-amber-50 text-amber-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/></svg>
                </span>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">
                {{ $pricingScheme['subsidy_percentage'] }}%
            </div>
            <p class="text-xs text-slate-500 mt-1">
                {{ $pricingScheme['subsidy_count'] }} gratis &bull; {{ $pricingScheme['commercial_count'] }} komersial (Rp {{ number_format($pricingScheme['total_revenue'], 0, ',', '.') }})
            </p>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-3 overflow-hidden">
                <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $pricingScheme['subsidy_percentage'] }}%"></div>
            </div>
        </div>

        <!-- KPI 4: Insiden Fasilitas -->
        <div class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Statistik Insiden</span>
                <span class="p-1.5 rounded-full bg-rose-50 text-rose-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </span>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">
                {{ $kpis['total_incidents'] }} Kasus
            </div>
            <p class="text-xs text-slate-500 mt-1">
                {{ $kpis['resolved_incidents'] }} terselesaikan &bull; <span class="text-rose-600 font-bold">{{ $kpis['unresolved_incidents'] }} tertahan</span>
            </p>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-3 overflow-hidden">
                <div class="bg-rose-500 h-1.5 rounded-full" style="width: {{ $kpis['total_incidents'] > 0 ? min(100, ($kpis['resolved_incidents'] / $kpis['total_incidents']) * 100) : 100 }}%"></div>
            </div>
        </div>

    </div>

    <!-- 2-Column Grid: Floor Occupancy & 17 Ekraf Subsectors Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        
        <!-- Left: Occupancy Rate per Floor (Lantai 1 s/d 8) -->
        <div class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Tingkat Okupansi per Tingkat Lantai</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Persentase jam terpakai vs kapasitas jam operasional per lantai</p>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#001A72] text-white">L1 - L8</span>
            </div>

            <div class="space-y-3">
                @foreach($floorOccupancy as $f => $data)
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800">Lantai {{ $f }} ({{ $data['rooms_count'] }} Ruangan)</span>
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400 font-mono text-[11px]">{{ $data['used_hours'] }}j / {{ $data['available_hours'] }}j</span>
                                <span class="font-black text-slate-900 w-12 text-right">{{ $data['occupancy_rate'] }}%</span>
                            </div>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#001A72] h-2 rounded-full transition-all duration-500" style="width: {{ min(100, $data['occupancy_rate']) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right: 17 Sub-Sektor Ekraf Distribution -->
        <div class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Distribusi 17 Sub-Sektor Ekraf</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Proporsi kegiatan kreatif yang difasilitasi gedung MCC</p>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900">Kemenparekraf</span>
            </div>

            <div class="space-y-2.5 max-h-[360px] overflow-y-auto custom-scrollbar pr-1">
                @php $displayedEkraf = 0; @endphp
                @foreach($ekrafDistribution as $sub)
                    @if($sub['count'] > 0 || $displayedEkraf < 7)
                        @php $displayedEkraf++; @endphp
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-slate-800 truncate pr-2">{{ $sub['subsector'] }}</span>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-slate-400 font-mono text-[11px]">{{ $sub['count'] }} Acara</span>
                                    <span class="font-black text-slate-900 w-12 text-right">{{ $sub['percentage'] }}%</span>
                                </div>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-amber-500 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, $sub['percentage']) }}%"></div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

    </div>

    <!-- Bottom 2-Column Grid: Room Occupancy Leaderboard & Incident Recap -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        
        <!-- Left: Leaderboard Utilisasi Ruangan -->
        <div class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Peringkat Utilisasi Ruangan Teratas</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Ruangan paling produktif berdasarkan jam pemakaian</p>
                </div>
                <span class="text-xs font-bold text-slate-400">Top 10</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="text-slate-400 font-bold uppercase text-[9px] border-b border-slate-100">
                        <tr>
                            <th class="py-2 px-2">Ruangan</th>
                            <th class="py-2 px-2 text-center">Lantai</th>
                            <th class="py-2 px-2 text-center">Acara</th>
                            <th class="py-2 px-2 text-right">Jam Terpakai</th>
                            <th class="py-2 px-2 text-right">Okupansi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach(array_slice($roomOccupancy, 0, 8) as $room)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2.5 px-2 font-bold text-slate-900 truncate max-w-[160px]">
                                    {{ $room['name'] }}
                                </td>
                                <td class="py-2.5 px-2 text-center">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-[#EEF2FF] text-[#001A72]">
                                        Lt {{ $room['floor_level'] }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-2 text-center font-mono text-slate-600">
                                    {{ $room['events_count'] }}
                                </td>
                                <td class="py-2.5 px-2 text-right font-mono text-slate-700">
                                    {{ $room['used_hours'] }} j
                                </td>
                                <td class="py-2.5 px-2 text-right font-black text-[#001A72]">
                                    {{ $room['occupancy_rate'] }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Incident Recap & Facilities Damage -->
        <div class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Rekapitulasi Insiden Kerusakan Fasilitas</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Kategori kerusakan yang dilaporkan inspeksi Duty Officer</p>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Inspeksi</span>
            </div>

            @if(count($incidents['by_category']) > 0)
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 bg-slate-50 rounded-brand border border-slate-200">
                            <span class="text-[10px] font-bold text-slate-500 uppercase block">Terselesaikan</span>
                            <span class="text-lg font-black text-emerald-600">{{ $incidents['resolved'] }} Kasus</span>
                        </div>
                        <div class="p-3 bg-rose-50 rounded-brand border border-rose-200">
                            <span class="text-[10px] font-bold text-rose-700 uppercase block">Menunggu Ganti Rugi</span>
                            <span class="text-lg font-black text-rose-700">{{ $incidents['unresolved'] }} Kasus</span>
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <span class="text-[11px] font-bold text-slate-700 block">Kategori Kerusakan:</span>
                        @foreach($incidents['by_category'] as $category => $count)
                            <div class="flex items-center justify-between text-xs py-1 px-2.5 rounded bg-slate-50 border border-slate-100">
                                <span class="font-medium text-slate-800">{{ $category }}</span>
                                <span class="font-bold text-rose-700 font-mono">{{ $count }} Kasus</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-8 text-center text-slate-400 space-y-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mx-auto text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <p class="text-xs font-bold text-slate-600">Nihil Laporan Insiden Fasilitas</p>
                    <p class="text-[11px] text-slate-400">Seluruh fasilitas ruangan terawat dan tertib operasional pada periode ini.</p>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
