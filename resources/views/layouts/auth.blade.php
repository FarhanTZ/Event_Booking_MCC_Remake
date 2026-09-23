<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale() ?? 'id') }}" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() ?? '' }}">

    <title>{{  ?? 'Autentikasi - MCC Event Booking' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <!-- Main Auth Slot -->
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <a href="/" class="flex justify-center items-center gap-3 group">
            <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-200 group-hover:scale-105 transition duration-200">
                <i data-lucide="building-2" class="w-6 h-6"></i>
            </div>
            <div class="text-left">
                <span class="font-black text-xl tracking-tight text-slate-900 block leading-none">MCC Booking</span>
                <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Malang Creative Center</span>
            </div>
        </a>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full {{  ?? 'sm:max-w-md' }}">
        {{  ??  ?? '' }}
    </div>

    <!-- Simple Auth Footer -->
    <div class="mt-8 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} Malang Creative Center. Portal Pemohon Acara & Reservasi Gedung.
    </div>
</body>
</html>