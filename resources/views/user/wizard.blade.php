<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Formulir Reservasi Ruangan - MCC Event Booking</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-[#001A72] selection:text-white"
      x-data="{
          currentStep: 1,
          timeRemaining: 900,
          timerInterval: null,
          timeExpiredModal: false,
          roomId: '{{ $room?->id ?? 1 }}',
          selectedDate: '{{ $date ?? now()->toDateString() }}',
          selectedSlot: '{{ $slotTime ?? "08:00 - 09:00" }}',
          roomName: '{{ $room?->name ?? "Auditorium Utama MCC" }} (Lt. {{ $room?->floor_level ?? 2 }})',
          
          form: {
              pic_name: 'Ahmad Fauzi',
              pic_phone: '081234567890',
              pic_email: 'fauzi@komunitasdesain.id',
              pic_emergency: '',
              institution_name: 'Komunitas Desain Malang',
              institution_type: 'Komunitas Kreatif / Seni',
              
              event_name: '',
              event_category: '',
              estimated_participants: 50,
              event_description: '',
              requested_facilities: [],
              
              proposal_file: null,
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

          nextStep() {
              if (this.currentStep < 4) this.currentStep++;
              window.scrollTo({ top: 0, behavior: 'smooth' });
          },

          prevStep() {
              if (this.currentStep > 1) this.currentStep--;
              window.scrollTo({ top: 0, behavior: 'smooth' });
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

    <!-- Floating Redis Lock Countdown Bar -->
    <div class="sticky top-0 z-40 bg-[#001A72] text-white px-4 py-2.5 shadow-md flex items-center justify-between">
        <div class="max-w-7xl mx-auto w-full flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                <span class="text-xs font-medium text-slate-100">Slot Ruangan Terkunci Sementara:</span>
                <span class="font-mono font-bold text-sm tracking-wider text-amber-300" x-text="formatTime(timeRemaining)">15:00</span>
            </div>
            <span class="text-xs text-indigo-200 hidden md:inline">Selesaikan permohonan sebelum batas waktu berakhir.</span>
        </div>
    </div>

    <!-- Header Navigation -->
    <header class="bg-white border-b border-slate-200 py-4 px-4 sm:px-8">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <!-- Tombol Batal & Release Lock Instan -->
            <button type="button" 
                    @click="cancelBooking()" 
                    class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-rose-600 transition-colors cursor-pointer group">
                <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform"></i>
                <span>Batal & Kembali ke Kalender</span>
            </button>
            
            <span class="text-xs font-bold text-[#001A72] bg-[#EEF2FF] px-3 py-1 rounded-full border border-[#C7D2FE]" x-text="roomName">
            </span>
        </div>
    </header>

    <div class="max-w-4xl w-full mx-auto px-4 py-8 flex-1">
        <!-- Stepper Indicator -->
        <div class="mb-8">
            <div class="grid grid-cols-4 gap-2 text-center text-xs font-semibold">
                <div :class="currentStep >= 1 ? 'text-[#001A72] border-[#001A72]' : 'text-slate-400 border-slate-200'" class="border-b-4 pb-2">
                    <span class="block text-base font-bold">1</span>
                    <span class="hidden sm:inline">Identitas PIC</span>
                </div>
                <div :class="currentStep >= 2 ? 'text-[#001A72] border-[#001A72]' : 'text-slate-400 border-slate-200'" class="border-b-4 pb-2">
                    <span class="block text-base font-bold">2</span>
                    <span class="hidden sm:inline">Detail Acara</span>
                </div>
                <div :class="currentStep >= 3 ? 'text-[#001A72] border-[#001A72]' : 'text-slate-400 border-slate-200'" class="border-b-4 pb-2">
                    <span class="block text-base font-bold">3</span>
                    <span class="hidden sm:inline">Proposal & KTP</span>
                </div>
                <div :class="currentStep >= 4 ? 'text-[#001A72] border-[#001A72]' : 'text-slate-400 border-slate-200'" class="border-b-4 pb-2">
                    <span class="block text-base font-bold">4</span>
                    <span class="hidden sm:inline">Konfirmasi</span>
                </div>
            </div>
        </div>

        <!-- Form Card Container -->
        <div class="bg-white rounded-brand border border-slate-200 shadow-card p-6 sm:p-8">
            <!-- Step 1 -->
            <div x-show="currentStep === 1" class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Langkah 1: Identitas Pemohon & Legalitas Lembaga</h2>
                    <p class="text-xs text-slate-500">Data otomatis ditarik dari profil akun terdaftar Anda.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama PIC Pemohon</label>
                        <input type="text" x-model="form.pic_name" class="w-full text-sm border-slate-300 rounded-brand bg-slate-50 text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">No. WhatsApp PIC</label>
                        <input type="text" x-model="form.pic_phone" class="w-full text-sm border-slate-300 rounded-brand bg-slate-50 text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email PIC</label>
                        <input type="email" x-model="form.pic_email" class="w-full text-sm border-slate-300 rounded-brand bg-slate-50 text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Lembaga / Komunitas</label>
                        <input type="text" x-model="form.institution_name" class="w-full text-sm border-slate-300 rounded-brand bg-slate-50 text-slate-700">
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="button" @click="nextStep()" class="inline-flex items-center justify-center px-5 py-2.5 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white text-sm font-semibold shadow-xs transition-colors">
                        Lanjut ke Detail Acara &rarr;
                    </button>
                </div>
            </div>

            <!-- Step 2 -->
            <div x-show="currentStep === 2" class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Langkah 2: Deskripsi & Kebutuhan Kegiatan</h2>
                    <p class="text-xs text-slate-500">Jelaskan kegiatan kreatif yang akan diselenggarakan di MCC.</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Kegiatan / Event *</label>
                        <input type="text" x-model="form.event_name" placeholder="Contoh: Workshop Blender 3D Animation Basic" class="w-full text-sm border-slate-300 rounded-brand">
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Kategori Kegiatan *</label>
                            <select x-model="form.event_category" class="w-full text-sm border-slate-300 rounded-brand">
                                <option value="">Pilih Kategori</option>
                                <option value="Workshop / Pelatihan">Workshop / Pelatihan</option>
                                <option value="Pameran / Eksibisi">Pameran / Eksibisi</option>
                                <option value="Diskusi / Seminar">Diskusi / Seminar</option>
                                <option value="Pertunjukan Seni / Musik">Pertunjukan Seni / Musik</option>
                                <option value="Meetup Komunitas">Meetup Komunitas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Estimasi Jumlah Peserta *</label>
                            <input type="number" x-model="form.estimated_participants" min="1" max="1000" class="w-full text-sm border-slate-300 rounded-brand">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Deskripsi Ringkas Kegiatan *</label>
                        <textarea x-model="form.event_description" rows="3" placeholder="Tuliskan tujuan, susunan acara garis besar, dan target peserta..." class="w-full text-sm border-slate-300 rounded-brand"></textarea>
                    </div>
                </div>

                <div class="pt-4 flex justify-between">
                    <button type="button" @click="prevStep()" class="inline-flex items-center justify-center px-5 py-2.5 rounded-brand border border-slate-300 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">Kembali</button>
                    <button type="button" @click="nextStep()" class="inline-flex items-center justify-center px-5 py-2.5 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white text-sm font-semibold shadow-xs transition-colors">Lanjut ke Dokumen Proposal &rarr;</button>
                </div>
            </div>

            <!-- Step 3 -->
            <div x-show="currentStep === 3" class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Langkah 3: Unggah Proposal & Persetujuan Jaminan KTP</h2>
                    <p class="text-xs text-slate-500">Unggah proposal resmi bertandatangan/stempel lembaga.</p>
                </div>

                <div class="border-2 border-dashed border-slate-300 rounded-brand p-8 text-center hover:border-[#001A72] transition-colors">
                    <i data-lucide="file-up" class="w-10 h-10 text-slate-400 mx-auto mb-2"></i>
                    <p class="text-xs font-bold text-slate-700">Klik untuk upload atau drag and drop file proposal</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Format PDF (Maksimal 10 MB)</p>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-brand p-4 text-xs text-amber-900 space-y-2">
                    <span class="font-bold block flex items-center gap-1.5 text-amber-950">
                        <i data-lucide="shield-alert" class="w-4 h-4 text-amber-700"></i> Ketentuan Kustodian Fisik Jaminan KTP (SOP MCC)
                    </span>
                    <p class="leading-relaxed">
                        PIC Pemohon diwajibkan menitipkan <strong>1 lembar KTP asli</strong> kepada petugas Front Office saat Check-in operasional dan akan disimpan dalam KTP Vault Box resmi. KTP akan dikembalikan penuh saat Check-out setelah Berita Acara Clearance dinyatakan bersih.
                    </p>
                    <label class="flex items-center gap-2 pt-2 cursor-pointer">
                        <input type="checkbox" x-model="form.tos_agreed" class="rounded-sm text-[#001A72] focus:ring-[#001A72]">
                        <span class="font-bold text-slate-900">Saya memahami dan menyetujui SOP Penitipan Jaminan KTP Fisik MCC.</span>
                    </label>
                </div>

                <div class="pt-4 flex justify-between">
                    <button type="button" @click="prevStep()" class="inline-flex items-center justify-center px-5 py-2.5 rounded-brand border border-slate-300 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">Kembali</button>
                    <button type="button" @click="nextStep()" :disabled="!form.tos_agreed" :class="form.tos_agreed ? 'bg-[#001A72] hover:bg-[#001250] text-white cursor-pointer' : 'bg-slate-200 text-slate-400 cursor-not-allowed'" class="inline-flex items-center justify-center px-5 py-2.5 rounded-brand text-sm font-semibold shadow-xs transition-colors">Lanjut ke Ringkasan &rarr;</button>
                </div>
            </div>

            <!-- Step 4 -->
            <div x-show="currentStep === 4" class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Langkah 4: Konfirmasi Permohonan</h2>
                    <p class="text-xs text-slate-500">Periksa kembali data reservasi Anda sebelum dikirimkan ke tim kurasi Marketing MCC.</p>
                </div>

                <div class="bg-slate-50 rounded-brand p-5 border border-slate-200 space-y-3 text-xs">
                    <div class="grid grid-cols-3 gap-2 border-b border-slate-200 pb-2">
                        <span class="text-slate-500 font-semibold">Ruangan & Slot:</span>
                        <span class="col-span-2 font-bold text-slate-900" x-text="roomName + ' &bull; ' + selectedDate + ' (' + selectedSlot + ')'"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 border-b border-slate-200 pb-2">
                        <span class="text-slate-500 font-semibold">Nama Acara:</span>
                        <span class="col-span-2 font-bold text-[#001A72]" x-text="form.event_name || '-'"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 border-b border-slate-200 pb-2">
                        <span class="text-slate-500 font-semibold">Estimasi Peserta:</span>
                        <span class="col-span-2 font-bold text-slate-900" x-text="form.estimated_participants + ' Orang'"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 border-b border-slate-200 pb-2">
                        <span class="text-slate-500 font-semibold">Pemohon / PIC:</span>
                        <span class="col-span-2 font-bold text-slate-900" x-text="form.pic_name + ' (' + form.institution_name + ')'"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="text-slate-500 font-semibold">Jaminan KTP:</span>
                        <span class="col-span-2 font-bold text-emerald-700 flex items-center gap-1">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i> Bersedia Menyerahkan KTP Asli Saat Check-in
                        </span>
                    </div>
                </div>

                <div class="pt-4 flex justify-between">
                    <button type="button" @click="prevStep()" class="inline-flex items-center justify-center px-5 py-2.5 rounded-brand border border-slate-300 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">Kembali</button>
                    <button type="button" class="inline-flex items-center justify-center px-5 py-2.5 rounded-brand bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-sm transition-colors flex items-center gap-2 cursor-pointer">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Kirim Pengajuan Permohonan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Expired -->
    <div x-show="timeExpiredModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-brand max-w-md w-full p-6 text-center space-y-4 shadow-elevated">
            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 mx-auto flex items-center justify-center">
                <i data-lucide="alert-octagon" class="w-6 h-6"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Sesi Pengisian Formulir Berakhir</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Batas waktu penguncian slot 15 menit telah habis. Slot telah dilepaskan kembali ke kalender umum.
            </p>
            <a href="/jadwal" class="block w-full py-2.5 bg-[#001A72] text-white text-xs font-bold rounded-brand hover:bg-[#001250] transition-colors">
                Kembali ke Kalender Jadwal
            </a>
        </div>
    </div>

    <div class="py-6 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} Malang Creative Center. Zero Double-Booking Protection.
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>