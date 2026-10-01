@extends('layouts.admin')

@section('title', 'Audit Trail Sistem Menyeluruh - Super Admin MCC')
@section('page_title', 'Audit Trail Sistem Menyeluruh')

@section('content')
<div class="space-y-4">
    
    <!-- Top Action Card: Info -->
    <div class="bg-[#001A72] text-white p-4 sm:p-5 rounded-brand border border-indigo-900 shadow-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-white/20 text-white">
                    FR-ADM-07
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-400 text-slate-950">
                    Akuntabilitas &amp; Kepatuhan
                </span>
            </div>
            <h2 class="text-base sm:text-lg font-black tracking-tight">
                Audit Trail &amp; Rekam Jejak Aktivitas Sistem Menyeluruh
            </h2>
            <p class="text-indigo-200 text-xs mt-0.5 max-w-2xl">
                Rekapitulasi kronologis seluruh aktivitas krusial sistem: keputusan kurasi marketing, perpindahan KTP fisik laci Front Office, pelaporan insiden Duty Officer, serta aksi super override pembatalan dan pembekuan akun.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <div class="px-3.5 py-2 rounded-brand bg-white/10 border border-white/15 text-xs font-semibold flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span>{{ $metrics['total_logs'] ?? 0 }} Rekam Log</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar (Harmonized Single-Row Layout) -->
    <div class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 space-y-3 w-full">
        <form method="GET" action="{{ route('admin.audit.index') }}" x-ref="filterForm" class="space-y-3 w-full">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full">
                
                <!-- 1. Search Bar -->
                <div class="relative flex-1 min-w-0">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           @input.debounce.400ms="$refs.filterForm.submit()"
                           placeholder="Cari kode booking, nama acara, aktor pelaksana, atau catatan log..." 
                           class="block w-full pl-10 pr-3.5 py-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] placeholder:text-slate-400 bg-white transition">
                </div>

                <!-- 2. Filter Kategori Log -->
                <div class="w-full sm:w-48 shrink-0">
                    <select name="type" 
                            @change="$refs.filterForm.submit()"
                            class="block w-full py-2.5 px-3 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-700 font-medium cursor-pointer bg-white">
                        <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>Semua Aktivitas</option>
                        <option value="curation" {{ request('type') === 'curation' ? 'selected' : '' }}>Kurasi Marketing ({{ $metrics['curation_count'] }})</option>
                        <option value="vault" {{ request('type') === 'vault' ? 'selected' : '' }}>KTP Front Office ({{ $metrics['vault_count'] }})</option>
                        <option value="incident" {{ request('type') === 'incident' ? 'selected' : '' }}>Inspeksi Insiden DO ({{ $metrics['incident_count'] }})</option>
                        <option value="override" {{ request('type') === 'override' ? 'selected' : '' }}>Super Override Admin ({{ $metrics['override_count'] }})</option>
                    </select>
                </div>

                <!-- 3. Filter Role Aktor -->
                <div class="w-full sm:w-48 shrink-0">
                    <select name="actor_role" 
                            @change="$refs.filterForm.submit()"
                            class="block w-full py-2.5 px-3 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 font-medium cursor-pointer bg-white">
                        <option value="all" {{ request('actor_role') === 'all' ? 'selected' : '' }}>Semua Posisi Role</option>
                        <option value="marketing" {{ request('actor_role') === 'marketing' ? 'selected' : '' }}>Marketing</option>
                        <option value="front_office" {{ request('actor_role') === 'front_office' ? 'selected' : '' }}>Front Office</option>
                        <option value="duty_officer" {{ request('actor_role') === 'duty_officer' ? 'selected' : '' }}>Duty Officer</option>
                        <option value="super_admin" {{ request('actor_role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                </div>

                <!-- 4. Tombol Reset Cepat -->
                @if(request()->hasAny(['search', 'type', 'actor_role', 'date_from', 'date_to']) && (request('search') || (request('type') && request('type') !== 'all') || (request('actor_role') && request('actor_role') !== 'all')))
                    <div class="shrink-0">
                        <a href="{{ route('admin.audit.index') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-brand text-xs font-bold text-rose-600 hover:text-white hover:bg-rose-600 bg-rose-50 border border-rose-200 transition shadow-2xs"
                           title="Reset Semua Filter">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            <span>Reset</span>
                        </a>
                    </div>
                @endif

            </div>

            <!-- Active Chips -->
            @if(request()->hasAny(['search', 'type', 'actor_role']) && (request('search') || (request('type') && request('type') !== 'all') || (request('actor_role') && request('actor_role') !== 'all')))
                <div class="flex items-center gap-2 pt-2 border-t border-slate-100 text-xs flex-wrap">
                    <span class="text-slate-400 text-[11px] font-bold">Filter Aktif:</span>
                    @if(request('search'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100">
                            Pencarian: "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('type') && request('type') !== 'all')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100 uppercase">
                            Kategori: {{ request('type') }}
                        </span>
                    @endif
                    @if(request('actor_role') && request('actor_role') !== 'all')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100 uppercase">
                            Role: {{ request('actor_role') }}
                        </span>
                    @endif
                </div>
            @endif
        </form>
    </div>

    <!-- Audit Logs Timeline Table Container -->
    @if($auditLogs->count() > 0)
        <div class="bg-white rounded-brand border border-slate-200 shadow-card overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] border-b border-slate-200 tracking-wider">
                        <tr>
                            <th class="py-3 px-4 w-36 whitespace-nowrap">Waktu &amp; Tanggal</th>
                            <th class="py-3 px-3 w-40">Pos Operasional</th>
                            <th class="py-3 px-3 min-w-[180px]">Tindakan / Keputusan</th>
                            <th class="py-3 px-3 min-w-[200px]">Terkait Reservasi &amp; Acara</th>
                            <th class="py-3 px-3 w-44">Aktor Pelaksana</th>
                            <th class="py-3 px-4 min-w-[240px]">Catatan / Audit Trail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-800">
                        @foreach($auditLogs as $log)
                            <tr class="hover:bg-slate-50/80 transition">
                                
                                <!-- 1. Timestamp -->
                                <td class="py-3 px-4 whitespace-nowrap align-top">
                                    <div class="font-mono font-bold text-slate-900 text-xs">
                                        {{ $log['timestamp'] ? \Carbon\Carbon::parse($log['timestamp'])->format('H:i:s') : '-' }} WIB
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        {{ $log['timestamp'] ? \Carbon\Carbon::parse($log['timestamp'])->translatedFormat('d M Y') : '-' }}
                                    </div>
                                </td>

                                <!-- 2. Pos Operasional / Kategori -->
                                <td class="py-3 px-3 align-top">
                                    @if($log['category'] === 'curation')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            Marketing
                                        </span>
                                    @elseif($log['category'] === 'vault')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Front Office
                                        </span>
                                    @elseif($log['category'] === 'incident')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Duty Officer
                                        </span>
                                    @elseif($log['category'] === 'override')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white">
                                            Super Override
                                        </span>
                                    @endif
                                    <div class="text-[10px] text-slate-500 mt-1 line-clamp-1">
                                        {{ $log['category_label'] }}
                                    </div>
                                </td>

                                <!-- 3. Tindakan / Keputusan -->
                                <td class="py-3 px-3 align-top">
                                    <div class="font-bold text-slate-900 text-xs">
                                        {{ $log['action_title'] }}
                                    </div>
                                </td>

                                <!-- 4. Reservasi & Pemohon -->
                                <td class="py-3 px-3 align-top">
                                    <div class="space-y-0.5">
                                        <div class="font-mono font-bold text-[#001A72] text-xs">
                                            #{{ $log['booking_code'] }}
                                        </div>
                                        <div class="font-semibold text-slate-900 text-xs line-clamp-1">
                                            {{ $log['event_name'] }}
                                        </div>
                                        <div class="text-[10px] text-slate-500 truncate">
                                            Pemohon: {{ $log['applicant_name'] }}
                                        </div>
                                    </div>
                                </td>

                                <!-- 5. Aktor Pelaksana -->
                                <td class="py-3 px-3 align-top">
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-slate-900 text-xs truncate">
                                            {{ $log['actor_name'] }}
                                        </div>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-700">
                                            {{ $log['actor_role_label'] }}
                                        </span>
                                    </div>
                                </td>

                                <!-- 6. Catatan Audit -->
                                <td class="py-3 px-4 align-top">
                                    <div class="text-xs text-slate-700 break-words leading-relaxed max-w-sm font-medium">
                                        {{ $log['notes'] ?: '-' }}
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            @if($auditLogs->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50">
                    {{ $auditLogs->links() }}
                </div>
            @endif
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-brand border border-slate-200 shadow-card p-12 text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Tidak Ada Rekam Audit yang Sesuai Kriteria Filter</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Ubah parameter filter kategori aktivitas atau reset filter untuk melihat rekaman audit trail sistem lainnya.
            </p>
            <div class="pt-2">
                <a href="{{ route('admin.audit.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-brand text-xs font-bold text-[#001A72] bg-[#EEF2FF] hover:bg-indigo-100 transition shadow-2xs">
                    Reset Filter Pencarian
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
