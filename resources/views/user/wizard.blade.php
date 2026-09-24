<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Formulir Reservasi Multi-Ruangan & Multi-Jadwal - Malang Creative Center (MCC)</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-700 min-h-full flex flex-col antialiased selection:bg-[#001A72] selection:text-white"
      x-data="{
          timeRemaining: 900,
          timerInterval: null,
          timeExpiredModal: false,
          isSubmitting: false,
          
          sessions: @js($sessions),
          minCapacity: {{ $minCapacity }},
          totalHours: {{ $totalHours }},
          
          proposalFileName: '',
          proposalFileSize: '',
          posterFileName: '',
          posterPreviewUrl: '',

          form: {
              pic_name: '{{ auth()->user()->full_name ?? auth()->user()->name ?? "" }}',
              pic_phone: '{{ auth()->user()->phone_number ?? "" }}',
              pic_emergency_phone: '',
              official_letter_number: '',
              event_name: '',
              event_category: '',
              ekraf_subsector: '',
              event_type: 'Terbuka Untuk Umum',
              estimated_participants: 1,
              event_description: '',
              equipment_needed: '',
              tos_agreed: false
          },

          init() {
              this.timerInterval = setInterval(() => {
                  if (this.timeRemaining > 0) {
                      this.timeRemaining--;
                  } else {
                      clearInterval(this.timerInterval);
                      this.timeExpiredModal = true;
                  }
              }, 1000);
          },

          formatTime(seconds) {
              const m = Math.floor(seconds / 60).toString().padStart(2, '0');
              const s = (seconds % 60).toString().padStart(2, '0');
              return m + ':' + s;
          },

          handleProposalChange(e) {
              const file = e.target.files[0];
              if (file) {
                  if (file.size > 2 * 1024 * 1024) {
                      alert('Ukuran file proposal melebihi batas maksimal 2 MB.');
                      e.target.value = '';
                      this.proposalFileName = '';
                      this.proposalFileSize = '';
                      return;
                  }
                  if (file.type !== 'application/pdf') {
                      alert('Format berkas proposal wajib berupa dokumen PDF (*.pdf).');
                      e.target.value = '';
                      this.proposalFileName = '';
                      this.proposalFileSize = '';
                      return;
                  }
                  this.proposalFileName = file.name;
                  this.proposalFileSize = (file.size / 1024).toFixed(0) + ' KB';
              }
          },

          handlePosterChange(e) {
              const file = e.target.files[0];
              if (file) {
                  if (file.size > 2 * 1024 * 1024) {
                      alert('Ukuran file banner/poster melebihi batas maksimal 2 MB.');
                      e.target.value = '';
                      this.posterFileName = '';
                      this.posterPreviewUrl = '';
                      return;
                  }
                  if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
                      alert('Format banner/poster harus berupa gambar (*.jpg, *.jpeg, *.png).');
                      e.target.value = '';
                      this.posterFileName = '';
                      this.posterPreviewUrl = '';
                      return;
                  }
                  this.posterFileName = file.name;
                  this.posterPreviewUrl = URL.createObjectURL(file);
              }
          },

          async cancelBooking() {
              if (confirm('Apakah Anda yakin ingin membatalkan pemesanan ini? Seluruh kunci jadwal di keranjang akan langsung dilepas seketika.')) {
                  try {
                      const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');
                      const unlockPayload = [];
                      this.sessions.forEach(s => {
                          s.slots.forEach(slotTime => {
                              unlockPayload.push({
                                  room_id: parseInt(s.room_id),
                                  date: s.date,
                                  slot_time: slotTime
                              });
                          });
                      });

                      await fetch('/api/booking/batch-unlock', {
                          method: 'POST',
                          headers: {
                              'Content-Type': 'application/json',
                              'Accept': 'application/json',
                              'X-CSRF-TOKEN': csrfToken,
                              'X-Requested-With': 'XMLHttpRequest'
                          },
                          body: JSON.stringify({ slots: unlockPayload })
                      });
                  } catch (e) {
                      console.error('Gagal melepas kunci batch:', e);
                  } finally {
                      localStorage.removeItem('mcc_schedule_basket');
                      window.location.href = '/jadwal';
                  }
              }
          }
      }">

    <!-- 3.4 Floating Redis Lock Countdown Bar (Alpine.js & Design System) -->
    <div class="sticky top-0 z-40 bg-[#001A72] text-white px-4 sm:px-6 lg:px-8 py-3 shadow-md">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                <span class="text-xs font-medium text-slate-100">Slot Multi-Jadwal Terkunci Sementara (15m):</span>
                <span class="font-mono font-bold text-sm tracking-wider text-amber-300" x-text="formatTime(timeRemaining)">15:00</span>
            </div>
            <span class="text-xs text-indigo-200 hidden md:inline">Selesaikan pengisian proposal sebelum kunci otomatis dilepaskan.</span>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="bg-white border-b border-slate-200 py-3.5 px-4 sm:px-6 lg:px-8 sticky top-[45px] z-30 shadow-2xs">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <button type="button" 
                    @click="cancelBooking()" 
                    class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-rose-600 transition-colors cursor-pointer group">
                <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform"></i>
                <span>Batal & Kembali ke Kalender</span>
            </button>
            
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-[#001A72] bg-[#EEF2FF] px-3.5 py-1.5 rounded-full border border-[#C7D2FE]">
                    {{ count($sessions) }} Sesi Jadwal Terkonsolidasi ({{ $totalHours }} Jam)
                </span>
            </div>
        </div>
    </header>

    <main class="max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex-1">
        <!-- Error Alert Banner -->
        @if ($errors->any())
            <div class="mb-6 p-5 bg-rose-50 border border-rose-200 rounded-brand text-rose-800 text-xs shadow-xs">
                <div class="flex items-center gap-2 font-bold uppercase tracking-wider mb-1.5 text-rose-700">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                    <span>Terdapat kesalahan pengisian formulir:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('booking.store') }}" 
              method="POST" 
              enctype="multipart/form-data" 
              @submit="isSubmitting = true; localStorage.removeItem('mcc_schedule_basket');"
              class="space-y-6">
            @csrf

            <!-- Hidden inputs for Single & Multi-session payload (REQ-USR-07) -->
            <input type="hidden" name="room_id" value="{{ $primaryRoom->id }}">
            <input type="hidden" name="date" value="{{ $sessions[0]['date'] ?? $date }}">
            @foreach($sessions as $idx => $s)
                <input type="hidden" name="sessions[{{ $idx }}][room_id]" value="{{ $s['room_id'] }}">
                <input type="hidden" name="sessions[{{ $idx }}][date]" value="{{ $s['date'] }}">
                @foreach($s['slots'] as $sl)
                    <input type="hidden" name="sessions[{{ $idx }}][slots][]" value="{{ $sl }}">
                    <input type="hidden" name="slots[]" value="{{ $sl }}">
                @endforeach
            @endforeach

            <!-- ======================================================================== -->
            <!-- SEKSI 1: RINGKASAN RUANGAN & JADWAL (READ-ONLY CONTEXT - REQ-USR-07.C) -->
            <!-- ======================================================================== -->
            <section class="bg-white rounded-brand border border-slate-200 shadow-card p-6 sm:p-7 space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 flex-wrap gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-brand bg-[#EEF2FF] text-[#001A72] flex items-center justify-center font-bold text-sm shrink-0">
                            <i data-lucide="layers" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Seksi 1: Ringkasan Ruangan & Jadwal</h2>
                            <p class="text-xs text-slate-500">Tabel kompilasi seluruh sesi ruangan & tanggal yang digabungkan dalam satu permohonan.</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full border border-emerald-200">
                        {{ count($sessions) }} Sesi Terkunci
                    </span>
                </div>

                <!-- Tabel Kompilasi Multi-Ruangan & Multi-Jadwal -->
                <div class="overflow-x-auto border border-slate-200 rounded-brand">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-slate-900 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3">Nama Ruangan & Fasilitas</th>
                                <th class="px-4 py-3">Lantai</th>
                                <th class="px-4 py-3">Tanggal Sesi</th>
                                <th class="px-4 py-3">Rentang Jam</th>
                                <th class="px-4 py-3 text-center">Durasi</th>
                                <th class="px-4 py-3 text-right">Skema Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($sessions as $index => $sess)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-4 py-3.5 text-center font-semibold text-slate-500">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="font-bold text-slate-900 block text-xs">{{ $sess['room']->name }}</span>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @forelse($sess['room']->facilities as $fac)
                                                <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 text-[10px] rounded font-medium">
                                                    {{ $fac->facility_name }}
                                                </span>
                                            @empty
                                                <span class="text-slate-400 text-[10px] italic">Hanya Area</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="inline-block px-2 py-0.5 bg-[#001A72] text-white text-[10px] font-bold rounded">
                                            Lantai {{ $sess['room']->floor_level }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 font-medium text-slate-800 whitespace-nowrap">
                                        {{ $sess['date_formatted'] }}
                                    </td>
                                    <td class="px-4 py-3.5 font-mono font-bold text-[#001A72] whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-[#EEF2FF] rounded-md border border-[#C7D2FE]">
                                            <i data-lucide="clock" class="w-3 h-3 text-[#001A72]"></i>
                                            <span>{{ $sess['start_time'] }} – {{ $sess['end_time'] }} WIB</span>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-bold text-slate-900">
                                        {{ $sess['duration_hours'] }} Jam
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-medium">
                                        @if($sess['room']->base_hourly_rate > 0)
                                            <span class="text-slate-900 font-bold">
                                                Rp {{ number_format($sess['room']->base_hourly_rate * $sess['duration_hours'], 0, ',', '.') }}
                                            </span>
                                            <span class="block text-[10px] text-slate-400">Komersial</span>
                                        @else
                                            <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                                Gratis / Subsidi
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-[#EEF2FF]/80 font-bold text-slate-900 border-t border-[#C7D2FE]">
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-right text-xs uppercase tracking-wider text-[#001A72]">
                                    Akumulasi Total Waktu Penggunaan Ruangan:
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-extrabold text-[#001A72]">
                                    {{ $totalHours }} Jam
                                </td>
                                <td class="px-4 py-3 text-right text-xs">
                                    @if($totalFee > 0)
                                        <span class="text-sm font-extrabold text-slate-900">
                                            Rp {{ number_format($totalFee, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-emerald-800 font-bold">
                                            Subsidi Penuh (Rp 0)
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="p-3 bg-indigo-50/70 rounded-brand border border-indigo-200 text-xs text-[#001A72] flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-4 h-4 shrink-0 text-[#001A72]"></i>
                    <span>
                        Seluruh jadwal di atas telah diamankan secara atomik selama 15 menit dan digabungkan menjadi <strong>1 berkas permohonan tunggal</strong>.
                    </span>
                </div>
            </section>

            <!-- ======================================================================== -->
            <!-- SEKSI 2: DATA PENYELENGGARA & PIC (PRE-FILLED + EDITABLE) -->
            <!-- ======================================================================== -->
            <section class="bg-white rounded-brand border border-slate-200 shadow-card p-6 sm:p-7 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-brand bg-[#EEF2FF] text-[#001A72] flex items-center justify-center font-bold text-sm shrink-0">
                        <i data-lucide="user-check" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Seksi 2: Data Penyelenggara & PIC</h2>
                        <p class="text-xs text-slate-500">Auto-fill dari akun Anda, dapat disesuaikan untuk kontak lapangan hari-H.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Nama PIC Penyelenggara -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nama PIC Penyelenggara <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative rounded-brand">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </div>
                            <input type="text" 
                                   name="pic_name" 
                                   x-model="form.pic_name" 
                                   required 
                                   placeholder="Nama lengkap penanggung jawab" 
                                   class="block w-full pl-10 pr-3 py-2.5 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors shadow-2xs">
                        </div>
                    </div>

                    <!-- Nomor Telepon PIC Penyelenggara -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nomor Telepon PIC Penyelenggara <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative rounded-brand">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="phone" class="w-4 h-4"></i>
                            </div>
                            <input type="tel" 
                                   name="pic_phone" 
                                   x-model="form.pic_phone" 
                                   required 
                                   placeholder="cth: 08123456789 atau +628123456789" 
                                   class="block w-full pl-10 pr-3 py-2.5 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors shadow-2xs">
                        </div>
                    </div>
                </div>
            </section>

            <!-- ======================================================================== -->
            <!-- SEKSI 3: RINCIAN KEGIATAN (DATA EVENT) -->
            <!-- ======================================================================== -->
            <section class="bg-white rounded-brand border border-slate-200 shadow-card p-6 sm:p-7 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-brand bg-[#EEF2FF] text-[#001A72] flex items-center justify-center font-bold text-sm shrink-0">
                        <i data-lucide="calendar-days" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Seksi 3: Rincian Kegiatan (Data Event)</h2>
                        <p class="text-xs text-slate-500">Lengkapi parameter teknis dan kurasi rangkaian kegiatan kreatif.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Nama Event -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nama Event <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" 
                               name="event_name" 
                               x-model="form.event_name" 
                               maxlength="200" 
                               required 
                               placeholder="Nama resmi kegiatan yang akan diselenggarakan (Maksimal 200 karakter)" 
                               class="block w-full py-2.5 px-3 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors shadow-2xs">
                    </div>

                    <!-- Kategori Event & Kategori Ekraf -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Kategori Event -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Kategori Event <span class="text-rose-600">*</span>
                            </label>
                            <select name="event_category" 
                                    x-model="form.event_category" 
                                    required 
                                    class="block w-full py-2.5 px-3 sm:text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72] text-slate-900 transition-colors shadow-2xs">
                                <option value="">Pilih Kategori Event</option>
                                <option value="Dinas">Dinas</option>
                                <option value="Kampus">Kampus</option>
                                <option value="Komersial">Komersial</option>
                                <option value="Komunitas">Komunitas</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <!-- Kategori Ekraf (17 Sub-Sektor) -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Kategori Ekraf (17 Sub-Sektor) <span class="text-rose-600">*</span>
                            </label>
                            <select name="ekraf_subsector" 
                                    x-model="form.ekraf_subsector" 
                                    required 
                                    class="block w-full py-2.5 px-3 sm:text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72] text-slate-900 transition-colors shadow-2xs">
                                <option value="">Pilih Kategori Ekraf</option>
                                <option value="Aplikasi">Aplikasi</option>
                                <option value="Arsitektur">Arsitektur</option>
                                <option value="Desain Interior">Desain Interior</option>
                                <option value="DKV">DKV</option>
                                <option value="Fashion">Fashion</option>
                                <option value="Film">Film</option>
                                <option value="Fotografi">Fotografi</option>
                                <option value="Game">Game</option>
                                <option value="Konten Digital">Konten Digital</option>
                                <option value="Kriya">Kriya</option>
                                <option value="Kuliner">Kuliner</option>
                                <option value="Modifikasi Otomotif">Modifikasi Otomotif</option>
                                <option value="Musik">Musik</option>
                                <option value="Penerbitan">Penerbitan</option>
                                <option value="Periklanan">Periklanan</option>
                                <option value="Pertunjukan">Pertunjukan</option>
                                <option value="Produk">Produk</option>
                                <option value="Seni Rupa">Seni Rupa</option>
                                <option value="Sulih Suara">Sulih Suara</option>
                                <option value="Teknologi Baru">Teknologi Baru</option>
                                <option value="Televisi & Radio">Televisi & Radio</option>
                            </select>
                        </div>
                    </div>

                    <!-- Jenis Event & Jumlah Peserta -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Jenis Event -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Jenis Event <span class="text-rose-600">*</span>
                            </label>
                            <select name="event_type" 
                                    x-model="form.event_type" 
                                    required 
                                    class="block w-full py-2.5 px-3 sm:text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72] text-slate-900 transition-colors shadow-2xs">
                                <option value="Terbuka Untuk Umum">Terbuka Untuk Umum</option>
                                <option value="Terbatas">Terbatas</option>
                                <option value="Internal">Internal</option>
                            </select>
                        </div>

                        <!-- Jumlah Peserta -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Jumlah Peserta <span class="text-rose-600">*</span>
                            </label>
                            <input type="number" 
                                   name="estimated_participants" 
                                   x-model="form.estimated_participants" 
                                   min="1" 
                                   max="{{ $minCapacity }}" 
                                   required 
                                   class="block w-full py-2.5 px-3 sm:text-sm rounded-brand border text-slate-900 transition-colors shadow-2xs"
                                   :class="form.estimated_participants > minCapacity ? 'border-rose-500 focus:ring-2 focus:ring-rose-500' : 'border-slate-300 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72]'">
                            <span class="text-[11px] text-slate-500 mt-1 block">
                                Batas kapasitas ruangan terkecil dalam rangkaian acara ini adalah <strong>{{ $minCapacity }} Orang</strong>.
                            </span>
                        </div>
                    </div>

                    <!-- Deskripsi Event -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Deskripsi Event <span class="text-rose-600">*</span>
                        </label>
                        <textarea name="event_description" 
                                  x-model="form.event_description" 
                                  required 
                                  rows="3" 
                                  placeholder="Penjelasan konsep dan tujuan pelaksanaan kegiatan..." 
                                  class="block w-full p-3 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors shadow-2xs"></textarea>
                    </div>

                    <!-- Peralatan yang Digunakan -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Peralatan yang Digunakan <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea name="equipment_needed" 
                                  x-model="form.equipment_needed" 
                                  rows="2" 
                                  placeholder="Rincian peralatan mandiri yang akan dibawa ke dalam gedung oleh pihak penyelenggara..." 
                                  class="block w-full p-3 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors shadow-2xs"></textarea>
                    </div>
                </div>
            </section>

            <!-- ======================================================================== -->
            <!-- SEKSI 4: BERKAS PENDUKUNG & PAKTA INTEGRITAS LEGALITAS -->
            <!-- ======================================================================== -->
            <section class="bg-white rounded-brand border border-slate-200 shadow-card p-6 sm:p-7 space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-brand bg-[#EEF2FF] text-[#001A72] flex items-center justify-center font-bold text-sm shrink-0">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Seksi 4: Berkas Pendukung & Pakta Integritas Legalitas</h2>
                        <p class="text-xs text-slate-500">Unggah berkas kurasi dan tandatangani pakta integritas penyerahan jaminan KTP.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Proposal Event (Wajib / .pdf maks 2 MB - REQ-USR-07.D) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Proposal Event Terpadu <span class="text-rose-600">*</span>
                        </label>
                        <div class="border-2 border-dashed border-slate-300 hover:border-[#001A72] rounded-brand p-6 text-center transition-colors relative bg-slate-50/70 hover:bg-[#EEF2FF]/30 cursor-pointer group">
                            <input type="file" 
                                   name="proposal_file" 
                                   required 
                                   accept=".pdf" 
                                   @change="handleProposalChange($event)" 
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <div class="w-10 h-10 rounded-full bg-white shadow-xs border border-slate-200 text-slate-500 group-hover:text-[#001A72] flex items-center justify-center mx-auto mb-2.5 transition-colors">
                                <i data-lucide="file-up" class="w-5 h-5"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-800 block" x-text="proposalFileName || 'Pilih Berkas Proposal (.pdf)'"></span>
                            <span class="text-[11px] text-[#001A72] font-semibold block mt-0.5" x-text="proposalFileSize"></span>
                            <p class="text-[11px] text-slate-500 mt-1">1 Berkas PDF terpadu untuk keseluruhan rangkaian acara (Maks. 2 MB)</p>
                        </div>
                    </div>

                    <!-- Banner / Poster Event (Opsional / .jpg, .jpeg, .png maks 2 MB) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Banner / Poster Event <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <div class="border-2 border-dashed border-slate-300 hover:border-[#001A72] rounded-brand p-6 text-center transition-colors relative bg-slate-50/70 hover:bg-[#EEF2FF]/30 cursor-pointer group">
                            <input type="file" 
                                   name="poster_file" 
                                   accept=".jpg,.jpeg,.png" 
                                   @change="handlePosterChange($event)" 
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            
                            <template x-if="posterPreviewUrl">
                                <div class="mb-2">
                                    <img :src="posterPreviewUrl" alt="Poster Preview" class="h-14 mx-auto object-cover rounded shadow-xs border border-slate-200">
                                </div>
                            </template>
                            <template x-if="!posterPreviewUrl">
                                <div class="w-10 h-10 rounded-full bg-white shadow-xs border border-slate-200 text-slate-500 group-hover:text-[#001A72] flex items-center justify-center mx-auto mb-2.5 transition-colors">
                                    <i data-lucide="image" class="w-5 h-5"></i>
                                </div>
                            </template>

                            <span class="text-xs font-bold text-slate-800 block" x-text="posterFileName || 'Pilih File Gambar Banner/Poster'"></span>
                            <p class="text-[11px] text-slate-500 mt-1">Maksimal ukuran file gambar 2Mb dengan format (*.jpg, *.jpeg, *.png)</p>
                        </div>
                    </div>
                </div>

                <!-- Persetujuan Ketentuan & Jaminan KTP (Exact Text) -->
                <div class="p-4 sm:p-5 bg-amber-50 border border-amber-200 rounded-brand text-xs text-amber-950 shadow-2xs">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" 
                               name="tos_agreed" 
                               value="1" 
                               x-model="form.tos_agreed" 
                               required 
                               class="mt-0.5 rounded text-[#001A72] focus:ring-[#001A72] border-amber-300 w-4 h-4 cursor-pointer">
                        <span class="leading-relaxed font-semibold text-slate-900">
                            Saya menyetujui syarat dan ketentuan pemakaian gedung MCC serta bersedia menyerahkan fisik KTP asli penanggung jawab acara di meja Front Office saat registrasi hari-H.
                        </span>
                    </label>
                </div>

                <!-- Submit Button Bar (Design System 3.1) -->
                <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-t border-slate-100">
                    <button type="button" 
                            @click="cancelBooking()" 
                            class="inline-flex items-center justify-center px-5 py-2.5 rounded-brand border border-slate-300 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">
                        <span>Batal</span>
                    </button>

                    <button type="submit" 
                            :disabled="!form.tos_agreed || isSubmitting" 
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-brand bg-[#001A72] text-white text-sm font-semibold shadow-sm hover:bg-[#001250] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#001A72] transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span x-text="isSubmitting ? 'Mengirim Permohonan...' : 'Kirim Pengajuan Permohonan'"></span>
                    </button>
                </div>
            </section>
        </form>
    </main>

    <!-- Modal Expired (REQ-USR-05) -->
    <div x-show="timeExpiredModal" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-brand max-w-md w-full p-6 text-center space-y-4 shadow-elevated">
            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 mx-auto flex items-center justify-center">
                <i data-lucide="alert-octagon" class="w-6 h-6"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Sesi Pengisian Formulir Berakhir</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Batas waktu penguncian slot 15 menit telah habis. Kunci slot telah dilepaskan kembali secara aman ke kalender umum.
            </p>
            <a href="/jadwal" class="block w-full py-2.5 bg-[#001A72] hover:bg-[#001250] text-white text-xs font-bold rounded-brand transition-colors">
                Kembali ke Kalender Jadwal
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-6 text-center text-xs text-slate-400 border-t border-slate-200 mt-auto bg-white">
        &copy; {{ date('Y') }} Malang Creative Center (MCC). Reservasi Ruangan Resmi Terpadu.
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>