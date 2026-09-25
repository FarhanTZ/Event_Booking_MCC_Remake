<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale() ?? 'id') }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() ?? '' }}">

    <title>Antrean Kurasi Proposal - Malang Creative Center (MCC)</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        [x-cloak] { display: none !important; }

        /* Custom MCC Theme for Flatpickr Range Calendar */
        .flatpickr-calendar {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            border-radius: 0.625rem !important;
            box-shadow: 0 20px 25px -5px rgba(0, 26, 114, 0.15), 0 8px 10px -6px rgba(0, 26, 114, 0.1) !important;
            border: 1px solid #CBD5E1 !important;
        }
        .flatpickr-months .flatpickr-month {
            background: #001A72 !important;
            color: #ffffff !important;
            border-top-left-radius: 0.625rem;
            border-top-right-radius: 0.625rem;
        }
        .flatpickr-current-month .flatpickr-monthDropdown-months, 
        .flatpickr-current-month input.cur-year {
            color: #ffffff !important;
            font-weight: 700 !important;
        }
        .flatpickr-months .flatpickr-prev-month svg, 
        .flatpickr-months .flatpickr-next-month svg {
            fill: #ffffff !important;
        }
        .flatpickr-day.selected, 
        .flatpickr-day.startRange, 
        .flatpickr-day.endRange, 
        .flatpickr-day.selected.inRange, 
        .flatpickr-day.startRange.inRange, 
        .flatpickr-day.endRange.inRange {
            background: #001A72 !important;
            border-color: #001A72 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
        }
        .flatpickr-day.inRange {
            background: #EEF2FF !important;
            border-color: #EEF2FF !important;
            color: #001A72 !important;
            box-shadow: -5px 0 0 #EEF2FF, 5px 0 0 #EEF2FF !important;
        }
        .flatpickr-day:hover {
            background: #EEF2FF !important;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-[#001A72] selection:text-white"
      x-data="marketingDashboard()">

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

    <!-- ======================================================================== -->
    <!-- MAIN MCC TOP NAVBAR (Strictly follows PRD/Design.md) -->
    <!-- ======================================================================== -->
    <header class="w-full bg-[#001A72] text-white sticky top-0 z-40 shadow-elevated border-b border-indigo-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20 gap-4">
                
                <!-- Left Brand & Navigation Link -->
                <div class="flex items-center gap-6 min-w-0">
                    <a href="{{ route('marketing.dashboard') }}" class="flex items-center gap-3 shrink-0 group">
                        <div class="w-10 h-10 rounded-brand bg-white flex items-center justify-center p-1.5 shadow-sm transition-transform group-hover:scale-105">
                            <img src="{{ asset('assets/images/media/branding/mcc-aset-logo blue 1.png') }}" 
                                 alt="MCC Logo" 
                                 class="h-full w-auto object-contain">
                        </div>
                        <div class="min-w-0">
                            <span class="font-extrabold text-sm sm:text-base tracking-tight text-white block truncate leading-tight">
                                Malang Creative Center
                            </span>
                            <span class="text-[11px] font-medium text-indigo-200 block truncate">
                                Reservation System &bull; <span class="font-bold text-amber-400">Marketing &amp; Kurasi</span>
                            </span>
                        </div>
                    </a>

                    <!-- Nav Items -->
                    <nav class="hidden md:flex items-center gap-2 pl-4 border-l border-white/15">
                        <a href="{{ route('marketing.dashboard') }}" 
                           class="flex items-center gap-2 px-3 py-1.5 rounded-brand font-bold text-xs bg-white text-[#001A72] shadow-xs transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="m9 12 2 2 4-4"/></svg>
                            <span>Antrean Kurasi</span>
                            @if($metrics['awaiting_curation'] > 0)
                                <span class="px-2 py-0.2 rounded-full text-[10px] font-extrabold bg-amber-400 text-slate-950">
                                    {{ $metrics['awaiting_curation'] }}
                                </span>
                            @endif
                        </a>
                    </nav>
                </div>

                <!-- Right Live Status, User Profile & Actions -->
                <div class="flex items-center gap-3 sm:gap-4 shrink-0">
                    <!-- Date Pill -->
                    <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 rounded-brand bg-[#001250] border border-white/10 text-xs text-indigo-100 font-mono">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>

                    <!-- User Snapshot -->
                    <div class="flex items-center gap-2.5 pl-2">
                        <div class="w-9 h-9 rounded-full bg-amber-400 text-slate-950 font-black text-xs flex items-center justify-center shrink-0 shadow-xs border-2 border-white/20">
                            {{ strtoupper(substr(auth()->user()->full_name ?? 'M', 0, 1)) }}
                        </div>
                        <div class="hidden sm:block text-left min-w-0">
                            <span class="font-bold text-white text-xs block truncate leading-tight">{{ auth()->user()->full_name ?? 'Petugas Marketing' }}</span>
                            <span class="text-indigo-200 text-[10px] block truncate leading-tight">{{ auth()->user()->email ?? 'marketing@mcc.id' }}</span>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}" class="inline-flex">
                        @csrf
                        <button type="submit" 
                                title="Keluar dari Akun"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-brand bg-white/10 hover:bg-rose-600 text-white text-xs font-bold transition cursor-pointer border border-white/15">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                            <span class="hidden sm:inline">Keluar</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </header>

    <!-- ======================================================================== -->
    <!-- MAIN CONTENT WORKSPACE -->
    <!-- ======================================================================== -->
    <div class="flex-1 flex flex-col w-full min-w-0">

        <!-- Main Workspace -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">

            <!-- Page Title -->
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Dashboard
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Panel kurasi dan triase antrean permohonan event Gedung MCC
                </p>
            </div>
            <!-- ======================================================================== -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- 1. Butuh Kurasi (Amber) -->
                <a href="{{ route('marketing.dashboard', ['status' => 'awaiting_marketing']) }}" 
                   class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 hover:border-amber-400 hover:shadow-elevated transition block group {{ $currentStatus === 'awaiting_marketing' ? 'ring-2 ring-amber-500 bg-amber-50/20' : '' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Butuh Kurasi</span>
                        <div class="w-8 h-8 rounded-brand bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200 group-hover:scale-105 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900">{{ $metrics['awaiting_curation'] }}</span>
                        <span class="text-xs font-semibold text-amber-600">Permohonan</span>
                    </div>
                    <p class="mt-1 text-[11px] text-slate-500">Antrean baru belum dinilai</p>
                </a>

                <!-- 2. Toleransi Revisi 24 Jam (Indigo) -->
                <a href="{{ route('marketing.dashboard', ['status' => 'revised']) }}" 
                   class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 hover:border-indigo-400 hover:shadow-elevated transition block group {{ $currentStatus === 'revised' ? 'ring-2 ring-indigo-500 bg-indigo-50/20' : '' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Toleransi Revisi (24J)</span>
                        <div class="w-8 h-8 rounded-brand bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-200 group-hover:scale-105 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900">{{ $metrics['revised'] }}</span>
                        <span class="text-xs font-semibold text-indigo-600">Revisi Aktif</span>
                    </div>
                    <p class="mt-1 text-[11px] text-slate-500">Menunggu perbaikan pemohon</p>
                </a>

                <!-- 3. Verifikasi Pembayaran (Purple) -->
                <a href="{{ route('marketing.dashboard', ['status' => 'awaiting_payment']) }}" 
                   class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 hover:border-purple-400 hover:shadow-elevated transition block group {{ $currentStatus === 'awaiting_payment' ? 'ring-2 ring-purple-500 bg-purple-50/20' : '' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Verifikasi Pembayaran</span>
                        <div class="w-8 h-8 rounded-brand bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-200 group-hover:scale-105 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900">{{ $metrics['awaiting_payment'] }}</span>
                        <span class="text-xs font-semibold text-purple-600">Komersial</span>
                    </div>
                    <p class="mt-1 text-[11px] text-slate-500">Menunggu bukti transfer</p>
                </a>

                <!-- 4. Disetujui Bulan Ini (Emerald) -->
                <a href="{{ route('marketing.dashboard', ['status' => 'confirmed']) }}" 
                   class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 hover:border-emerald-400 hover:shadow-elevated transition block group {{ $currentStatus === 'confirmed' ? 'ring-2 ring-emerald-500 bg-emerald-50/20' : '' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Disetujui Bulan Ini</span>
                        <div class="w-8 h-8 rounded-brand bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200 group-hover:scale-105 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900">{{ $metrics['approved_this_month'] }}</span>
                        <span class="text-xs font-semibold text-emerald-600">Acara Sah</span>
                    </div>
                    <p class="mt-1 text-[11px] text-slate-500">E-Pass terbit (Total Sah: {{ $metrics['total_confirmed'] }})</p>
                </a>
            </div>

            <!-- ======================================================================== -->
            <!-- 3. REQ-MKT-03: FILTER MULTI-KRITERIA & SEARCH BAR -->
            <!-- ======================================================================== -->
            <div class="bg-white rounded-brand border border-slate-200 shadow-card p-5 space-y-4">
                <!-- Multi-Parameter Realtime Search & Filter Form -->
                <form method="GET" 
                      action="{{ route('marketing.dashboard') }}" 
                      x-ref="filterForm"
                      class="space-y-3.5">

                    <!-- Row 1: Search, Status Dropdown, Kategori Acara -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <!-- 1. Text Search Input (Realtime debounced) -->
                        <div class="sm:col-span-5 relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </div>
                            <input type="text" 
                                   name="q" 
                                   value="{{ $currentSearch }}" 
                                   @input.debounce.500ms="$refs.filterForm.submit()"
                                   placeholder="Cari kode (MCC-...), PIC, acara, atau lembaga..." 
                                   class="block w-full pl-10 pr-3 py-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] placeholder:text-slate-400">
                        </div>

                        <!-- 2. Status Dropdown Filter -->
                        <div class="sm:col-span-4">
                            <select name="status" 
                                    @change="$refs.filterForm.submit()"
                                    class="block w-full py-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 font-medium">
                                <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>
                                    Semua Status Permohonan ({{ $metrics['total_all'] }})
                                </option>
                                <option value="awaiting_marketing" {{ $currentStatus === 'awaiting_marketing' ? 'selected' : '' }}>
                                    Menunggu Kurasi ({{ $metrics['awaiting_curation'] }})
                                </option>
                                <option value="revised" {{ $currentStatus === 'revised' ? 'selected' : '' }}>
                                    Perlu Revisi ({{ $metrics['revised'] }})
                                </option>
                                <option value="awaiting_payment" {{ $currentStatus === 'awaiting_payment' ? 'selected' : '' }}>
                                    Menunggu Pembayaran ({{ $metrics['awaiting_payment'] }})
                                </option>
                                <option value="confirmed" {{ $currentStatus === 'confirmed' ? 'selected' : '' }}>
                                    Disetujui ({{ $metrics['total_confirmed'] }})
                                </option>
                                <option value="rejected" {{ $currentStatus === 'rejected' ? 'selected' : '' }}>
                                    Ditolak ({{ $metrics['rejected'] }})
                                </option>
                            </select>
                        </div>

                        <!-- 3. Kategori Acara Dropdown -->
                        <div class="sm:col-span-3">
                            <select name="category" 
                                    @change="$refs.filterForm.submit()"
                                    class="block w-full py-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-700">
                                <option value="all" {{ $currentCategory === 'all' ? 'selected' : '' }}>Semua Kategori Acara</option>
                                <option value="Dinas" {{ $currentCategory === 'Dinas' ? 'selected' : '' }}>Dinas / Pemerintah</option>
                                <option value="Kampus" {{ $currentCategory === 'Kampus' ? 'selected' : '' }}>Kampus / Akademisi</option>
                                <option value="Komunitas" {{ $currentCategory === 'Komunitas' ? 'selected' : '' }}>Komunitas Kreatif</option>
                                <option value="Komersial" {{ $currentCategory === 'Komersial' ? 'selected' : '' }}>Komersial / Bisnis</option>
                                <option value="Lainnya" {{ $currentCategory === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Sub-Sektor, Lantai, Rentang Tanggal Popover, & Reset Action -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                        <!-- 4. Sub-Sektor Ekraf Dropdown -->
                        <div class="sm:col-span-4">
                            <select name="sub_sector" 
                                    @change="$refs.filterForm.submit()"
                                    class="block w-full py-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-700">
                                <option value="all" {{ $currentSubSector === 'all' ? 'selected' : '' }}>Semua Sub-Sektor Ekraf</option>
                                @php
                                    $ekrafList = [
                                        'Aplikasi', 'Arsitektur', 'Desain Interior', 'DKV', 'Fashion', 'Film', 'Fotografi', 
                                        'Game', 'Konten Digital', 'Kriya', 'Kuliner', 'Modifikasi Otomotif', 'Musik', 
                                        'Penerbitan', 'Periklanan', 'Pertunjukan', 'Produk', 'Seni Rupa', 'Sulih Suara', 
                                        'Teknologi Baru', 'Televisi & Radio'
                                    ];
                                @endphp
                                @foreach($ekrafList as $ekraf)
                                    <option value="{{ $ekraf }}" {{ $currentSubSector === $ekraf ? 'selected' : '' }}>{{ $ekraf }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 5. Lantai Ruangan -->
                        <div class="sm:col-span-3">
                            <select name="floor_level" 
                                    @change="$refs.filterForm.submit()"
                                    class="block w-full py-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-700">
                                <option value="all" {{ $currentFloor === 'all' ? 'selected' : '' }}>Semua Lantai (1 - 8)</option>
                                @for($f = 1; $f <= 8; $f++)
                                    <option value="{{ $f }}" {{ (string)$currentFloor === (string)$f ? 'selected' : '' }}>Lantai {{ $f }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- 6. Date Range Picker with Clean Calendar Icon Popover -->
                        <div class="sm:col-span-4 relative" 
                             x-data="{
                                 init() {
                                     flatpickr(this.$refs.dateRangeInput, {
                                         mode: 'range',
                                         dateFormat: 'd M Y',
                                         altInput: false,
                                         locale: {
                                             rangeSeparator: ' s.d. '
                                         },
                                         defaultDate: [
                                             @if($currentDateFrom) '{{ $currentDateFrom }}', @endif
                                             @if($currentDateTo) '{{ $currentDateTo }}', @endif
                                         ],
                                         onChange: (selectedDates, dateStr, instance) => {
                                             if (selectedDates.length === 2) {
                                                 this.$refs.dateFrom.value = instance.formatDate(selectedDates[0], 'Y-m-d');
                                                 this.$refs.dateTo.value = instance.formatDate(selectedDates[1], 'Y-m-d');
                                                 this.$refs.filterForm.submit();
                                             }
                                         },
                                         onClose: (selectedDates, dateStr, instance) => {
                                             if (selectedDates.length === 2) {
                                                 this.$refs.dateFrom.value = instance.formatDate(selectedDates[0], 'Y-m-d');
                                                 this.$refs.dateTo.value = instance.formatDate(selectedDates[1], 'Y-m-d');
                                                 this.$refs.filterForm.submit();
                                             }
                                         }
                                     });
                                 }
                             }">
                            <input type="hidden" name="date_from" x-ref="dateFrom" value="{{ $currentDateFrom }}">
                            <input type="hidden" name="date_to" x-ref="dateTo" value="{{ $currentDateTo }}">
                            
                            <div class="relative flex items-center">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#001A72] z-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                </div>
                                <input type="text" 
                                       x-ref="dateRangeInput"
                                       readonly
                                       placeholder="Pilih Rentang Tanggal..." 
                                       class="block w-full pl-10 pr-3 py-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 font-medium cursor-pointer bg-white">
                            </div>
                        </div>

                        <!-- 7. Reset Action Icon Button (Only when filters are active) -->
                        <div class="sm:col-span-1 flex items-center justify-end">
                            @if($currentSearch || $currentCategory !== 'all' || $currentSubSector !== 'all' || $currentFloor !== 'all' || $currentDateFrom || $currentDateTo || $currentStatus !== 'all')
                                <a href="{{ route('marketing.dashboard') }}" 
                                   class="w-9 h-9 sm:w-[38px] sm:h-[38px] inline-flex items-center justify-center rounded-full border border-slate-300 bg-white hover:bg-rose-50 hover:text-rose-600 hover:border-rose-300 text-slate-500 transition cursor-pointer shadow-2xs shrink-0"
                                   title="Reset Semua Filter">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- ======================================================================== -->
            <!-- 4. REQ-MKT-02: TABEL LENGKAP ANTREAN TRIASE PROPOSAL -->
            <!-- ======================================================================== -->
            <div class="bg-white rounded-brand border border-slate-200 shadow-card overflow-hidden">
                <!-- Card Header -->
                <div class="px-6 py-4 border-b border-slate-100 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-brand bg-[#EEF2FF] text-[#001A72] flex items-center justify-center font-bold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><line x1="10" x2="8" y1="9" y2="9"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-sm text-slate-900">
                                Daftar Antrean Proposal Masuk
                            </h3>
                            <p class="text-[11px] text-slate-500">
                                Diurutkan berdasarkan urgensi waktu pelaksanaan &amp; antrean FIFO
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                            {{ $curations->total() }} Data Ditemukan
                        </span>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50/80 text-slate-600 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200/80">
                            <tr>
                                <th scope="col" class="px-5 py-3.5">Kode &amp; Pembiayaan</th>
                                <th scope="col" class="px-5 py-3.5">Pemohon &amp; PIC</th>
                                <th scope="col" class="px-5 py-3.5">Detail Acara &amp; Ekraf</th>
                                <th scope="col" class="px-5 py-3.5">Jadwal &amp; Ruangan</th>
                                <th scope="col" class="px-5 py-3.5">Status</th>
                                <th scope="col" class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($curations as $booking)
                                @php
                                    $statusEnum = $booking->status instanceof \App\Enums\BookingStatus ? $booking->status : \App\Enums\BookingStatus::tryFrom($booking->status) ?? \App\Enums\BookingStatus::AWAITING_MARKETING;
                                    $urgencyInfo = $booking->getUrgencyBadgeInfo();
                                    $userAccountType = $booking->user->account_type ?? \App\Enums\AccountType::KOMUNITAS;
                                    $accountTypeLabel = is_object($userAccountType) && method_exists($userAccountType, 'label') ? $userAccountType->label() : ucfirst((string)$userAccountType);
                                @endphp
                                <tr class="hover:bg-slate-50/90 transition-colors {{ $urgencyInfo ? 'bg-rose-50/20' : '' }}">
                                    
                                    <!-- 1. Kode, Tanggal Submit & Skema -->
                                    <td class="px-5 py-4 align-top space-y-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-mono font-extrabold text-sm text-[#001A72] block">{{ $booking->booking_code }}</span>
                                            
                                            <!-- Urgency Warning Icon beside Booking Code with Alpine Hover Tooltip -->
                                            @if($urgencyInfo)
                                                <div class="relative inline-flex items-center"
                                                     x-data="{ showTooltip: false }"
                                                     @mouseenter="showTooltip = true"
                                                     @mouseleave="showTooltip = false">
                                                    <button type="button"
                                                            class="inline-flex items-center justify-center text-rose-600 hover:text-rose-700 animate-pulse cursor-pointer p-0.5 rounded focus:outline-none"
                                                            aria-label="{{ $urgencyInfo['label'] }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/></svg>
                                                    </button>
                                                    
                                                    <!-- Tooltip Popover with Alpine Transition (Slate Charcoal Gray) -->
                                                    <div x-show="showTooltip"
                                                         x-transition:enter="transition ease-out duration-150"
                                                         x-transition:enter-start="opacity-0 translate-x-1"
                                                         x-transition:enter-end="opacity-100 translate-x-0"
                                                         x-transition:leave="transition ease-in duration-100"
                                                         x-transition:leave-start="opacity-100 translate-x-0"
                                                         x-transition:leave-end="opacity-0 translate-x-1"
                                                         style="display: none;"
                                                         class="absolute left-full top-1/2 -translate-y-1/2 ml-2 z-50 pointer-events-none flex items-center drop-shadow-xl">
                                                        <div class="w-2 h-2 bg-slate-800 rotate-45 -mr-1 border-l border-b border-slate-600"></div>
                                                        <div class="bg-slate-800 text-white text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap flex items-center gap-1.5 border border-slate-600 shadow-2xl">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-ping shrink-0"></span>
                                                            <span>{{ $urgencyInfo['label'] }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <span class="text-[11px] text-slate-500 block">{{ $booking->created_at->translatedFormat('d M Y, H:i') }} WIB</span>

                                        <!-- Skema Pembiayaan (Rounded Full Pill with spacing) -->
                                        <div class="pt-2">
                                            @if($booking->pricing_scheme === 'commercial')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">Komersial</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Subsidi Pemkot</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- 2. Pemohon, Lembaga & PIC -->
                                    <td class="px-5 py-4 align-top space-y-1">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-bold text-slate-900 text-xs block truncate max-w-56">
                                                {{ $booking->eventDetail->institution_name_snapshot ?? ($booking->user->institution_name ?? $booking->user->full_name) }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $accountTypeLabel }}
                                            </span>
                                        </div>

                                        <div class="text-[11px] text-slate-600 flex items-center gap-1.5 pt-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                            <span class="font-medium text-slate-800">{{ $booking->eventDetail->pic_name_snapshot ?? $booking->user->full_name }}</span>
                                        </div>
                                        <div class="text-[11px] font-mono font-semibold text-emerald-700 flex items-center gap-1.5 pt-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                            <span>{{ $booking->eventDetail->pic_phone_snapshot ?? $booking->user->phone_number }}</span>
                                        </div>
                                    </td>

                                    <!-- 3. Detail Acara & Sub-Sektor Ekraf -->
                                    <td class="px-5 py-4 align-top space-y-1.5 max-w-64">
                                        <span class="font-bold text-slate-900 text-xs block leading-snug">
                                            {{ $booking->eventDetail->event_name ?? 'Event MCC' }}
                                        </span>
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100">
                                                {{ $booking->eventDetail->ekraf_subsector ?? 'Ekraf Umum' }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">
                                                {{ $booking->eventDetail->estimated_participants ?? 0 }} Peserta
                                            </span>
                                        </div>
                                    </td>

                                    <!-- 4. Rekapitulasi Jadwal & Ruangan -->
                                    <td class="px-5 py-4 align-top">
                                        <div class="space-y-1.5 max-w-60">
                                            @foreach($booking->slots->take(2) as $slot)
                                                <div class="text-[11px] bg-slate-50/90 p-2 rounded-brand border border-slate-200/80">
                                                    <div class="flex items-center justify-between gap-1">
                                                        <span class="font-bold text-slate-900 truncate">{{ $slot->room->name ?? 'Ruangan MCC' }}</span>
                                                        <span class="text-[10px] px-1.5 py-0.2 rounded bg-slate-200 text-slate-800 font-bold shrink-0">Lt. {{ $slot->room->floor_level ?? 1 }}</span>
                                                    </div>
                                                    <span class="text-slate-500 font-mono text-[10px] block mt-0.5">
                                                        {{ \Carbon\Carbon::parse($slot->event_start_at)->translatedFormat('d M Y') }} &bull; {{ \Carbon\Carbon::parse($slot->event_start_at)->format('H:i') }}-{{ \Carbon\Carbon::parse($slot->event_end_at)->format('H:i') }}
                                                    </span>
                                                </div>
                                            @endforeach
                                            @if($booking->slots->count() > 2)
                                                <span class="text-[10px] text-[#001A72] font-extrabold block pl-1">
                                                    +{{ $booking->slots->count() - 2 }} sesi jadwal lainnya
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- 5. Status Kurasi -->
                                    <td class="px-5 py-4 align-top">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $statusEnum->badgeClasses() }}">
                                            {{ $statusEnum->label() }}
                                        </span>
                                    </td>

                                    <!-- 6. Aksi Review Modal Button -->
                                    <td class="px-5 py-4 align-top text-right">
                                        <button @click="openModal({{ json_encode([
                                                    'code' => $booking->booking_code,
                                                    'status' => $statusEnum->label(),
                                                    'status_badge' => $statusEnum->badgeClasses(),
                                                    'status_raw' => $booking->status instanceof \App\Enums\BookingStatus ? $booking->status->value : (string) $booking->status,
                                                    'urgency_label' => $urgencyInfo['label'] ?? null,
                                                    'created_at' => $booking->created_at->translatedFormat('l, d F Y - H:i') . ' WIB',
                                                    'institution' => $booking->eventDetail->institution_name_snapshot ?? ($booking->user->institution_name ?? $booking->user->full_name),
                                                    'account_type' => $accountTypeLabel,
                                                    'user_email' => $booking->user->email ?? '-',
                                                    'pic_name' => $booking->eventDetail->pic_name_snapshot ?? $booking->user->full_name,
                                                    'pic_phone' => $booking->eventDetail->pic_phone_snapshot ?? $booking->user->phone_number,
                                                    'event_name' => $booking->eventDetail->event_name ?? '-',
                                                    'event_category' => $booking->eventDetail->event_category ?? '-',
                                                    'ekraf_subsector' => $booking->eventDetail->ekraf_subsector ?? '-',
                                                    'event_type' => $booking->eventDetail->event_type ?? '-',
                                                    'participants' => $booking->eventDetail->estimated_participants ?? 0,
                                                    'description' => $booking->eventDetail->event_description ?? '-',
                                                    'equipment' => $booking->eventDetail->equipment_needed ?? 'Tidak membawa peralatan khusus',
                                                    'proposal_url' => $booking->eventDetail ? asset('storage/' . $booking->eventDetail->proposal_file_url) : null,
                                                    'pricing_scheme' => $booking->pricing_scheme === 'commercial' ? 'Komersial' : 'Subsidi Pemkot Malang (Gratis)',
                                                    'slots' => $booking->slots->map(function($s) {
                                                        return [
                                                             'room_name' => $s->room->name ?? 'Ruangan',
                                                             'floor' => 'Lantai ' . ($s->room->floor_level ?? 1),
                                                             'capacity' => ($s->room->capacity ?? 0) . ' Orang',
                                                             'date' => \Carbon\Carbon::parse($s->event_start_at)->translatedFormat('l, d M Y'),
                                                             'time' => \Carbon\Carbon::parse($s->event_start_at)->format('H:i') . ' - ' . \Carbon\Carbon::parse($s->event_end_at)->format('H:i') . ' WIB',
                                                        ];
                                                    }),
                                                ]) }})"
                                                title="Lihat Detail & Kurasi"
                                                aria-label="Lihat Detail & Kurasi"
                                                class="w-9 h-9 inline-flex items-center justify-center rounded-full bg-[#001A72] hover:bg-[#001250] text-white transition shadow-sm hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center text-slate-500">
                                        <div class="max-w-sm mx-auto space-y-3">
                                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                            </div>
                                            <p class="font-extrabold text-sm text-slate-800">Tidak ada permohonan ditemukan</p>
                                            <p class="text-xs text-slate-500 leading-relaxed">
                                                Tidak ada antrean proposal dengan parameter pencarian atau filter yang aktif.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                @if($curations->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/60">
                        {{ $curations->links() }}
                    </div>
                @endif
            </div>

        </main>
    </div>

    <!-- ======================================================================== -->
    <!-- SLIDE-OVER / MODAL REVIEW KURASI PROPOSAL -->
    <!-- ======================================================================== -->
    <div x-show="showModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop -->
        <div x-show="showModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="closeModal()" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-6">
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-elevated transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-slate-200">
                
                <!-- Modal Header -->
                <div class="bg-[#001A72] text-white px-6 py-4 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-amber-300 text-sm" x-text="activeItem.code"></span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" 
                                  :class="activeItem.status_badge" 
                                  x-text="activeItem.status"></span>
                            <template x-if="activeItem.urgency_label">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-600 text-white animate-pulse" 
                                      x-text="activeItem.urgency_label"></span>
                            </template>
                        </div>
                        <h3 class="text-base font-bold text-white mt-1" x-text="activeItem.event_name"></h3>
                    </div>
                    <button @click="closeModal()" class="p-1 rounded text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body: Tabs & Detail Breakdown -->
                <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                    
                    <!-- Seksi 1: Ringkasan Ruangan & Jadwal -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#001A72] mb-2 flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                            <span>1. Jadwal &amp; Ruangan Terpilih</span>
                        </h4>
                        <div class="border border-slate-200 rounded-brand overflow-hidden">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                                    <tr>
                                        <th class="px-3 py-2">Ruangan</th>
                                        <th class="px-3 py-2">Lantai</th>
                                        <th class="px-3 py-2">Tanggal Sesi</th>
                                        <th class="px-3 py-2">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="(slot, idx) in activeItem.slots" :key="idx">
                                        <tr>
                                            <td class="px-3 py-2 font-bold text-slate-900" x-text="slot.room_name"></td>
                                            <td class="px-3 py-2 text-slate-600" x-text="slot.floor"></td>
                                            <td class="px-3 py-2 font-semibold text-[#001A72]" x-text="slot.date"></td>
                                            <td class="px-3 py-2 font-mono text-slate-800" x-text="slot.time"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Seksi 2: Data Pemohon & PIC -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#001A72] mb-2 flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <span>2. Data Pemohon &amp; Kontak PIC</span>
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 p-4 rounded-brand border border-slate-200 text-xs">
                            <div>
                                <span class="text-slate-400 block">Lembaga / Pemohon:</span>
                                <span class="font-bold text-slate-900" x-text="activeItem.institution"></span>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-800" x-text="'Tipe: ' + activeItem.account_type"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Email Akun:</span>
                                <span class="font-bold text-slate-900" x-text="activeItem.user_email"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Nama PIC Hari-H:</span>
                                <span class="font-bold text-slate-900" x-text="activeItem.pic_name"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Nomor WhatsApp PIC:</span>
                                <span class="font-mono font-bold text-emerald-700" x-text="activeItem.pic_phone"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Seksi 3: Rincian Event & Ekraf -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#001A72] mb-2 flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/></svg>
                            <span>3. Rincian Event &amp; Kurasi Ekraf</span>
                        </h4>
                        <div class="space-y-3 bg-white p-4 rounded-brand border border-slate-200 text-xs">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <span class="text-slate-400 block">Kategori Event:</span>
                                    <span class="font-bold text-slate-900" x-text="activeItem.event_category"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">Sub-Sektor Ekraf:</span>
                                    <span class="font-bold text-[#001A72]" x-text="activeItem.ekraf_subsector"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">Estimasi Peserta:</span>
                                    <span class="font-bold text-slate-900" x-text="activeItem.participants + ' Orang'"></span>
                                </div>
                            </div>
                            <div>
                                <span class="text-slate-400 block mb-1">Deskripsi Kegiatan:</span>
                                <p class="text-slate-700 bg-slate-50 p-3 rounded border border-slate-100 leading-relaxed whitespace-pre-line" x-text="activeItem.description"></p>
                            </div>
                            <div>
                                <span class="text-slate-400 block mb-1">Peralatan Mandiri yang Dibawa:</span>
                                <p class="text-slate-700 bg-slate-50 p-2.5 rounded border border-slate-100" x-text="activeItem.equipment"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Seksi 4: Berkas Proposal -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#001A72] mb-2 flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                            <span>4. Berkas Proposal &amp; Skema Pembiayaan</span>
                        </h4>
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 p-4 bg-slate-50 rounded-brand border border-slate-200">
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Skema Penetapan Biaya:</span>
                                <span class="text-xs font-semibold text-indigo-700" x-text="activeItem.pricing_scheme"></span>
                            </div>
                            <template x-if="activeItem.proposal_url">
                                <a :href="activeItem.proposal_url" 
                                   target="_blank" 
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white text-xs font-bold transition shadow-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                                    <span>Buka Berkas Proposal (PDF)</span>
                                </a>
                            </template>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer: Action Button Row (Strictly follows PRD/Design.md) -->
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-xs text-slate-500 w-full sm:w-auto">
                        <span class="inline-block w-2 h-2 rounded-full bg-indigo-500"></span>
                        <span>Kode: <strong class="font-mono text-slate-800" x-text="activeItem.code"></strong></span>
                    </div>

                    <!-- Action Buttons: Destructive, Secondary, Primary CTA -->
                    <div class="flex flex-wrap items-center justify-end gap-2.5 w-full sm:w-auto">
                        <button type="button" 
                                @click="closeModal()" 
                                class="min-h-[44px] px-4 py-2 rounded-brand border border-slate-300 bg-white text-slate-700 text-xs font-bold hover:bg-slate-100 transition cursor-pointer shadow-2xs">
                            Tutup
                        </button>

                        <button type="button"
                                @click="alert('Fitur kurasi: Tolak Proposal ' + activeItem.code)"
                                class="min-h-[44px] inline-flex items-center gap-1.5 px-4 py-2 rounded-brand bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>
                            <span>Tolak Proposal</span>
                        </button>

                        <button type="button"
                                @click="alert('Fitur kurasi: Minta Revisi ' + activeItem.code)"
                                class="min-h-[44px] inline-flex items-center gap-1.5 px-4 py-2 rounded-brand bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition shadow-xs cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                            <span>Minta Revisi</span>
                        </button>

                        <button type="button"
                                @click="alert('Fitur kurasi: Setujui Proposal ' + activeItem.code)"
                                class="min-h-[44px] inline-flex items-center gap-1.5 px-5 py-2 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white text-xs font-bold transition shadow-xs cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            <span>Setujui Proposal</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function marketingDashboard() {
            return {
                showModal: false,
                activeItem: {
                    code: '',
                    status: '',
                    status_badge: '',
                    status_raw: '',
                    urgency_label: null,
                    created_at: '',
                    institution: '',
                    account_type: '',
                    user_email: '',
                    pic_name: '',
                    pic_phone: '',
                    event_name: '',
                    event_category: '',
                    ekraf_subsector: '',
                    event_type: '',
                    participants: 0,
                    description: '',
                    equipment: '',
                    proposal_url: null,
                    pricing_scheme: '',
                    slots: []
                },

                openModal(item) {
                    this.activeItem = item;
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                }
            };
        }
    </script>
</body>
</html>
