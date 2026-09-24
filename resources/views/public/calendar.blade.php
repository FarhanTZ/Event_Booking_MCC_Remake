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
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-[#001A72] selection:text-white pb-28 sm:pb-32"
      x-data="{
          selectedDate: '{{ $selectedDate }}',
          selectedRoomId: '{{ $selectedRoomId }}',
          isLoading: false,
          isLocking: false,
          errorMessage: '',
          showBasketDrawer: false,
          rooms: @js($rooms->map(fn($r) => [
              'id' => $r->id,
              'name' => $r->name,
              'floor' => $r->floor_level,
              'capacity' => $r->capacity,
              'buffer_minutes' => $r->buffer_minutes,
          ])),
          slots: @js($initialSlots),
          basket: [],
          
          init() {
              const saved = localStorage.getItem('mcc_schedule_basket');
              if (saved) {
                  try {
                      this.basket = JSON.parse(saved);
                  } catch (e) {
                      this.basket = [];
                  }
              }
              this.$watch('basket', (val) => {
                  localStorage.setItem('mcc_schedule_basket', JSON.stringify(val));
              });
          },

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
                      
                      // Auto-purge any stale booked/buffer/maintenance slots from basket for this room & date
                      this.basket = this.basket.filter(b => {
                          if (parseInt(b.room_id) === parseInt(this.selectedRoomId) && b.date === this.selectedDate) {
                              const matched = this.slots.find(s => s.time === b.slot_time);
                              return matched && (matched.status === 'available' || matched.is_mine);
                          }
                          return true;
                      });
                  } else {
                      this.errorMessage = data.message || 'Gagal memuat slot ruangan.';
                  }
              } catch (err) {
                  this.errorMessage = 'Terjadi kesalahan jaringan saat memuat jadwal slot.';
              } finally {
                  this.isLoading = false;
                  this.$nextTick(() => {
                      if (window.lucide) window.lucide.createIcons();
                  });
              }
          },

          isInBasket(roomId, date, slotTime) {
              const slotObj = this.slots.find(s => s.time === slotTime);
              if (slotObj && slotObj.status !== 'available' && !slotObj.is_mine) {
                  return false;
              }
              return this.basket.some(b => 
                  parseInt(b.room_id) === parseInt(roomId) && 
                  b.date === date && 
                  b.slot_time === slotTime
              );
          },

          toggleBasketSlot(slot) {
              if (slot.status !== 'available' && !slot.is_mine) return;

              const room = this.getSelectedRoom();
              const existingIndex = this.basket.findIndex(b => 
                  parseInt(b.room_id) === parseInt(this.selectedRoomId) && 
                  b.date === this.selectedDate && 
                  b.slot_time === slot.time
              );

              if (existingIndex > -1) {
                  this.basket.splice(existingIndex, 1);
              } else {
                  this.basket.push({
                      room_id: parseInt(this.selectedRoomId),
                      room_name: room.name || 'Ruangan MCC',
                      floor: room.floor || 1,
                      date: this.selectedDate,
                      slot_time: slot.time
                  });
              }
          },

          removeFromBasket(index) {
              this.basket.splice(index, 1);
          },

          clearBasket() {
              if (confirm('Apakah Anda yakin ingin mengosongkan seluruh isi keranjang jadwal?')) {
                  this.basket = [];
                  this.showBasketDrawer = false;
              }
          },

          async proceedToWizard() {
              if (this.basket.length === 0) {
                  alert('Keranjang jadwal Anda masih kosong. Silakan pilih minimal 1 slot waktu.');
                  return;
              }

              this.isLocking = true;
              try {
                  const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');
                  const payloadSlots = this.basket.map(b => ({
                      room_id: parseInt(b.room_id),
                      date: b.date,
                      slot_time: b.slot_time
                  }));

                  // Atomic Batch Lock via Redis/Cache (REQ-USR-07.B)
                  const response = await fetch('/api/booking/batch-lock', {
                      method: 'POST',
                      headers: {
                          'Content-Type': 'application/json',
                          'Accept': 'application/json',
                          'X-CSRF-TOKEN': csrfToken,
                          'X-Requested-With': 'XMLHttpRequest'
                      },
                      body: JSON.stringify({ slots: payloadSlots })
                  });

                  const result = await response.json();

                  if (response.ok && result.success) {
                      // Redirect to wizard with basket data
                      window.location.href = `/booking/wizard?basket=${encodeURIComponent(JSON.stringify(this.basket))}`;
                  } else {
                      // Atomic Conflict notification
                      alert(result.message || 'Terjadi bentrok jadwal dengan pemohon lain. Seluruh penguncian dibatalkan.');
                      if (result.conflict_slot) {
                          const conflictIndex = this.basket.findIndex(b => 
                              parseInt(b.room_id) === parseInt(result.conflict_slot.room_id) &&
                              b.date === result.conflict_slot.date &&
                              b.slot_time === result.conflict_slot.slot_time
                          );
                          if (conflictIndex > -1) {
                              this.basket.splice(conflictIndex, 1);
                          }
                      }
                      await this.fetchSlots();
                  }
              } catch (err) {
                  alert('Terjadi kendala jaringan saat mengunci jadwal batch. Silakan coba kembali.');
              } finally {
                  this.isLocking = false;
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
                <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                <span>Multi-Room & Multi-Schedule Tray Engine (REQ-USR-07)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                Kalender Ketersediaan Slot Ruangan
            </h1>
            <p class="mt-2 text-sm text-slate-200 max-w-3xl leading-relaxed">
                Pilih satu atau beberapa ruangan dan tanggal sekaligus ke dalam <strong>Keranjang Jadwal</strong> untuk kegiatan multi-hari, festival, atau workshop paralel.
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

                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-semibold bg-[#EEF2FF] text-[#001A72] border border-[#C7D2FE]">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5 text-[#001A72]"></i>
                    Dipilih di Keranjang
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
                    Buffer 30m
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
                            Matriks Ketersediaan: <span class="text-[#001A72]" x-text="getSelectedRoom().name || 'Ruangan'"></span>
                        </h2>
                        <span class="text-xs text-slate-500 font-medium">
                            Tanggal: <strong class="text-slate-800" x-text="selectedDate"></strong> &bull; Operasional: 08:00 – 22:00 WIB
                        </span>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full border border-emerald-200">
                        <span x-text="slots.filter(s => s.status === 'available').length + ' Slot Kosong'"></span>
                    </span>
                </div>
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
                             'bg-rose-50/40': slot.status === 'booked',
                             'bg-slate-50/80': slot.status === 'buffer',
                             'bg-zinc-100/60': slot.status === 'maintenance',
                             'bg-[#EEF2FF]/70 border-l-4 border-l-[#001A72]': slot.status === 'available' && isInBasket(selectedRoomId, selectedDate, slot.time),
                             'bg-amber-50/30': slot.status === 'locked' && !isInBasket(selectedRoomId, selectedDate, slot.time),
                             'hover:bg-emerald-50/30 bg-white': slot.status === 'available' && !isInBasket(selectedRoomId, selectedDate, slot.time)
                         }">
                        
                        <!-- Left Meta: Jam & Status Description -->
                        <div class="flex items-start sm:items-center gap-4 flex-1">
                            <div class="flex items-center gap-1.5 min-w-36">
                                <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                                <span class="font-mono font-bold text-sm text-slate-900" x-text="slot.time"></span>
                            </div>

                            <div class="flex-1">
                                <!-- 1. Status: Booked (Sudah Terisi) -->
                                <template x-if="slot.status === 'booked'">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                                        <span x-text="slot.label"></span>
                                    </span>
                                </template>

                                <!-- 2. Status: Buffer (Pembersihan & Reset) -->
                                <template x-if="slot.status === 'buffer'">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-slate-200 text-slate-700 border border-slate-300">
                                        <i data-lucide="timer" class="w-3.5 h-3.5 text-slate-500"></i>
                                        <span x-text="slot.label"></span>
                                    </span>
                                </template>

                                <!-- 3. Status: Maintenance / Libur -->
                                <template x-if="slot.status === 'maintenance'">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-zinc-200 text-zinc-700 border border-zinc-300">
                                        <i data-lucide="wrench" class="w-3.5 h-3.5 text-zinc-500"></i>
                                        <span x-text="slot.label"></span>
                                    </span>
                                </template>

                                <!-- 4. Status: Locked (Diproses Orang Lain) -->
                                <template x-if="slot.status === 'locked' && !isInBasket(selectedRoomId, selectedDate, slot.time)">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-900 border border-amber-300">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span x-text="slot.holder"></span>
                                    </span>
                                </template>

                                <!-- 5. Status: Is In Basket -->
                                <template x-if="slot.status === 'available' && isInBasket(selectedRoomId, selectedDate, slot.time)">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#001A72] text-white shadow-xs">
                                        <i data-lucide="check" class="w-3 h-3 text-amber-300"></i>
                                        <span>Dipilih di Keranjang Jadwal</span>
                                    </span>
                                </template>

                                <!-- 6. Status: Available & Not in Basket -->
                                <template x-if="slot.status === 'available' && !isInBasket(selectedRoomId, selectedDate, slot.time)">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                        <span x-text="slot.label"></span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        <!-- Right Action: Add to Basket / Select -->
                        <div class="sm:shrink-0">
                            <!-- Booked Action: Disabled Button -->
                            <template x-if="slot.status === 'booked'">
                                <button disabled 
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-brand text-xs font-semibold bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed">
                                    <i data-lucide="slash" class="w-3.5 h-3.5"></i>
                                    <span>Slot Tidak Tersedia</span>
                                </button>
                            </template>

                            <!-- Buffer Action: Disabled Button -->
                            <template x-if="slot.status === 'buffer'">
                                <button disabled 
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-brand text-xs font-semibold bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed">
                                    <i data-lucide="slash" class="w-3.5 h-3.5"></i>
                                    <span>Slot Tidak Tersedia</span>
                                </button>
                            </template>

                            <!-- Maintenance Action: Disabled Button -->
                            <template x-if="slot.status === 'maintenance'">
                                <button disabled 
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-brand text-xs font-semibold bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed">
                                    <i data-lucide="slash" class="w-3.5 h-3.5"></i>
                                    <span>Slot Tidak Tersedia</span>
                                </button>
                            </template>

                            <!-- Locked by another user -->
                            <template x-if="slot.status === 'locked' && !slot.is_mine && !isInBasket(selectedRoomId, selectedDate, slot.time)">
                                <button disabled 
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-brand text-xs font-semibold bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed">
                                    <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                                    <span>Slot Tidak Tersedia</span>
                                </button>
                            </template>

                            <!-- Available slot toggling -->
                            <template x-if="slot.status === 'available' || slot.is_mine">
                                <button @click="toggleBasketSlot(slot)" 
                                        :class="isInBasket(selectedRoomId, selectedDate, slot.time) ? 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 hover:border-rose-300' : 'bg-[#001A72] hover:bg-[#001250] text-white shadow-xs hover:shadow-sm'"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-brand text-xs font-bold transition-all cursor-pointer">
                                    <i :data-lucide="isInBasket(selectedRoomId, selectedDate, slot.time) ? 'x' : 'plus-circle'" class="w-4 h-4"></i>
                                    <span x-text="isInBasket(selectedRoomId, selectedDate, slot.time) ? 'Batalkan Pilihan' : '+ Tambah ke Jadwal'"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- ======================================================================== -->
    <!-- FLOATING SCHEDULE BASKET / TRAY BAR (REQ-USR-07.A) -->
    <!-- ======================================================================== -->
    <div x-show="basket.length > 0" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         class="fixed bottom-0 inset-x-0 z-50 bg-[#001A72] text-white shadow-elevated border-t border-indigo-900">
        
        <!-- Expandable Drawer for Basket Items Details -->
        <div x-show="showBasketDrawer" 
             x-collapse
             class="bg-white text-slate-800 border-b border-slate-200 max-h-72 overflow-y-auto px-4 sm:px-8 py-4">
            <div class="max-w-7xl mx-auto space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <i data-lucide="layers" class="w-4 h-4 text-[#001A72]"></i>
                        <h4 class="font-bold text-xs uppercase tracking-wider text-slate-900">
                            Daftar Jadwal Terpilih (<span x-text="basket.length"></span> Slot)
                        </h4>
                    </div>
                    <button @click="clearBasket()" class="text-xs text-rose-600 hover:text-rose-800 font-semibold cursor-pointer">
                        Kosongkan Keranjang
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                    <template x-for="(item, idx) in basket" :key="idx">
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-brand flex items-center justify-between gap-2 shadow-2xs">
                            <div class="min-w-0 flex-1">
                                <span class="font-bold text-xs text-slate-900 block truncate" x-text="item.room_name"></span>
                                <div class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                    <span class="font-semibold text-[#001A72]" x-text="item.date"></span>
                                    <span>&bull;</span>
                                    <span class="font-mono font-bold text-slate-700" x-text="item.slot_time"></span>
                                </div>
                            </div>
                            <button @click="removeFromBasket(idx)" 
                                    class="w-7 h-7 rounded-md bg-white border border-slate-300 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-300 flex items-center justify-center text-slate-400 transition cursor-pointer">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Main Bar -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between gap-4">
            <!-- Left Info -->
            <div class="flex items-center gap-3 min-w-0">
                <button @click="showBasketDrawer = !showBasketDrawer" 
                        class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 flex items-center justify-center shrink-0 transition cursor-pointer relative">
                    <i data-lucide="shopping-bag" class="w-5 h-5 text-amber-300"></i>
                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-amber-400 text-slate-950 rounded-full text-[11px] font-extrabold flex items-center justify-center shadow-xs" 
                          x-text="basket.length"></span>
                </button>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-sm sm:text-base text-white truncate">Keranjang Jadwal Terpilih</span>
                        <button @click="showBasketDrawer = !showBasketDrawer" 
                                class="text-xs text-amber-300 hover:underline font-semibold inline-flex items-center gap-0.5 cursor-pointer">
                            <span x-text="showBasketDrawer ? 'Tutup Rincian' : 'Lihat Rincian'"></span>
                            <i :data-lucide="showBasketDrawer ? 'chevron-down' : 'chevron-up'" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                    <p class="text-xs text-indigo-200 truncate hidden sm:block">
                        <span x-text="basket.length + ' sesi jam dipilih.'"></span> 
                        Dapat mencakup multi-ruangan, multi-hari, atau shift terbagi.
                    </p>
                </div>
            </div>

            <!-- Right CTA Button (Atomic Lock) -->
            <div class="shrink-0 flex items-center gap-2.5">
                <button @click="clearBasket()" 
                        class="hidden md:inline-flex items-center justify-center px-4 py-2.5 rounded-brand border border-white/20 hover:bg-white/10 text-slate-200 text-xs font-semibold transition cursor-pointer">
                    Kosongkan
                </button>

                <button @click="proceedToWizard()" 
                        :disabled="isLocking" 
                        class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-brand bg-amber-400 hover:bg-amber-300 text-slate-950 text-xs sm:text-sm font-extrabold shadow-md hover:shadow-lg transition-all cursor-pointer">
                    <i data-lucide="lock" class="w-4 h-4" :class="isLocking ? 'animate-spin' : ''"></i>
                    <span x-text="isLocking ? 'Mengunci Batch Slot...' : `Lanjut Pesan (${basket.length} Jadwal)`"></span>
                </button>
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