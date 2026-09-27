<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale() ?? 'id') }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() ?? '' }}">

    <title>Buku Inventaris KTP Tertinggal - Front Office MCC</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html, body {
            overflow-x: hidden !important;
            max-width: 100vw;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        [x-cloak] { display: none !important; }

        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #94A3B8 #F1F5F9;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #F1F5F9;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #94A3B8;
            border-radius: 9999px;
            border: 1px solid #F1F5F9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #64748B;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-[#001A72] selection:text-white"
      x-data="unclaimedKtpInventory()">

    <!-- Flash Messages Notification -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
             class="bg-emerald-600 text-white px-4 py-3 shadow-lg fixed top-6 right-6 z-50 rounded-brand flex items-center gap-3 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span class="text-xs sm:text-sm font-semibold">{{ session('success') }}</span>
            <button @click="show = false" class="ml-2 hover:opacity-80 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
    @endif

    <div class="flex-1 w-full max-w-[1720px] 2xl:max-w-[1920px] mx-auto p-3 sm:p-4 lg:p-5 flex flex-col lg:flex-row gap-4 lg:gap-5 items-start min-h-screen">
        
        <!-- 1. Sidebar Navigasi Front Office -->
        @include('front_office.partials.sidebar-left', ['metrics' => $manifestMetrics ?? []])

        <!-- 2. Workspace Utama: Buku Inventaris KTP Tertinggal -->
        <main class="flex-1 min-w-0 w-full space-y-4">
            
            <!-- Top Control Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 sm:p-5 rounded-brand border border-slate-200 shadow-card">
                <div class="flex items-center gap-3">
                    <button type="button"
                            @click="sidebarLeft = !sidebarLeft"
                            :class="sidebarLeft ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-[#001A72] text-white hover:bg-[#001250]'"
                            class="w-9 h-9 inline-flex items-center justify-center rounded-brand transition cursor-pointer shrink-0 shadow-2xs"
                            title="Buka / Tutup Menu Navigasi Kiri">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/></svg>
                    </button>

                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight leading-tight">
                                Buku Inventaris KTP Tertinggal (Unclaimed ID Log)
                            </h1>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                                FR-FO-09
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Melacak seluruh fisik identitas KTP yang masih tertahan di laci operasional atau kotak arsip Front Office.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('front_office.manifest') }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-brand bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition shadow-2xs border border-slate-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        <span>Kembali ke Manifes Hari Ini</span>
                    </a>
                </div>
            </div>

            <!-- KPI Metric Cards Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <!-- 1. Total KTP Tertahan -->
                <div class="bg-white p-4 rounded-brand border border-slate-200 shadow-card flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-brand bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 block">Total KTP Tertahan</span>
                        <span class="text-xl font-black text-slate-900">{{ $metrics['total_retained_ktp'] }} Kartu</span>
                    </div>
                </div>

                <!-- 2. Di Kotak Arsip Tertinggal -->
                <div class="bg-white p-4 rounded-brand border border-slate-200 shadow-card flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-brand bg-amber-50 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 block">Di Kotak Arsip</span>
                        <span class="text-xl font-black text-amber-800">{{ $metrics['in_unclaimed_box'] }} Kartu</span>
                    </div>
                </div>

                <!-- 3. Di Laci Operasional -->
                <div class="bg-white p-4 rounded-brand border border-slate-200 shadow-card flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-brand bg-indigo-50 text-indigo-700 flex items-center justify-center shrink-0 border border-indigo-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="8" x="2" y="3" rx="1"/><rect width="20" height="8" x="2" y="13" rx="1"/><line x1="10" x2="14" y1="7" y2="7"/><line x1="10" x2="14" y1="17" y2="17"/></svg>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 block">Di Laci Operasional</span>
                        <span class="text-xl font-black text-[#001A72]">{{ $metrics['in_drawer'] }} Kartu</span>
                    </div>
                </div>

                <!-- 4. Ada Insiden Ruangan -->
                <div class="bg-white p-4 rounded-brand border border-slate-200 shadow-card flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-brand bg-rose-50 text-rose-700 flex items-center justify-center shrink-0 border border-rose-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 block">Berkas Insiden</span>
                        <span class="text-xl font-black text-rose-700">{{ $metrics['with_incidents'] }} Kartu</span>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="bg-white p-4 rounded-brand border border-slate-200 shadow-card space-y-3">
                <form method="GET" action="{{ route('front_office.unclaimed_ktp') }}" class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative flex-1 w-full">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </div>
                        <input type="text" 
                               name="q" 
                               value="{{ $currentSearch }}"
                               placeholder="Cari nama pemilik KTP, NIK, kotak arsip, laci, kode booking, acara..." 
                               class="w-full pl-10 pr-4 py-2.5 rounded-brand border border-slate-200 text-xs font-semibold placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#001A72] focus:border-transparent bg-slate-50/50">
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit" class="w-full sm:w-auto px-4 py-2.5 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white font-bold text-xs transition shadow-xs cursor-pointer">
                            Cari
                        </button>
                        @if($currentSearch || $currentStatus !== 'all')
                            <a href="{{ route('front_office.unclaimed_ktp') }}" class="w-full sm:w-auto px-3.5 py-2.5 rounded-brand bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition text-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Status Filter Pills -->
                <div class="flex items-center gap-2 pt-2 border-t border-slate-100 overflow-x-auto custom-scrollbar">
                    <a href="{{ route('front_office.unclaimed_ktp', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}"
                       class="px-3 py-1.5 rounded-brand text-xs font-bold transition whitespace-nowrap {{ $currentStatus === 'all' ? 'bg-[#001A72] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua KTP Tertahan ({{ $metrics['total_retained_ktp'] }})
                    </a>
                    <a href="{{ route('front_office.unclaimed_ktp', array_merge(request()->except('status', 'page'), ['status' => 'unclaimed_box'])) }}"
                       class="px-3 py-1.5 rounded-brand text-xs font-bold transition whitespace-nowrap {{ $currentStatus === 'unclaimed_box' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200' }}">
                        Di Kotak Arsip ({{ $metrics['in_unclaimed_box'] }})
                    </a>
                    <a href="{{ route('front_office.unclaimed_ktp', array_merge(request()->except('status', 'page'), ['status' => 'in_drawer'])) }}"
                       class="px-3 py-1.5 rounded-brand text-xs font-bold transition whitespace-nowrap {{ $currentStatus === 'in_drawer' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-indigo-50 text-indigo-800 hover:bg-indigo-100 border border-indigo-200' }}">
                        Di Laci Operasional ({{ $metrics['in_drawer'] }})
                    </a>
                    <a href="{{ route('front_office.unclaimed_ktp', array_merge(request()->except('status', 'page'), ['status' => 'with_incidents'])) }}"
                       class="px-3 py-1.5 rounded-brand text-xs font-bold transition whitespace-nowrap {{ $currentStatus === 'with_incidents' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 text-rose-800 hover:bg-rose-100 border border-rose-200' }}">
                        Ada Insiden Kerusakan ({{ $metrics['with_incidents'] }})
                    </a>
                </div>
            </div>

            <!-- Tabel Inventaris KTP Tertinggal -->
            <div class="bg-white rounded-brand border border-slate-200 shadow-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 font-extrabold border-b border-slate-200">
                            <tr>
                                <th scope="col" class="px-5 py-3.5">Waktu Tertahan</th>
                                <th scope="col" class="px-5 py-3.5">Pemilik KTP &amp; NIK</th>
                                <th scope="col" class="px-5 py-3.5">Lokasi Fisik</th>
                                <th scope="col" class="px-5 py-3.5">Acara &amp; PIC Kontak</th>
                                <th scope="col" class="px-5 py-3.5">Status &amp; Riwayat Kerusakan</th>
                                <th scope="col" class="px-5 py-3.5 text-center">Aksi Serah Terima</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white font-normal">
                            @forelse($unclaimedLogs as $log)
                                @php
                                    $booking = $log->booking;
                                    $user = $booking?->user;
                                    $eventDetail = $booking?->eventDetail;
                                    $retainedCarbon = \Carbon\Carbon::parse($log->retained_at);
                                    $earliestSlot = $booking?->slots->sortBy('event_start_at')->first();
                                    $hasIncident = ($booking?->status === \App\Enums\BookingStatus::ABANDONED_INCIDENT) || ($booking?->clearance_status === 'incident_detected');
                                    
                                    $payload = [
                                        'id' => $log->id,
                                        'booking_id' => $booking?->id,
                                        'booking_code' => $booking?->booking_code ?? '-',
                                        'ktp_holder_name' => $log->ktp_holder_name,
                                        'nik_number' => $log->nik_number ?? '-',
                                        'unclaimed_box_id' => $log->unclaimed_box_id,
                                        'storage_drawer_number' => $log->storage_drawer_number,
                                        'pic_whatsapp_number' => $log->pic_whatsapp_number ?? ($eventDetail?->pic_phone_snapshot ?? ($user?->phone_number ?? '-')),
                                        'event_name' => $eventDetail?->event_name ?? 'Acara MCC',
                                        'user_name' => $user?->full_name ?? '-',
                                        'user_suspended' => $user?->is_suspended ?? false,
                                        'suspension_reason' => $user?->suspension_reason ?? null,
                                        'notes' => $log->notes,
                                    ];
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition group">
                                    <!-- 1. Waktu Tertahan -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-extrabold text-slate-900 text-xs">
                                            {{ $retainedCarbon->translatedFormat('d M Y, H:i') }} WIB
                                        </div>
                                        <div class="text-[10px] text-slate-500 mt-0.5">
                                            Tertahan sejak {{ $retainedCarbon->diffForHumans() }}
                                        </div>
                                    </td>

                                    <!-- 2. Pemilik KTP & NIK -->
                                    <td class="px-5 py-4">
                                        <div class="font-extrabold text-slate-900 text-sm flex items-center gap-1.5">
                                            <span>{{ $log->ktp_holder_name }}</span>
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-mono mt-0.5 flex items-center gap-1.5">
                                            <span>NIK: {{ $log->nik_number ?? '-' }}</span>
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            Pemohon: {{ $user?->full_name ?? '-' }} ({{ $eventDetail?->institution_name_snapshot ?? ($user?->institution_name ?? '-') }})
                                        </div>
                                    </td>

                                    <!-- 3. Lokasi Fisik -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if($log->unclaimed_box_id)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-brand text-xs font-extrabold bg-amber-50 text-amber-900 border border-amber-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-amber-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                                                <span>Kotak Arsip: {{ $log->unclaimed_box_id }}</span>
                                            </span>
                                        @elseif($log->storage_drawer_number)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-brand text-xs font-extrabold bg-indigo-50 text-[#001A72] border border-indigo-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="8" x="2" y="3" rx="1"/><rect width="20" height="8" x="2" y="13" rx="1"/></svg>
                                                <span>Laci {{ $log->storage_drawer_number }}</span>
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-xs italic">Meja FO</span>
                                        @endif
                                    </td>

                                    <!-- 4. Acara & Kontak PIC -->
                                    <td class="px-5 py-4 max-w-xs">
                                        <div class="font-bold text-slate-900 line-clamp-1">
                                            {{ $eventDetail?->event_name ?? 'Acara MCC' }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                            {{ $booking?->booking_code ?? '-' }}
                                        </div>
                                        <div class="mt-1.5 flex items-center gap-2">
                                            @php
                                                $picPhone = $log->pic_whatsapp_number ?? ($eventDetail?->pic_phone_snapshot ?? ($user?->phone_number ?? null));
                                            @endphp
                                            @if($picPhone && $picPhone !== '-')
                                                <a href="{{ route('front_office.overdue.notify_pic', $booking?->id ?? 0) }}" 
                                                   target="_blank"
                                                   class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition"
                                                   title="Hubungi PIC via WhatsApp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                                    <span>{{ $picPhone }}</span>
                                                </a>
                                            @else
                                                <span class="text-slate-400 text-[11px]">-</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- 5. Status & Riwayat Kerusakan -->
                                    <td class="px-5 py-4">
                                        @if($booking)
                                            <div class="flex items-center gap-1.5">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $booking->status->badgeClasses() }}">
                                                    {{ $booking->status->label() }}
                                                </span>
                                                @if($user?->is_suspended)
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                                        Akun Dibekukan
                                                    </span>
                                                @endif
                                            </div>
                                        @endif

                                        @if($hasIncident && $booking?->latestIncident)
                                            <div class="mt-1.5 p-2 rounded bg-rose-50 border border-rose-200 text-[11px] text-rose-800">
                                                <span class="font-bold block">{{ $booking->latestIncident->item_name }}:</span>
                                                <span class="line-clamp-2 text-[10px]">{{ $booking->latestIncident->description }}</span>
                                            </div>
                                        @endif

                                        @if($log->notes)
                                            <div class="text-[10px] text-slate-500 italic mt-1 line-clamp-2" title="{{ $log->notes }}">
                                                {{ $log->notes }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- 6. Aksi Serah Terima -->
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        <button type="button"
                                                @click="openHandoverModal({{ json_encode($payload) }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-brand bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-2xs cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                            <span>Serah Terima</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-20 text-center bg-white">
                                        <div class="max-w-sm mx-auto flex flex-col items-center justify-center space-y-3">
                                            <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-2xs">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-800">Tidak ada KTP tertahan di inventaris</p>
                                                <p class="text-xs text-slate-500 mt-1">Seluruh identitas fisik KTP pemohon telah berhasil diserahkan atau tidak ditemukan data sesuai filter pencarian.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($unclaimedLogs->hasPages())
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                        {{ $unclaimedLogs->links() }}
                    </div>
                @endif
            </div>

        </main>
    </div>

    <!-- Modal Serah Terima KTP Susulan -->
    <div x-show="showHandoverModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <div x-show="showHandoverModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="closeHandoverModal()" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6 relative z-10 pointer-events-none">
            <div x-show="showHandoverModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.stop
                 class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-lg border border-slate-200 pointer-events-auto my-8">
                
                <div class="bg-[#001A72] text-white px-6 py-4 flex items-center justify-between gap-4">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-indigo-200 block">Formulir Pengambilan Susulan (FR-FO-09)</span>
                        <h3 class="text-base font-bold text-white tracking-tight">Serah Terima KTP Tertinggal</h3>
                    </div>
                    <button @click="closeHandoverModal()" class="p-1.5 rounded-brand text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                    <!-- Info Kartu KTP -->
                    <div class="bg-slate-50 p-3.5 rounded-brand border border-slate-200 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-semibold">Nama Pemilik KTP:</span>
                            <span class="font-extrabold text-slate-900" x-text="selectedLog.ktp_holder_name"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-semibold">Lokasi Penyimpanan:</span>
                            <span class="font-bold text-[#001A72]" x-text="selectedLog.unclaimed_box_id ? ('Kotak Arsip ' + selectedLog.unclaimed_box_id) : ('Laci ' + selectedLog.storage_drawer_number)"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-semibold">Acara Terkait:</span>
                            <span class="font-bold text-slate-800" x-text="selectedLog.event_name + ' (' + selectedLog.booking_code + ')'"></span>
                        </div>
                    </div>

                    <!-- Error Alert -->
                    <template x-if="handoverError">
                        <div class="p-3 bg-rose-50 border border-rose-200 rounded-brand text-xs text-rose-800 font-semibold flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span x-text="handoverError"></span>
                        </div>
                    </template>

                    <!-- Form Inputs -->
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Pengambil / Penerima KTP <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" 
                                   x-model="retrievedByName" 
                                   placeholder="Contoh: Budi Santoso (PIC / Perwakilan)" 
                                   class="w-full px-3.5 py-2 rounded-brand border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#001A72] focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                NIK Penerima (Opsional)
                            </label>
                            <input type="text" 
                                   x-model="retrievedByNik" 
                                   placeholder="Contoh: 357301xxxxxxxxxx" 
                                   class="w-full px-3.5 py-2 rounded-brand border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#001A72] focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Catatan Serah Terima (Opsional)
                            </label>
                            <textarea x-model="handoverNotes" 
                                      rows="2" 
                                      placeholder="Contoh: Pengambilan susulan KTP dengan menunjukkan kartu identitas pendukung..." 
                                      class="w-full px-3.5 py-2 rounded-brand border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#001A72] focus:border-transparent"></textarea>
                        </div>

                        <!-- Checkbox Unsuspend jika User Suspended -->
                        <template x-if="selectedLog.user_suspended">
                            <div class="p-3 bg-amber-50 border border-amber-300 rounded-brand">
                                <label class="flex items-start gap-2.5 cursor-pointer">
                                    <input type="checkbox" 
                                           x-model="unsuspendUser" 
                                           class="mt-0.5 rounded border-amber-400 text-[#001A72] focus:ring-[#001A72]">
                                    <div class="text-xs">
                                        <span class="font-extrabold text-amber-900 block">Buka Pemblokiran Akun Pemohon</span>
                                        <span class="text-amber-800 text-[11px] block mt-0.5">
                                            Akun pemohon saat ini dibekukan: "<span class="italic" x-text="selectedLog.suspension_reason"></span>". Centang jika sanksi/ganti rugi telah diselesaikan.
                                        </span>
                                    </div>
                                </label>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" 
                            @click="closeHandoverModal()" 
                            class="px-4 py-2 rounded-brand bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" 
                            @click="submitHandover()" 
                            :disabled="isSubmittingHandover || !retrievedByName" 
                            class="px-4 py-2 rounded-brand bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-xs cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1.5">
                        <svg x-show="!isSubmittingHandover" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        <svg x-show="isSubmittingHandover" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="isSubmittingHandover ? 'Menyimpan...' : 'Konfirmasi Serah Terima'"></span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <script>
        function unclaimedKtpInventory() {
            return {
                sidebarLeft: true,
                showHandoverModal: false,
                isSubmittingHandover: false,
                handoverError: '',
                selectedLog: {
                    id: null,
                    booking_id: null,
                    booking_code: '',
                    ktp_holder_name: '',
                    nik_number: '',
                    unclaimed_box_id: '',
                    storage_drawer_number: '',
                    event_name: '',
                    user_name: '',
                    user_suspended: false,
                    suspension_reason: '',
                    notes: ''
                },
                retrievedByName: '',
                retrievedByNik: '',
                handoverNotes: '',
                unsuspendUser: false,

                openHandoverModal(log) {
                    this.selectedLog = log;
                    this.retrievedByName = log.ktp_holder_name || '';
                    this.retrievedByNik = log.nik_number !== '-' ? log.nik_number : '';
                    this.handoverNotes = '';
                    this.unsuspendUser = log.user_suspended || false;
                    this.handoverError = '';
                    this.isSubmittingHandover = false;
                    this.showHandoverModal = true;
                },

                closeHandoverModal() {
                    this.showHandoverModal = false;
                    this.handoverError = '';
                },

                async submitHandover() {
                    if (!this.retrievedByName) {
                        this.handoverError = 'Nama penerima KTP wajib diisi.';
                        return;
                    }

                    this.isSubmittingHandover = true;
                    this.handoverError = '';

                    const payload = {
                        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        ktp_vault_log_id: this.selectedLog.id,
                        retrieved_by_name: this.retrievedByName,
                        retrieved_by_nik: this.retrievedByNik || null,
                        notes: this.handoverNotes || null,
                        unsuspend_user: this.unsuspendUser
                    };

                    try {
                        const response = await fetch('{{ route('front_office.unclaimed_ktp.handover') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': payload._token
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (!response.ok || !data.success) {
                            const errMessage = data.errors?.retrieved_by_name?.[0] || data.message || 'Gagal memproses serah-terima KTP.';
                            this.handoverError = errMessage;
                            this.isSubmittingHandover = false;
                            return;
                        }

                        alert(data.message || 'Serah-terima KTP tertinggal berhasil dicatat!');
                        this.closeHandoverModal();
                        window.location.reload();
                    } catch (e) {
                        this.handoverError = 'Terjadi kesalahan komunikasi dengan server: ' + e.message;
                        this.isSubmittingHandover = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
