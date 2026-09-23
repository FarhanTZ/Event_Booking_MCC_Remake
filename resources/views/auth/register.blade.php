<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Pemohon - Malang Creative Center (MCC)</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-screen bg-slate-50 flex flex-col justify-center py-10 sm:px-6 lg:px-8 selection:bg-[#001A72] selection:text-white">

    <!-- Header Branding -->
    <div class="sm:mx-auto sm:w-full sm:max-w-2xl text-center px-4">
        <a href="/" class="inline-flex items-center justify-center group transition-transform hover:scale-105 duration-200">
            <img src="{{ asset('assets/images/media/branding/mcc-aset-logohorizontal.png') }}" 
                 alt="Malang Creative Center Logo" 
                 class="h-12 w-auto object-contain">
        </a>
        <h1 class="mt-5 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
            Registrasi Akun Pemohon Acara
        </h1>
        <p class="mt-2 text-xs sm:text-sm text-slate-600">
            Sudah memiliki akun terdaftar?
            <a href="/login{{ request()->has('redirect_slot') ? '?redirect_slot=' . request('redirect_slot') : '' }}" class="font-bold text-[#001A72] hover:text-[#001250] hover:underline">Masuk di sini</a>
        </p>
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-2xl px-4 sm:px-0"
         x-data="{
             accountType: '{{ old('account_type', 'instansi') }}',
             isSubmitting: false,
             showPassword: false,
             logoPreview: null,
             handleFile(event) {
                 const file = event.target.files[0];
                 if (file) {
                     if (file.size > 2097152) {
                         alert('Ukuran file maksimal 2MB.');
                         event.target.value = '';
                         this.logoPreview = null;
                         return;
                     }
                     const reader = new FileReader();
                     reader.onload = (e) => {
                         this.logoPreview = e.target.result;
                     };
                     reader.readAsDataURL(file);
                 } else {
                     this.logoPreview = null;
                 }
             }
         }">

        <!-- Form Card Container -->
        <div class="bg-white py-8 px-6 shadow-card border border-slate-200 rounded-brand sm:px-10">

            @if ($errors->any())
                <div class="mb-6 bg-rose-50 border border-rose-200 rounded-brand p-4 text-xs text-rose-900">
                    <div class="flex items-center gap-2 font-bold uppercase tracking-wider mb-1 text-rose-700">
                        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                        <span>Mohon periksa kembali formulir Anda:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-rose-800">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="space-y-6" 
                  action="/register" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  @submit="isSubmitting = true">
                @csrf
                <input type="hidden" name="redirect_slot" value="{{ request('redirect_slot', session('preserved_slot')) }}">

                <!-- 1. Tipe Akun Selector -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2.5">
                        Anda Ingin Mendaftar Sebagai: <span class="text-rose-600">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Instansi -->
                        <label class="relative flex flex-col p-4 rounded-brand border-2 cursor-pointer transition-all duration-200"
                               :class="accountType === 'instansi' ? 'border-[#001A72] bg-[#EEF2FF] shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-slate-50/50'">
                            <input type="radio" name="account_type" value="instansi" class="sr-only" x-model="accountType">
                            <div class="flex items-center justify-between mb-2">
                                <i data-lucide="building-2" class="w-5 h-5" :class="accountType === 'instansi' ? 'text-[#001A72]' : 'text-slate-400'"></i>
                                <span class="w-4 h-4 rounded-full border flex items-center justify-center"
                                      :class="accountType === 'instansi' ? 'border-[#001A72] bg-[#001A72]' : 'border-slate-300'">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="accountType === 'instansi'"></span>
                                </span>
                            </div>
                            <span class="text-sm font-bold text-slate-900">Instansi</span>
                            <span class="text-[11px] text-slate-500 mt-0.5">Perusahaan, BUMN, Dinas & Lembaga</span>
                        </label>

                        <!-- Kelompok / Komunitas -->
                        <label class="relative flex flex-col p-4 rounded-brand border-2 cursor-pointer transition-all duration-200"
                               :class="accountType === 'komunitas' ? 'border-[#001A72] bg-[#EEF2FF] shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-slate-50/50'">
                            <input type="radio" name="account_type" value="komunitas" class="sr-only" x-model="accountType">
                            <div class="flex items-center justify-between mb-2">
                                <i data-lucide="users" class="w-5 h-5" :class="accountType === 'komunitas' ? 'text-[#001A72]' : 'text-slate-400'"></i>
                                <span class="w-4 h-4 rounded-full border flex items-center justify-center"
                                      :class="accountType === 'komunitas' ? 'border-[#001A72] bg-[#001A72]' : 'border-slate-300'">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="accountType === 'komunitas'"></span>
                                </span>
                            </div>
                            <span class="text-sm font-bold text-slate-900">Kelompok/Komunitas</span>
                            <span class="text-[11px] text-slate-500 mt-0.5">Kreator, Kolektif Seni & EO</span>
                        </label>

                        <!-- Kampus / Sekolah -->
                        <label class="relative flex flex-col p-4 rounded-brand border-2 cursor-pointer transition-all duration-200"
                               :class="accountType === 'sekolah' ? 'border-[#001A72] bg-[#EEF2FF] shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-slate-50/50'">
                            <input type="radio" name="account_type" value="sekolah" class="sr-only" x-model="accountType">
                            <div class="flex items-center justify-between mb-2">
                                <i data-lucide="graduation-cap" class="w-5 h-5" :class="accountType === 'sekolah' ? 'text-[#001A72]' : 'text-slate-400'"></i>
                                <span class="w-4 h-4 rounded-full border flex items-center justify-center"
                                      :class="accountType === 'sekolah' ? 'border-[#001A72] bg-[#001A72]' : 'border-slate-300'">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="accountType === 'sekolah'"></span>
                                </span>
                            </div>
                            <span class="text-sm font-bold text-slate-900">Kampus/Sekolah</span>
                            <span class="text-[11px] text-slate-500 mt-0.5">Universitas, SMA/SMK & BEM</span>
                        </label>
                    </div>
                </div>

                <!-- 2. Section: Identitas Entitas -->
                <div class="border-t border-slate-200 pt-5 space-y-4">
                    <div class="flex items-center gap-2 text-[#001A72]">
                        <i data-lucide="file-badge" class="w-4 h-4"></i>
                        <h3 class="text-xs font-bold uppercase tracking-wider" 
                            x-text="accountType === 'instansi' ? 'Identitas Instansi' : (accountType === 'komunitas' ? 'Identitas Kelompok / Komunitas' : 'Identitas Kampus / Sekolah')">
                        </h3>
                    </div>

                    <!-- Upload Logo / Foto -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5"
                               x-text="accountType === 'instansi' ? 'Logo Instansi' : 'Foto / Logo'">
                        </label>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-brand border border-slate-300 bg-slate-100 flex items-center justify-center overflow-hidden shrink-0">
                                <template x-if="logoPreview">
                                    <img :src="logoPreview" class="w-full h-full object-cover" alt="Preview Logo">
                                </template>
                                <template x-if="!logoPreview">
                                    <i data-lucide="image" class="w-6 h-6 text-slate-400"></i>
                                </template>
                            </div>
                            <div class="flex-1">
                                <input type="file" 
                                       id="logo" 
                                       name="logo" 
                                       accept="image/png, image/jpeg, image/jpg"
                                       @change="handleFile($event)"
                                       class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-brand file:border-0 file:text-xs file:font-bold file:bg-[#EEF2FF] file:text-[#001A72] hover:file:bg-[#E0E7FF] file:cursor-pointer cursor-pointer">
                                <p class="mt-1 text-[11px] text-slate-500">
                                    Pastikan gambar yang akan digunakan jelas, maksimal ukuran 2Mb dengan ekstensi file (*.jpg, *.jpeg, *.png)
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Nama Entitas -->
                    <div>
                        <label for="institution_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            <span x-text="accountType === 'instansi' ? 'Nama Instansi' : (accountType === 'komunitas' ? 'Nama Kelompok/Komunitas' : 'Nama Kampus/Sekolah')"></span>
                            <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative rounded-brand">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="building" class="w-4 h-4"></i>
                            </div>
                            <input id="institution_name" 
                                   name="institution_name" 
                                   type="text" 
                                   required 
                                   value="{{ old('institution_name') }}"
                                   :placeholder="accountType === 'instansi' ? 'cth: PT Kreasi Digital Malang' : (accountType === 'komunitas' ? 'cth: Komunitas Desain Grafis Malang' : 'cth: Universitas Brawijaya')"
                                   class="block w-full pl-10 pr-3 py-2.5 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors">
                        </div>
                    </div>

                    <!-- Khusus Instansi: Website, Kategori, Sub-Sektor, Kota, Kecamatan -->
                    <div x-show="accountType === 'instansi'" x-transition class="space-y-4">
                        <!-- Website -->
                        <div>
                            <label for="website" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Website
                            </label>
                            <div class="relative rounded-brand">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="globe" class="w-4 h-4"></i>
                                </div>
                                <input id="website" 
                                       name="website" 
                                       type="url" 
                                       value="{{ old('website') }}"
                                       placeholder="https://example.com"
                                       class="block w-full pl-10 pr-3 py-2.5 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors">
                            </div>
                        </div>

                        <!-- Kategori & Sub-Sektor -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Kategori <span class="text-rose-600" x-show="accountType === 'instansi'">*</span>
                                </label>
                                <select id="category" name="category" :required="accountType === 'instansi'"
                                        class="block w-full py-2.5 px-3 sm:text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72] text-slate-900 transition-colors">
                                    <option value="">Pilih Kategori</option>
                                    <option value="Agregator" {{ old('category') == 'Agregator' ? 'selected' : '' }}>Agregator</option>
                                    <option value="Usaha/Bisnis" {{ old('category') == 'Usaha/Bisnis' ? 'selected' : '' }}>Usaha / Bisnis (PT/CV)</option>
                                    <option value="Lembaga Pemerintah" {{ old('category') == 'Lembaga Pemerintah' ? 'selected' : '' }}>Lembaga Pemerintah / BUMN</option>
                                    <option value="Lembaga Non-Profit" {{ old('category') == 'Lembaga Non-Profit' ? 'selected' : '' }}>Lembaga Non-Profit / Yayasan</option>
                                    <option value="Agensi Kreatif" {{ old('category') == 'Agensi Kreatif' ? 'selected' : '' }}>Agensi Kreatif / Event Organizer</option>
                                    <option value="Lainnya" {{ old('category') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>

                            <div>
                                <label for="sub_sector" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Sub-Sektor <span class="text-rose-600" x-show="accountType === 'instansi'">*</span>
                                </label>
                                <select id="sub_sector" name="sub_sector" :required="accountType === 'instansi'"
                                        class="block w-full py-2.5 px-3 sm:text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72] text-slate-900 transition-colors">
                                    <option value="">Pilih Sub-Sektor</option>
                                    <option value="Aplikasi" {{ old('sub_sector') == 'Aplikasi' ? 'selected' : '' }}>Aplikasi & Pengembangan Digital</option>
                                    <option value="Game" {{ old('sub_sector') == 'Game' ? 'selected' : '' }}>Game & Animasi Interaktif</option>
                                    <option value="Arsitektur" {{ old('sub_sector') == 'Arsitektur' ? 'selected' : '' }}>Arsitektur</option>
                                    <option value="Desain Interior" {{ old('sub_sector') == 'Desain Interior' ? 'selected' : '' }}>Desain Interior</option>
                                    <option value="DKV" {{ old('sub_sector') == 'DKV' ? 'selected' : '' }}>Desain Komunikasi Visual (DKV)</option>
                                    <option value="Desain Produk" {{ old('sub_sector') == 'Desain Produk' ? 'selected' : '' }}>Desain Produk</option>
                                    <option value="Film Animasi Video" {{ old('sub_sector') == 'Film Animasi Video' ? 'selected' : '' }}>Film, Animasi & Video</option>
                                    <option value="Fotografi" {{ old('sub_sector') == 'Fotografi' ? 'selected' : '' }}>Fotografi</option>
                                    <option value="Kriya" {{ old('sub_sector') == 'Kriya' ? 'selected' : '' }}>Kriya (Handicraft)</option>
                                    <option value="Kuliner" {{ old('sub_sector') == 'Kuliner' ? 'selected' : '' }}>Kuliner</option>
                                    <option value="Musik" {{ old('sub_sector') == 'Musik' ? 'selected' : '' }}>Musik</option>
                                    <option value="Fesyen" {{ old('sub_sector') == 'Fesyen' ? 'selected' : '' }}>Fesyen</option>
                                    <option value="Penerbitan" {{ old('sub_sector') == 'Penerbitan' ? 'selected' : '' }}>Penerbitan & Literasi</option>
                                    <option value="Periklanan" {{ old('sub_sector') == 'Periklanan' ? 'selected' : '' }}>Periklanan</option>
                                    <option value="Seni Pertunjukan" {{ old('sub_sector') == 'Seni Pertunjukan' ? 'selected' : '' }}>Seni Pertunjukan & Teater</option>
                                    <option value="Seni Rupa" {{ old('sub_sector') == 'Seni Rupa' ? 'selected' : '' }}>Seni Rupa</option>
                                    <option value="Televisi & Radio" {{ old('sub_sector') == 'Televisi & Radio' ? 'selected' : '' }}>Televisi & Radio / Broadcasting</option>
                                </select>
                            </div>
                        </div>

                        <!-- Kota & Kecamatan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="city" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Kota <span class="text-rose-600" x-show="accountType === 'instansi'">*</span>
                                </label>
                                <select id="city" name="city" :required="accountType === 'instansi'"
                                        class="block w-full py-2.5 px-3 sm:text-sm border-slate-300 rounded-brand focus:ring-2 focus:ring-[#001A72] focus:border-[#001A72] text-slate-900 transition-colors">
                                    <option value="">Pilih Kota</option>
                                    <option value="Kota Malang" {{ old('city') == 'Kota Malang' ? 'selected' : '' }}>Kota Malang</option>
                                    <option value="Kabupaten Malang" {{ old('city') == 'Kabupaten Malang' ? 'selected' : '' }}>Kabupaten Malang</option>
                                    <option value="Kota Batu" {{ old('city') == 'Kota Batu' ? 'selected' : '' }}>Kota Batu</option>
                                    <option value="Kota Surabaya" {{ old('city') == 'Kota Surabaya' ? 'selected' : '' }}>Kota Surabaya</option>
                                    <option value="Luar Jawa Timur" {{ old('city') == 'Luar Jawa Timur' ? 'selected' : '' }}>Luar Jawa Timur</option>
                                </select>
                            </div>

                            <div>
                                <label for="district" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Kecamatan <span class="text-rose-600" x-show="accountType === 'instansi'">*</span>
                                </label>
                                <input id="district" 
                                       name="district" 
                                       type="text" 
                                       :required="accountType === 'instansi'"
                                       value="{{ old('district') }}"
                                       placeholder="cth: Blimbing / Klojen"
                                       class="block w-full py-2.5 px-3 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors">
                            </div>
                        </div>
                    </div>

                    <!-- Alamat Lengkap -->
                    <div>
                        <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Alamat <span class="text-rose-600">*</span>
                        </label>
                        <textarea id="address" 
                                  name="address" 
                                  rows="2" 
                                  required 
                                  placeholder="Jl. Jenderal A. Yani No. 53, Blimbing, Kota Malang..."
                                  class="block w-full p-3 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors">{{ old('address') }}</textarea>
                    </div>

                    <!-- Deskripsi Singkat -->
                    <div>
                        <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Deskripsi <span class="text-slate-400 font-normal">(Tidak wajib diisi)</span>
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="2" 
                                  placeholder="Jelaskan fokus kegiatan atau profil singkat..."
                                  class="block w-full p-3 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors">{{ old('description') }}</textarea>
                    </div>
                </div>

                <!-- 3. Section: Kontak & Penanggung Jawab (PIC) -->
                <div class="border-t border-slate-200 pt-5 space-y-4">
                    <div class="flex items-center gap-2 text-[#001A72]">
                        <i data-lucide="contact-2" class="w-4 h-4"></i>
                        <h3 class="text-xs font-bold uppercase tracking-wider">Kontak & Penanggung Jawab (PIC)</h3>
                    </div>

                    <div>
                        <label for="full_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nama Lengkap PIC <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative rounded-brand">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </div>
                            <input id="full_name" 
                                   name="full_name" 
                                   type="text" 
                                   required 
                                   value="{{ old('full_name') }}"
                                   placeholder="cth: Ahmad Fauzi"
                                   class="block w-full pl-10 pr-3 py-2.5 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Email <span class="text-rose-600">*</span>
                            </label>
                            <div class="relative rounded-brand">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="mail" class="w-4 h-4"></i>
                                </div>
                                <input id="email" 
                                       name="email" 
                                       type="email" 
                                       required 
                                       value="{{ old('email') }}"
                                       placeholder="pic@instansi.id"
                                       class="block w-full pl-10 pr-3 py-2.5 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors">
                            </div>
                        </div>

                        <div>
                            <label for="phone_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Nomor Telepon <span class="text-rose-600">*</span>
                            </label>
                            <div class="relative rounded-brand">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="phone" class="w-4 h-4"></i>
                                </div>
                                <input id="phone_number" 
                                       name="phone_number" 
                                       type="tel" 
                                       required 
                                       value="{{ old('phone_number') }}"
                                       placeholder="+62 8341..."
                                       class="block w-full pl-10 pr-3 py-2.5 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Section: Keamanan Kata Sandi -->
                <div class="border-t border-slate-200 pt-5 space-y-4">
                    <div class="flex items-center gap-2 text-[#001A72]">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                        <h3 class="text-xs font-bold uppercase tracking-wider">Kata Sandi</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Kata Sandi <span class="text-rose-600">*</span>
                            </label>
                            <div class="relative rounded-brand">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="lock" class="w-4 h-4"></i>
                                </div>
                                <input :type="showPassword ? 'text' : 'password'" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       placeholder="Min. 8 karakter"
                                       class="block w-full pl-10 pr-10 py-2.5 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors">
                                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                    <i x-show="!showPassword" data-lucide="eye" class="w-4 h-4"></i>
                                    <i x-show="showPassword" data-lucide="eye-off" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Konfirmasi Kata Sandi <span class="text-rose-600">*</span>
                            </label>
                            <div class="relative rounded-brand">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="lock" class="w-4 h-4"></i>
                                </div>
                                <input :type="showPassword ? 'text' : 'password'" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required 
                                       placeholder="Ulangi kata sandi"
                                       class="block w-full pl-10 pr-10 py-2.5 sm:text-sm rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. Section: Social Media (Opsional) -->
                <div class="border-t border-slate-200 pt-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-[#001A72]">
                            <i data-lucide="share-2" class="w-4 h-4"></i>
                            <h3 class="text-xs font-bold uppercase tracking-wider">Social Media</h3>
                        </div>
                        <span class="text-[11px] text-slate-400 font-medium">Tidak wajib diisi</span>
                    </div>

                    <div class="space-y-3">
                        <!-- Facebook -->
                        <div>
                            <label for="facebook" class="block text-xs font-semibold text-slate-700 mb-1">Facebook</label>
                            <input id="facebook" 
                                   name="facebook" 
                                   type="text" 
                                   value="{{ old('facebook') }}"
                                   placeholder="https://www.facebook.com/..."
                                   class="block w-full px-3 py-2 sm:text-xs rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors">
                        </div>

                        <!-- Instagram -->
                        <div>
                            <label for="instagram" class="block text-xs font-semibold text-slate-700 mb-1">Instagram</label>
                            <input id="instagram" 
                                   name="instagram" 
                                   type="text" 
                                   value="{{ old('instagram') }}"
                                   placeholder="https://www.instagram.com/..."
                                   class="block w-full px-3 py-2 sm:text-xs rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors">
                        </div>

                        <!-- Twitter / X -->
                        <div>
                            <label for="twitter" class="block text-xs font-semibold text-slate-700 mb-1">Twitter / X</label>
                            <input id="twitter" 
                                   name="twitter" 
                                   type="text" 
                                   value="{{ old('twitter') }}"
                                   placeholder="https://www.twitter.com/..."
                                   class="block w-full px-3 py-2 sm:text-xs rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors">
                        </div>

                        <!-- TikTok -->
                        <div>
                            <label for="tiktok" class="block text-xs font-semibold text-slate-700 mb-1">TikTok</label>
                            <input id="tiktok" 
                                   name="tiktok" 
                                   type="text" 
                                   value="{{ old('tiktok') }}"
                                   placeholder="https://www.tiktok.com/..."
                                   class="block w-full px-3 py-2 sm:text-xs rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors">
                        </div>

                        <!-- YouTube -->
                        <div>
                            <label for="youtube" class="block text-xs font-semibold text-slate-700 mb-1">YouTube</label>
                            <input id="youtube" 
                                   name="youtube" 
                                   type="text" 
                                   value="{{ old('youtube') }}"
                                   placeholder="https://www.youtube.com/..."
                                   class="block w-full px-3 py-2 sm:text-xs rounded-brand border border-slate-300 text-slate-900 focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72] placeholder:text-slate-400 transition-colors">
                        </div>
                    </div>
                </div>

                <!-- 6. Persetujuan Syarat & KTP PIC -->
                <div class="bg-amber-50/80 border border-amber-200 rounded-brand p-4 flex items-start gap-3">
                    <input id="terms" name="terms" type="checkbox" required class="mt-0.5 h-4 w-4 rounded-xs border-slate-300 text-[#001A72] focus:ring-[#001A72]">
                    <label for="terms" class="text-xs text-amber-950 leading-relaxed font-medium">
                        Saya menyetujui <a href="/panduan" target="_blank" class="text-[#001A72] underline font-bold">SOP Fasilitas MCC</a> dan bersedia menyerahkan <strong>KTP Asli PIC</strong> pada saat check-in di Front Office MCC pada hari kegiatan.
                    </label>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white text-sm font-bold shadow-md hover:shadow-lg transition-all duration-200"
                            :disabled="isSubmitting">
                        <span x-show="!isSubmitting" class="flex items-center justify-center gap-2">
                            <span>Selesaikan Pendaftaran Akun</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </span>
                        <span x-show="isSubmitting" class="flex items-center justify-center gap-2" style="display: none;">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Menyimpan Data Akun...</span>
                        </span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <div class="mt-8 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Malang Creative Center. Portal Pemohon Acara & Reservasi Gedung.
    </div>

</body>
</html>