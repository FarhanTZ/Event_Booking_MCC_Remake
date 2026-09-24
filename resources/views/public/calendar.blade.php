<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kalender Ketersediaan Slot Real-Time - MCC Event Booking</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-[#001A72] selection:text-white"
      x-data="{
          selectedDate: '{{ $selectedDate }}',
          selectedRoomId: '{{ $selectedRoomId }}',
          isLoading: false,
          errorMessage: '',
          rooms: @js($rooms->map(fn($r) => [
              'id' => $r->id,
              'name' => $r->name,
              'floor' => $r->floor_level,
              'capacity' => $r->capacity,
              'buffer_minutes' => $r->buffer_minutes,
          ])),
          slots: @js($initialSlots),
          
          async fetchSlots() {
              this.isLoading = true;
              this.errorMessage = '';
              try {
                  const response = await fetch(`/api/rooms/${this.selectedRoomId}/availability?date=${this.selectedDate}`, {
                      headers: {
                          'Accept': 'application/json',
                          'X-Requested-With': 'XMLHttpRequest'
                      }
                  });
                  const data = await response.json();
                  if (data.success) {
                      this.slots = data.slots;
                  } else {
                      this.errorMessage = data.message || 'Gagal memuat slot ruangan.';
                  }
              } catch (err) {
                  this.errorMessage = 'Terjadi kesalahan jaringan saat memuat jadwal slot.';
              } finally {
                  this.isLoading = false;
              }
          },

          async selectSlot(slot) {
              if (slot.status !== 'available' && !slot.is_mine) return;
              
              this.isLoading = true;
              try {
                  const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');
                  const response = await fetch('/api/booking/lock-slot', {
                      method: 'POST',
                      headers: {
                          'Content-Type': 'application/json',
                          'Accept': 'application/json',
                          'X-CSRF-TOKEN': csrfToken,
                          'X-Requested-With': 'XMLHttpRequest'
                      },
                      body: JSON.stringify({
                          room_id: parseInt(this.selectedRoomId),
                          date: this.selectedDate,
                          slot_time: slot.time
                      })
                  });

                  const result = await response.json();
                  if (response.ok && result.success) {
                      window.location.href = `/booking/wizard?room_id=${this.selectedRoomId}&date=${this.selectedDate}&slot=${encodeURIComponent(slot.time)}`;
                  } else {
                      alert(result.message || 'Slot waktu baru saja diambil pemohon lain. Halaman akan diperbarui.');
                      await this.fetchSlots();
                  }
              } catch (err) {
                  alert('Terjadi kendala saat mengamankan slot. Silakan coba kembali.');
              } finally {
                  this.isLoading = false;
              }
          },

          getSelectedRoom() {
              return this.rooms.find(r => r.id.toString() === this.selectedRoomId.toString()) || {};
          }
      }">

    <!-- Navbar -->
    @include('layouts.partials.navbar')

    <!-- Header Hero Bar -->
    <section class="bg-[#001A72] text-white py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-white/10 text-amber-300 text-xs font-semibold mb-3 border border-white/20">
                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                <span>Zero Double-Booking Engine & Transparansi Real-Time</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                Kalender Ketersediaan Slot Ruangan
            </h1>
            <p class="mt-2 text-sm text-slate-200 max-w-3xl leading-relaxed">
                Pantau jadwal ketersediaan 32 fasilitas ruangan per jam (08:00 – 22:00 WIB). Slot yang Anda pilih akan diamankan sementara selama 15 menit untuk pengisian proposal.
            </p>
        </div>
    </section>

    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 -mt-4 mb-12 flex-1 relative z-20">
        <!-- Control Panel: Room & Date Selector -->
        <div class="bg-white p-5 rounded-brand border border-slate-200 shadow-card mb-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <!-- Room Selector -->
                <div class="md:col-span-6">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i data-lucide="layout-grid" class="w-3.5 h-3.5 text-[#001A72]"></i>
                        <span>Pilih Ruangan Gedung MCC</span>
                    </label>
                    <select x-model="selectedRoomId" 
                            @change="fetchSlots()" 
                            class="w-full text-xs sm:text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72] text-slate-900 bg-slate-50 font-medium">
                        @foreach($rooms->groupBy('floor_level') as $floor => $floorRooms)
                            <optgroup label="--- LANTAI {{ $floor }} ---">
                                @foreach($floorRooms as $room)
                                    <option value="{{ $room->id }}" {{ $room->id == $selectedRoomId ? 'selected' : '' }}>
                                        Lt. {{ $room->floor_level }} - {{ $room->name }} (Kap: {{ $room->capacity }} org)
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <!-- Date Selector -->
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-[#001A72]"></i>
                        <span>Pilih Tanggal Kegiatan</span>
                    </label>
                    <input type="date" 
                           x-model="selectedDate" 
                           @change="fetchSlots()" 
                           min="{{ now()->toDateString() }}"
                           class="w-full text-xs sm:text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72] text-slate-900 bg-slate-50 font-medium">
                </div>

                <!-- Live Refresh Button -->
                <div class="md:col-span-3">
                    <button @click="fetchSlots()" 
                            :disabled="isLoading" 
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 rounded-brand text-xs sm:text-sm font-semibold transition shadow-xs cursor-pointer">
                        <i data-lucide="refresh-cw" class="w-4 h-4" :class="isLoading ? 'animate-spin text-[#001A72]' : ''"></i>
                        <span>Segarkan Jadwal</span>
                    </button>
                </div>
            </div>

            <!-- Status Badges Legend -->
            <div class="mt-5 pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2.5 text-xs">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mr-1">Status Slot:</span>
                
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Tersedia / Kosong
                </span>
                
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Sedang Diproses (Lock 15m)
                </span>
                
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                    <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                    Sudah Dipesan
                </span>
                
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                    <i data-lucide="timer" class="w-3.5 h-3.5 text-slate-400"></i>
                    Buffer 30m (Pembersihan)
                </span>

                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-semibold bg-zinc-100 text-zinc-600 border border-zinc-300">
                    <i data-lucide="wrench" class="w-3.5 h-3.5 text-zinc-500"></i>
                    Pemeliharaan / Libur
                </span>
            </div>
        </div>

        <!-- Error Message Banner -->
        <template x-if="errorMessage">
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-brand text-rose-700 text-xs font-semibold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                <span x-text="errorMessage"></span>
            </div>
        </template>

        <!-- Timeline Grid Jadwal -->
        <div class="bg-white rounded-brand border border-slate-200 shadow-card overflow-hidden">
            <!-- Header Table Bar -->
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-md bg-[#001A72] text-white flex items-center justify-center font-bold text-xs">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-sm text-slate-900">
                            Matriks Ketersediaan Slot: <span class="text-[#001A72]" x-text="getSelectedRoom().name || 'Ruangan'"></span>
                        </h2>
                        <span class="text-xs text-slate-500 font-medium">
                            Tanggal: <strong class="text-slate-800" x-text="selectedDate"></strong> &bull; Operasional: 08:00 – 22:00 WIB
                        </span>
                    </div>
                </div>
                
                <span class="text-xs font-semibold px-3 py-1 bg-[#EEF2FF] text-[#001A72] rounded-full border border-[#C7D2FE] self-start sm:self-auto">
                    <span x-text="slots.filter(s => s.status === 'available').length + ' Slot Kosong'"></span>
                </span>
            </div>

            <!-- Loading Skeleton Overlay -->
            <div x-show="isLoading" class="p-12 text-center text-slate-500">
                <i data-lucide="loader" class="w-8 h-8 text-[#001A72] animate-spin mx-auto mb-2"></i>
                <p class="text-xs font-medium">Memeriksa ketersediaan database & kunci slot real-time...</p>
            </div>

            <!-- Slot Items List -->
            <div x-show="!isLoading" class="divide-y divide-slate-100">
                <template x-for="slot in slots" :key="slot.time">
                    <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition-colors"
                         :class="{
                             'hover:bg-emerald-50/40 bg-white': slot.status === 'available',
                             'bg-amber-50/20': slot.status === 'locked',
                             'bg-rose-50/20': slot.status === 'booked',
                             'bg-slate-50/60': slot.status === 'buffer',
                             'bg-zinc-100/60': slot.status === 'maintenance'
                         }">
                        
                        <!-- Left Meta: Jam & Status Description -->
                        <div class="flex items-start sm:items-center gap-4 flex-1">
                            <div class="flex items-center gap-1.5 min-w-36">
                                <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                                <span class="font-mono font-bold text-sm text-slate-900" x-text="slot.time"></span>
                            </div>

                            <div class="flex-1">
                                <!-- Status: Available -->
                                <template x-if="slot.status === 'available'">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                        <span x-text="slot.label"></span>
                                    </span>
                                </template>

                                <!-- Status: Locked (Diproses Orang Lain / Sedang Diisi) -->
                                <template x-if="slot.status === 'locked'">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-900 border border-amber-300">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span x-text="slot.holder"></span>
                                    </span>
                                </template>

                                <!-- Status: Booked (Sudah Terisi) -->
                                <template x-if="slot.status === 'booked'">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 border border-rose-200">
                                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                                        <span x-text="slot.label"></span>
                                    </span>
                                </template>

                                <!-- Status: Buffer (Pembersihan & Reset) -->
                                <template x-if="slot.status === 'buffer'">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-slate-200 text-slate-700 border border-slate-300">
                                        <i data-lucide="timer" class="w-3.5 h-3.5 text-slate-500"></i>
                                        <span x-text="slot.label"></span>
                                    </span>
                                </template>

                                <!-- Status: Maintenance / Libur -->
                                <template x-if="slot.status === 'maintenance'">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-zinc-200 text-zinc-700 border border-zinc-300">
                                        <i data-lucide="wrench" class="w-3.5 h-3.5 text-zinc-500"></i>
                                        <span x-text="slot.label"></span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        <!-- Right Action: Booking Button -->
                        <div class="sm:shrink-0">
                            <button @click="selectSlot(slot)" 
                                    :disabled="slot.status !== 'available' && !slot.is_mine"
                                    :class="(slot.status === 'available' || slot.is_mine) ? 'bg-[#001A72] hover:bg-[#001250] text-white cursor-pointer shadow-sm hover:shadow-md' : 'bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200'"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2 rounded-brand text-xs font-semibold transition-all">
                                <i :data-lucide="(slot.status === 'available' || slot.is_mine) ? 'lock' : 'slash'" class="w-3.5 h-3.5"></i>
                                <span x-text="slot.is_mine ? 'Lanjutkan Pengisian Form' : (slot.status === 'available' ? 'Kunci & Booking Slot' : 'Slot Tidak Tersedia')"></span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Footer -->
    @include('layouts.partials.footer')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>