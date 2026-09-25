<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Fasilitas & Ruangan (32 Ruangan) - Malang Creative Center (MCC)</title>
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
          rooms: @js($rooms->map(fn($r) => [
              'id' => $r->id,
              'name' => $r->name,
              'slug' => $r->slug,
              'floor' => $r->floor_level,
              'capacity' => $r->capacity,
              'description' => $r->description,
              'image' => $r->getThumbnailUrl(),
              'facilities' => $r->facilities->pluck('facility_name')->toArray(),
          ])),
          filterRoom(room) {
              const matchFloor = this.selectedFloor === 'all' || room.floor.toString() === this.selectedFloor;
              const matchSearch = room.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                  room.description.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                  room.facilities.some(f => f.toLowerCase().includes(this.searchQuery.toLowerCase()));
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
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-white/10 text-white text-xs font-semibold mb-3 border border-white/20">
                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                <span>Sistem Reservasi Mandiri Resmi MCC</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                Katalog Fasilitas Ruangan Gedung MCC (32 Ruangan)
            </h1>
            <p class="mt-2 text-sm text-slate-200 max-w-2xl leading-relaxed">
                Jelajahi 32 studio, laboratorium, hall pertunjukan, dan ruang kreatif terpadu 8 lantai. Pilih ruangan untuk melihat deskripsi detail, fasilitas, dan slot ketersediaan.
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
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Cari nama ruangan, fasilitas, atau deskripsi..." 
                           class="w-full pl-10 pr-3 py-2 text-xs sm:text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72] text-slate-900 placeholder:text-slate-400">
                </div>

                <!-- Filter Lantai -->
                <div class="sm:col-span-3">
                    <select x-model="selectedFloor" class="w-full py-2 px-3 text-xs sm:text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72] text-slate-700">
                        <option value="all">Semua Lantai (1 – 8)</option>
                        @foreach($floors as $floor)
                            <option value="{{ $floor }}">Lantai {{ $floor }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Kapasitas -->
                <div class="sm:col-span-4">
                    <select x-model="selectedCapacity" class="w-full py-2 px-3 text-xs sm:text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72] text-slate-700">
                        <option value="all">Semua Kapasitas</option>
                        <option value="small">Kecil (&le; 30 Orang / Studio, Lab, R. Rapat)</option>
                        <option value="medium">Menengah (31 – 150 Orang / Kelas, Kriya, Galeri)</option>
                        <option value="large">Besar (&gt; 150 Orang / Main Hall & Auditorium)</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms Data Grid -->
    <section class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-base font-bold text-slate-900">Daftar Fasilitas Ruangan</h2>
                <p class="text-xs text-slate-500">Pilih ruangan untuk melihat rincian fasilitas dan spesifikasi teknis.</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 bg-[#EEF2FF] text-[#001A72] rounded-full border border-[#C7D2FE]" 
                  x-text="rooms.filter(r => filterRoom(r)).length + ' Ruangan Ditemukan'"></span>
        </div>

        <!-- Grid Container -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="room in rooms" :key="room.id">
                <div x-show="filterRoom(room)" 
                     class="bg-white rounded-brand border border-slate-200 shadow-card hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col overflow-hidden group">
                    
                    <!-- Room Thumbnail -->
                    <div class="relative h-48 bg-slate-100 overflow-hidden">
                        <img :src="room.image" :alt="room.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        
                        <!-- Badges -->
                        <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                            <span class="bg-[#001A72] text-white text-[11px] font-bold px-2.5 py-0.5 rounded-brand shadow-sm" x-text="'Lantai ' + room.floor"></span>
                        </div>

                        <!-- Capacity Meta -->
                        <div class="absolute bottom-2.5 left-3 right-3 flex justify-between text-white text-xs font-medium">
                            <span class="flex items-center gap-1.5 font-semibold">
                                <i data-lucide="users" class="w-3.5 h-3.5 text-slate-300"></i>
                                <span x-text="'Kapasitas ' + room.capacity + ' Orang'"></span>
                            </span>
                            <span class="text-[11px] text-white font-semibold uppercase tracking-wider">
                                Siap Digunakan
                            </span>
                        </div>
                    </div>

                    <!-- Room Body -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="font-bold text-base text-slate-900 leading-snug group-hover:text-[#001A72] transition-colors" x-text="room.name"></h3>
                            
                            <!-- Deskripsi Ruangan dari Database -->
                            <p class="text-xs text-slate-600 mt-2 leading-relaxed line-clamp-3" x-text="room.description"></p>

                            <!-- Facilities Inventory -->
                            <div class="mt-4 pt-3 border-t border-slate-100">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-2">Fasilitas Bawaan</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="(fac, index) in room.facilities.slice(0, 3)" :key="index">
                                        <span class="inline-flex items-center gap-1 text-[11px] bg-slate-50 text-slate-700 px-2 py-0.5 rounded-md border border-slate-200">
                                            <i data-lucide="check" class="w-3 h-3 text-emerald-600"></i>
                                            <span x-text="fac"></span>
                                        </span>
                                    </template>
                                    <template x-if="room.facilities.length > 3">
                                        <span class="inline-flex items-center text-[10px] text-slate-500 px-1.5 py-0.5 font-semibold" 
                                              x-text="'+' + (room.facilities.length - 3) + ' lainnya'"></span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 pt-3.5 border-t border-slate-100 flex items-center gap-2">
                            <a :href="'/ruangan/' + room.id" 
                               class="flex-1 text-center py-2 px-3 border border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 rounded-brand text-xs font-bold text-slate-700 hover:text-[#001A72] transition-colors">
                                Detail Ruangan
                            </a>
                            <a :href="'/jadwal?room_id=' + room.id" 
                               class="flex-1 inline-flex justify-center items-center gap-1.5 py-2 px-3 bg-[#001A72] hover:bg-[#001250] active:bg-[#000A30] text-white rounded-brand text-xs font-bold shadow-xs hover:shadow transition-all">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-white"></i>
                                <span>Cek Jadwal</span>
                            </a>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty Filter State -->
        <div x-show="rooms.filter(r => filterRoom(r)).length === 0" 
             class="bg-white rounded-brand border border-slate-200 p-12 text-center my-8">
            <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                <i data-lucide="search-x" class="w-7 h-7"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-900">Ruangan Tidak Ditemukan</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                Coba sesuaikan kata kunci pencarian, filter lantai, atau kapasitas ruangan yang dipilih.
            </p>
            <button type="button" @click="selectedFloor = 'all'; selectedCapacity = 'all'; searchQuery = ''" 
                    class="mt-4 px-4 py-2 rounded-brand bg-[#EEF2FF] text-[#001A72] text-xs font-bold hover:bg-[#E0E7FF] transition-colors">
                Reset Filter Pencarian
            </button>
        </div>
    </section>

    <!-- Footer -->
    @include('layouts.partials.footer')

</body>
</html>