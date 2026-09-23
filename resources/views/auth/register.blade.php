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
        <a href="/" class="inline-flex items-center justify-center group">
            <img src="{{ asset('assets/images/media/branding/mcc-aset-logohorizontal.png') }}" 
                 alt="Malang Creative Center Logo" 
                 class="h-12 w-auto object-contain group-hover:opacity-95 transition-opacity">
        </a>
        <h2 class="mt-6 text-xl font-bold tracking-tight text-slate-900">Registrasi Akun Pemohon Acara</h2>
        <p class="mt-1.5 text-xs text-slate-600">
            Sudah memiliki akun terdaftar?
            <a href="/login{{ request()->has('redirect_slot') ? '?redirect_slot=' . request('redirect_slot') : '' }}" class="font-semibold text-[#001A72] hover:text-[#001250] hover:underline">Masuk di sini</a>
        </p>
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-xl px-4 sm:px-0">
        <div class="bg-white py-8 px-6 shadow-card border border-slate-200 rounded-brand sm:px-8">
            <form class="space-y-4" action="/register" method="POST" x-data="{ isSubmitting: false, showPassword: false }" @submit="isSubmitting = true">
                @csrf
                <input type="hidden" name="redirect_slot" value="{{ request('redirect_slot', session('preserved_slot')) }}">

                <div class="border-b border-slate-100 pb-2 mb-2">
                    <h3 class="text-xs font-bold text-[#001A72] uppercase tracking-wider">Informasi Penanggung Jawab (PIC)</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form.input 
                        label="Nama Lengkap PIC" 
                        name="name" 
                        icon="user" 
                        placeholder="cth: Ahmad Fauzi" 
                        required 
                    />
                    <x-form.input 
                        label="Nomor WhatsApp" 
                        name="phone" 
                        type="tel" 
                        icon="phone" 
                        placeholder="081234567890" 
                        required 
                    />
                </div>

                <x-form.input 
                    label="Email Resmi / Organisasi" 
                    name="email" 
                    type="email" 
                    icon="mail" 
                    placeholder="pic@organisasi.id" 
                    required 
                />

                <div class="border-b border-slate-100 pb-2 mb-2 pt-2">
                    <h3 class="text-xs font-bold text-[#001A72] uppercase tracking-wider">Identitas Lembaga / Komunitas</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form.input 
                        label="Nama Organisasi / Komunitas" 
                        name="institution_name" 
                        icon="users" 
                        placeholder="cth: Komunitas Desain Malang" 
                        required 
                    />

                    <div>
                        <label for="institution_type" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Tipe Organisasi <span class="text-rose-600">*</span>
                        </label>
                        <select id="institution_type" name="institution_type" required
                                class="block w-full py-2.5 px-3 sm:text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72] text-slate-900 transition-colors">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="komunitas" {{ old('institution_type') == 'komunitas' ? 'selected' : '' }}>Komunitas Kreatif / Seni</option>
                            <option value="kampus" {{ old('institution_type') == 'kampus' ? 'selected' : '' }}>Universitas / Mahasiswa</option>
                            <option value="instansi_pemerintah" {{ old('institution_type') == 'instansi_pemerintah' ? 'selected' : '' }}>Instansi Pemerintah / BUMN</option>
                            <option value="perusahaan" {{ old('institution_type') == 'perusahaan' ? 'selected' : '' }}>Perusahaan Swasta / Startup</option>
                            <option value="eo" {{ old('institution_type') == 'eo' ? 'selected' : '' }}>Event Organizer / Agensi</option>
                            <option value="lainnya" {{ old('institution_type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="border-b border-slate-100 pb-2 mb-2 pt-2">
                    <h3 class="text-xs font-bold text-[#001A72] uppercase tracking-wider">Keamanan Akun</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form.input 
                        label="Kata Sandi" 
                        name="password" 
                        type="password" 
                        placeholder="Min. 8 karakter" 
                        required 
                    />
                    <x-form.input 
                        label="Konfirmasi Kata Sandi" 
                        name="password_confirmation" 
                        type="password" 
                        placeholder="Ulangi kata sandi" 
                        required 
                    />
                </div>

                <!-- Jaminan KTP Asli -->
                <div class="bg-amber-50/70 border border-amber-200 rounded-brand p-3.5 flex items-start gap-3">
                    <input id="terms" name="terms" type="checkbox" required class="mt-0.5 h-4 w-4 rounded-xs border-slate-300 text-[#001A72] focus:ring-[#001A72]">
                    <label for="terms" class="text-xs text-amber-950 leading-relaxed font-medium">
                        Saya menyetujui <a href="/panduan" target="_blank" class="text-[#001A72] underline font-bold">SOP Fasilitas MCC</a> dan bersedia menyerahkan <strong>KTP Asli PIC</strong> pada saat check-in hari-H.
                    </label>
                </div>

                <div class="pt-2">
                    <x-button.primary type="submit" class="w-full" ::disabled="isSubmitting">
                        <span x-show="!isSubmitting" class="flex items-center justify-center gap-2">
                            <span>Selesaikan Pendaftaran</span>
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </span>
                        <span x-show="isSubmitting" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Mendaftarkan...</span>
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