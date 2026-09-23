<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Malang Creative Center (MCC) - Pusat Kolaborasi & Inkubasi Industri Kreatif</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-[#001A72] selection:text-white">

    <!-- Navbar Partial -->
    @include('layouts.partials.navbar')

    <!-- Hero Section -->
    <section class="relative bg-[#001A72] text-white min-h-[calc(100vh-4rem)] flex flex-col justify-between px-4 sm:px-6 lg:px-8 overflow-hidden pt-12 pb-6"
             x-data="{ loaded: false }" 
             x-init="setTimeout(() => loaded = true, 50)">
        <!-- Background Canvas -->
        <div class="absolute inset-0 z-0 bg-[#001A72]">
            <img src="{{ asset('assets/images/media/backgrounds/background.png') }}" 
                 alt="MCC Background Pattern" 
                 class="w-full h-full object-cover object-center">
        </div>

        <!-- Building Image: Bottom Right Edge -->
        <div class="absolute right-0 bottom-0 pointer-events-none z-0 hidden lg:block w-1/2 max-w-[700px] h-[88%] transition-all duration-1000 ease-out transform gpu-accelerated"
             :class="loaded ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'">
            <img src="{{ asset('assets/images/media/backgrounds/gedungmcc.png') }}" 
                 alt="Gedung Malang Creative Center" 
                 class="w-full h-full object-contain object-right-bottom drop-shadow-2xl">
        </div>

        <div class="max-w-7xl mx-auto w-full relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center my-auto">
            <div class="lg:col-span-7 space-y-6 text-left py-4 transition-all duration-700 ease-out transform gpu-accelerated"
                 :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
                <!-- Judul Utama -->
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
                    Wadah Kolaborasi, Kreasi & Eksplorasi Insan Kreatif
                </h1>

                <!-- Deskripsi Pengenalan MCC -->
                <p class="text-sm sm:text-base text-slate-200 max-w-2xl leading-relaxed">
                    <strong>Malang Creative Center (MCC)</strong> adalah ekosistem terpadu 8 lantai yang didedikasikan untuk mendukung pertumbuhan komunitas, pelaku industri kreatif, kreator digital, dan institusi pendidikan di Jawa Timur.
                </p>

                <!-- Tombol CTA -->
                <div class="pt-2 flex flex-wrap items-center gap-3">
                    <a href="/katalog" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-brand bg-white text-[#001A72] text-sm font-bold shadow-md hover:bg-slate-100 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                        <i data-lucide="layout-grid" class="w-4 h-4 text-[#001A72]"></i>
                        <span>Jelajahi Fasilitas Ruangan</span>
                    </a>
                    <a href="/jadwal" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-brand bg-[#001250] border border-white/30 text-white text-sm font-semibold hover:bg-[#000A30] hover:border-white/50 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                        <i data-lucide="calendar" class="w-4 h-4 text-amber-300"></i>
                        <span>Cek Ketersediaan Slot</span>
                    </a>
                </div>

                <!-- Metrik Kunci -->
                <div class="pt-6 grid grid-cols-3 gap-4 border-t border-white/15 max-w-xl">
                    <div class="transition-transform hover:scale-105 duration-200">
                        <span class="block text-2xl font-black text-white">8 Lantai</span>
                        <span class="text-xs text-indigo-200">Fasilitas Tematik</span>
                    </div>
                    <div class="transition-transform hover:scale-105 duration-200">
                        <span class="block text-2xl font-black text-white">30+ Ruang</span>
                        <span class="text-xs text-indigo-200">Studio & Lab Kreatif</span>
                    </div>
                    <div class="transition-transform hover:scale-105 duration-200">
                        <span class="block text-2xl font-black text-white">08.00 - 22.00</span>
                        <span class="text-xs text-indigo-200">Operasional Harian</span>
                    </div>
                </div>
            </div>

            <!-- Mobile Only View of Building -->
            <div class="lg:hidden relative flex items-center justify-center pt-2 transition-all duration-700 ease-out delay-200"
                 :class="loaded ? 'opacity-100 scale-100' : 'opacity-0 scale-95'">
                <img src="{{ asset('assets/images/media/backgrounds/gedungmcc.png') }}" 
                     alt="Gedung Malang Creative Center" 
                     class="w-full max-h-[300px] object-contain drop-shadow-xl">
            </div>
        </div>

        <!-- Scroll Down Indicator -->
        <div class="relative z-10 text-center pt-4">
            <a href="#tentang-mcc" class="inline-flex flex-col items-center gap-1.5 text-xs text-indigo-200 hover:text-white transition-colors group">
                <span class="font-medium tracking-wide">Scroll untuk mengenal MCC</span>
                <i data-lucide="chevron-down" class="w-4 h-4 animate-bounce group-hover:translate-y-1 transition-transform"></i>
            </a>
        </div>
    </section>

    <!-- Section: Apa itu MCC? (Identity & Mission) -->
    <section id="tentang-mcc" 
             class="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full scroll-mt-16 overflow-hidden"
             x-data="{ inView: false }" 
             x-intersect:enter.half="inView = true">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Image Showcase Grid -->
            <div class="grid grid-cols-2 gap-3.5 transition-all duration-700 ease-out transform gpu-accelerated"
                 :class="inView ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-8'">
                <div class="space-y-3.5">
                    <img src="{{ asset('assets/images/rooms/rooms_mcc/creative_city_planing_galery_lt_2.jpg') }}" 
                         loading="lazy" decoding="async"
                         class="rounded-brand shadow-card object-cover h-44 w-full transition-transform duration-500 hover:scale-[1.02]" 
                         alt="Galeri MCC">
                    <img src="{{ asset('assets/images/rooms/rooms_mcc/lab_komputer_lt_4.jpg') }}" 
                         loading="lazy" decoding="async"
                         class="rounded-brand shadow-card object-cover h-56 w-full transition-transform duration-500 hover:scale-[1.02]" 
                         alt="Lab Komputer">
                </div>
                <div class="space-y-3.5 pt-6">
                    <img src="{{ asset('assets/images/rooms/rooms_mcc/auditorium_lt_7.jpg') }}" 
                         loading="lazy" decoding="async"
                         class="rounded-brand shadow-card object-cover h-56 w-full transition-transform duration-500 hover:scale-[1.02]" 
                         alt="Auditorium">
                    <img src="{{ asset('assets/images/rooms/rooms_mcc/food_lab_lt_3.jpg') }}" 
                         loading="lazy" decoding="async"
                         class="rounded-brand shadow-card object-cover h-44 w-full transition-transform duration-500 hover:scale-[1.02]" 
                         alt="Food Lab">
                </div>
            </div>

            <!-- Content Explanation -->
            <div class="space-y-5 transition-all duration-700 delay-150 ease-out transform gpu-accelerated"
                 :class="inView ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-8'">
                <div class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-[#001A72] bg-[#EEF2FF] px-3 py-1 rounded-full border border-[#C7D2FE]">
                    <i data-lucide="info" class="w-3.5 h-3.5 text-[#001A72]"></i>
                    <span>Mengenal Malang Creative Center</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight leading-snug">
                    Pusat Ekosistem Kreatif Terlengkap di Kota Malang
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Diresmikan sebagai wadah representatif pemberdayaan generasi muda, MCC dirancang untuk mengintegrasikan potensi riset, produksi konten, inkubasi bisnis, pameran seni, hingga pementasan skala nasional dan internasional.
                </p>

                <!-- Value Pillars -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="p-4 rounded-brand border border-slate-200 bg-white shadow-card transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                        <div class="w-8 h-8 rounded-md bg-[#EEF2FF] text-[#001A72] flex items-center justify-center mb-2.5">
                            <i data-lucide="layers" class="w-4 h-4"></i>
                        </div>
                        <h3 class="font-bold text-xs text-slate-900">Infrastruktur Terintegrasi</h3>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Mulai dari studio podcast, recording, photo studio, motion capture, hingga food lab pro.</p>
                    </div>

                    <div class="p-4 rounded-brand border border-slate-200 bg-white shadow-card transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                        <div class="w-8 h-8 rounded-md bg-emerald-50 text-emerald-700 flex items-center justify-center mb-2.5">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                        </div>
                        <h3 class="font-bold text-xs text-slate-900">Akses Mandiri & Transparan</h3>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Sistem reservasi online self-service tanpa birokrasi manual dengan proteksi zero double-booking.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section: Panduan 4 Langkah Peminjaman Fasilitas (PRD & SOP) -->
    <section class="bg-white border-y border-slate-200 py-16 px-4 sm:px-6 lg:px-8 overflow-hidden"
             x-data="{ inView: false }" 
             x-intersect:enter.half="inView = true">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-12 transition-all duration-500 ease-out transform gpu-accelerated"
                 :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'">
                <span class="text-xs font-bold text-[#001A72] uppercase tracking-wider block mb-1">Alur & Ketentuan</span>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Bagaimana Cara Meminjam Ruangan di MCC?</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-2">
                    Proses pemesanan cepat, transparan, dan terstandarisasi untuk komunitas, instansi, maupun penyelenggara acara.
                </p>
            </div>

            <!-- 4-Steps Grid with Stagger Animation -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Step 1 -->
                <div class="p-5 rounded-brand border border-slate-200 bg-slate-50/50 relative flex flex-col justify-between transition-all duration-500 delay-75 ease-out transform gpu-accelerated hover:bg-white hover:shadow-lg hover:-translate-y-1.5"
                     :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
                    <div>
                        <span class="w-8 h-8 rounded-full bg-[#001A72] text-white font-bold text-xs flex items-center justify-center mb-4 shadow-sm">1</span>
                        <h3 class="font-bold text-sm text-slate-900 mb-1.5">Pilih Ruangan & Slot</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Pilih ruangan di katalog dan tentukan slot waktu operasional pada kalender ketersediaan real-time.
                        </p>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 mt-4 uppercase">Langkah Pertama</span>
                </div>

                <!-- Step 2 -->
                <div class="p-5 rounded-brand border border-slate-200 bg-slate-50/50 relative flex flex-col justify-between transition-all duration-500 delay-150 ease-out transform gpu-accelerated hover:bg-white hover:shadow-lg hover:-translate-y-1.5"
                     :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
                    <div>
                        <span class="w-8 h-8 rounded-full bg-[#001A72] text-white font-bold text-xs flex items-center justify-center mb-4 shadow-sm">2</span>
                        <h3 class="font-bold text-sm text-slate-900 mb-1.5">Kunci Slot (15 Menit)</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Slot terkunci sementara selama 15 menit agar tidak diambil pemohon lain saat Anda mengisi formulir.
                        </p>
                    </div>
                    <span class="text-[10px] font-bold text-amber-600 mt-4 uppercase">Anti Double-Booking</span>
                </div>

                <!-- Step 3 -->
                <div class="p-5 rounded-brand border border-slate-200 bg-slate-50/50 relative flex flex-col justify-between transition-all duration-500 delay-225 ease-out transform gpu-accelerated hover:bg-white hover:shadow-lg hover:-translate-y-1.5"
                     :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
                    <div>
                        <span class="w-8 h-8 rounded-full bg-[#001A72] text-white font-bold text-xs flex items-center justify-center mb-4 shadow-sm">3</span>
                        <h3 class="font-bold text-sm text-slate-900 mb-1.5">Unggah Proposal PDF</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Lengkapi detail kegiatan, kebutuhan fasilitas tambahan, dan unggah berkas proposal resmi.
                        </p>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 mt-4 uppercase">Legalitas Acara</span>
                </div>

                <!-- Step 4 -->
                <div class="p-5 rounded-brand border border-slate-200 bg-slate-50/50 relative flex flex-col justify-between transition-all duration-500 delay-300 ease-out transform gpu-accelerated hover:bg-white hover:shadow-lg hover:-translate-y-1.5"
                     :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
                    <div>
                        <span class="w-8 h-8 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center mb-4 shadow-sm">4</span>
                        <h3 class="font-bold text-sm text-slate-900 mb-1.5">Terbitkan E-Pass QR</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Setelah kurasi disetujui, bawa E-Pass QR dan <strong>titipkan KTP Asli PIC</strong> di Front Office saat check-in.
                        </p>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-600 mt-4 uppercase">Selesai & Sah</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Section: Preview Ruangan Unggulan -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full overflow-hidden"
             x-data="{ inView: false }" 
             x-intersect:enter.half="inView = true">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4 transition-all duration-500 ease-out transform gpu-accelerated"
             :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'">
            <div>
                <span class="text-xs font-bold text-[#001A72] uppercase tracking-wider block mb-1">Fasilitas Pilihan</span>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Ruangan Terpopuler di MCC</h2>
            </div>
            <a href="/katalog" class="inline-flex items-center gap-1 text-xs font-bold text-[#001A72] hover:text-[#001250] hover:underline group">
                <span>Lihat Semua 30+ Ruangan</span>
                <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1 -->
            <div class="bg-white rounded-brand border border-slate-200 shadow-card overflow-hidden flex flex-col transition-all duration-500 delay-75 ease-out transform gpu-accelerated hover:shadow-xl hover:-translate-y-1.5 group"
                 :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'">
                <div class="overflow-hidden h-44 w-full bg-slate-100">
                    <img src="{{ asset('assets/images/rooms/rooms_mcc/auditorium_lt_7.jpg') }}" 
                         loading="lazy" decoding="async"
                         alt="Auditorium" 
                         class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="bg-[#001A72] text-white text-[10px] font-bold px-2 py-0.5 rounded-brand">Lantai 7</span>
                            <span class="text-xs text-slate-500 font-medium">Kapasitas 450 Orang</span>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900">Auditorium Utama MCC</h3>
                        <p class="text-xs text-slate-500 mt-1">Dilengkapi Videotron 8x4m, sound system 10kW & lighting pertunjukan.</p>
                    </div>
                    <div class="pt-4 mt-4 border-t border-slate-100">
                        <a href="/jadwal?room_id=1" class="block text-center py-2 bg-[#001A72] hover:bg-[#001250] text-white rounded-brand text-xs font-bold transition-all shadow-sm hover:shadow">
                            Cek Jadwal Ruangan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-white rounded-brand border border-slate-200 shadow-card overflow-hidden flex flex-col transition-all duration-500 delay-150 ease-out transform gpu-accelerated hover:shadow-xl hover:-translate-y-1.5 group"
                 :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'">
                <div class="overflow-hidden h-44 w-full bg-slate-100">
                    <img src="{{ asset('assets/images/rooms/rooms_mcc/amphitheater_1_lt_5.jpg') }}" 
                         loading="lazy" decoding="async"
                         alt="Amphitheater" 
                         class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="bg-[#001A72] text-white text-[10px] font-bold px-2 py-0.5 rounded-brand">Lantai 5</span>
                            <span class="text-xs text-slate-500 font-medium">Kapasitas 120 Orang</span>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900">Amphitheater 1</h3>
                        <p class="text-xs text-slate-500 mt-1">Format tribun bertingkat ideal untuk screening film, diskusi, & seminar.</p>
                    </div>
                    <div class="pt-4 mt-4 border-t border-slate-100">
                        <a href="/jadwal?room_id=2" class="block text-center py-2 bg-[#001A72] hover:bg-[#001250] text-white rounded-brand text-xs font-bold transition-all shadow-sm hover:shadow">
                            Cek Jadwal Ruangan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-white rounded-brand border border-slate-200 shadow-card overflow-hidden flex flex-col transition-all duration-500 delay-225 ease-out transform gpu-accelerated hover:shadow-xl hover:-translate-y-1.5 group"
                 :class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'">
                <div class="overflow-hidden h-44 w-full bg-slate-100">
                    <img src="{{ asset('assets/images/rooms/rooms_mcc/ruang_podcast_lt_2.jpg') }}" 
                         loading="lazy" decoding="async"
                         alt="Podcast Studio" 
                         class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="bg-[#001A72] text-white text-[10px] font-bold px-2 py-0.5 rounded-brand">Lantai 2</span>
                            <span class="text-xs text-slate-500 font-medium">Kapasitas 10 Orang</span>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900">Ruang Podcast & Broadcasting</h3>
                        <p class="text-xs text-slate-500 mt-1">Set studio rekaman audio/video profesional dengan mikrofon broadcast Shure.</p>
                    </div>
                    <div class="pt-4 mt-4 border-t border-slate-100">
                        <a href="/jadwal?room_id=5" class="block text-center py-2 bg-[#001A72] hover:bg-[#001250] text-white rounded-brand text-xs font-bold transition-all shadow-sm hover:shadow">
                            Cek Jadwal Ruangan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Partial -->
    @include('layouts.partials.footer')

</body>
</html>