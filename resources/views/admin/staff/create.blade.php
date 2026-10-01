@extends('layouts.admin')

@section('title', 'Tambah Akun Staf - Super Admin MCC')
@section('page_title', 'Tambah Akun Staf Baru')
@section('header_title', 'Tambah Akun Staf Baru')
@section('header_subtitle', 'Pendaftaran identitas staf operasional dan penugasan pos divisi kerja MCC.')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb / Back Button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-navy-900 transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Staf
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-lg">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h4 class="text-xs font-bold text-rose-800 uppercase tracking-wider">Terdapat Kesalahan Validasi</h4>
                    <ul class="mt-1 text-xs text-rose-700 list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.staff.store') }}" method="POST" class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
        @csrf

        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-sm font-bold text-navy-900 uppercase tracking-wider">Informasi Profil & Penugasan Staf</h3>
            <p class="text-xs text-slate-500 mt-0.5">Pastikan nomor WhatsApp dan email aktif untuk pengiriman kredensial sistem.</p>
        </div>

        <div class="p-6 space-y-6">
            <!-- Grid Identitas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Lengkap Pegawai <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        placeholder="Contoh: Budi Prasetyo"
                        class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-900 focus:ring-navy-900 placeholder:text-slate-400">
                    @error('name')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alamat Email Resmi <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        placeholder="staf.divisi@malangcreativecenter.id"
                        class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-900 focus:ring-navy-900 placeholder:text-slate-400">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nomor WhatsApp / Seluler <span class="text-rose-500">*</span>
                    </label>
                    <input type="tel" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required
                        placeholder="081234567890"
                        class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-900 focus:ring-navy-900 placeholder:text-slate-400">
                    @error('phone_number')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-[11px] text-slate-400">Format: 08xx atau 628xx (8-15 digit).</p>
                </div>

                <div>
                    <label for="role" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Penetapan Divisi & Hak Akses <span class="text-rose-500">*</span>
                    </label>
                    <select id="role" name="role" required
                        class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-900 focus:ring-navy-900">
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>Pilih Posisi / Divisi Staf</option>
                        <option value="marketing" {{ old('role') === 'marketing' ? 'selected' : '' }}>
                            Marketing (Kurasi Proposal, Skema Biaya, Triase)
                        </option>
                        <option value="front_office" {{ old('role') === 'front_office' ? 'selected' : '' }}>
                            Front Office (Manifes Harian, QR Scanner Check-in, Kustodian KTP)
                        </option>
                        <option value="duty_officer" {{ old('role') === 'duty_officer' ? 'selected' : '' }}>
                            Duty Officer (Inspeksi Lapangan, Checklist Clearance Mobile)
                        </option>
                        <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>
                            Super Admin (Tata Kelola Sistem Penuh)
                        </option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Role Scope Info Cards -->
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 space-y-2.5">
                <div class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Ringkasan Hak Akses Divisi:</div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div class="p-2.5 bg-white rounded border border-slate-200">
                        <div class="font-bold text-navy-900">Divisi Marketing</div>
                        <p class="text-[11px] text-slate-500 mt-1">Akses kurasi proposal acara, verifikasi kelayakan perizinan, dan penerbitan skema tarif PAD/retribusi.</p>
                    </div>
                    <div class="p-2.5 bg-white rounded border border-slate-200">
                        <div class="font-bold text-emerald-800">Front Office</div>
                        <p class="text-[11px] text-slate-500 mt-1">Pemindaian QR check-in penyewa, manifes harian operasional, dan pengelolaan penitipan identitas KTP.</p>
                    </div>
                    <div class="p-2.5 bg-white rounded border border-slate-200">
                        <div class="font-bold text-amber-800">Duty Officer</div>
                        <p class="text-[11px] text-slate-500 mt-1">Pemeriksaan fisik sarana prasarana sebelum/sesudah acara, input checklist kerusakan, dan sinyal checkout.</p>
                    </div>
                </div>
            </div>

            <!-- Grid Kata Sandi -->
            <div class="border-t border-slate-100 pt-5">
                <h4 class="text-xs font-bold text-navy-900 uppercase tracking-wider mb-4">Pengaturan Kata Sandi Awal</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kata Sandi <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" id="password" name="password" required
                            placeholder="Minimal 8 karakter"
                            class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-900 focus:ring-navy-900">
                        @error('password')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            placeholder="Ulangi kata sandi"
                            class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-900 focus:ring-navy-900">
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.staff.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-200 rounded-lg transition-colors">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-navy-900 hover:bg-navy-800 rounded-lg shadow-xs transition-colors">
                Simpan & Daftarkan Staf
            </button>
        </div>
    </form>
</div>
@endsection
