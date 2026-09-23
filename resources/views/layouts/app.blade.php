<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale() ?? 'id') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() ?? '' }}">

    <title>{{  ?? 'MCC Event Booking - Malang Creative Center' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen font-sans antialiased selection:bg-indigo-500 selection:text-white flex flex-col">
    <!-- Header / Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-600 flex items-center justify-center text-white shadow-md">
                        <i data-lucide="building-2" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="font-bold text-lg tracking-tight text-slate-900">MCC Event Booking</span>
                        <span class="hidden sm:inline-block ml-2 text-xs font-medium px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Aktif</span>
                    </div>
                </div>

                <nav class="flex items-center gap-4">
                    <a href="#" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition">Jadwal Gedung</a>
                    <a href="#" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition">Reservasi Baru</a>
                    <button class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition shadow-xs">
                        <i data-lucide="calendar-plus" class="w-4 h-4"></i>
                        <span>Booking Ruangan</span>
                    </button>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{  ??  ?? '' }}
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-slate-500">
            &copy; {{ date('Y') }} Malang Creative Center. All rights reserved.
        </div>
    </footer>
</body>
</html>