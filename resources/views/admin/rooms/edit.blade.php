@extends('layouts.admin')

@section('title', 'Edit Ruangan ' . $room->name . ' - Super Admin MCC')
@section('page_title', 'Edit Master Ruangan: ' . $room->name)

@section('content')
<div class="space-y-4">
    
    <!-- Top Nav Header -->
    <div class="bg-white p-4 sm:p-5 rounded-brand border border-slate-200 shadow-2xs flex items-center justify-between">
        <div>
            <a href="{{ route('admin.rooms.show', $room) }}" class="text-[#001A72] hover:underline text-xs font-bold flex items-center gap-1.5 mb-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                <span>Kembali ke Detail Ruangan</span>
            </a>
            <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">
                Perbarui Informasi Master Ruangan
            </h2>
            <p class="text-xs text-slate-500">
                Perbarui parameter fisik, tarif operasional, luas area, atau tata letak yang didukung.
            </p>
        </div>
    </div>

    <!-- Main Form Container -->
    <form method="POST" action="{{ route('admin.rooms.update', $room) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <!-- Section 1: Informasi Dasar Ruangan -->
        <div class="bg-white p-4 sm:p-5 rounded-brand border border-slate-200 shadow-2xs space-y-4">
            <h3 class="text-sm font-black text-[#001A72] border-b border-slate-100 pb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#001A72]"></span>
                <span>1. Informasi Identitas Ruangan</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Nama Ruangan -->
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">
                        Nama Ruangan <span class="text-rose-500">*</span>:
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $room->name) }}" 
                           required 
                           class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72] @error('name') border-rose-500 @enderror">
                    @error('name')
                        <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">
                        Slug URL:
                    </label>
                    <input type="text" 
                           name="slug" 
                           value="{{ old('slug', $room->slug) }}" 
                           class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72] @error('slug') border-rose-500 @enderror">
                    @error('slug')
                        <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tingkat Lantai -->
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">
                        Tingkat Lantai Gedung MCC <span class="text-rose-500">*</span>:
                    </label>
                    <select name="floor_level" required class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72] bg-white font-medium @error('floor_level') border-rose-500 @enderror">
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ old('floor_level', $room->floor_level) == $i ? 'selected' : '' }}>Lantai {{ $i }}</option>
                        @endfor
                    </select>
                    @error('floor_level')
                        <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Kapasitas Maksimal -->
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">
                        Kapasitas Maksimal (Orang) <span class="text-rose-500">*</span>:
                    </label>
                    <input type="number" 
                           name="capacity" 
                           value="{{ old('capacity', $room->capacity) }}" 
                           required 
                           min="1" 
                           max="5000" 
                           class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72] @error('capacity') border-rose-500 @enderror">
                    @error('capacity')
                        <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Luas Area Ruangan -->
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">
                        Luas Area (m&sup2;) <span class="text-rose-500">*</span>:
                    </label>
                    <input type="number" 
                           name="room_size_sqm" 
                           value="{{ old('room_size_sqm', $room->room_size_sqm) }}" 
                           required 
                           step="0.1" 
                           min="1" 
                           class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72] @error('room_size_sqm') border-rose-500 @enderror">
                    @error('room_size_sqm')
                        <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi Ruangan -->
            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">
                    Deskripsi Lengkap Ruangan <span class="text-rose-500">*</span>:
                </label>
                <textarea name="description" 
                          rows="3" 
                          required 
                          class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72] @error('description') border-rose-500 @enderror">{{ old('description', $room->description) }}</textarea>
                @error('description')
                    <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Section 2: Aturan Operasional Gedung -->
        <div class="bg-white p-4 sm:p-5 rounded-brand border border-slate-200 shadow-2xs space-y-4">
            <h3 class="text-sm font-black text-[#001A72] border-b border-slate-100 pb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#001A72]"></span>
                <span>2. Aturan Operasional &amp; Skema Tarif</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Buffer Minutes -->
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">
                        Buffer Sterilisasi/Pembersihan (Menit) <span class="text-rose-500">*</span>:
                    </label>
                    <input type="number" 
                           name="buffer_minutes" 
                           value="{{ old('buffer_minutes', $room->buffer_minutes) }}" 
                           required 
                           min="0" 
                           max="240" 
                           class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72] @error('buffer_minutes') border-rose-500 @enderror">
                    <span class="text-[11px] text-slate-500 mt-1 block">Jeda otomatis sebelum dan sesudah sesi acara.</span>
                </div>

                <!-- Base Hourly Rate -->
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">
                        Tarif Dasar Komersial (Rp / Jam) <span class="text-rose-500">*</span>:
                    </label>
                    <input type="number" 
                           name="base_hourly_rate" 
                           value="{{ old('base_hourly_rate', (int)$room->base_hourly_rate) }}" 
                           required 
                           min="0" 
                           step="1000" 
                           class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72] @error('base_hourly_rate') border-rose-500 @enderror">
                    <span class="text-[11px] text-slate-500 mt-1 block">Diisi 0 jika ruangan disubsidi penuh untuk komunitas.</span>
                </div>
            </div>

            <!-- Switches: Izin Komersial & Status Aktif -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                <label class="flex items-start gap-3 p-3 rounded-brand bg-slate-50 border border-slate-200 hover:bg-slate-100 cursor-pointer transition select-none">
                    <input type="checkbox" name="is_commercial_allowed" value="1" {{ old('is_commercial_allowed', $room->is_commercial_allowed) ? 'checked' : '' }} class="w-4 h-4 mt-0.5 rounded border-slate-300 text-[#001A72] focus:ring-[#001A72]">
                    <div class="text-xs">
                        <span class="font-black text-slate-900 block">Izinkan Penggunaan Komersial</span>
                        <span class="text-[11px] text-slate-500 leading-tight">Ruangan dapat disewa untuk kegiatan berbayar/tiket komersial.</span>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-3 rounded-brand bg-slate-50 border border-slate-200 hover:bg-slate-100 cursor-pointer transition select-none">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $room->is_active) ? 'checked' : '' }} class="w-4 h-4 mt-0.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <div class="text-xs">
                        <span class="font-black text-slate-900 block">Status Operasional Aktif</span>
                        <span class="text-[11px] text-slate-500 leading-tight">Jika dimatikan, ruangan otomatis berstatus Maintenance di kalender publik.</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Section 3: Konfigurasi Layout yang Didukung -->
        <div class="bg-white p-4 sm:p-5 rounded-brand border border-slate-200 shadow-2xs space-y-3">
            <h3 class="text-sm font-black text-[#001A72] border-b border-slate-100 pb-2 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#001A72]"></span>
                    <span>3. Konfigurasi Layout yang Didukung</span>
                </div>
                <span class="text-xs text-slate-400 font-normal">Pilih minimal 1 layout</span>
            </h3>

            @php
                $availableLayouts = [
                    'theater' => ['label' => 'Theater', 'desc' => 'Deretan kursi berbaris menghadap panggung / layar utama.'],
                    'classroom' => ['label' => 'Classroom', 'desc' => 'Meja dan kursi berbaris menghadap instruktur / proyektor.'],
                    'u_shape' => ['label' => 'U-Shape', 'desc' => 'Konfigurasi meja berbentuk huruf U untuk interaksi rapat.'],
                    'roundtable' => ['label' => 'Roundtable / Island', 'desc' => 'Meja bundar/kelompok untuk workshop & diskusi fokus.'],
                    'hollow_square' => ['label' => 'Hollow Square', 'desc' => 'Meja persegi dengan ruang kosong di tengah.'],
                    'boardroom' => ['label' => 'Boardroom', 'desc' => 'Satu meja konferensi panjang untuk rapat eksekutif.'],
                    'standing' => ['label' => 'Standing / Cocktail', 'desc' => 'Ruang terbuka tanpa kursi untuk pameran atau temu komunitas.'],
                ];
                $currentLayouts = is_array($room->supported_layouts) ? $room->supported_layouts : json_decode($room->supported_layouts ?? '[]', true);
                $selectedLayouts = old('supported_layouts', $currentLayouts ?? ['theater']);
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                @foreach($availableLayouts as $key => $item)
                    <label class="flex items-start gap-2.5 p-2.5 rounded-brand bg-slate-50 border border-slate-200 hover:bg-slate-100 cursor-pointer transition select-none">
                        <input type="checkbox" 
                               name="supported_layouts[]" 
                               value="{{ $key }}" 
                               {{ in_array($key, $selectedLayouts) ? 'checked' : '' }} 
                               class="w-4 h-4 mt-0.5 rounded border-slate-300 text-[#001A72] focus:ring-[#001A72]">
                        <div class="text-xs">
                            <span class="font-black text-slate-900 block">{{ $item['label'] }}</span>
                            <span class="text-[11px] text-slate-500 leading-tight">{{ $item['desc'] }}</span>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('supported_layouts')
                <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <!-- Section 4: Foto Ruangan -->
        <div class="bg-white p-4 sm:p-5 rounded-brand border border-slate-200 shadow-2xs space-y-3">
            <h3 class="text-sm font-black text-[#001A72] border-b border-slate-100 pb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#001A72]"></span>
                <span>4. Foto Ruangan Saat Ini &amp; Tambah Foto Baru</span>
            </h3>

            <!-- Existing Photos Gallery -->
            @if($room->images->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                    @foreach($room->images as $img)
                        <div class="relative rounded-brand overflow-hidden border border-slate-200 bg-slate-100 aspect-video group">
                            <img src="{{ asset('storage/' . $img->image_url) }}" class="w-full h-full object-cover">
                            @if($img->is_primary)
                                <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded text-[9px] font-black bg-[#001A72] text-white">
                                    Utama
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="border-2 border-dashed border-slate-300 hover:border-[#001A72] bg-slate-50/50 rounded-brand p-5 text-center cursor-pointer transition relative">
                <input type="file" 
                       name="photos[]" 
                       multiple 
                       accept="image/*" 
                       class="opacity-0 absolute inset-0 w-full h-full cursor-pointer z-10">
                <div class="space-y-1.5 pointer-events-none">
                    <div class="w-10 h-10 mx-auto rounded-full bg-indigo-50 text-[#001A72] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                    </div>
                    <span class="text-xs sm:text-sm font-black text-slate-800 block">Tambah / Unggah Foto Tambahan</span>
                    <span class="text-[11px] text-slate-500 block">Foto baru akan ditambahkan ke galeri ruangan.</span>
                </div>
            </div>
        </div>

        <!-- Form Action Buttons -->
        <div class="flex items-center justify-end gap-2.5 pt-2">
            <a href="{{ route('admin.rooms.show', $room) }}" 
               class="px-4 py-2 rounded-brand bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-xs transition">
                Batal
            </a>
            <button type="submit" 
                    class="px-5 py-2 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white font-black text-xs shadow-md transition active:scale-95 cursor-pointer">
                Perbarui Data Ruangan
            </button>
        </div>
    </form>

</div>
@endsection
