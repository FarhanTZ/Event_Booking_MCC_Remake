<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Ruangan & Fasilitas - MCC Event Booking</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-[#001A72] selection:text-white"
      x-data="{
          selectedFloor: 'all',
          selectedCapacity: 'all',
          searchQuery: '',
          rooms: [
              {
                  id: 1,
                  name: 'Auditorium Utama MCC',
                  floor: 7,
                  capacity: 450,
                  area: '520 m²',
                  image: '{{ asset('assets/images/rooms/rooms_mcc/auditorium_lt_7.jpg') }}',
                  badge: 'Pertunjukan & Konferensi',
                  facilities: ['Videotron 8x4m', 'Sound System 10kW', 'Lighting Panggung', 'AC Sentral']
              },
              {
                  id: 2,
                  name: 'Amphitheater 1',
                  floor: 5,
                  capacity: 120,
                  area: '240 m²',
                  image: '{{ asset('assets/images/rooms/rooms_mcc/amphitheater_1_lt_5.jpg') }}',
                  badge: 'Diskusi & Screening',
                  facilities: ['Tribun Seating', 'Projector & Screen', 'Sound Ambient', 'Peredam Suara']
              },
              {
                  id: 3,
                  name: 'Creative City Planning Gallery',
                  floor: 2,
                  capacity: 200,
                  area: '340 m²',
                  image: '{{ asset('assets/images/rooms/rooms_mcc/creative_city_planing_galery_lt_2.jpg') }}',
                  badge: 'Ekshibisi & Pameran',
                  facilities: ['Spotlight Track', 'Display Panel Partisi', 'Area Registrasi', 'CCTV 360']
              },
              {
                  id: 4,
                  name: 'Lab Komputer Multimedia',
                  floor: 4,
                  capacity: 40,
                  area: '110 m²',
                  image: '{{ asset('assets/images/rooms/rooms_mcc/lab_komputer_lt_4.jpg') }}',
                  badge: 'Pelatihan Digital',
                  facilities: ['40 PC Workstation', 'High-Speed LAN 1Gbps', 'Smart Projector', 'AC Sentral']
              },
              {
                  id: 5,
                  name: 'Ruang Podcast & Broadcasting',
                  floor: 2,
                  capacity: 10,
                  area: '35 m²',
                  image: '{{ asset('assets/images/rooms/rooms_mcc/ruang_podcast_lt_2.jpg') }}',
                  badge: 'Studio Audio & Podcast',
                  facilities: ['4x Shure Mic SM7B', 'Rodecaster Audio Board', 'Acoustic Soundproofing', 'Ring Lighting']
              },
              {
                  id: 6,
                  name: 'Studio Musik & Rekaman',
                  floor: 4,
                  capacity: 15,
                  area: '50 m²',
                  image: '{{ asset('assets/images/rooms/rooms_mcc/studio_musik_recording_lt_4.jpg') }}',
                  badge: 'Produksi Musik & Vokal',
                  facilities: ['Drum Set & Ampli', 'Control Room Mixer', 'Studio Monitor', 'Peredam Akustik']
              },
              {
                  id: 7,
                  name: 'Food Lab & Kuliner Kreatif',
                  floor: 3,
                  capacity: 35,
                  area: '95 m²',
                  image: '{{ asset('assets/images/rooms/rooms_mcc/food_lab_lt_3.jpg') }}',
                  badge: 'Inkubasi Kuliner',
                  facilities: ['Kitchen Island', 'Stove & Oven Pro', 'Exhaust Hood', 'Meja Preparasi']
              },
              {
                  id: 8,
                  name: 'Studio Foto & Produk',
                  floor: 5,
                  capacity: 20,
                  area: '80 m²',
                  image: '{{ asset('assets/images/rooms/rooms_mcc/studio_foto_lt_5.jpg') }}',
                  badge: 'Fotografi & Fashion',
                  facilities: ['Continuous Lighting', 'Softbox & Backdrop', 'Fitting Room', 'Meja Produk 360']
              },
              {
                  id: 9,
                  name: 'Rooftop Lounge & Area Terbuka',
                  floor: 8,
                  capacity: 150,
                  area: '400 m²',
                  image: '{{ asset('assets/images/rooms/rooms_mcc/rooftop_lt_8.jpg') }}',
                  badge: 'Networking & Semi-Outdoor',
                  facilities: ['City View 360', 'Stage Mini', 'Ambient String Light', 'Bar Counter']
              }
          ],
          filterRoom(room) {
              const matchFloor = this.selectedFloor === 'all' || room.floor.toString() === this.selectedFloor;
              const matchSearch = room.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || room.badge.toLowerCase().includes(this.searchQuery.toLowerCase());
              let matchCapacity = true;
              if (this.selectedCapacity === 'small') matchCapacity = room.capacity <= 30;
              if (this.selectedCapacity === 'medium') matchCapacity = room.capacity > 30 && room.capacity <= 150;
              if (this.selectedCapacity === 'large') matchCapacity = room.capacity > 150;
              return matchFloor && matchSearch && matchCapacity;
          }
      }">

    <!-- Navbar -->
    @include('layouts.partials.navbar')

    <!-- Solid Brand Hero Section -->
    <section class="bg-[#001A72] text-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto text-left">
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-white/10 text-amber-300 text-xs font-semibold mb-3 border border-white/20">
                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                <span>Sistem Reservasi Mandiri Resmi MCC</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Katalog Ruangan & Fasilitas Gedung MCC</h1>
            <p class="mt-2 text-sm text-slate-200 max-w-2xl leading-relaxed">
                Pilih ruangan, periksa jadwal ketersediaan real-time secara mandiri, dan amankan slot waktu acara tanpa birokrasi berbelit.
            </p>
        </div>
    </section>

    <!-- Filter & Search Toolbar -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-5 relative z-20">
        <div class="bg-white p-4 rounded-brand shadow-card border border-slate-200">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <!-- Search Input -->
                <div class="sm:col-span-5 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" x-model="searchQuery" placeholder="Cari ruangan (Auditorium, Studio, Lab, Galeri)..." 
                           class="w-full pl-10 pr-3 py-2 text-xs sm:text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72] text-slate-900 placeholder:text-slate-400">
                </div>

                <!-- Filter Lantai -->
                <div class="sm:col-span-3">
                    <select x-model="selectedFloor" class="w-full py-2 px-3 text-xs sm:text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72] text-slate-700">
                        <option value="all">Semua Lantai (1 – 8)</option>
                        <option value="2">Lantai 2 (Galeri & Podcast)</option>
                        <option value="3">Lantai 3 (Food Lab & Co-Working)</option>
                        <option value="4">Lantai 4 (Lab Komputer & Musik)</option>
                        <option value="5">Lantai 5 (Amphitheater & Foto)</option>
                        <option value="7">Lantai 7 (Auditorium Utama)</option>
                        <option value="8">Lantai 8 (Rooftop Lounge)</option>
                    </select>
                </div>

                <!-- Filter Kapasitas -->
                <div class="sm:col-span-4">
                    <select x-model="selectedCapacity" class="w-full py-2 px-3 text-xs sm:text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72] text-slate-700">
                        <option value="all">Semua Kapasitas</option>
                        <option value="small">Kecil (&le; 30 Orang / Podcast & Studio)</option>
                        <option value="medium">Menengah (31 – 150 Orang / Lab & Amphitheater)</option>
                        <option value="large">Besar (&gt; 150 Orang / Galeri & Auditorium)</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms Data Grid -->
    <section class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-base font-bold text-slate-900">Daftar Ruangan Aktif</h2>
                <p class="text-xs text-slate-500">Menampilkan foto asli fasilitas gedung Malang Creative Center.</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-full border border-slate-200" 
                  x-text="rooms.filter(r => filterRoom(r)).length + ' Ruangan'"></span>
        </div>

        <!-- Grid Container (Clean Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <template x-for="room in rooms" :key="room.id">
                <div x-show="filterRoom(room)" 
                     class="bg-white rounded-brand border border-slate-200 shadow-card hover:shadow-elevated transition-shadow duration-200 flex flex-col overflow-hidden">
                    
                    <!-- Real MCC Room Thumbnail -->
                    <div class="relative h-48 bg-slate-100 overflow-hidden">
                        <img :src="room.image" :alt="room.name" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/15 to-transparent"></div>
                        
                        <!-- Badges -->
                        <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                            <span class="bg-[#001A72] text-white text-[11px] font-bold px-2 py-0.5 rounded-brand shadow-xs" x-text="'Lt. ' + room.floor"></span>
                            <span class="bg-slate-900/90 text-white text-[11px] font-semibold px-2 py-0.5 rounded-brand" x-text="room.badge"></span>
                        </div>

                        <!-- Capacity & Area Meta -->
                        <div class="absolute bottom-2.5 left-3 right-3 flex justify-between text-white text-xs font-medium">
                            <span class="flex items-center gap-1.5 font-semibold">
                                <i data-lucide="users" class="w-3.5 h-3.5 text-slate-300"></i>
                                <span x-text="'Kapasitas ' + room.capacity + ' Orang'"></span>
                            </span>
                            <span class="flex items-center gap-1 font-semibold">
                                <i data-lucide="maximize" class="w-3.5 h-3.5 text-slate-300"></i>
                                <span x-text="room.area"></span>
                            </span>
                        </div>
                    </div>

                    <!-- Room Body -->
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="font-bold text-sm text-slate-900 leading-snug" x-text="room.name"></h3>
                            
                            <!-- Facilities Inventory -->
                            <div class="mt-3">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Fasilitas Termasuk</span>
                                <div class="flex flex-wrap gap-1">
                                    <template x-for="fac in room.facilities" :key="fac">
                                        <span class="inline-flex items-center gap-1 text-[11px] bg-slate-50 text-slate-700 px-2 py-0.5 rounded-md border border-slate-200">
                                            <i data-lucide="check" class="w-3 h-3 text-emerald-600"></i>
                                            <span x-text="fac"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-5 pt-3.5 border-t border-slate-100 flex items-center gap-2">
                            <a :href="'/ruangan/' + room.id" 
                               class="flex-1 text-center py-2 px-3 border border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 rounded-brand text-xs font-semibold text-slate-700 transition-colors">
                                Detail Info
                            </a>
                            <a :href="'/jadwal?room_id=' + room.id" 
                               class="flex-1 inline-flex justify-center items-center gap-1.5 py-2 px-3 bg-[#001A72] hover:bg-[#001250] active:bg-[#000A30] text-white rounded-brand text-xs font-semibold shadow-xs transition-colors">
                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                <span>Cek Kalender</span>
                            </a>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </section>

    <!-- Footer -->
    @include('layouts.partials.footer')

</body>
</html>