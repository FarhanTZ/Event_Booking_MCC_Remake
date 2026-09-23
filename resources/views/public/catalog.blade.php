<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale() ?? 'id') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Ruangan & Fasilitas - MCC Event Booking</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-indigo-600 selection:text-white"
      x-data="{
          selectedFloor: 'all',
          selectedCapacity: 'all',
          searchQuery: '',
          rooms: [
              {
                  id: 1,
                  name: 'Auditorium Utama MCC',
                  floor: 2,
                  capacity: 450,
                  area: '520 m²',
                  image: 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=800&q=80',
                  badge: 'Gedung Pertunjukan & Seminar',
                  layouts: ['Theater', 'Classroom'],
                  facilities: ['Videotron 8x4m', 'Sound System 10kW', 'Lighting Panggung', 'AC Sentral']
              },
              {
                  id: 2,
                  name: 'Studio Animasi & Motion Capture',
                  floor: 4,
                  capacity: 35,
                  area: '110 m²',
                  image: 'https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?auto=format&fit=crop&w=800&q=80',
                  badge: 'Produksi Digital & VFX',
                  layouts: ['Workstation'],
                  facilities: ['Green Screen Wall', 'PC Workstation GPU', 'Audio Monitor', 'Peredam Akustik']
              },
              {
                  id: 3,
                  name: 'Co-Working Space & Creative Hub',
                  floor: 3,
                  capacity: 120,
                  area: '280 m²',
                  image: 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=800&q=80',
                  badge: 'Workshop & Diskusi',
                  layouts: ['Open Space', 'Round Table'],
                  facilities: ['High Speed WiFi 1Gbps', 'Smart Projector', 'Dispenser', 'Whiteboard Wall']
              },
              {
                  id: 4,
                  name: 'Ruang Rapat Eksekutif (Meeting Room A)',
                  floor: 5,
                  capacity: 25,
                  area: '65 m²',
                  image: 'https://images.unsplash.com/photo-1517502884422-41eaead166d4?auto=format&fit=crop&w=800&q=80',
                  badge: 'Rapat & Kurasi Formal',
                  layouts: ['Conference Board'],
                  facilities: ['Video Conference 4K', 'Mic Meja Delegate', 'Smart TV 75 inch', 'AC Sentral']
              },
              {
                  id: 5,
                  name: 'Galeri Pameran Seni & Ekshibisi',
                  floor: 2,
                  capacity: 200,
                  area: '340 m²',
                  image: 'https://images.unsplash.com/photo-1531058020387-3be344556be6?auto=format&fit=crop&w=800&q=80',
                  badge: 'Pameran Produk & Seni',
                  layouts: ['Exhibition / Open Gallery'],
                  facilities: ['Spotlight Track', 'Display Panel Partisi', 'Sound System Ambient', 'CCTV 360']
              },
              {
                  id: 6,
                  name: 'Podcast Studio & Audio Recording',
                  floor: 4,
                  capacity: 8,
                  area: '30 m²',
                  image: 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?auto=format&fit=crop&w=800&q=80',
                  badge: 'Audio & Podcast',
                  layouts: ['Recording Pod'],
                  facilities: ['4x Shure SM7B Mic', 'Rodecaster Pro II', 'Acoustic Soundproofing', 'Lighting Ring']
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

    <!-- Hero Section -->
    <section class="bg-gradient-to-b from-indigo-900 via-indigo-950 to-slate-900 text-white py-16 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="max-w-7xl mx-auto relative z-10 text-center">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-semibold uppercase tracking-wider mb-4 border border-indigo-400/30">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> Portal Reservasi Mandiri
            </span>
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">Katalog Ruangan Malang Creative Center</h1>
            <p class="mt-4 text-base sm:text-lg text-slate-300 max-w-2xl mx-auto font-normal">
                Pilih ruangan, periksa jadwal ketersediaan real-time secara transparan tanpa birokrasi manual, dan amankan slot acara Anda.
            </p>
        </div>
    </section>

    <!-- Filter & Search Bar Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-7 relative z-20">
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-lg border border-slate-200">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                <!-- Search -->
                <div class="sm:col-span-5 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" x-model="searchQuery" placeholder="Cari nama ruangan atau jenis acara..." 
                           class="w-full pl-10 pr-4 py-2.5 text-sm border-slate-200 rounded-xl focus:ring-indigo-600 focus:border-indigo-600">
                </div>

                <!-- Filter Lantai -->
                <div class="sm:col-span-3">
                    <select x-model="selectedFloor" class="w-full py-2.5 px-3 text-sm border-slate-200 rounded-xl focus:ring-indigo-600 focus:border-indigo-600 text-slate-700">
                        <option value="all">Semua Lantai (1 – 8)</option>
                        <option value="1">Lantai 1 (Lobby & Gallery)</option>
                        <option value="2">Lantai 2 (Auditorium & Pameran)</option>
                        <option value="3">Lantai 3 (Co-Working Hub)</option>
                        <option value="4">Lantai 4 (Studio Multimedia)</option>
                        <option value="5">Lantai 5 (Executive Meeting)</option>
                    </select>
                </div>

                <!-- Filter Kapasitas -->
                <div class="sm:col-span-4">
                    <select x-model="selectedCapacity" class="w-full py-2.5 px-3 text-sm border-slate-200 rounded-xl focus:ring-indigo-600 focus:border-indigo-600 text-slate-700">
                        <option value="all">Semua Kapasitas Peserta</option>
                        <option value="small">Kecil (&le; 30 Orang / Meeting/Pod)</option>
                        <option value="medium">Menengah (31 – 150 Orang / Hub)</option>
                        <option value="large">Besar (&gt; 150 Orang / Auditorium)</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms Grid Section -->
    <section class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Daftar Ruangan Tersedia</h2>
                <p class="text-xs text-slate-500 mt-0.5">Dilengkapi inventaris fasilitas siap pakai & kalender ketersediaan.</p>
            </div>
            <span class="text-xs font-semibold px-3 py-1 bg-slate-200 text-slate-700 rounded-full" 
                  x-text="rooms.filter(r => filterRoom(r)).length + ' Ruangan'"></span>
        </div>

        <!-- Grid Container -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="room in rooms" :key="room.id">
                <div x-show="filterRoom(room)" 
                     class="bg-white rounded-2xl border border-slate-200 shadow-xs hover:shadow-lg transition-all duration-300 flex flex-col overflow-hidden group">
                    <!-- Thumbnail with Badges -->
                    <div class="relative h-52 overflow-hidden bg-slate-100">
                        <img :src="room.image" :alt="room.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        
                        <!-- Badges -->
                        <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                            <span class="bg-indigo-600/90 backdrop-blur-xs text-white text-[11px] font-semibold px-2.5 py-1 rounded-lg shadow-xs" x-text="'Lt. ' + room.floor"></span>
                            <span class="bg-emerald-600/90 backdrop-blur-xs text-white text-[11px] font-semibold px-2.5 py-1 rounded-lg shadow-xs" x-text="room.badge"></span>
                        </div>

                        <!-- Capacity & Area Overlay -->
                        <div class="absolute bottom-3 left-3 right-3 flex justify-between text-white text-xs font-medium">
                            <span class="flex items-center gap-1.5 drop-shadow-sm">
                                <i data-lucide="users" class="w-4 h-4 text-slate-200"></i>
                                <span x-text="'Kapasitas ' + room.capacity + ' Orang'"></span>
                            </span>
                            <span class="flex items-center gap-1 drop-shadow-sm">
                                <i data-lucide="maximize" class="w-4 h-4 text-slate-200"></i>
                                <span x-text="room.area"></span>
                            </span>
                        </div>
                    </div>

                    <!-- Room Body -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900 group-hover:text-indigo-600 transition" x-text="room.name"></h3>
                            
                            <!-- Facilities Highlights -->
                            <div class="mt-3">
                                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-1.5">Fasilitas Utama</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="fac in room.facilities" :key="fac">
                                        <span class="inline-flex items-center gap-1 text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md">
                                            <i data-lucide="check" class="w-3 h-3 text-emerald-600"></i>
                                            <span x-text="fac"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-2">
                            <a :href="'/ruangan/' + room.id" 
                               class="flex-1 text-center py-2 px-3 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                Detail Info
                            </a>
                            <a :href="'/jadwal?room_id=' + room.id" 
                               class="flex-1 inline-flex justify-center items-center gap-1.5 py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                <span>Cek Slot Jadwal</span>
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