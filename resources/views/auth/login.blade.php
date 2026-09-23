<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Akun - MCC Event Booking</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <a href="/" class="inline-flex items-center justify-center group">
            <img src="{{ asset('assets/images/media/branding/mcc-aset-logohorizontal.png') }}" 
                 alt="Malang Creative Center Logo" 
                 class="h-12 w-auto object-contain group-hover:opacity-95 transition-opacity">
        </a>
        <h2 class="mt-6 text-xl font-bold tracking-tight text-slate-900">Masuk ke Portal Pemohon</h2>
        <p class="mt-1.5 text-xs text-slate-600">
            Belum memiliki akun instansi/komunitas?
            <a href="/register{{ request()->has('redirect_slot') ? '?redirect_slot=' . request('redirect_slot') : '' }}" class="font-semibold text-[#001A72] hover:text-[#001250] hover:underline">Daftar sekarang</a>
        </p>
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        @if(request()->has('redirect_slot') || session('preserved_slot'))
            <div class="mb-4 bg-[#EEF2FF] border border-[#C7D2FE] text-[#001A72] p-4 rounded-brand flex items-start gap-3 text-sm shadow-2xs">
                <i data-lucide="info" class="w-5 h-5 text-[#001A72] shrink-0 mt-0.5"></i>
                <div>
                    <span class="font-bold block text-xs uppercase tracking-wide">Slot Jadwal Telah Dipilih</span>
                    <span class="text-xs text-slate-600">Silakan login untuk mengunci slot reservasi 15 menit dan melanjutkan formulir.</span>
                </div>
            </div>
        @endif

        <div class="bg-white py-8 px-6 shadow-card border border-slate-200 rounded-brand sm:px-8">
            <form class="space-y-4" action="/login" method="POST" x-data="{ isSubmitting: false, showPassword: false }" @submit="isSubmitting = true">
                @csrf
                <input type="hidden" name="redirect_slot" value="{{ request('redirect_slot', session('preserved_slot')) }}">

                <x-form.input 
                    label="Alamat Email" 
                    name="email" 
                    type="email" 
                    icon="mail" 
                    placeholder="nama@organisasi.id" 
                    required 
                />

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Kata Sandi <span class="text-rose-600">*</span>
                        </label>
                        <a href="/forgot-password" class="text-xs font-semibold text-[#001A72] hover:text-[#001250]">Lupa sandi?</a>
                    </div>
                    <div class="relative rounded-brand">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required 
                               placeholder="••••••••"
                               class="block w-full pl-10 pr-10 py-2.5 sm:text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72] text-slate-900 placeholder:text-slate-400 transition-colors">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                            <i x-show="!showPassword" data-lucide="eye" class="w-4 h-4"></i>
                            <i x-show="showPassword" data-lucide="eye-off" class="w-4 h-4"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                            <span>{{  }}</span>
                        </p>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-1">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded-xs border-slate-300 text-[#001A72] focus:ring-[#001A72]">
                        <label for="remember" class="ml-2 block text-xs font-medium text-slate-700">Ingat sesi saya</label>
                    </div>
                </div>

                <div class="pt-2">
                    <x-button.primary type="submit" class="w-full" ::disabled="isSubmitting">
                        <span x-show="!isSubmitting" class="flex items-center justify-center gap-2">
                            <span>Masuk ke Akun</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </span>
                        <span x-show="isSubmitting" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Memproses...</span>
                        </span>
                    </x-button.primary>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-8 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Malang Creative Center. Portal Pemohon Acara & Reservasi Gedung.
    </div>
</body>
</html>