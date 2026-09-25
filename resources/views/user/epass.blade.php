<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Pass Resmi - {{ $booking->booking_code }} - Malang Creative Center (MCC)</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }

        @media print {
            body { background: #ffffff !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .print-ticket {
                box-shadow: none !important;
                border: 2px solid #001A72 !important;
                margin: 0 auto !important;
                max-width: 100% !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 min-h-full flex flex-col antialiased selection:bg-[#001A72] selection:text-white p-4 sm:p-6 lg:p-8">

    <!-- Top Action Bar (Screen Only) -->
    <div class="max-w-3xl mx-auto w-full mb-6 no-print flex items-center justify-between gap-4 flex-wrap">
        <a href="{{ route('dashboard') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-brand bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors shadow-2xs">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Dashboard</span>
        </a>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('bookings.epass.download', ['booking' => $booking->id]) }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-brand bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md hover:shadow-lg transition-all cursor-pointer">
                <i data-lucide="download" class="w-4 h-4 text-emerald-100"></i>
                <span>Unduh E-Pass (PDF)</span>
            </a>
        </div>
    </div>

    <!-- Main Digital E-Pass Ticket (Printable & Interactive) -->
    <div class="max-w-3xl mx-auto w-full bg-white rounded-brand border-2 border-slate-200 shadow-xl overflow-hidden print-ticket my-auto">
        
        <!-- Ticket Header (MCC Deep Navy Brand) -->
        <div class="bg-[#001A72] text-white p-6 sm:p-7 relative overflow-hidden">
            <!-- Background Watermark Pattern -->
            <div class="absolute right-0 top-0 translate-x-1/4 -translate-y-1/4 w-64 h-64 bg-white/5 rounded-full pointer-events-none"></div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-brand bg-white p-1.5 flex items-center justify-center shadow-xs shrink-0">
                        <img src="{{ asset('images/logo/mcc-logo.png') }}" 
                             alt="Logo MCC" 
                             class="w-full h-full object-contain"
                             onerror="this.onerror=null; this.src=''; this.parentElement.innerHTML='<span class=\'font-black text-xs text-[#001A72]\'>MCC</span>';">
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-indigo-200 block">
                            Pemerintah Kota Malang • Dinas Koperasi, Perindustrian & Perdagangan
                        </span>
                        <h1 class="text-base sm:text-lg font-extrabold tracking-tight text-white leading-snug">
                            E-PASS AKSES RESMI GEDUNG MCC
                        </h1>
                        <p class="text-xs text-slate-200">Malang Creative Center - Tiket Masuk & Validasi Kedatangan</p>
                    </div>
                </div>

                <div class="flex sm:flex-col items-end justify-between sm:justify-center shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-emerald-500 text-white shadow-xs">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                        <span>STATUS: DISETUJUI</span>
                    </span>
                    <span class="text-[10px] text-indigo-200 mt-1 font-mono">
                        ID: {{ substr($booking->id, 0, 8) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Ticket Body -->
        <div class="p-6 sm:p-8 space-y-6">

            <!-- Section: QR Code & Booking Code Header -->
            <div class="bg-indigo-50/60 border border-indigo-100 rounded-brand p-5 sm:p-6 flex flex-col md:flex-row items-center gap-6 justify-between">
                <!-- QR Code Dynamic Container -->
                <div class="flex flex-col items-center text-center shrink-0">
                    <div class="p-3 bg-white rounded-brand border-2 border-[#001A72]/20 shadow-md flex items-center justify-center">
                        <div class="w-44 h-44 sm:w-48 sm:h-48 flex items-center justify-center">
                            {!! $qrCodeSvg !!}
                        </div>
                    </div>
                    <span class="text-[11px] text-[#001A72] font-bold mt-2 flex items-center gap-1">
                        <i data-lucide="scan" class="w-3.5 h-3.5"></i>
                        <span>Scan di Meja Front Office</span>
                    </span>
                </div>

                <!-- Booking Meta Details & Copy -->
                <div class="flex-1 space-y-3.5 text-left w-full" x-data="{ copied: false }">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Kode Unik Reservasi:</span>
                        <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                            <span class="font-mono text-xl sm:text-2xl font-black text-[#001A72] tracking-wider">
                                #{{ $booking->booking_code }}
                            </span>
                            <button type="button" 
                                    @click="navigator.clipboard.writeText('{{ $booking->booking_code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="no-print inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 rounded text-xs font-semibold shadow-2xs transition cursor-pointer">
                                <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Nama Kegiatan:</span>
                            <span class="font-extrabold text-slate-900 block text-sm mt-0.5">
                                {{ $booking->eventDetail->event_name ?? 'Kegiatan Komunitas MCC' }}
                            </span>
                            <span class="text-[11px] text-slate-500 block">
                                Sub-sektor: <strong>{{ $booking->eventDetail->ekraf_subsector ?? 'Ekonomi Kreatif' }}</strong>
                            </span>
                        </div>

                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Penyelenggara / Institusi:</span>
                            <span class="font-bold text-slate-800 block mt-0.5">
                                {{ $booking->eventDetail->institution_name_snapshot ?? $booking->user->institution_name ?? $booking->user->full_name }}
                            </span>
                            <span class="text-[11px] text-slate-500 block">
                                Sifat: <strong>{{ $booking->eventDetail->event_type ?? 'Terbuka Untuk Umum' }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-indigo-100 flex items-center justify-between text-xs text-slate-600 flex-wrap gap-2">
                        <span>Skema Pembiayaan: 
                            <strong class="text-emerald-700">
                                {{ $booking->pricing_scheme === 'commercial' ? 'Komersial (Rp ' . number_format($booking->nominal_amount, 0, ',', '.') . ')' : 'Subsidi Penuh Pemkot Malang (Gratis)' }}
                            </strong>
                        </span>
                        <span class="text-[11px] text-slate-500">
                            Peserta: <strong>{{ $booking->eventDetail->estimated_participants ?? 1 }} Orang</strong>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Section: Schedule & Room Multi-Session Table -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-4 h-4 text-[#001A72]"></i>
                        <span>Rincian Ruangan &amp; Jadwal Penggunaan:</span>
                    </h2>
                    <span class="text-xs font-semibold text-slate-500">
                        Total: {{ $scheduleData['total_hours'] }} Jam ({{ count($scheduleData['sessions']) }} Sesi)
                    </span>
                </div>

                <div class="overflow-x-auto border border-slate-200 rounded-brand">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-slate-900 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-2.5 text-center w-12">No</th>
                                <th class="px-4 py-2.5">Nama Ruangan</th>
                                <th class="px-4 py-2.5">Lantai</th>
                                <th class="px-4 py-2.5">Tanggal Pelaksanaan</th>
                                <th class="px-4 py-2.5">Waktu Sesi</th>
                                <th class="px-4 py-2.5 text-center">Durasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($scheduleData['sessions'] as $idx => $sess)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-2.5 text-center font-semibold text-slate-500">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-2.5 font-bold text-slate-900">{{ $sess['room_name'] }}</td>
                                    <td class="px-4 py-2.5">Lantai {{ $sess['floor_level'] }}</td>
                                    <td class="px-4 py-2.5 text-slate-800">{{ $sess['date_formatted'] }}</td>
                                    <td class="px-4 py-2.5 font-mono font-bold text-[#001A72]">{{ $sess['time_range'] }}</td>
                                    <td class="px-4 py-2.5 text-center">{{ $sess['duration_hours'] }} Jam</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Section: PIC Contact Information -->
            <div class="p-4 bg-slate-50 rounded-brand border border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Penanggung Jawab (PIC Lapangan):</span>
                    <span class="font-extrabold text-slate-900 block text-sm mt-0.5">
                        {{ $booking->eventDetail->pic_name_snapshot ?? $booking->user->full_name }}
                    </span>
                    <span class="text-slate-600 block text-xs mt-0.5">
                        Nomor HP: <strong>{{ $booking->eventDetail->pic_phone_snapshot ?? $booking->user->phone_number }}</strong>
                    </span>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Kontak Tambahan / Darurat:</span>
                    <span class="text-slate-700 block text-xs mt-0.5">
                        No. Darurat: <strong>{{ $booking->eventDetail->pic_emergency_phone ?: '-' }}</strong>
                    </span>
                    <span class="text-slate-700 block text-xs">
                        No. Surat Resmi: <strong>{{ $booking->eventDetail->official_letter_number ?: '-' }}</strong>
                    </span>
                </div>
            </div>

            <!-- ======================================================================== -->
            <!-- MANDATORY CHECK-IN INSTRUCTION (EXACT TEXT REQ-USR-06 POIN 2) -->
            <!-- ======================================================================== -->
            <div class="p-4 sm:p-5 bg-amber-50 border-2 border-amber-300 rounded-brand text-amber-950 shadow-xs space-y-1.5">
                <div class="flex items-center gap-2 font-bold text-xs sm:text-sm text-amber-900">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600 shrink-0"></i>
                    <span>Instruksi Wajib Check-In Hari-H:</span>
                </div>
                <p class="text-xs sm:text-sm leading-relaxed font-semibold text-amber-950">
                    "Harap datang 30 menit sebelum acara dimulai dan titipkan fisik KTP asli penanggung jawab acara di meja Front Office Lantai Dasar untuk ditukar dengan akses ruangan."
                </p>
            </div>

        </div>

        <!-- Ticket Footer (Security & Verification Signature) -->
        <div class="bg-slate-50 border-t border-slate-200 p-4 sm:p-5 text-center text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2 text-left">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                </div>
                <div class="text-[11px]">
                    <span class="font-bold text-slate-800 block">Dokumen Resmi Digital Disahkan Sistem MCC</span>
                    <span>Tervalidasi oleh Tim Marketing MCC</span>
                </div>
            </div>

            <div class="text-[11px] text-slate-400 font-mono text-right">
                <span>Diterbitkan: {{ now()->translatedFormat('d M Y - H:i') }} WIB</span>
            </div>
        </div>
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
