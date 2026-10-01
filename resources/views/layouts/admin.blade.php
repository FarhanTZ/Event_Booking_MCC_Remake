<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale() ?? 'id') }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() ?? '' }}">

    <title>@yield('title', 'Super Admin Dashboard - Malang Creative Center (MCC)')</title>

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

        /* Custom MCC Scrollbar */
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
      x-data="{
          sidebarLeft: true,
          sidebarRight: true,
          toggleLeft() { this.sidebarLeft = !this.sidebarLeft },
          toggleRight() { this.sidebarRight = !this.sidebarRight }
      }">

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

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" 
             class="bg-rose-600 text-white px-4 py-3 shadow-lg fixed top-6 right-6 z-50 rounded-brand flex items-center gap-3 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span class="text-xs sm:text-sm font-semibold">{{ session('error') }}</span>
            <button @click="show = false" class="ml-2 hover:opacity-80 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- ======================================================================== -->
    <!-- 3-COLUMN WORKSPACE WRAPPER (FIXED SIDES, SCROLLABLE CENTER) -->
    <!-- ======================================================================== -->
    <div class="flex-1 w-full max-w-[1720px] 2xl:max-w-[1920px] mx-auto p-3 sm:p-4 lg:p-5 flex flex-col lg:flex-row gap-4 lg:gap-5 items-start min-h-screen">
        
        <!-- 1. SISI KIRI: Sidebar Navigasi & Menu Master (Wider & Fixed) -->
        @include('admin.partials.sidebar-left')

        <!-- 2. BAGIAN TENGAH: Workspace Utama (Scrolls Independently) -->
        <main class="flex-1 min-w-0 w-full space-y-4">
            
            <!-- Top Control Bar: Title, Toggles, & Breadcrumbs -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 sm:p-5 rounded-brand border border-slate-200 shadow-card">
                
                <!-- Left Section: Toggle Left Sidebar & Dashboard Title -->
                <div class="flex items-center gap-3">
                    <button type="button"
                            @click="toggleLeft()"
                            :class="sidebarLeft ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-[#001A72] text-white hover:bg-[#001250]'"
                            class="w-9 h-9 inline-flex items-center justify-center rounded-brand transition cursor-pointer shrink-0 shadow-2xs"
                            title="Buka / Tutup Menu Navigasi Kiri">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/></svg>
                    </button>

                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-[#001A72] text-white uppercase tracking-wider">
                                Super Admin
                            </span>
                            <span class="text-xs text-slate-400 font-medium">| Malang Creative Center</span>
                        </div>
                        <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight leading-tight mt-0.5">
                            @yield('page_title', 'Manajemen Gedung & Fasilitas')
                        </h1>
                    </div>
                </div>

                <!-- Right Section: Date & Toggle Right Sidebar -->
                <div class="flex items-center gap-2.5 self-end sm:self-auto">
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-brand bg-[#EEF2FF] text-xs text-[#001A72] font-semibold border border-indigo-100 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        <span>{{ now()->translatedFormat('d M Y') }}</span>
                    </div>

                    <button type="button"
                            @click="toggleRight()"
                            :class="sidebarRight ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-[#001A72] text-white hover:bg-[#001250]'"
                            class="w-9 h-9 inline-flex items-center justify-center rounded-brand transition cursor-pointer shrink-0 shadow-2xs"
                            title="Buka / Tutup Profil & Ringkasan Status Kanan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M15 3v18"/></svg>
                    </button>
                </div>
            </div>

            <!-- Main Page Content Section -->
            @yield('content')

        </main>

        <!-- 3. SISI KANAN: Profil Super Admin & Status Master Gedung -->
        @include('admin.partials.sidebar-right')

    </div>

    @stack('scripts')
</body>
</html>
