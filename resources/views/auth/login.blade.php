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
        <a href="/" class="inline-flex items-center gap-3 group">
            <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-200 group-hover:scale-105 transition duration-200">
                <i data-lucide="building-2" class="w-6 h-6"></i>
            </div>
            <div class="text-left">
                <span class="font-black text-xl tracking-tight text-slate-900 block leading-none">MCC Booking</span>
                <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Malang Creative Center</span>
            </div>
        </a>
        <h2 class="mt-6 text-2xl font-bold tracking-tight text-slate-900">Masuk ke Portal Pemohon</h2>
        <p class="mt-2 text-sm text-slate-600">
            Belum memiliki akun instansi/komunitas?
            <a href="/register{{ request()->has('redirect_slot') ? '?redirect_slot=' . request('redirect_slot') : '' }}" class="font-medium text-indigo-600 hover:text-indigo-500 hover:underline">Daftar sekarang</a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <!-- Banner Progressive Login (Jika user datang dari klik slot kalender) -->
        @if(request()->has('redirect_slot') || session('preserved_slot'))
            <div class="mb-4 bg-indigo-50 border border-indigo-200 text-indigo-800 p-4 rounded-xl flex items-start gap-3 text-sm">
                <i data-lucide="info" class="w-5 h-5 text-indigo-600 shrink-0 mt-0.5"></i>
                <div>
                    <span class="font-semibold block">Slot Jadwal Telah Dipilih!</span>
                    <span class="text-xs text-indigo-700">Silakan login untuk mengunci slot reservasi 15 menit dan melanjutkan pengisian formulir.</span>
                </div>
            </div>
        @endif

        <div class="bg-white py-8 px-6 shadow-sm border border-slate-200 rounded-2xl sm:px-10">
            <form class="space-y-5" action="/login" method="POST" x-data="{ isSubmitting: false, showPassword: false }" @submit="isSubmitting = true">
                @csrf
                <input type="hidden" name="redirect_slot" value="{{ request('redirect_slot', session('preserved_slot')) }}">

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Alamat Email</label>
                    <div class="mt-1.5 relative rounded-lg">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                               value="{{ old('email') }}"
                               placeholder="nama@organisasi.id"
                               class="block w-full pl-10 pr-3 py-2.5 sm:text-sm border-slate-300 rounded-lg focus:ring-indigo-600 focus:border-indigo-600 text-slate-900 placeholder:text-slate-400">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                            {{  }}
                        </p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-slate-700">Kata Sandi</label>
                        <a href="/forgot-password" class="text-xs font-medium text-indigo-600 hover:text-indigo-500">Lupa kata sandi?</a>
                    </div>
                    <div class="mt-1.5 relative rounded-lg">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required 
                               placeholder="••••••••"
                               class="block w-full pl-10 pr-10 py-2.5 sm:text-sm border-slate-300 rounded-lg focus:ring-indigo-600 focus:border-indigo-600 text-slate-900 placeholder:text-slate-400">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <i x-show="!showPassword" data-lucide="eye" class="w-4 h-4"></i>
                            <i x-show="showPassword" data-lucide="eye-off" class="w-4 h-4"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                            {{  }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600">
                        <label for="remember" class="ml-2 block text-sm text-slate-700">Ingat sesi saya</label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            :disabled="isSubmitting" 
                            class="w-full flex justify-center items-center gap-2 py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-75 disabled:cursor-not-allowed transition">
                        <span x-show="!isSubmitting" class="flex items-center gap-2">
                            <span>Masuk ke Akun</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </span>
                        <span x-show="isSubmitting" class="flex items-center gap-2">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Memproses...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-8 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} Malang Creative Center. Portal Pemohon Acara & Reservasi Gedung.
    </div>
</body>
</html>