<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $room->name }} - Detail Fasilitas Malang Creative Center (MCC)</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-[#001A72] selection:text-white">

    <!-- Navbar -->
    @include('layouts.partials.navbar')

    <!-- Breadcrumbs Navigation -->
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <nav class="flex items-center gap-2 text-xs text-slate-500">
                <a href="/" class="hover:text-[#001A72] transition-colors">Beranda</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                <a href="/katalog" class="hover:text-[#001A72] transition-colors">Katalog Ruangan</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                <span class="font-bold text-slate-900">{{ $room->name }}</span>
            </nav>
        </div>
    </div>

    <!-- Main Content Container -->
    <main class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Left Column: Media Showcase & Deep Descriptions -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Room Main Visual Card -->
                <div class="bg-white rounded-brand border border-slate-200 shadow-card overflow-hidden">
                    <div class="relative h-72 sm:h-96 w-full bg-slate-900 overflow-hidden">
                        <img src="{{ $room->getThumbnailUrl() }}" 
                             alt="{{ $room->name }}" 
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        
                        <div class="absolute top-4 left-4 flex gap-2">
                            <span class="bg-[#001A72] text-white text-xs font-bold px-3 py-1 rounded-brand shadow-md">
                                Lantai {{ $room->floor_level }}
                            </span>
                            <span class="bg-emerald-600 text-white text-xs font-bold px-3 py-1 rounded-brand shadow-md flex items-center gap-1">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i> Ruangan Aktif
                            </span>
                        </div>

                        <div class="absolute bottom-5 left-5 right-5 text-white">
                            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">{{ $room->name }}</h1>
                            <p class="text-xs sm:text-sm text-slate-200 mt-1">Malang Creative Center &bull; Gedung Inkubasi 8 Lantai</p>
                        </div>
                    </div>

                    <!-- Quick Specification Chips -->
                    <div class="grid grid-cols-3 divide-x divide-slate-100 border-b border-slate-100 bg-slate-50/50 p-4 text-center">
                        <div>
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Lokasi Lantai</span>
                            <span class="text-base font-extrabold text-[#001A72]">Lantai {{ $room->floor_level }}</span>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Kapasitas Maksimal</span>
                            <span class="text-base font-extrabold text-slate-900">{{ $room->capacity }} Orang</span>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Buffer Pembersihan</span>
                            <span class="text-base font-extrabold text-slate-900">{{ $room->buffer_minutes }} Menit</span>
                        </div>
                    </div>

                    <!-- Detailed Description Section -->
                    <div class="p-6 sm:p-8 space-y-6">
                        <div>
                            <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider text-xs text-[#001A72] mb-2 flex items-center gap-1.5">
                                <i data-lucide="align-left" class="w-4 h-4"></i>
                                <span>Deskripsi Lengkap & Peruntukan Ruangan</span>
                            </h2>
                            <div class="prose prose-sm text-slate-700 leading-relaxed text-sm bg-slate-50 p-4 rounded-brand border border-slate-200/80">
                                <p>{{ $room->description }}</p>
                            </div>
                        </div>

                        <!-- Facilities Inventory Detailed List -->
                        <div>
                            <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider text-xs text-[#001A72] mb-3 flex items-center gap-1.5">
                                <i data-lucide="layers" class="w-4 h-4"></i>
                                <span>Daftar Fasilitas & Peralatan Tersedia</span>
                            </h2>

                            @if($room->facilities->isEmpty())
                                <p class="text-xs text-slate-500 italic">Peralatan pendukung dibawa mandiri oleh penyelenggara acara.</p>
                            @else
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($room->facilities as $facility)
                                        <div class="flex items-center gap-3 p-3 rounded-brand border border-slate-200 bg-white shadow-2xs">
                                            <div class="w-8 h-8 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                                <i data-lucide="check" class="w-4 h-4"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-xs font-bold text-slate-900">{{ $facility->facility_name }}</h3>
                                                <span class="text-[11px] text-slate-500">Kondisi: Siap Pakai</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Tata Tertib & Ketentuan Operasional -->
                        <div class="border-t border-slate-100 pt-6">
                            <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider text-xs text-[#001A72] mb-3 flex items-center gap-1.5">
                                <i data-lucide="info" class="w-4 h-4"></i>
                                <span>Ketentuan & SOP Peminjaman Fasilitas</span>
                            </h2>
                            <ul class="space-y-2 text-xs text-slate-600">
                                <li class="flex items-start gap-2">
                                    <i data-lucide="check-circle-2" class="w-4 h-4 text-[#001A72] shrink-0 mt-0.5"></i>
                                    <span>Pemesanan dilakukan online secara mandiri dengan sistem proteksi anti-bentrok jadwal.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i data-lucide="check-circle-2" class="w-4 h-4 text-[#001A72] shrink-0 mt-0.5"></i>
                                    <span>Slot waktu operasional yang dipilih akan <strong>terkunci otomatis selama 15 menit</strong> saat pengisian form agar tidak diambil pemohon lain.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i data-lucide="check-circle-2" class="w-4 h-4 text-[#001A72] shrink-0 mt-0.5"></i>
                                    <span>Penanggung jawab (PIC) wajib <strong>menitipkan KTP Asli</strong> di Front Office MCC saat check-in pada hari acara berlangsung.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Related Rooms Section -->
                @if($relatedRooms->isNotEmpty())
                    <div class="space-y-4">
                        <h2 class="font-bold text-sm text-slate-900">Ruangan Terkait di Lantai {{ $room->floor_level }}</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @foreach($relatedRooms as $rel)
                                <a href="/ruangan/{{ $rel->id }}" class="bg-white p-4 rounded-brand border border-slate-200 shadow-2xs hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col justify-between group">
                                    <div>
                                        <div class="flex items-center justify-between text-[11px] text-slate-500 mb-1.5">
                                            <span class="font-bold text-[#001A72]">Lt. {{ $rel->floor_level }}</span>
                                            <span>{{ $rel->capacity }} Orang</span>
                                        </div>
                                        <h3 class="font-bold text-xs text-slate-900 group-hover:text-[#001A72] transition-colors leading-snug line-clamp-2">
                                            {{ $rel->name }}
                                        </h3>
                                    </div>
                                    <span class="text-[11px] font-semibold text-[#001A72] mt-3 inline-flex items-center gap-1">
                                        <span>Lihat Detail</span>
                                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform"></i>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            <!-- Right Column: Sticky Booking Action Card -->
            <div class="lg:col-span-4 sticky top-6 space-y-4">
                <div class="bg-white rounded-brand border border-slate-200 shadow-card p-6">
                    <div class="border-b border-slate-100 pb-4 mb-4">
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Status Reservasi</span>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-sm font-extrabold text-slate-900">Tersedia untuk Reservasi</span>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500">Jam Operasional:</span>
                            <strong class="text-slate-900">08.00 – 22.00 WIB</strong>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500">Skema Penggunaan:</span>
                            <strong class="text-emerald-700 font-bold">Subsidi / Komunitas</strong>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500">Batas Kuota:</span>
                            <strong class="text-slate-900">Maks. {{ $room->capacity }} Orang</strong>
                        </div>
                    </div>

                    <!-- Primary Booking Button -->
                    <a href="/jadwal?room_id={{ $room->id }}" 
                       class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white text-xs font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i data-lucide="calendar" class="w-4 h-4 text-amber-300"></i>
                        <span>Cek Ketersediaan Slot & Booking</span>
                    </a>

                    <a href="/katalog" 
                       class="w-full inline-flex items-center justify-center gap-1.5 mt-2.5 py-2.5 text-slate-600 hover:text-slate-900 text-xs font-semibold">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        <span>Kembali ke Semua Ruangan</span>
                    </a>
                </div>

                <!-- Assistance Card -->
                <div class="bg-[#EEF2FF] border border-[#C7D2FE] rounded-brand p-4 text-xs text-[#001A72]">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <i data-lucide="help-circle" class="w-4 h-4"></i>
                        <span>Butuh Bantuan Teknis?</span>
                    </div>
                    <p class="text-slate-600 leading-relaxed">
                        Hubungi Duty Officer atau tim marketing MCC melalui meja Front Office Lantai 2 untuk konsultasi kebutuhan fasilitas khusus.
                    </p>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    @include('layouts.partials.footer')

</body>
</html>
