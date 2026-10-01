@extends('layouts.admin')

@section('title', 'Manajemen Data Master Ruangan & Layout - Super Admin MCC')
@section('page_title', 'Manajemen Data Master Ruangan')

@section('content')
<div class="space-y-4" x-data="{ deleteModalOpen: false, roomToDelete: null, deleteUrl: '' }">
    
    <!-- Top Action Card: Info & Create Button -->
    <div class="bg-[#001A72] text-white p-4 sm:p-5 rounded-brand border border-indigo-900 shadow-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-white/20 text-white">
                    FR-ADM-01 &amp; FR-ADM-02
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-400 text-slate-950">
                    Master Data MCC
                </span>
            </div>
            <h2 class="text-base sm:text-lg font-black tracking-tight">
                Manajemen Data Master Ruangan &amp; Inventaris
            </h2>
            <p class="text-indigo-200 text-xs mt-0.5 max-w-2xl">
                Tabel master entitas fisik lantai 1 s/d 8 MCC, tata letak layout, kapasitas, buffer sterilisasi, skema tarif per jam, dan inventaris fasilitas.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('admin.rooms.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-brand bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs shadow-md transition active:scale-95 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>Tambah Ruangan Baru</span>
            </a>
        </div>
    </div>

    <!-- Multi-Parameter Realtime Search & Filter Panel Component -->
    @include('admin.partials.filter-bar')


    <!-- Master Rooms Table Container -->
    @if($rooms->count() > 0)
        <div class="bg-white rounded-brand border border-slate-200 shadow-card overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] border-b border-slate-200 tracking-wider">
                        <tr>
                            <th class="py-3 px-4 min-w-[220px]">Ruangan</th>
                            <th class="py-3 px-3 text-center w-24">Lantai</th>
                            <th class="py-3 px-3">Kapasitas &amp; Luas</th>
                            <th class="py-3 px-3">Skema Tarif</th>
                            <th class="py-3 px-3">Layout Didukung</th>
                            <th class="py-3 px-3 text-center">Inventaris</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-800">
                        @foreach($rooms as $room)
                            @php
                                $layouts = is_array($room->supported_layouts) ? $room->supported_layouts : json_decode($room->supported_layouts ?? '[]', true);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                
                                <!-- 1. Ruangan: Thumbnail & Info (Bisa wrap/enter multi-line) -->
                                <td class="py-3 px-4 align-middle">
                                    <div class="flex items-start gap-3">
                                        <div class="w-12 h-12 rounded-brand bg-slate-100 border border-slate-200 overflow-hidden shrink-0 mt-0.5">
                                            <img src="{{ $room->getThumbnailUrl() }}" 
                                                 alt="{{ $room->name }}" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <div class="min-w-0 max-w-[240px]">
                                            <a href="{{ route('admin.rooms.show', $room) }}" 
                                               class="font-black text-slate-900 hover:text-[#001A72] text-xs sm:text-sm leading-snug break-words">
                                                {{ $room->name }}
                                            </a>
                                            <span class="text-[10px] font-mono text-slate-400 block mt-0.5 break-all">
                                                slug: {{ $room->slug }} &bull; #{{ $room->id }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- 2. Tingkat Lantai (Sebelah Kanan Ruangan) -->
                                <td class="py-3 px-3 text-center whitespace-nowrap align-middle">
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-[11px] font-black bg-[#001A72] text-white shadow-2xs">
                                        Lantai {{ $room->floor_level }}
                                    </span>
                                </td>

                                <!-- Kapasitas & Luas Area -->
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-1.5 text-slate-900 font-bold">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                            <span>{{ $room->capacity }} Orang</span>
                                        </div>
                                        <div class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                            <span>Luas: <strong>{{ $room->room_size_sqm ?? '-' }} m&sup2;</strong></span>
                                            <span>&bull;</span>
                                            <span>Buffer: <strong>{{ $room->buffer_minutes }}m</strong></span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Skema Tarif & Izin Komersial -->
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <div class="space-y-0.5">
                                        <span class="font-black block {{ $room->base_hourly_rate > 0 ? 'text-emerald-700' : 'text-[#001A72]' }}">
                                            {{ $room->base_hourly_rate > 0 ? 'Rp ' . number_format($room->base_hourly_rate, 0, ',', '.') . ' / jam' : 'Subsidi (Gratis)' }}
                                        </span>
                                        @if($room->is_commercial_allowed)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                                Komersial Diizinkan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                Non-Komersial
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Layout yang Didukung -->
                                <td class="py-3 px-3">
                                    <div class="flex flex-wrap gap-1 max-w-[180px]">
                                        @foreach(array_slice($layouts, 0, 3) as $layout)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-700 capitalize border border-slate-200">
                                                {{ str_replace('_', ' ', $layout) }}
                                            </span>
                                        @endforeach
                                        @if(count($layouts) > 3)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-50 text-[#001A72] border border-indigo-100">
                                                +{{ count($layouts) - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Inventaris Fasilitas Counter -->
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    <a href="{{ route('admin.rooms.show', $room) }}" 
                                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-brand bg-[#EEF2FF] hover:bg-indigo-100 text-[#001A72] font-black text-[11px] transition shadow-2xs"
                                       title="Lihat daftar inventaris fasilitas">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                                        <span>{{ $room->facilities_count ?? $room->facilities->count() }} Fasilitas</span>
                                    </a>
                                </td>

                                <!-- Status Operasional (Toggle Switch) -->
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    <form method="POST" action="{{ route('admin.rooms.toggle_status', $room) }}" class="inline-block m-0">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-brand text-[11px] font-black shadow-2xs cursor-pointer transition active:scale-95 {{ $room->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-300 hover:bg-emerald-100' : 'bg-rose-50 text-rose-800 border border-rose-300 hover:bg-rose-100' }}"
                                                title="Klik untuk mengubah status aktif/maintenance">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $room->is_active ? 'bg-emerald-600 animate-pulse' : 'bg-rose-600' }}"></span>
                                            <span>{{ $room->is_active ? 'Aktif' : 'Maintenance' }}</span>
                                        </button>
                                    </form>
                                </td>

                                <!-- Aksi CRUD -->
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        <!-- Detail Button -->
                                        <a href="{{ route('admin.rooms.show', $room) }}" 
                                           class="p-1.5 rounded-brand bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
                                           title="Detail &amp; Inventaris">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>

                                        <!-- Edit Button -->
                                        <a href="{{ route('admin.rooms.edit', $room) }}" 
                                           class="p-1.5 rounded-brand bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
                                           title="Edit Data Ruangan">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                        </a>

                                        <!-- Delete Button Trigger -->
                                        <button type="button" 
                                                @click="roomToDelete = '{{ addslashes($room->name) }}'; deleteUrl = '{{ route('admin.rooms.destroy', $room) }}'; deleteModalOpen = true"
                                                class="p-1.5 rounded-brand bg-rose-50 hover:bg-rose-100 text-rose-600 transition cursor-pointer"
                                                title="Hapus Ruangan">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Table Footer & Pagination -->
            <div class="p-3.5 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <span class="text-[11px] text-slate-500 font-medium">
                    Menampilkan <strong>{{ $rooms->firstItem() ?? 0 }}</strong> - <strong>{{ $rooms->lastItem() ?? 0 }}</strong> dari <strong>{{ $rooms->total() }}</strong> ruangan master
                </span>
                <div>
                    {{ $rooms->links() }}
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-brand border border-slate-200 p-8 text-center max-w-lg mx-auto space-y-3">
            <div class="w-12 h-12 mx-auto rounded-full bg-indigo-50 text-[#001A72] flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-sm sm:text-base font-black text-slate-900">Tidak ada ruangan ditemukan</h3>
                <p class="text-xs text-slate-500">Coba ubah kata kunci pencarian atau reset filter tingkat lantai.</p>
            </div>
            <a href="{{ route('admin.rooms.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-brand bg-[#001A72] text-white font-bold text-xs shadow-md">
                <span>Tambah Ruangan Baru</span>
            </a>
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus Ruangan -->
    <div x-show="deleteModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         role="dialog" 
         aria-modal="true">
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity" @click="deleteModalOpen = false"></div>
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-md p-5 border border-slate-200 space-y-4">
                <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                </div>

                <div class="text-center space-y-1">
                    <h3 class="text-sm sm:text-base font-black text-slate-900">Hapus Master Ruangan?</h3>
                    <p class="text-xs text-slate-600">
                        Apakah Anda yakin ingin menghapus ruangan <strong class="text-slate-900" x-text="roomToDelete"></strong>? Ruangan yang masih memiliki jadwal acara aktif tidak dapat dihapus.
                    </p>
                </div>

                <form :action="deleteUrl" method="POST" class="flex items-center gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="flex-1 py-2 px-3 rounded-brand bg-rose-600 hover:bg-rose-700 text-white font-black text-xs shadow-md transition cursor-pointer">
                        Hapus Permanen
                    </button>
                    <button type="button" 
                            @click="deleteModalOpen = false" 
                            class="flex-1 py-2 px-3 rounded-brand bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
