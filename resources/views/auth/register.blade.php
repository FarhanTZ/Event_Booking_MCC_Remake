<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Pemohon - MCC Event Booking</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-xl text-center">
        <a href="/" class="inline-flex items-center gap-3 group">
            <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-200 group-hover:scale-105 transition duration-200">
                <i data-lucide="building-2" class="w-6 h-6"></i>
            </div>
            <div class="text-left">
                <span class="font-black text-xl tracking-tight text-slate-900 block leading-none">MCC Booking</span>
                <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Malang Creative Center</span>
            </div>
        </a>
        <h2 class="mt-6 text-2xl font-bold tracking-tight text-slate-900">Registrasi Akun Pemohon Acara</h2>
        <p class="mt-2 text-sm text-slate-600">
            Sudah memiliki akun terdaftar?
            <a href="/login{{ request()->has('redirect_slot') ? '?redirect_slot=' . request('redirect_slot') : '' }}" class="font-medium text-indigo-600 hover:text-indigo-500 hover:underline">Masuk di sini</a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-xl px-4 sm:px-0">
        <div class="bg-white py-8 px-6 shadow-sm border border-slate-200 rounded-2xl sm:px-10">
            <form class="space-y-5" action="/register" method="POST" x-data="{ isSubmitting: false, showPassword: false }" @submit="isSubmitting = true">
                @csrf
                <input type="hidden" name="redirect_slot" value="{{ request('redirect_slot', session('preserved_slot')) }}">

                <div class="border-b border-slate-100 pb-3 mb-4">
                    <h3 class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Informasi Penanggung Jawab (PIC)</h3>
                </div>

                <!-- Grid Nama Lengkap & WhatsApp -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap PIC <span class="text-rose-500">*</span></label>
                        <div class="mt-1.5 relative rounded-lg">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </div>
                            <input id="name" name="name" type="text" required value="{{ old('name') }}" placeholder="cth: Ahmad Fauzi"
                                   class="block w-full pl-10 pr-3 py-2 sm:text-sm border-slate-300 rounded-lg focus:ring-indigo-600 focus:border-indigo-600 text-slate-900">
                        </div>
                        @error('name')
                            <p class="mt-1 text-xs text-rose-600">{{  }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                        <div class="mt-1.5 relative rounded-lg">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="phone" class="w-4 h-4"></i>
                            </div>
                            <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}" placeholder="081234567890"
                                   class="block w-full pl-10 pr-3 py-2 sm:text-sm border-slate-300 rounded-lg focus:ring-indigo-600 focus:border-indigo-600 text-slate-900">
                        </div>
                        @error('phone')
                            <p class="mt-1 text-xs text-rose-600">{{  }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email Resmi / Organisasi <span class="text-rose-500">*</span></label>
                    <div class="mt-1.5 relative rounded-lg">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input id="email" name="email" type="email" required value="{{ old('email') }}" placeholder="pic@organisasi.id"
                               class="block w-full pl-10 pr-3 py-2 sm:text-sm border-slate-300 rounded-lg focus:ring-indigo-600 focus:border-indigo-600 text-slate-900">
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600">{{  }}</p>
                    @enderror
                </div>

                <div class="border-b border-slate-100 pb-3 mb-4 pt-2">
                    <h3 class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Identitas Lembaga / Komunitas</h3>
                </div>

                <!-- Grid Nama Lembaga & Tipe Lembaga -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="institution_name" class="block text-sm font-medium text-slate-700">Nama Organisasi / Komunitas <span class="text-rose-500">*</span></label>
                        <div class="mt-1.5 relative rounded-lg">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="users" class="w-4 h-4"></i>
                            </div>
                            <input id="institution_name" name="institution_name" type="text" required value="{{ old('institution_name') }}" placeholder="cth: Komunitas Desain Malang"
                                   class="block w-full pl-10 pr-3 py-2 sm:text-sm border-slate-300 rounded-lg focus:ring-indigo-600 focus:border-indigo-600 text-slate-900">
                        </div>
                        @error('institution_name')
                            <p class="mt-1 text-xs text-rose-600">{{  }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="institution_type" class="block text-sm font-medium text-slate-700">Tipe Organisasi <span class="text-rose-500">*</span></label>
                        <div class="mt-1.5 relative rounded-lg">
                            <select id="institution_type" name="institution_type" required
                                    class="block w-full py-2 px-3 sm:text-sm border-slate-300 rounded-lg focus:ring-indigo-600 focus:border-indigo-600 text-slate-900">
                                <option value="">-- Pilih Kategori --</option>
                                <option value="komunitas" {{ old('institution_type') == 'komunitas' ? 'selected' : '' }}>Komunitas Kreatif / Seni</option>
                                <option value="kampus" {{ old('institution_type') == 'kampus' ? 'selected' : '' }}>Universitas / Mahasiswa</option>
                                <option value="instansi_pemerintah" {{ old('institution_type') == 'instansi_pemerintah' ? 'selected' : '' }}>Instansi Pemerintah / BUMN</option>
                                <option value="perusahaan" {{ old('institution_type') == 'perusahaan' ? 'selected' : '' }}>Perusahaan Swasta / Startup</option>
                                <option value="eo" {{ old('institution_type') == 'eo' ? 'selected' : '' }}>Event Organizer / Agensi</option>
                                <option value="lainnya" {{ old('institution_type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                        @error('institution_type')
                            <p class="mt-1 text-xs text-rose-600">{{  }}</p>
                        @enderror
                    </div>
                </div>

                <div class="border-b border-slate-100 pb-3 mb-4 pt-2">
                    <h3 class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Keamanan Akun</h3>
                </div>

                <!-- Password & Konfirmasi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700">Kata Sandi <span class="text-rose-500">*</span></label>
                        <div class="mt-1.5 relative rounded-lg">
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required placeholder="Min. 8 karakter"
                                   class="block w-full px-3 py-2 sm:text-sm border-slate-300 rounded-lg focus:ring-indigo-600 focus:border-indigo-600 text-slate-900">
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-rose-600">{{  }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Kata Sandi <span class="text-rose-500">*</span></label>
                        <div class="mt-1.5 relative rounded-lg">
                            <input :type="showPassword ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi kata sandi"
                                   class="block w-full px-3 py-2 sm:text-sm border-slate-300 rounded-lg focus:ring-indigo-600 focus:border-indigo-600 text-slate-900">
                        </div>
                    </div>
                </div>

                <!-- Syarat & Ketentuan -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms" type="checkbox" required class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600">
                    </div>
                    <div class="ml-3 text-xs text-slate-600">
                        <label for="terms">Saya menyetujui <a href="/panduan" target="_blank" class="text-indigo-600 underline font-medium">SOP Penggunaan Fasilitas MCC</a> dan bersedia menyerahkan <strong>KTP Asli PIC</strong> pada saat check-in hari-H.</label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            :disabled="isSubmitting" 
                            class="w-full flex justify-center items-center gap-2 py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-75 disabled:cursor-not-allowed transition">
                        <span x-show="!isSubmitting" class="flex items-center gap-2">
                            <span>Selesaikan Pendaftaran</span>
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </span>
                        <span x-show="isSubmitting" class="flex items-center gap-2">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Mendaftarkan...</span>
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