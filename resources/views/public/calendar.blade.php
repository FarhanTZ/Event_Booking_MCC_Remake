<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Ketersediaan Slot - MCC Event Booking</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased"
      x-data="{
          selectedDate: new Date().toISOString().split('T')[0],
          selectedRoomId: '1',
          slots: [
              { time: '08:00 - 09:00', status: 'available' },
              { time: '09:00 - 10:00', status: 'available' },
              { time: '10:00 - 11:00', status: 'locked', holder: 'Sedang diisi pemohon lain' },
              { time: '11:00 - 12:00', status: 'booked', event: 'Workshop Animasi Digital' },
              { time: '12:00 - 13:00', status: 'booked', event: 'Workshop Animasi Digital' },
              { time: '13:00 - 13:30', status: 'buffer', note: 'Buffer time (Pembersihan & Reset)' },
              { time: '13:30 - 15:00', status: 'available' },
              { time: '15:00 - 17:00', status: 'available' },
              { time: '17:00 - 18:00', status: 'available' },
              { time: '18:00 - 20:00', status: 'booked', event: 'Meetup Game Developer' },
              { time: '20:00 - 22:00', status: 'available' },
          ],
          selectSlot(slot) {
              if (slot.status !== 'available') return;
              window.location.href = '/booking/wizard?room_id=' + this.selectedRoomId + '&date=' + this.selectedDate + '&slot=' + encodeURIComponent(slot.time);
          }
      }">

    <!-- Navbar -->
    @include('layouts.partials.navbar')

    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
        <!-- Header & Breadcrumbs -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                    <a href="/" class="hover:text-[#001A72]">Katalog</a>
                    <span>/</span>
                    <span class="text-[#001A72] font-bold">Kalender Real-Time</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-900">Kalender Ketersediaan Slot Ruangan</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pantau jadwal real-time operasional 08:00 – 22:00 WIB (Zero Double-Booking Engine).</p>
            </div>

            <!-- Status Badges Sesuai Design.md Bagian 1.2 -->
            <div class="flex flex-wrap items-center gap-2.5 bg-white px-4 py-2.5 rounded-brand border border-slate-200 text-xs shadow-card">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    Available (Kosong)
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Locked 15m
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                    <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                    Booked (Terisi)
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                    <i data-lucide="timer" class="w-3.5 h-3.5 text-slate-400"></i>
                    Buffer 30m
                </span>
            </div>
        </div>

        <!-- Filter Ruangan & Tanggal Selector -->
        <div class="bg-white p-5 rounded-brand border border-slate-200 shadow-card mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Pilih Ruangan</label>
                    <select x-model="selectedRoomId" class="w-full text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72]">
                        <option value="1">Auditorium Utama MCC (Lt. 2)</option>
                        <option value="2">Studio Animasi & Mocap (Lt. 4)</option>
                        <option value="3">Co-Working Space & Creative Hub (Lt. 3)</option>
                        <option value="4">Meeting Room A (Lt. 5)</option>
                        <option value="5">Galeri Pameran Seni (Lt. 2)</option>
                        <option value="6">Podcast Studio (Lt. 4)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Pilih Tanggal Kegiatan</label>
                    <input type="date" x-model="selectedDate" class="w-full text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72]">
                </div>

                <div class="bg-[#EEF2FF] border border-[#C7D2FE] rounded-brand p-3.5 text-xs text-[#001A72] flex items-start gap-2.5">
                    <i data-lucide="shield-check" class="w-4 h-4 text-[#001A72] shrink-0 mt-0.5"></i>
                    <div>
                        <span class="font-bold block">Proteksi Double-Booking</span>
                        <span>Pilih slot kosong untuk mengamankan hak pengisian form selama 15 menit via Redis Lock.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Grid Jadwal -->
        <div class="bg-white rounded-brand border border-slate-200 shadow-card overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                <span class="font-bold text-sm text-slate-800 flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4 text-[#001A72]"></i>
                    <span>Jadwal Slot Waktu: <span x-text="selectedDate" class="text-[#001A72]"></span></span>
                </span>
                <span class="text-xs text-slate-500 font-medium">Operasional: 08:00 - 22:00 WIB</span>
            </div>

            <div class="divide-y divide-slate-100">
                <template x-for="slot in slots" :key="slot.time">
                    <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-[#EEF2FF]/40 transition">
                        <div class="flex items-center gap-4">
                            <span class="font-mono font-bold text-sm text-slate-800 min-w-32" x-text="slot.time"></span>
                            
                            <div>
                                <template x-if="slot.status === 'available'">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        Slot Kosong & Siap Dipesan
                                    </span>
                                </template>
                                <template x-if="slot.status === 'locked'">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span x-text="slot.holder"></span>
                                    </span>
                                </template>
                                <template x-if="slot.status === 'booked'">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                                        <span x-text="'Terisi: ' + slot.event"></span>
                                    </span>
                                </template>
                                <template x-if="slot.status === 'buffer'">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                        <i data-lucide="timer" class="w-3.5 h-3.5 text-slate-400"></i>
                                        <span x-text="slot.note"></span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        <div>
                            <button @click="selectSlot(slot)" 
                                    :disabled="slot.status !== 'available'"
                                    :class="slot.status === 'available' ? 'bg-[#001A72] hover:bg-[#001250] text-white cursor-pointer' : 'bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200'"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-brand text-xs font-semibold transition-colors shadow-xs">
                                <i :data-lucide="slot.status === 'available' ? 'lock' : 'slash'" class="w-3.5 h-3.5"></i>
                                <span x-text="slot.status === 'available' ? 'Kunci & Booking Slot' : 'Slot Tidak Tersedia'"></span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Footer -->
    @include('layouts.partials.footer')

</body>
</html>