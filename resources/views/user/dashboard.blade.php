<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemohon - Malang Creative Center (MCC)</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-[#001A72] selection:text-white">

    <!-- Navbar -->
    @include('layouts.partials.navbar')

    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-brand flex items-start gap-3 text-xs leading-relaxed shadow-xs"
                 x-data="{ show: true }" x-show="show">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                <div class="flex-1 font-semibold">{{ session('success') }}</div>
                <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700">
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
                    <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-white/10 text-amber-300 text-xs font-bold mb-1.5 border border-white/20">
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
                        <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider block">Menunggu Kurasi</span>
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
                    <p class="text-xs text-slate-500">Live Status Tracking & E-Pass</p>
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
                        <div class="p-5 hover:bg-slate-50/70 transition-colors">
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs font-bold text-slate-500">#{{ $booking->booking_code }}</span>
                                        <x-badge.status :status="$booking->status">{{ $booking->status instanceof \App\Enums\BookingStatus ? $booking->status->label() : ucfirst((string)$booking->status) }}</x-badge.status>
                                    </div>
                                    <h3 class="font-bold text-slate-900 text-sm">
                                        Permohonan Reservasi Gedung MCC
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 font-medium">
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                                            Dibuat: {{ $booking->created_at->translatedFormat('d F Y, H:i') }} WIB
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    @php
                                        $statusKey = $booking->status instanceof \BackedEnum ? $booking->status->value : (string) $booking->status;
                                    @endphp
                                    @if(in_array($statusKey, ['confirmed', 'checked_in', 'completed']))
                                        <button class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-brand text-xs font-semibold shadow-xs transition-colors">
                                            <i data-lucide="qr-code" class="w-4 h-4"></i>
                                            <span>Buka E-Pass Digital</span>
                                        </button>
                                    @elseif($statusKey === 'revised')
                                        <button class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-brand text-xs font-semibold shadow-xs transition-colors">
                                            <i data-lucide="upload" class="w-4 h-4"></i>
                                            <span>Unggah Revisi</span>
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Dalam proses antrean kurasi</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    @include('layouts.partials.footer')

</body>
</html>
