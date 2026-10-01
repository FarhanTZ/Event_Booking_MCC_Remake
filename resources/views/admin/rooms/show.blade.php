@extends('layouts.admin')

@section('title', 'Detail & Inventaris ' . $room->name . ' - Super Admin MCC')
@section('page_title', 'Detail & Inventaris: ' . $room->name)

@section('content')
<div class="space-y-4" 
     x-data="{ 
         addFacilityModal: false, 
         editFacilityModal: false, 
         deleteFacilityModal: false,
         facilityToEdit: { id: null, facility_name: '', quantity: 1, unit: 'unit', condition: 'good' },
         editActionUrl: '',
         deleteActionUrl: '',
         facilityToDeleteName: ''
     }">
    
    <!-- Top Action Card: Info & Action Buttons -->
    <div class="bg-white p-4 sm:p-5 rounded-brand border border-slate-200 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.rooms.index') }}" class="text-[#001A72] hover:underline text-xs font-bold flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    <span>Daftar Master Ruangan</span>
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold text-slate-600">Lantai {{ $room->floor_level }} MCC</span>
            </div>
            <h2 class="text-base sm:text-xl font-black text-slate-900 tracking-tight">
                {{ $room->name }}
            </h2>
            <p class="text-xs text-slate-500 font-mono mt-0.5">
                slug: {{ $room->slug }} &bull; ID: #{{ $room->id }}
            </p>
        </div>

        <!-- Action Header Buttons -->
        <div class="flex items-center gap-2">
            <!-- Toggle Active/Maintenance Form -->
            <form method="POST" action="{{ route('admin.rooms.toggle_status', $room) }}">
                @csrf
                @method('PATCH')
                <button type="submit" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-brand text-xs font-black shadow-2xs cursor-pointer transition active:scale-95 {{ $room->is_active ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-rose-600 hover:bg-rose-700 text-white' }}"
                        title="Klik untuk mengubah status aktif/maintenance">
                    <span class="w-1.5 h-1.5 rounded-full {{ $room->is_active ? 'bg-white animate-pulse' : 'bg-rose-200' }}"></span>
                    <span>{{ $room->is_active ? 'Status: Aktif' : 'Status: Maintenance' }}</span>
                </button>
            </form>

            <!-- Edit Button -->
            <a href="{{ route('admin.rooms.edit', $room) }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-brand bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                <span>Edit Ruangan</span>
            </a>
        </div>
    </div>

    <!-- Room Specifications & Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        
        <!-- Left Column: Specs Matrix -->
        <div class="bg-white p-4 sm:p-5 rounded-brand border border-slate-200 shadow-2xs space-y-3">
            <h3 class="text-xs font-black text-[#001A72] uppercase tracking-wider border-b border-slate-100 pb-2">
                Parameter Fisik &amp; Tarif
            </h3>

            <div class="space-y-2 text-xs">
                <div class="flex items-center justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-semibold">Tingkat Lantai:</span>
                    <span class="font-black text-[#001A72] bg-indigo-50 px-2 py-0.5 rounded-md">Lantai {{ $room->floor_level }} MCC</span>
                </div>

                <div class="flex items-center justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-semibold">Kapasitas Maksimal:</span>
                    <span class="font-bold text-slate-800">{{ $room->capacity }} Orang</span>
                </div>

                <div class="flex items-center justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-semibold">Luas Area:</span>
                    <span class="font-bold text-slate-800">{{ $room->room_size_sqm ?? '-' }} m&sup2;</span>
                </div>

                <div class="flex items-center justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-semibold">Buffer Sterilisasi:</span>
                    <span class="font-bold text-slate-800">{{ $room->buffer_minutes }} Menit</span>
                </div>

                <div class="flex items-center justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-semibold">Izin Komersial:</span>
                    <span class="font-bold {{ $room->is_commercial_allowed ? 'text-emerald-700' : 'text-slate-500' }}">
                        {{ $room->is_commercial_allowed ? 'Diizinkan (Sewa Berbayar)' : 'Khusus Non-Komersial' }}
                    </span>
                </div>

                <div class="flex items-center justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-semibold">Tarif Dasar per Jam:</span>
                    <span class="font-black text-emerald-700">
                        {{ $room->base_hourly_rate > 0 ? 'Rp ' . number_format($room->base_hourly_rate, 0, ',', '.') . ' / Jam' : 'Subsidi Penuh (Gratis)' }}
                    </span>
                </div>
            </div>

            <!-- Description -->
            <div class="pt-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Deskripsi Ruangan:</span>
                <p class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-2.5 rounded-brand border border-slate-100">
                    {{ $room->description }}
                </p>
            </div>
        </div>

        <!-- Middle Column: Supported Layouts -->
        <div class="bg-white p-4 sm:p-5 rounded-brand border border-slate-200 shadow-2xs space-y-3">
            <h3 class="text-xs font-black text-[#001A72] uppercase tracking-wider border-b border-slate-100 pb-2 flex items-center justify-between">
                <span>Layout yang Didukung</span>
                <span class="text-[10px] text-slate-400 font-mono">FR-ADM-01</span>
            </h3>

            @php
                $layouts = is_array($room->supported_layouts) ? $room->supported_layouts : json_decode($room->supported_layouts ?? '[]', true);
                $layoutLabels = [
                    'theater' => 'Theater (Baris Kursi Menghadap Panggung)',
                    'classroom' => 'Classroom (Meja & Kursi Belajar)',
                    'u_shape' => 'U-Shape (Bentuk U untuk Rapat & Diskusi)',
                    'roundtable' => 'Roundtable / Island (Grup Meja Bundar)',
                    'hollow_square' => 'Hollow Square (Persegi Terbuka)',
                    'boardroom' => 'Boardroom (Meja Konferensi Panjang)',
                    'standing' => 'Standing / Cocktail (Terbuka Tanpa Kursi)',
                ];
            @endphp

            <div class="space-y-1.5">
                @forelse($layouts as $l)
                    <div class="p-2.5 rounded-brand bg-slate-50 border border-slate-200 flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded bg-indigo-100 text-[#001A72] flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><rect width="18" height="18" x="3" y="3" rx="2"/></svg>
                        </div>
                        <div>
                            <span class="font-bold text-xs text-slate-900 block capitalize">{{ str_replace('_', ' ', $l) }}</span>
                            <span class="text-[10px] text-slate-500 leading-tight">{{ $layoutLabels[$l] ?? 'Konfigurasi ruangan kustom' }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic">Belum ada layout terkonfigurasi.</p>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Room Photos Gallery -->
        <div class="bg-white p-4 sm:p-5 rounded-brand border border-slate-200 shadow-2xs space-y-3">
            <h3 class="text-xs font-black text-[#001A72] uppercase tracking-wider border-b border-slate-100 pb-2">
                Foto &amp; Galeri Ruangan
            </h3>

            <div class="space-y-2">
                <div class="rounded-brand overflow-hidden aspect-video bg-slate-100 border border-slate-200 shadow-2xs">
                    <img src="{{ $room->getThumbnailUrl() }}" class="w-full h-full object-cover">
                </div>

                @if($room->images->count() > 0)
                    <div class="grid grid-cols-3 gap-1.5">
                        @foreach($room->images as $img)
                            <div class="rounded overflow-hidden aspect-video bg-slate-100 border border-slate-200">
                                <img src="{{ asset('storage/' . $img->image_url) }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- ======================================================== -->
    <!-- FR-ADM-02: MANAJEMEN INVENTARIS MASTER FASILITAS RUANGAN -->
    <!-- ======================================================== -->
    <div class="bg-white p-4 sm:p-5 rounded-brand border border-slate-200 shadow-2xs space-y-4">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-indigo-100 text-[#001A72]">
                        FR-ADM-02
                    </span>
                    <h3 class="text-sm sm:text-base font-black text-slate-900">
                        Daftar Inventaris Master Fasilitas Ruangan
                    </h3>
                </div>
                <p class="text-xs text-slate-500">
                    Inventaris ini menjadi baseline acuan checklist inspeksi fisik Duty Officer saat buka akses ruangan (check-in) dan penutupan (checkout).
                </p>
            </div>

            <button type="button" 
                    @click="addFacilityModal = true"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white font-bold text-xs transition active:scale-95 shadow-md cursor-pointer self-start sm:self-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>Tambah Inventaris Fasilitas</span>
            </button>
        </div>

        <!-- Facilities Inventory Table -->
        @if($room->facilities->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">#</th>
                            <th class="py-2.5 px-3">Nama Barang / Fasilitas</th>
                            <th class="py-2.5 px-3">Jumlah &amp; Satuan</th>
                            <th class="py-2.5 px-3">Kondisi Aset</th>
                            <th class="py-2.5 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-800">
                        @foreach($room->facilities as $idx => $facility)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-3 font-mono text-slate-400 font-bold">{{ $idx + 1 }}</td>
                                <td class="py-3 px-3 font-black text-slate-900">
                                    {{ $facility->facility_name }}
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $facility->quantity }} {{ $facility->unit }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    @if($facility->condition === 'good')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                            <span>Baik / Siap Pakai</span>
                                        </span>
                                    @elseif($facility->condition === 'fair')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                            <span>Cukup Baik</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                            <span>Perlu Perbaikan</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <!-- Edit Trigger -->
                                        <button type="button" 
                                                @click="facilityToEdit = { id: {{ $facility->id }}, facility_name: '{{ addslashes($facility->facility_name) }}', quantity: {{ $facility->quantity }}, unit: '{{ addslashes($facility->unit) }}', condition: '{{ $facility->condition }}' }; editActionUrl = '{{ route('admin.rooms.facilities.update', [$room, $facility]) }}'; editFacilityModal = true"
                                                class="p-1 rounded-brand bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer"
                                                title="Edit Fasilitas">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                        </button>

                                        <!-- Delete Trigger -->
                                        <button type="button" 
                                                @click="facilityToDeleteName = '{{ addslashes($facility->facility_name) }}'; deleteActionUrl = '{{ route('admin.rooms.facilities.destroy', [$room, $facility]) }}'; deleteFacilityModal = true"
                                                class="p-1 rounded-brand bg-rose-50 hover:bg-rose-100 text-rose-600 transition cursor-pointer"
                                                title="Hapus Fasilitas">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6 text-center bg-slate-50 rounded-brand border border-dashed border-slate-200 space-y-2">
                <p class="text-xs text-slate-500 font-medium">
                    Belum ada inventaris fasilitas master yang terdaftar untuk ruangan ini.
                </p>
                <button type="button" 
                        @click="addFacilityModal = true"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-brand bg-[#001A72] text-white font-bold text-xs shadow-md cursor-pointer">
                    <span>Tambah Fasilitas Pertama</span>
                </button>
            </div>
        @endif

    </div>

    <!-- ======================================================== -->
    <!-- MODAL 1: TAMBAH INVENTARIS FASILITAS MASTER (FR-ADM-02) -->
    <!-- ======================================================== -->
    <div x-show="addFacilityModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         role="dialog" 
         aria-modal="true">
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity" @click="addFacilityModal = false"></div>
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-md p-5 border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <div>
                        <span class="text-[10px] font-mono font-bold text-indigo-600 block">FR-ADM-02</span>
                        <h3 class="text-sm sm:text-base font-black text-slate-900">Tambah Fasilitas Inventaris</h3>
                    </div>
                    <button type="button" @click="addFacilityModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.rooms.facilities.store', $room) }}" class="space-y-3">
                    @csrf

                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">
                            Nama Barang / Fasilitas <span class="text-rose-500">*</span>:
                        </label>
                        <input type="text" 
                               name="facility_name" 
                               required 
                               placeholder="Contoh: Mikrofon Wireless Shure, Proyektor Laser..." 
                               class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72]">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">
                                Jumlah Unit <span class="text-rose-500">*</span>:
                            </label>
                            <input type="number" 
                                   name="quantity" 
                                   value="1" 
                                   required 
                                   min="1" 
                                   class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72]">
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">
                                Satuan <span class="text-rose-500">*</span>:
                            </label>
                            <input type="text" 
                                   name="unit" 
                                   value="unit" 
                                   required 
                                   placeholder="unit, buah, set, kursi..." 
                                   class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72]">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">
                            Kondisi Fisik Aset <span class="text-rose-500">*</span>:
                        </label>
                        <select name="condition" required class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72] bg-white font-medium">
                            <option value="good" selected>Baik / Siap Pakai (Good)</option>
                            <option value="fair">Cukup Baik (Fair)</option>
                            <option value="needs_repair">Perlu Perbaikan (Needs Repair)</option>
                        </select>
                    </div>

                    <div class="pt-2 flex items-center gap-2">
                        <button type="submit" 
                                class="flex-1 py-2 px-3 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white font-black text-xs shadow-md transition cursor-pointer">
                            Simpan Inventaris
                        </button>
                        <button type="button" 
                                @click="addFacilityModal = false" 
                                class="py-2 px-3 rounded-brand bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 2: EDIT INVENTARIS FASILITAS MASTER (FR-ADM-02) -->
    <!-- ======================================================== -->
    <div x-show="editFacilityModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         role="dialog" 
         aria-modal="true">
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity" @click="editFacilityModal = false"></div>
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-md p-5 border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <div>
                        <span class="text-[10px] font-mono font-bold text-indigo-600 block">FR-ADM-02</span>
                        <h3 class="text-sm sm:text-base font-black text-slate-900">Edit Fasilitas Inventaris</h3>
                    </div>
                    <button type="button" @click="editFacilityModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <form :action="editActionUrl" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">
                            Nama Barang / Fasilitas <span class="text-rose-500">*</span>:
                        </label>
                        <input type="text" 
                               name="facility_name" 
                               x-model="facilityToEdit.facility_name" 
                               required 
                               class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72]">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">
                                Jumlah Unit <span class="text-rose-500">*</span>:
                            </label>
                            <input type="number" 
                                   name="quantity" 
                                   x-model="facilityToEdit.quantity" 
                                   required 
                                   min="1" 
                                   class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72]">
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">
                                Satuan <span class="text-rose-500">*</span>:
                            </label>
                            <input type="text" 
                                   name="unit" 
                                   x-model="facilityToEdit.unit" 
                                   required 
                                   class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72]">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">
                            Kondisi Fisik Aset <span class="text-rose-500">*</span>:
                        </label>
                        <select name="condition" x-model="facilityToEdit.condition" required class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72] bg-white font-medium">
                            <option value="good">Baik / Siap Pakai (Good)</option>
                            <option value="fair">Cukup Baik (Fair)</option>
                            <option value="needs_repair">Perlu Perbaikan (Needs Repair)</option>
                        </select>
                    </div>

                    <div class="pt-2 flex items-center gap-2">
                        <button type="submit" 
                                class="flex-1 py-2 px-3 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white font-black text-xs shadow-md transition cursor-pointer">
                            Perbarui Inventaris
                        </button>
                        <button type="button" 
                                @click="editFacilityModal = false" 
                                class="py-2 px-3 rounded-brand bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 3: HAPUS INVENTARIS FASILITAS -->
    <!-- ======================================================== -->
    <div x-show="deleteFacilityModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         role="dialog" 
         aria-modal="true">
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity" @click="deleteFacilityModal = false"></div>
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-md p-5 border border-slate-200 space-y-4">
                <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                </div>

                <div class="text-center space-y-1">
                    <h3 class="text-sm sm:text-base font-black text-slate-900">Hapus Inventaris Fasilitas?</h3>
                    <p class="text-xs text-slate-600">
                        Apakah Anda yakin ingin menghapus <strong class="text-slate-900" x-text="facilityToDeleteName"></strong> dari daftar master inventaris ruangan ini?
                    </p>
                </div>

                <form :action="deleteActionUrl" method="POST" class="flex items-center gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="flex-1 py-2 px-3 rounded-brand bg-rose-600 hover:bg-rose-700 text-white font-black text-xs shadow-md transition cursor-pointer">
                        Hapus Inventaris
                    </button>
                    <button type="button" 
                            @click="deleteFacilityModal = false" 
                            class="flex-1 py-2 px-3 rounded-brand bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
