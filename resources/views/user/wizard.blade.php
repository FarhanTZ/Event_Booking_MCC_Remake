<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Formulir Reservasi Ruangan - Malang Creative Center (MCC)</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-[#001A72] selection:text-white"
      x-data="{
          timeRemaining: 900,
          timerInterval: null,
          timeExpiredModal: false,
          
          roomId: '{{ $room->id }}',
          selectedDate: '{{ $date }}',
          selectedSlot: '{{ $slotTime }}',
          roomCapacity: {{ $room->capacity }},
          
          proposalFileName: '',
          proposalFileSize: '',
          posterFileName: '',
          posterPreviewUrl: '',

          form: {
              pic_name: '{{ auth()->user()->full_name ?? auth()->user()->name ?? "" }}',
              pic_phone: '{{ auth()->user()->phone_number ?? "" }}',
              event_name: '',
              event_category: '',
              ekraf_subsector: '',
              event_type: 'Terbuka Untuk Umum',
              estimated_participants: 0,
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

          formatDateIndo(dateStr) {
              if (!dateStr) return '';
              const date = new Date(dateStr);
              return date.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
          },

          handleProposalChange(e) {
              const file = e.target.files[0];
              if (file) {
                  if (file.size > 2 * 1024 * 1024) {
                      alert('Ukuran file proposal melebihi batas maksimal 2 MB.');
                      e.target.value = '';
                      this.proposalFileName = '';
                      return;
                  }
                  if (file.type !== 'application/pdf') {
                      alert('Format berkas proposal wajib berupa dokumen PDF (*.pdf).');
                      e.target.value = '';
                      this.proposalFileName = '';
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
              if (confirm('Apakah Anda yakin ingin membatalkan pemesanan ini? Kunci slot ruangan akan langsung dilepas seketika agar dapat dipesan orang lain.')) {
                  try {
                      const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');
                      await fetch('/api/booking/unlock-slot', {
                          method: 'POST',
                          headers: {
                              'Content-Type': 'application/json',
                              'Accept': 'application/json',
                              'X-CSRF-TOKEN': csrfToken,
                              'X-Requested-With': 'XMLHttpRequest'
                          },
                          body: JSON.stringify({
                              room_id: parseInt(this.roomId),
                              date: this.selectedDate,
                              slot_time: this.selectedSlot
                          })
                      });
                  } catch (e) {
                      console.error('Gagal melepas kunci slot:', e);
                  } finally {
                      window.location.href = `/jadwal?room_id=${this.roomId}&date=${this.selectedDate}`;
                  }
              }
          }
      }">

    <!-- Floating Slot Lock Countdown Bar (FR-PUB-03 & REQ-USR-05) -->
    <div class="sticky top-0 z-40 bg-[#001A72] text-white px-4 py-2.5 shadow-md flex items-center justify-between">
        <div class="max-w-5xl mx-auto w-full flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                <span class="text-xs font-medium text-slate-100">Slot Ruangan Terkunci Sementara (15m):</span>
                <span class="font-mono font-bold text-sm tracking-wider text-amber-300" x-text="formatTime(timeRemaining)">15:00</span>
            </div>
            <span class="text-xs text-indigo-200 hidden md:inline">Selesaikan pengisian formulir sebelum waktu berakhir.</span>
        </div>
    </div>

    <!-- Header Navigation -->
    <header class="bg-white border-b border-slate-200 py-4 px-4 sm:px-8">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <button type="button" 
                    @click="cancelBooking()" 
                    class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-rose-600 transition-colors cursor-pointer group">
                <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform"></i>
                <span>Batal & Kembali ke Kalender</span>
            </button>
            
            <span class="text-xs font-bold text-[#001A72] bg-[#EEF2FF] px-3.5 py-1.5 rounded-full border border-[#C7D2FE]">
                {{ $room->name }} (Lantai {{ $room->floor_level }})
            </span>
        </div>
    </header>

    <main class="max-w-5xl w-full mx-auto px-4 sm:px-6 py-8 flex-1">
        <!-- Error Alert Banner -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-brand text-rose-800 text-xs">
                <span class="font-bold block mb-1">Terdapat kesalahan pengisian formulir:</span>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('booking.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" name="room_id" value="{{ $room->id }}">
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name="slots[]" value="{{ $slotTime }}">

            <!-- ======================================================================== -->
            <!-- SEKSI 1: RINGKASAN RUANGAN & JADWAL (READ-ONLY CONTEXT) -->
            <!-- ======================================================================== -->
            <section class="bg-white rounded-brand border border-slate-200 shadow-card p-6">
                <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 mb-5">
                    <div class="w-8 h-8 rounded-brand bg-[#EEF2FF] text-[#001A72] flex items-center justify-center font-bold text-sm">
                        <i data-lucide="building-2" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Seksi 1: Ringkasan Ruangan & Jadwal</h2>
                        <p class="text-xs text-slate-500">Data otomatis ditarik dari slot kalender yang dipilih dan terkunci sementara.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                    <!-- Nama Ruangan & Tipe Stage -->
                    <div class="p-4 bg-slate-50 rounded-brand border border-slate-200">
                        <span class="text-slate-500 font-medium block mb-1">Nama Ruangan & Tipe Stage</span>
                        <span class="font-bold text-sm text-slate-900 block">{{ $room->name }}</span>
                        <span class="inline-block mt-1 px-2 py-0.5 bg-[#001A72] text-white text-[11px] font-bold rounded-md">
                            Lantai {{ $room->floor_level }}
                        </span>
                    </div>

                    <!-- Kapasitas Maksimal Ruangan & Biaya Sewa -->
                    <div class="p-4 bg-slate-50 rounded-brand border border-slate-200">
                        <span class="text-slate-500 font-medium block mb-1">Kapasitas Maksimal & Biaya Sewa</span>
                        <span class="font-bold text-sm text-slate-900 block">{{ $room->capacity }} Orang</span>
                        <span class="inline-block mt-1 px-2.5 py-0.5 bg-emerald-100 text-emerald-800 text-[11px] font-bold rounded-md border border-emerald-200">
                            {{ $room->base_hourly_rate > 0 ? 'Komersial (Rp ' . number_format($room->base_hourly_rate, 0, ',', '.') . ' / jam)' : 'Gratis / Subsidi Pemkot Malang' }}
                        </span>
                    </div>

                    <!-- Fasilitas Bawaan Ruangan -->
                    <div class="p-4 bg-slate-50 rounded-brand border border-slate-200">
                        <span class="text-slate-500 font-medium block mb-1">Fasilitas Bawaan Ruangan</span>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @forelse($room->facilities as $fac)
                                <span class="px-2 py-0.5 bg-white text-slate-700 text-[11px] font-semibold rounded border border-slate-200">
                                    {{ $fac->facility_name }}
                                </span>
                            @empty
                                <span class="text-slate-600 italic text-[11px]">Hanya Area</span>
                            @endforelse
                        </div>
                    </div>

                    <!-- Tanggal & Slot Jam Booking (Full Width / Span across) -->
                    <div class="sm:col-span-2 lg:col-span-3 p-4 bg-[#EEF2FF]/60 rounded-brand border border-[#C7D2FE] flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="text-[#001A72] font-semibold block text-xs">Tanggal & Slot Jam Booking</span>
                            <span class="font-bold text-sm text-slate-900 block mt-0.5" x-text="formatDateIndo('{{ $date }}')"></span>
                        </div>
                        <div class="bg-[#001A72] text-white px-3.5 py-1.5 rounded-brand font-mono font-bold text-xs inline-flex items-center gap-1.5 shadow-xs">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-300"></i>
                            <span>{{ $slotTime }} WIB</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ======================================================================== -->
            <!-- SEKSI 2: DATA PENYELENGGARA & PIC (PRE-FILLED + EDITABLE) -->
            <!-- ======================================================================== -->
            <section class="bg-white rounded-brand border border-slate-200 shadow-card p-6 sm:p-8 space-y-5">
                <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-brand bg-[#EEF2FF] text-[#001A72] flex items-center justify-center font-bold text-sm">
                        <i data-lucide="user-check" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Seksi 2: Data Penyelenggara & PIC</h2>
                        <p class="text-xs text-slate-500">Auto-fill dari akun Anda dan dapat disesuaikan untuk PIC lapangan hari-H.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nama PIC Penyelenggara -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nama PIC Penyelenggara <span class="text-rose-500">*</span>
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
                                   class="block w-full pl-10 pr-3 py-2.5 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors">
                        </div>
                    </div>

                    <!-- Nomor Telepon PIC Penyelenggara -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nomor Telepon PIC Penyelenggara <span class="text-rose-500">*</span>
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
                                   class="block w-full pl-10 pr-3 py-2.5 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors">
                        </div>
                    </div>
                </div>
            </section>

            <!-- ======================================================================== -->
            <!-- SEKSI 3: RINCIAN KEGIATAN (DATA EVENT) -->
            <!-- ======================================================================== -->
            <section class="bg-white rounded-brand border border-slate-200 shadow-card p-6 sm:p-8 space-y-5">
                <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-brand bg-[#EEF2FF] text-[#001A72] flex items-center justify-center font-bold text-sm">
                        <i data-lucide="calendar-days" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Seksi 3: Rincian Kegiatan (Data Event)</h2>
                        <p class="text-xs text-slate-500">Lengkapi parameter teknis dan kurasi kegiatan kreatif.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Nama Event -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nama Event <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="event_name" 
                               x-model="form.event_name" 
                               maxlength="200" 
                               required 
                               placeholder="Nama resmi kegiatan yang akan diselenggarakan (Maksimal 200 karakter)" 
                               class="w-full text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72]">
                    </div>

                    <!-- Kategori Event & Kategori Ekraf -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Kategori Event -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Kategori Event <span class="text-rose-500">*</span>
                            </label>
                            <select name="event_category" 
                                    x-model="form.event_category" 
                                    required 
                                    class="w-full text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72]">
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
                                Kategori Ekraf (17 Sub-Sektor) <span class="text-rose-500">*</span>
                            </label>
                            <select name="ekraf_subsector" 
                                    x-model="form.ekraf_subsector" 
                                    required 
                                    class="w-full text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72]">
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
                                Jenis Event <span class="text-rose-500">*</span>
                            </label>
                            <select name="event_type" 
                                    x-model="form.event_type" 
                                    required 
                                    class="w-full text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72]">
                                <option value="Terbuka Untuk Umum">Terbuka Untuk Umum</option>
                                <option value="Terbatas">Terbatas</option>
                                <option value="Internal">Internal</option>
                            </select>
                        </div>

                        <!-- Jumlah Peserta -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Jumlah Peserta <span class="text-rose-500">*</span> (Maks: {{ $room->capacity }} org)
                            </label>
                            <input type="number" 
                                   name="estimated_participants" 
                                   x-model="form.estimated_participants" 
                                   min="1" 
                                   max="{{ $room->capacity }}" 
                                   required 
                                   class="w-full text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72]"
                                   :class="form.estimated_participants > {{ $room->capacity }} ? 'border-rose-500 focus:ring-rose-500' : ''">
                            <span class="text-[11px] text-slate-500 mt-1 block">Validasi sistem: Nilai wajib &le; kapasitas maksimal ruangan (<strong>{{ $room->capacity }} Orang</strong>).</span>
                        </div>
                    </div>

                    <!-- Deskripsi Event -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Deskripsi Event <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="event_description" 
                                  x-model="form.event_description" 
                                  required 
                                  rows="3" 
                                  placeholder="Penjelasan konsep dan tujuan pelaksanaan kegiatan..." 
                                  class="w-full text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72]"></textarea>
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
                                  class="w-full text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72]"></textarea>
                    </div>
                </div>
            </section>

            <!-- ======================================================================== -->
            <!-- SEKSI 4: BERKAS PENDUKUNG & PAKTA INTEGRITAS LEGALITAS -->
            <!-- ======================================================================== -->
            <section class="bg-white rounded-brand border border-slate-200 shadow-card p-6 sm:p-8 space-y-6">
                <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-brand bg-[#EEF2FF] text-[#001A72] flex items-center justify-center font-bold text-sm">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Seksi 4: Berkas Pendukung & Pakta Integritas Legalitas</h2>
                        <p class="text-xs text-slate-500">Unggah berkas kurasi dan tandatangani pakta integritas penyerahan jaminan KTP.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Proposal Event (Wajib / .pdf maks 2 MB) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Proposal Event <span class="text-rose-500">*</span>
                        </label>
                        <div class="border-2 border-dashed border-slate-300 hover:border-[#001A72] rounded-brand p-5 text-center transition-colors relative bg-slate-50/50">
                            <input type="file" 
                                   name="proposal_file" 
                                   required 
                                   accept=".pdf" 
                                   @change="handleProposalChange($event)" 
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <i data-lucide="file-up" class="w-8 h-8 text-slate-400 mx-auto mb-2"></i>
                            <span class="text-xs font-bold text-slate-800 block" x-text="proposalFileName || 'Pilih Berkas Proposal (.pdf)'"></span>
                            <span class="text-[11px] text-indigo-600 font-semibold block mt-0.5" x-text="proposalFileSize"></span>
                            <p class="text-[11px] text-slate-500 mt-1">Maksimal ukuran file dokumen 2Mb dengan ekstensi file (*.pdf)</p>
                        </div>
                    </div>

                    <!-- Banner / Poster Event (Opsional / .jpg, .jpeg, .png maks 2 MB) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Banner / Poster Event <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <div class="border-2 border-dashed border-slate-300 hover:border-[#001A72] rounded-brand p-5 text-center transition-colors relative bg-slate-50/50">
                            <input type="file" 
                                   name="poster_file" 
                                   accept=".jpg,.jpeg,.png" 
                                   @change="handlePosterChange($event)" 
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            
                            <template x-if="posterPreviewUrl">
                                <div class="mb-2">
                                    <img :src="posterPreviewUrl" alt="Poster Preview" class="h-16 mx-auto object-cover rounded shadow-xs">
                                </div>
                            </template>
                            <template x-if="!posterPreviewUrl">
                                <i data-lucide="image" class="w-8 h-8 text-slate-400 mx-auto mb-2"></i>
                            </template>

                            <span class="text-xs font-bold text-slate-800 block" x-text="posterFileName || 'Pilih File Gambar Banner/Poster'"></span>
                            <p class="text-[11px] text-slate-500 mt-1">Maksimal ukuran file gambar 2Mb dengan ekstensi file (*.jpg, *.jpeg, *.png)</p>
                        </div>
                    </div>
                </div>

                <!-- Persetujuan Ketentuan & Jaminan KTP (Exact Text) -->
                <div class="p-4 bg-amber-50/90 border border-amber-200 rounded-brand text-xs text-amber-950 space-y-2">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" 
                               name="tos_agreed" 
                               value="1" 
                               x-model="form.tos_agreed" 
                               required 
                               class="mt-0.5 rounded-sm text-[#001A72] focus:ring-[#001A72]">
                        <span class="leading-relaxed font-semibold text-slate-900">
                            Saya menyetujui syarat dan ketentuan pemakaian gedung MCC serta bersedia menyerahkan fisik KTP asli penanggung jawab acara di meja Front Office saat registrasi hari-H.
                        </span>
                    </label>
                </div>

                <!-- Submit Button Bar -->
                <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                    <button type="button" 
                            @click="cancelBooking()" 
                            class="inline-flex items-center px-5 py-2.5 rounded-brand border border-slate-300 bg-white text-slate-700 text-xs sm:text-sm font-semibold hover:bg-slate-50 transition-colors cursor-pointer">
                        Batal
                    </button>

                    <button type="submit" 
                            :disabled="!form.tos_agreed" 
                            :class="form.tos_agreed ? 'bg-[#001A72] hover:bg-[#001250] text-white cursor-pointer shadow-md' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-brand text-xs sm:text-sm font-bold transition-all">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Kirim Pengajuan Permohonan</span>
                    </button>
                </div>
            </section>
        </form>
    </main>

    <!-- Modal Expired (REQ-USR-05) -->
    <div x-show="timeExpiredModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-brand max-w-md w-full p-6 text-center space-y-4 shadow-elevated">
            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 mx-auto flex items-center justify-center">
                <i data-lucide="alert-octagon" class="w-6 h-6"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Sesi Pengisian Formulir Berakhir</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Batas waktu penguncian slot 15 menit telah habis. Kunci slot telah dilepaskan kembali secara aman ke kalender umum.
            </p>
            <a href="/jadwal" class="block w-full py-2.5 bg-[#001A72] text-white text-xs font-bold rounded-brand hover:bg-[#001250] transition-colors">
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