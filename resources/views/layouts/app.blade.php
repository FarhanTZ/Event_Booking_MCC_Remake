<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale() ?? 'id') }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() ?? '' }}">

    <title>{{  ?? 'MCC Event Booking - Malang Creative Center' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-indigo-600 selection:text-white">

    <!-- Flash Messages / Notifications Container -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-emerald-500 text-white px-4 py-3 shadow-md fixed top-20 right-4 z-50 rounded-xl flex items-center gap-3 transition">
            <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
            <button @click="show = false" class="ml-2 hover:opacity-80"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" class="bg-rose-500 text-white px-4 py-3 shadow-md fixed top-20 right-4 z-50 rounded-xl flex items-center gap-3 transition">
            <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            <span class="text-sm font-medium">{{ session('error') }}</span>
            <button @click="show = false" class="ml-2 hover:opacity-80"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
    @endif

    <!-- Navbar Partial -->
    @include('layouts.partials.navbar')

    <!-- Main Content -->
    <main class="flex-1">
        {{  ??  ?? '' }}
    </main>

    <!-- Footer Partial -->
    @include('layouts.partials.footer')

</body>
</html>