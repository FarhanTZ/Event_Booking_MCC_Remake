<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemohon - Malang Creative Center (MCC)</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-[#001A72] selection:text-white"
      x-data="userDashboard()">

    <!-- Navbar -->
    @include('layouts.partials.navbar')

    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">

        <!-- Flash Messages Notification (Success) -->
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-brand flex items-start gap-3 text-xs leading-relaxed shadow-xs"
                 x-data="{ show: true }" x-show="show">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                <div class="flex-1 font-semibold">{{ session('success') }}</div>
                <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        <!-- Flash Messages Notification (Error) -->
        @if(session('error') || $errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-900 p-4 rounded-brand flex items-start gap-3 text-xs leading-relaxed shadow-xs"
                 x-data="{ show: true }" x-show="show">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
                <div class="flex-1">
                    @if(session('error'))
                        <div class="font-bold">{{ session('error') }}</div>
                    @endif
                    @if($errors->any())
                        <ul class="list-disc pl-4 space-y-0.5 mt-1 font-medium">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <button type="button" @click="show = false" class="text-rose-500 hover:text-rose-700 cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        <!-- Solid Corporate Hero / Welcome Banner -->
        <div class="bg-[#001A72] rounded-brand p-6 sm:p-7 text-white shadow-card mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
            <div class="flex items-start gap-4">
                @if($user->logo_url)
                    <img src="{{ $user->getLogoAssetUrl() }}" 
                         alt="{{ $user->institution_name }}" 
                         class="w-14 h-14 rounded-brand object-cover bg-white p-1 border border-white/20 shadow-sm shrink-0">
                @else
                    <div class="w-14 h-14 rounded-brand bg-white/10 flex items-center justify-center border border-white/20 shrink-0">
                        <i data-lucide="building-2" class="w-7 h-7 text-indigo-200"></i>
                    </div>
                @endif
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-white/10 text-white text-xs font-bold mb-1.5 border border-white/20">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> 
                        <span>{{ $user->account_type->label() }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight">
                        Portal Pemohon: {{ $user->institution_name }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-200 mt-0.5">
                        PIC: <strong class="text-white">{{ $user->full_name }}</strong> ({{ $user->email }})
                    </p>
                </div>
            </div>

            <a href="/jadwal" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-brand bg-white text-[#001A72] font-bold text-xs hover:bg-slate-100 shadow-md transition-all hover:shadow-lg hover:-translate-y-0.5 shrink-0">
                <i data-lucide="calendar-plus" class="w-4 h-4 text-[#001A72]"></i>
                <span>Ajukan Booking Baru</span>
            </a>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <x-card.base>
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-brand bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider block">Menunggu Kurasi / Bayar</span>
                        <span class="text-lg font-extrabold text-slate-900">{{ $metrics['awaiting'] }} Pengajuan</span>
                    </div>
                </div>
            </x-card.base>

            <x-card.base>
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-brand bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider block">Perlu Revisi (24h)</span>
                        <span class="text-lg font-extrabold text-indigo-700">{{ $metrics['revised'] }} Pengajuan</span>
                    </div>
                </div>
            </x-card.base>

            <x-card.base>
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-brand bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider block">Disetujui (E-Pass)</span>
                        <span class="text-lg font-extrabold text-emerald-700">{{ $metrics['confirmed'] }} Acara</span>
                    </div>
                </div>
            </x-card.base>

            <x-card.base>
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-brand bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
                        <i data-lucide="archive" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider block">Total Riwayat</span>
                        <span class="text-lg font-extrabold text-slate-900">{{ $metrics['total'] }} Acara</span>
                    </div>
                </div>
            </x-card.base>
        </div>

        <!-- Section Permohonan Ruangan -->
        <div class="bg-white rounded-brand border border-slate-200 shadow-card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h2 class="font-bold text-sm text-slate-900">Daftar Permohonan Ruangan Anda</h2>
                    <p class="text-xs text-slate-500">Live Status Tracking, Revisi 24 Jam &amp; E-Pass</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600">
                    {{ $bookings->count() }} Permohonan
                </span>
            </div>

            @if($bookings->isEmpty())
                <!-- Clean Empty State -->
                <div class="py-16 px-6 text-center">
                    <div class="w-16 h-16 rounded-full bg-[#EEF2FF] text-[#001A72] mx-auto flex items-center justify-center mb-4">
                        <i data-lucide="calendar-x" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-1">Belum Ada Permohonan Ruangan</h3>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mb-6">
                        Akun Anda telah aktif dan siap digunakan. Silakan pilih ruangan dan jadwal acara yang diinginkan untuk memulai reservasi.
                    </p>
                    <a href="/jadwal" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white text-xs font-bold shadow-md hover:shadow-lg transition-all">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        <span>Cek Ketersediaan Slot Jadwal</span>
                    </a>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($bookings as $booking)
                        @php
                            $statusKey = $booking->status instanceof \BackedEnum ? $booking->status->value : (string) $booking->status;
                            $latestRevisionApproval = $booking->approvals ? $booking->approvals->where('decision', 'revised')->last() : null;
                            
                            $modalData = [
                                'id' => $booking->id,
                                'code' => $booking->booking_code,
                                'event_name' => $booking->eventDetail->event_name ?? 'Reservasi Gedung MCC',
                                'revision_notes' => $latestRevisionApproval->notes ?? 'Mohon perbaiki berkas proposal sesuai ketentuan.',
                                'revision_deadline' => $booking->revision_deadline ? $booking->revision_deadline->toISOString() : null,
                                'deadline_formatted' => $booking->revision_deadline ? $booking->revision_deadline->translatedFormat('d F Y, H:i') . ' WIB' : null,
                            ];
                        @endphp
                        <div class="p-6 hover:bg-slate-50/70 transition-colors {{ $statusKey === 'revised' ? 'bg-indigo-50/20' : '' }}">
                            <!-- Top Header: Booking Info & Action Button -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1.5 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-mono text-xs font-extrabold text-[#001A72]">#{{ $booking->booking_code }}</span>
                                        <x-badge.status :status="$booking->status">{{ $booking->status instanceof \App\Enums\BookingStatus ? $booking->status->label() : ucfirst((string)$booking->status) }}</x-badge.status>
                                        
                                        @if($booking->pricing_scheme === 'commercial')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">Komersial</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Subsidi Pemkot</span>
                                        @endif
                                    </div>

                                    <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">
                                        {{ $booking->eventDetail->event_name ?? 'Permohonan Reservasi Gedung MCC' }}
                                    </h3>

                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 font-medium">
                                        <span class="flex items-center gap-1.5">
                                            <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                                            Diajukan: {{ $booking->created_at->translatedFormat('d F Y, H:i') }} WIB
                                        </span>
                                        @if($booking->slots->isNotEmpty())
                                            <span class="flex items-center gap-1.5">
                                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                                                {{ $booking->slots->first()->room->name ?? 'MCC' }} ({{ $booking->slots->count() }} Sesi)
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Right Actions Section -->
                                <div class="flex items-center gap-2 shrink-0 self-start sm:self-center">
                                    @if(in_array($statusKey, ['confirmed', 'checked_in', 'completed']))
                                        <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-brand text-xs font-semibold shadow-xs transition-colors cursor-pointer">
                                            <i data-lucide="qr-code" class="w-4 h-4"></i>
                                            <span>Buka E-Pass Digital</span>
                                        </button>
                                    @elseif($statusKey === 'revised')
                                        <a href="{{ route('bookings.revise.show', ['booking' => $booking->id]) }}"
                                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-brand text-xs font-bold shadow-xs transition-colors cursor-pointer">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            <span>Perbaiki Permohonan</span>
                                        </a>
                                    @elseif($statusKey === 'awaiting_payment')
                                        <span class="text-xs text-amber-700 font-semibold px-3 py-2 bg-amber-50 rounded-brand border border-amber-200 inline-flex items-center gap-1.5">
                                            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                            Menunggu Verifikasi Bayar
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 italic px-2">Dalam antrean kurasi</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Full Width: Catatan Revisi & Countdown Timer (Khusus status revised - REQ-USR-05) -->
                            @if($statusKey === 'revised')
                                <div class="mt-3.5 p-4 sm:p-4.5 bg-indigo-50/90 border border-indigo-200 rounded-brand space-y-2.5 w-full">
                                    <div class="flex items-center justify-between gap-2.5 flex-wrap">
                                        <div class="flex items-center gap-2 font-bold text-xs sm:text-sm text-[#001A72]">
                                            <i data-lucide="alert-circle" class="w-4 h-4 text-indigo-600 shrink-0"></i>
                                            <span>Catatan Evaluasi Kurator Marketing:</span>
                                        </div>

                                        <!-- Real-time Countdown Timer (24h Grace Period) -->
                                        @if($booking->revision_deadline)
                                            <div x-data="countdownTimer('{{ $booking->revision_deadline->toISOString() }}')" 
                                                 class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-brand bg-white border border-indigo-200 shadow-2xs text-xs font-bold"
                                                 :class="isExpired ? 'text-rose-700 border-rose-200 bg-rose-50' : 'text-indigo-900'">
                                                <span class="text-[11px] text-slate-500 font-normal">Sisa Waktu:</span>
                                                <span class="font-mono text-indigo-700 font-bold" x-text="timeRemaining"></span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="bg-white px-3.5 py-2.5 rounded-brand border border-indigo-100 text-xs sm:text-sm text-slate-800 leading-relaxed font-medium whitespace-pre-line shadow-2xs flex items-center min-h-[38px]">
                                        <div class="w-full">{{ $latestRevisionApproval->notes ?? 'Mohon perbarui dan sesuaikan berkas proposal serta data permohonan sesuai arahan kurator.' }}</div>
                                    </div>

                                    <div class="flex items-center justify-between text-[11px] text-slate-600 pt-0.5 flex-wrap gap-2">
                                        <span>Batas tenggat waktu perbaikan: <strong class="text-slate-900">{{ $booking->revision_deadline ? $booking->revision_deadline->translatedFormat('l, d F Y - H:i') . ' WIB' : '24 Jam' }}</strong></span>
                                        <span class="text-emerald-700 font-bold flex items-center gap-1">
                                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                                            Slot jadwal tetap aman terikat di sistem
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <!-- Full Width: Instruksi Pembayaran (Khusus status awaiting_payment) -->
                            @if($statusKey === 'awaiting_payment')
                                <div class="mt-3.5 p-4 sm:p-4.5 bg-amber-50 border border-amber-200 rounded-brand space-y-2.5 text-xs text-amber-950 w-full">
                                    <div class="flex items-center justify-between gap-2.5 flex-wrap">
                                        <div class="flex items-center gap-2 font-bold text-xs sm:text-sm text-amber-900">
                                            <i data-lucide="receipt" class="w-4 h-4 text-amber-600 shrink-0"></i>
                                            <span>Instruksi Pembayaran Retribusi Daerah:</span>
                                        </div>
                                        <span class="font-extrabold font-mono text-xs px-2.5 py-0.5 rounded-brand bg-white text-amber-900 border border-amber-300 shadow-2xs">
                                            Rp {{ number_format($booking->nominal_amount, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="bg-white px-3.5 py-2.5 rounded-brand border border-amber-100 text-xs sm:text-sm text-amber-950 leading-relaxed font-medium whitespace-pre-line shadow-2xs flex items-center min-h-[38px]">
                                        <div class="w-full">{{ $booking->payment_notes ?? 'Silakan lakukan pembayaran sesuai instruksi marketing MCC.' }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- ==================================================================== -->
    <!-- MODAL UNGGAH BERKAS PROPOSAL REVISI (REQ-USR-05) -->
    <!-- ==================================================================== -->
    <div x-show="showRevisionModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="revision-modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop -->
        <div x-show="showRevisionModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="closeRevisionModal()" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

        <!-- Centered Modal Wrapper -->
        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6 relative z-10 pointer-events-none">
            <div x-show="showRevisionModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.stop
                 class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-lg border border-slate-200 pointer-events-auto my-8">
                
                <!-- Modal Header -->
                <div class="bg-[#001A72] text-white px-6 py-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-indigo-300 shrink-0">
                            <i data-lucide="upload" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 id="revision-modal-title" class="text-sm font-bold text-white tracking-tight leading-snug">
                                Unggah Berkas Proposal Perbaikan
                            </h3>
                            <p class="text-[11px] text-slate-200 font-mono" x-text="activeBooking.code"></p>
                        </div>
                    </div>

                    <button @click="closeRevisionModal()" class="p-1.5 rounded-brand text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer shrink-0" aria-label="Tutup Modal">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Form Revision -->
                <form :action="'/my-bookings/' + activeBooking.id + '/revise'" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      @submit="isSubmitting = true" 
                      class="p-6 space-y-5">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <!-- Catatan Evaluasi Reminder Card -->
                    <div class="p-3.5 bg-indigo-50 border border-indigo-200 rounded-brand text-xs text-indigo-950 space-y-1">
                        <span class="font-bold text-indigo-900 block">Poin Perbaikan dari Kurator Marketing:</span>
                        <p class="text-indigo-900 text-[11px] leading-relaxed font-medium whitespace-pre-line" x-text="activeBooking.revision_notes"></p>
                    </div>

                    <!-- File Upload Input Area -->
                    <div class="space-y-1.5">
                        <label for="revision_proposal_file" class="block text-xs font-bold uppercase tracking-wider text-slate-800">
                            Berkas Proposal Pengganti (.PDF) <span class="text-rose-500">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-brand hover:border-[#001A72] transition-colors bg-slate-50/50">
                            <div class="space-y-1 text-center">
                                <i data-lucide="file-text" class="mx-auto h-10 w-10 text-slate-400"></i>
                                <div class="flex text-xs text-slate-600 justify-center">
                                    <label for="revision_proposal_file" class="relative cursor-pointer rounded-brand bg-transparent font-bold text-[#001A72] hover:underline focus-within:outline-none">
                                        <span>Pilih berkas PDF baru</span>
                                        <input id="revision_proposal_file" 
                                               name="proposal_file" 
                                               type="file" 
                                               accept=".pdf" 
                                               required 
                                               @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                               class="sr-only">
                                    </label>
                                </div>
                                <p class="text-[11px] text-slate-500 font-medium">Format Dokumen PDF asli (Maks. 5 MB)</p>
                                <template x-if="fileName">
                                    <div class="pt-2 text-xs font-bold text-emerald-700 flex items-center justify-center gap-1.5">
                                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                                        <span x-text="fileName"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Optional: Applicant Notes -->
                    <div class="space-y-1">
                        <label for="revision_notes_input" class="block text-xs font-bold uppercase tracking-wider text-slate-800">
                            Catatan untuk Tim Marketing (Opsional)
                        </label>
                        <textarea id="revision_notes_input" 
                                  name="notes" 
                                  rows="2" 
                                  placeholder="Contoh: Telah ditambahkan lampiran rundown terperinci dan surat izin resmi pimpinan."
                                  class="w-full p-2.5 text-xs rounded-brand border border-slate-300 focus:border-[#001A72] focus:ring-1 focus:ring-[#001A72] outline-none leading-relaxed"></textarea>
                    </div>

                    <!-- Information Alert -->
                    <div class="p-3 rounded-brand bg-slate-50 border border-slate-200 text-[11px] text-slate-600 flex items-start gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-[#001A72] shrink-0 mt-0.5"></i>
                        <span>Setelah diunggah, status permohonan akan kembali ke antrean kurasi marketing dan slot jadwal acara tetap aman terikat.</span>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="border-t border-slate-200 pt-4 flex items-center justify-end gap-2.5">
                        <button type="button" 
                                @click="closeRevisionModal()" 
                                class="min-h-[44px] px-4 py-2 rounded-brand border border-slate-300 bg-white text-slate-700 text-xs font-bold hover:bg-slate-100 transition cursor-pointer shadow-2xs">
                            Batal
                        </button>

                        <button type="submit" 
                                :disabled="isSubmitting"
                                class="min-h-[44px] inline-flex items-center gap-2 px-5 py-2 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white text-xs font-bold transition shadow-xs cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                            <template x-if="isSubmitting">
                                <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </template>
                            <template x-if="!isSubmitting">
                                <i data-lucide="send" class="w-4 h-4 text-emerald-400"></i>
                            </template>
                            <span>Kirim Berkas Perbaikan</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Footer -->
    @include('layouts.partials.footer')

    <!-- Alpine.js Store & Countdown Timer Engine -->
    <script>
        function userDashboard() {
            return {
                showRevisionModal: false,
                isSubmitting: false,
                fileName: '',
                activeBooking: {
                    id: '',
                    code: '',
                    event_name: '',
                    revision_notes: '',
                    revision_deadline: null,
                    deadline_formatted: null,
                },

                openRevisionModal(booking) {
                    this.activeBooking = booking;
                    this.fileName = '';
                    this.isSubmitting = false;
                    this.showRevisionModal = true;
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });
                },

                closeRevisionModal() {
                    this.showRevisionModal = false;
                    this.fileName = '';
                }
            };
        }

        function countdownTimer(deadlineStr) {
            return {
                deadline: new Date(deadlineStr).getTime(),
                timeRemaining: 'Menghitung...',
                isExpired: false,
                interval: null,

                init() {
                    this.updateCountdown();
                    this.interval = setInterval(() => {
                        this.updateCountdown();
                    }, 1000);
                },

                updateCountdown() {
                    const now = new Date().getTime();
                    const distance = this.deadline - now;

                    if (distance <= 0) {
                        this.timeRemaining = 'Waktu Habis (Kedaluwarsa)';
                        this.isExpired = true;
                        if (this.interval) clearInterval(this.interval);
                        return;
                    }

                    const hours = Math.floor(distance / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    const pad = (n) => String(n).padStart(2, '0');
                    this.timeRemaining = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
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
