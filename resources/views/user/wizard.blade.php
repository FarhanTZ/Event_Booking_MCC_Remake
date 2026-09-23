<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Reservasi Ruangan - MCC Event Booking</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased"
      x-data="{
          currentStep: 1,
          timeRemaining: 900,
          timerInterval: null,
          timeExpiredModal: false,
          
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
          }
      }">

    <!-- Floating Redis Lock Countdown Bar Sesuai Design.md Bagian 3.4 -->
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
            <a href="/jadwal" class="flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-[#001A72]">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Batal & Kembali ke Kalender</span>
            </a>
            <span class="text-xs font-bold text-[#001A72] bg-[#EEF2FF] px-3 py-1 rounded-full border border-[#C7D2FE]">
                Auditorium Utama MCC (Lt. 2)
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

        <!-- Form Card Container Sesuai Design.md -->
        <div class="bg-white rounded-brand border border-slate-200 shadow-card p-6 sm:p-8">
            <!-- Step 1 -->
            <div x-show="currentStep === 1" class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Langkah 1: Identitas Pemohon & Legalitas Lembaga</h2>
                    <p class="text-xs text-slate-500">Data otomatis ditarik dari profil akun terdaftar Anda.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Lengkap PIC</label>
                        <input type="text" x-model="form.pic_name" class="w-full text-sm border-slate-300 rounded-brand bg-slate-50 font-medium" readonly>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nomor WhatsApp PIC</label>
                        <input type="text" x-model="form.pic_phone" class="w-full text-sm border-slate-300 rounded-brand bg-slate-50 font-medium" readonly>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Lembaga / Komunitas</label>
                        <input type="text" x-model="form.institution_name" class="w-full text-sm border-slate-300 rounded-brand bg-slate-50 font-medium" readonly>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Kontak Darurat Lapangan (PIC 2) <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="form.pic_emergency" placeholder="Nomor HP PIC Cadangan" class="w-full text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72]">
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="button" @click="nextStep()" class="inline-flex items-center justify-center px-5 py-2.5 rounded-brand bg-[#001A72] text-white text-sm font-semibold shadow-sm hover:bg-[#001250] transition-colors">
                        Lanjut ke Detail Acara &rarr;
                    </button>
                </div>
            </div>

            <!-- Step 2 -->
            <div x-show="currentStep === 2" class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Langkah 2: Detail Kegiatan & Fasilitas</h2>
                    <p class="text-xs text-slate-500">Jelaskan kegiatan yang akan diadakan di Malang Creative Center.</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Kegiatan / Event <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="form.event_name" placeholder="Contoh: Malang Design Week 2026" class="w-full text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72]">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Kategori Kegiatan <span class="text-rose-500">*</span></label>
                            <select x-model="form.event_category" class="w-full text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72]">
                                <option value="">-- Pilih Kategori --</option>
                                <option value="workshop">Workshop & Pelatihan</option>
                                <option value="seminar">Seminar / Talkshow</option>
                                <option value="pameran">Pameran & Ekshibisi Seni</option>
                                <option value="pertunjukan">Pertunjukan Seni & Musik</option>
                                <option value="rapat">Rapat / Musyawarah</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Estimasi Jumlah Peserta <span class="text-rose-500">*</span></label>
                            <input type="number" x-model="form.estimated_participants" max="450" class="w-full text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Deskripsi Singkat Acara <span class="text-rose-500">*</span></label>
                        <textarea x-model="form.event_description" rows="3" placeholder="Tuliskan tujuan kegiatan dan susunan ringkas acara..." class="w-full text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72]"></textarea>
                    </div>
                </div>

                <div class="pt-4 flex justify-between">
                    <button type="button" @click="prevStep()" class="inline-flex items-center justify-center px-5 py-2.5 rounded-brand border border-slate-300 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">Kembali</button>
                    <button type="button" @click="nextStep()" class="inline-flex items-center justify-center px-5 py-2.5 rounded-brand bg-[#001A72] text-white text-sm font-semibold hover:bg-[#001250] transition-colors">Lanjut ke Dokumen &rarr;</button>
                </div>
            </div>

            <!-- Step 3 -->
            <div x-show="currentStep === 3" class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Langkah 3: Dokumen Legalitas & Komitmen Jaminan KTP</h2>
                    <p class="text-xs text-slate-500">Unggah proposal resmi bertandatangan dan setujui SOP operasional gedung MCC.</p>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Unggah Berkas Proposal Acara (.PDF, Maks. 5 MB) <span class="text-rose-500">*</span></label>
                        <div class="border-2 border-dashed border-slate-300 rounded-brand p-6 text-center hover:border-[#001A72] transition bg-slate-50">
                            <i data-lucide="file-up" class="w-8 h-8 text-[#001A72] mx-auto mb-2"></i>
                            <span class="block text-xs font-semibold text-slate-700">Klik untuk memilih file PDF proposal</span>
                            <input type="file" accept=".pdf" class="mt-3 text-xs text-slate-500 mx-auto">
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-200 rounded-brand p-4 flex items-start gap-3">
                        <input id="tos_ktp" type="checkbox" x-model="form.tos_agreed" class="mt-1 h-4 w-4 rounded-xs border-amber-400 text-[#001A72] focus:ring-[#001A72]">
                        <label for="tos_ktp" class="text-xs text-amber-900 font-medium leading-relaxed">
                            <strong>Pernyataan Jaminan KTP Asli:</strong><br>
                            "Saya menyetujui bahwa penanggung jawab acara (PIC) <strong>wajib menyerahkan FISIK KTP ASLI</strong> di meja Front Office lantai dasar saat check-in hari-H sebagai jaminan kepatuhan fasilitas dan pengembalian ruangan dalam kondisi semula."
                        </label>
                    </div>
                </div>

                <div class="pt-4 flex justify-between">
                    <button type="button" @click="prevStep()" class="inline-flex items-center justify-center px-5 py-2.5 rounded-brand border border-slate-300 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">Kembali</button>
                    <button type="button" :disabled="!form.tos_agreed" @click="nextStep()" class="inline-flex items-center justify-center px-5 py-2.5 rounded-brand bg-[#001A72] text-white text-sm font-semibold hover:bg-[#001250] disabled:opacity-50 disabled:cursor-not-allowed transition-colors">Review & Konfirmasi &rarr;</button>
                </div>
            </div>

            <!-- Step 4 -->
            <div x-show="currentStep === 4" class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Langkah 4: Rekapitulasi & Konfirmasi Pengajuan</h2>
                    <p class="text-xs text-slate-500">Periksa kembali data sebelum mengirimkan permohonan ke tim kurasi MCC.</p>
                </div>

                <div class="bg-slate-50 rounded-brand p-4 space-y-3 text-xs border border-slate-200">
                    <div class="grid grid-cols-3 gap-2">
                        <span class="text-slate-500 font-semibold">Ruangan & Jadwal:</span>
                        <span class="col-span-2 font-bold text-slate-900">Auditorium Utama MCC (Lt. 2)</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="text-slate-500 font-semibold">Nama Acara:</span>
                        <span class="col-span-2 font-bold text-slate-900" x-text="form.event_name || 'Acara Kreatif'"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
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
                    <button type="button" class="inline-flex items-center justify-center px-5 py-2.5 rounded-brand bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-sm transition-colors flex items-center gap-2">
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
</body>
</html>