<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Perbaikan Berkas Permohonan #{{ $booking->booking_code }} - Malang Creative Center (MCC)</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-700 min-h-full flex flex-col antialiased selection:bg-[#001A72] selection:text-white"
      x-data="revisionWizard({
          deadline: '{{ $booking->revision_deadline ? $booking->revision_deadline->toISOString() : now()->addHours(24)->toISOString() }}',
          minCapacity: {{ $minCapacity }},
          form: {
              pic_name: @js($booking->eventDetail->pic_name_snapshot ?? $user->full_name ?? ''),
              pic_phone: @js($booking->eventDetail->pic_phone_snapshot ?? $user->phone_number ?? ''),
              pic_emergency_phone: @js($booking->eventDetail->pic_emergency_phone ?? ''),
              official_letter_number: @js($booking->eventDetail->official_letter_number ?? ''),
              event_name: @js($booking->eventDetail->event_name ?? ''),
              event_category: @js($booking->eventDetail->event_category ?? ''),
              ekraf_subsector: @js($booking->eventDetail->ekraf_subsector ?? ''),
              event_type: @js($booking->eventDetail->event_type ?? 'Terbuka Untuk Umum'),
              estimated_participants: {{ $booking->eventDetail->estimated_participants ?? 1 }},
              event_description: @js($booking->eventDetail->event_description ?? ''),
              equipment_needed: @js($booking->eventDetail->equipment_needed ?? ''),
              notes: '',
              tos_agreed: true
          }
      })">

    <!-- Sticky Grace Period Countdown Banner (REQ-USR-05) -->
    <div class="sticky top-0 z-40 bg-indigo-900 text-white px-4 sm:px-6 lg:px-8 py-3 shadow-md border-b border-indigo-700">
        <div class="max-w-5xl mx-auto flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2.5">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                <span class="text-xs font-semibold text-indigo-100">Masa Tenggang Revisi 24 Jam:</span>
                <span class="font-mono font-bold text-sm tracking-wider text-amber-300 bg-black/30 px-2.5 py-0.5 rounded border border-amber-400/30" 
                      x-text="timeRemainingDisplay">
                    Menghitung...
                </span>
            </div>
            <div class="text-xs text-indigo-200 flex items-center gap-1.5">
                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400 shrink-0"></i>
                <span>Slot jadwal Anda tetap aman terikat selama masa perbaikan</span>
            </div>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="bg-white border-b border-slate-200 py-3.5 px-4 sm:px-6 lg:px-8 sticky top-[49px] z-30 shadow-2xs">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-rose-600 transition-colors cursor-pointer group">
                <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform"></i>
                <span>Batal &amp; Kembali ke Dashboard</span>
            </a>
            
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-700 hidden sm:inline">
                    {{ $booking->eventDetail->event_name ?? 'Perbaikan Permohonan' }}
                </span>
                <span class="text-xs font-mono font-bold text-[#001A72] bg-[#EEF2FF] px-3.5 py-1.5 rounded-full border border-[#C7D2FE]">
                    #{{ $booking->booking_code }}
                </span>
            </div>
        </div>
    </header>

    <main class="max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex-1">
        
        <!-- Curators' Evaluation Notes Banner -->
        <div class="mb-6 p-4 sm:p-4.5 bg-indigo-50/90 border border-indigo-200 rounded-brand space-y-2.5 shadow-xs">
            <div class="flex items-center justify-between gap-2.5 flex-wrap">
                <div class="flex items-center gap-2 font-bold text-xs sm:text-sm text-[#001A72]">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-indigo-600 shrink-0"></i>
                    <span>Catatan Evaluasi Kurator Marketing:</span>
                </div>

                @if($booking->revision_deadline)
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-brand bg-white border border-indigo-200 shadow-2xs text-xs font-bold text-indigo-900">
                        <span class="text-[11px] text-slate-500 font-normal">Sisa Waktu:</span>
                        <span class="font-mono text-indigo-700 font-bold" x-text="timeRemainingDisplay"></span>
                    </div>
                @endif
            </div>

            <div class="bg-white px-3.5 py-2.5 rounded-brand border border-indigo-100 text-xs sm:text-sm text-slate-800 leading-relaxed font-medium whitespace-pre-line shadow-2xs flex items-center min-h-[38px]">
                <div class="w-full">{{ $latestRevisionApproval->notes ?? 'Mohon periksa dan perbaiki rincian formulir serta berkas proposal kegiatan Anda sesuai dengan arahan kurasi.' }}</div>
            </div>

            <div class="flex items-center justify-between text-[11px] text-slate-600 pt-0.5 flex-wrap gap-2">
                <span>Batas tenggat waktu perbaikan: <strong class="text-slate-900">{{ $booking->revision_deadline ? $booking->revision_deadline->translatedFormat('l, d F Y - H:i') . ' WIB' : '24 Jam' }}</strong></span>
                <span class="text-emerald-700 font-bold flex items-center gap-1">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                    Slot jadwal tetap aman terikat di sistem
                </span>
            </div>

            @if($booking->approvals && $booking->approvals->count() > 1)
                <div class="pt-2 border-t border-indigo-200/80" x-data="{ showAllHistory: false }">
                    <button type="button" 
                            @click="showAllHistory = !showAllHistory" 
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-[#001A72] hover:underline cursor-pointer">
                        <i data-lucide="history" class="w-3.5 h-3.5"></i>
                        <span>Lihat Semua Rekam Jejak Kurasi Terdahulu ({{ $booking->approvals->count() }} Kali)</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform duration-200" :class="showAllHistory ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="showAllHistory" class="mt-2.5 space-y-2 pl-3 border-l-2 border-indigo-300">
                        @foreach($booking->approvals->sortBy('created_at') as $approval)
                            <div class="bg-white/90 p-2.5 rounded-brand border border-indigo-100 text-xs space-y-1">
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $approval->decision === 'revised' ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-800' }}">
                                            {{ ucfirst($approval->decision) }}
                                        </span>
                                        <span class="font-bold text-slate-800">{{ $approval->actionBy?->full_name ?? $approval->actionBy?->name ?? 'Kurator Marketing MCC' }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-500">{{ $approval->created_at ? $approval->created_at->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}</span>
                                </div>
                                <p class="text-slate-700 leading-relaxed text-xs">{{ $approval->notes }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

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

        <form action="{{ route('bookings.revise.submit', ['booking' => $booking->id]) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              @submit="isSubmitting = true"
              class="space-y-6">
            @csrf

            <!-- ======================================================================== -->
            <!-- SEKSI 1: RINGKASAN RUANGAN & JADWAL (TERKUNCI & AMAN) -->
            <!-- ======================================================================== -->
            <section class="bg-white rounded-brand border border-slate-200 shadow-card p-6 sm:p-7 space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 flex-wrap gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-brand bg-[#EEF2FF] text-[#001A72] flex items-center justify-center font-bold text-sm shrink-0">
                            <i data-lucide="layers" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Seksi 1: Ringkasan Ruangan &amp; Jadwal</h2>
                            <p class="text-xs text-slate-500">Slot jadwal acara Anda tetap terkunci aman di sistem dan tidak dapat diambil pihak lain.</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full border border-emerald-200 flex items-center gap-1.5">
                        <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                        <span>{{ count($sessions) }} Sesi Terkunci</span>
                    </span>
                </div>

                <!-- Tabel Kompilasi Multi-Ruangan & Multi-Jadwal -->
                <div class="overflow-x-auto border border-slate-200 rounded-brand">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-slate-900 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3">Nama Ruangan &amp; Fasilitas</th>
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
                                        <span class="font-bold text-slate-900 block text-xs">{{ $sess['room']->name ?? 'Ruangan MCC' }}</span>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @if($sess['room'] && $sess['room']->facilities)
                                                @forelse($sess['room']->facilities as $fac)
                                                    <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 text-[10px] rounded font-medium">
                                                        {{ $fac->facility_name }}
                                                    </span>
                                                @empty
                                                    <span class="text-slate-400 text-[10px] italic">Hanya Area</span>
                                                @endforelse
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 font-medium text-slate-700 whitespace-nowrap">
                                        Lantai {{ $sess['room']->floor_level ?? '-' }}
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
                                    <td class="px-4 py-3.5 text-right font-medium whitespace-nowrap">
                                        @if(($sess['room']->base_hourly_rate ?? 0) > 0)
                                            <span class="text-slate-900 font-bold">
                                                 Rp {{ number_format($sess['room']->base_hourly_rate * $sess['duration_hours'], 0, ',', '.') }}
                                            </span>
                                            <span class="block text-[10px] text-slate-400">Komersial</span>
                                        @else
                                            <span class="text-slate-700 font-bold">
                                                Gratis
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
                                <td class="px-4 py-3 text-right text-xs whitespace-nowrap">
                                    @if($totalFee > 0)
                                        <span class="text-sm font-extrabold text-slate-900">
                                            Rp {{ number_format($totalFee, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-slate-800 font-bold">
                                            Gratis
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
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
                        <h2 class="text-base font-bold text-slate-900">Seksi 2: Data Penyelenggara &amp; PIC</h2>
                        <p class="text-xs text-slate-500">Sesuaikan informasi kontak penanggung jawab di lapangan jika diperlukan.</p>
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

                    <!-- Nomor Telepon Darurat (Opsional) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nomor Kontak Darurat Lapangan <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <div class="relative rounded-brand">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="phone-call" class="w-4 h-4"></i>
                            </div>
                            <input type="tel" 
                                   name="pic_emergency_phone" 
                                   x-model="form.pic_emergency_phone" 
                                   placeholder="Nomor telepon alternatif saat acara" 
                                   class="block w-full pl-10 pr-3 py-2.5 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors shadow-2xs">
                        </div>
                    </div>

                    <!-- Nomor Surat Resmi / Pengantar (Opsional) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nomor Surat Permohonan Resmi <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <div class="relative rounded-brand">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                            </div>
                            <input type="text" 
                                   name="official_letter_number" 
                                   x-model="form.official_letter_number" 
                                   placeholder="cth: 005/PAN-HUT/IX/2026" 
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
                        <p class="text-xs text-slate-500">Perbarui parameter teknis acara sesuai arahan evaluasi tim kurator.</p>
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
            <!-- SEKSI 4: BERKAS PENDUKUNG & PROPOSAL PERBAIKAN -->
            <!-- ======================================================================== -->
            <section class="bg-white rounded-brand border border-slate-200 shadow-card p-6 sm:p-7 space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-brand bg-[#EEF2FF] text-[#001A72] flex items-center justify-center font-bold text-sm shrink-0">
                        <i data-lucide="file-up" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Seksi 4: Berkas Proposal Perbaikan &amp; Poster</h2>
                        <p class="text-xs text-slate-500">Unggah berkas PDF proposal yang telah disesuaikan dengan masukan kurator marketing.</p>
                    </div>
                </div>

                <!-- Existing Proposal File Information Card -->
                @if($booking->eventDetail && $booking->eventDetail->proposal_file_url)
                    <div class="p-4 bg-slate-50 rounded-brand border border-slate-200 flex items-center justify-between gap-4 flex-wrap">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 text-[#001A72] flex items-center justify-center shrink-0">
                                <i data-lucide="file-check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="text-[11px] uppercase tracking-wider font-bold text-slate-500 block">Berkas Proposal Saat Ini:</span>
                                <a href="{{ $booking->eventDetail->proposal_file_url }}" target="_blank" class="text-xs font-bold text-[#001A72] hover:underline flex items-center gap-1">
                                    <span>Lihat Proposal Sebelumnya (PDF)</span>
                                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                </a>
                            </div>
                        </div>
                        <span class="text-xs text-slate-500 italic">Pilih berkas baru di bawah untuk mengganti berkas proposal ini.</span>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Proposal Event (Unggah Berkas Baru / Pengganti) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Unggah Proposal Perbaikan (.PDF) <span class="text-slate-400 font-normal">(Maks. 5 MB)</span>
                        </label>
                        <div class="border-2 border-dashed border-slate-300 hover:border-[#001A72] rounded-brand p-6 text-center transition-colors relative bg-slate-50/70 hover:bg-[#EEF2FF]/30 cursor-pointer group">
                            <input type="file" 
                                   name="proposal_file" 
                                   accept=".pdf" 
                                   @change="handleProposalChange($event)" 
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <div class="w-10 h-10 rounded-full bg-white shadow-xs border border-slate-200 text-slate-500 group-hover:text-[#001A72] flex items-center justify-center mx-auto mb-2.5 transition-colors">
                                <i data-lucide="file-up" class="w-5 h-5"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-800 block" x-text="proposalFileName || 'Pilih Berkas Proposal Pengganti (.pdf)'"></span>
                            <span class="text-[11px] text-[#001A72] font-semibold block mt-0.5" x-text="proposalFileSize"></span>
                            <p class="text-[11px] text-slate-500 mt-1">Kosongkan jika Anda hanya memperbaiki data formulir teks.</p>
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

                            <span class="text-xs font-bold text-slate-800 block" x-text="posterFileName || 'Pilih File Gambar Banner/Poster Baru'"></span>
                            <p class="text-[11px] text-slate-500 mt-1">Maksimal ukuran file gambar 2MB (*.jpg, *.jpeg, *.png)</p>
                        </div>
                    </div>
                </div>

                <!-- Catatan Tindak Lanjut dari Pemohon untuk Tim Marketing -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Catatan Tanggapan / Penjelasan Perbaikan untuk Marketing <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea name="notes" 
                              x-model="form.notes" 
                              rows="2" 
                              placeholder="Contoh: Berkas rundown per jam dan surat permohonan resmi pimpinan telah ditambahkan pada halaman 3 proposal..." 
                              class="block w-full p-3 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors shadow-2xs"></textarea>
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
                            Saya menyatakan data perbaikan ini benar dan tetap menyetujui syarat &amp; ketentuan pemakaian gedung MCC serta bersedia menyerahkan fisik KTP asli penanggung jawab acara di meja Front Office saat registrasi hari-H.
                        </span>
                    </label>
                </div>

                <!-- Submit Button Bar (Design System) -->
                <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-t border-slate-100">
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center justify-center px-5 py-2.5 rounded-brand border border-slate-300 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">
                        <span>Batal</span>
                    </a>

                    <button type="submit" 
                            :disabled="!form.tos_agreed || isSubmitting" 
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-brand bg-[#001A72] text-white text-sm font-semibold shadow-sm hover:bg-[#001250] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#001A72] transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                        <template x-if="isSubmitting">
                            <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <template x-if="!isSubmitting">
                            <i data-lucide="send" class="w-4 h-4 text-emerald-400"></i>
                        </template>
                        <span x-text="isSubmitting ? 'Mengirim Perbaikan...' : 'Kirim Perbaikan Permohonan'"></span>
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
            <h3 class="text-lg font-bold text-slate-900">Masa Tenggang Revisi Telah Berakhir</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Batas waktu tenggang revisi 24 jam untuk permohonan ini telah habis. Permohonan otomatis dibatalkan dan slot jadwal telah dibuka kembali untuk publik.
            </p>
            <a href="{{ route('dashboard') }}" class="block w-full py-2.5 bg-[#001A72] hover:bg-[#001250] text-white text-xs font-bold rounded-brand transition-colors">
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-6 text-center text-xs text-slate-400 border-t border-slate-200 mt-auto bg-white">
        &copy; {{ date('Y') }} Malang Creative Center (MCC). Reservasi Ruangan Resmi Terpadu.
    </footer>

    <!-- Alpine.js Revision Wizard Controller -->
    <script>
        function revisionWizard(config) {
            return {
                deadlineTime: new Date(config.deadline).getTime(),
                timeRemainingDisplay: 'Menghitung...',
                timeExpiredModal: false,
                timerInterval: null,
                isSubmitting: false,
                minCapacity: config.minCapacity,
                form: config.form,

                proposalFileName: '',
                proposalFileSize: '',
                posterFileName: '',
                posterPreviewUrl: '',

                init() {
                    this.updateTimer();
                    this.timerInterval = setInterval(() => {
                        this.updateTimer();
                    }, 1000);

                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });
                },

                updateTimer() {
                    const now = new Date().getTime();
                    const distance = this.deadlineTime - now;

                    if (distance <= 0) {
                        this.timeRemainingDisplay = '00:00:00 (Kedaluwarsa)';
                        this.timeExpiredModal = true;
                        if (this.timerInterval) clearInterval(this.timerInterval);
                        return;
                    }

                    const hours = Math.floor(distance / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    const pad = (n) => String(n).padStart(2, '0');
                    this.timeRemainingDisplay = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
                },

                handleProposalChange(e) {
                    const file = e.target.files[0];
                    if (file) {
                        if (file.size > 5 * 1024 * 1024) {
                            alert('Ukuran file proposal perbaikan melebihi batas maksimal 5 MB.');
                            e.target.value = '';
                            this.proposalFileName = '';
                            this.proposalFileSize = '';
                            return;
                        }
                        if (file.type !== 'application/pdf') {
                            alert('Format berkas proposal perbaikan wajib berupa dokumen PDF (*.pdf).');
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
                }
            };
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>
