@extends('layouts.admin')

@section('title', 'Manajemen Akun Staf Lintas Divisi - Super Admin MCC')
@section('page_title', 'Manajemen Akun Staf Lintas Divisi')

@section('content')
<div class="space-y-4" 
     x-data="{ 
         resetModalOpen: false, 
         staffToReset: null, 
         resetUrl: '',
         toggleModalOpen: false,
         staffToToggle: null,
         toggleAction: 'deactivate',
         toggleUrl: '',
         deleteModalOpen: false,
         staffToDelete: null,
         deleteUrl: ''
     }">
    
    <!-- Top Action Card: Info & Create Button -->
    <div class="bg-[#001A72] text-white p-4 sm:p-5 rounded-brand border border-indigo-900 shadow-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-white/20 text-white">
                    FR-ADM-03
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-400 text-slate-950">
                    Hak Akses Tim MCC
                </span>
            </div>
            <h2 class="text-base sm:text-lg font-black tracking-tight">
                Administrasi Pengguna Internal Gedung MCC
            </h2>
            <p class="text-indigo-200 text-xs mt-0.5 max-w-2xl">
                Pengelolaan akun staf operasional lintas divisi: Marketing &amp; Kurasi, Front Office / Kustodian KTP, Duty Officer Lapangan, dan Super Administrator.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('admin.staff.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-brand bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs shadow-md transition active:scale-95 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>Tambah Akun Staf Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar (Inline Single-Row) -->
    <div class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 space-y-3 w-full">
        <form method="GET" action="{{ route('admin.staff.index') }}" x-ref="filterForm" class="space-y-3 w-full">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full">
                
                <!-- 1. Search Bar (Memanjang fleksibel memenuhi ruang sampai batas dropdown) -->
                <div class="relative flex-1 min-w-0">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           @input.debounce.400ms="$refs.filterForm.submit()"
                           placeholder="Cari nama staf, email dinas, atau nomor WhatsApp..." 
                           class="block w-full pl-10 pr-3.5 py-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] placeholder:text-slate-400 bg-white transition">
                </div>

                <!-- 2. Filter Divisi Role (Sebelah Kanan Searchbar) -->
                <div class="w-full sm:w-48 shrink-0">
                    <select name="role" 
                            @change="$refs.filterForm.submit()"
                            class="block w-full py-2.5 px-3 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-700 font-medium cursor-pointer bg-white">
                        <option value="all" {{ request('role') === 'all' ? 'selected' : '' }}>Semua Divisi</option>
                        <option value="marketing" {{ request('role') === 'marketing' ? 'selected' : '' }}>Marketing &amp; Kurasi</option>
                        <option value="front_office" {{ request('role') === 'front_office' ? 'selected' : '' }}>Front Office (FO)</option>
                        <option value="duty_officer" {{ request('role') === 'duty_officer' ? 'selected' : '' }}>Duty Officer (DO)</option>
                        <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                </div>

                <!-- 3. Filter Status Akun (Sebelah Kanan Divisi / Paling Kanan) -->
                <div class="w-full sm:w-48 shrink-0">
                    <select name="status" 
                            @change="$refs.filterForm.submit()"
                            class="block w-full py-2.5 px-3 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 font-medium cursor-pointer bg-white">
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif / Mutasi</option>
                    </select>
                </div>

                <!-- 4. Tombol Reset Cepat -->
                @if(request()->hasAny(['search', 'role', 'status']) && (request('search') || (request('role') && request('role') !== 'all') || (request('status') && request('status') !== 'all')))
                    <div class="shrink-0">
                        <a href="{{ route('admin.staff.index') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-brand text-xs font-bold text-rose-600 hover:text-white hover:bg-rose-600 bg-rose-50 border border-rose-200 transition shadow-2xs"
                           title="Reset Semua Filter">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            <span>Reset</span>
                        </a>
                    </div>
                @endif

            </div>

            <!-- Active Chips -->
            @if(request()->hasAny(['search', 'role', 'status']) && (request('search') || (request('role') && request('role') !== 'all') || (request('status') && request('status') !== 'all')))
                <div class="flex items-center gap-2 pt-2 border-t border-slate-100 text-xs flex-wrap">
                    <span class="text-slate-400 text-[11px] font-bold">Filter Aktif:</span>
                    @if(request('search'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100">
                            Pencarian: "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('role') && request('role') !== 'all')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100 uppercase">
                            Divisi: {{ request('role') }}
                        </span>
                    @endif
                    @if(request('status') && request('status') !== 'all')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100 uppercase">
                            Status: {{ request('status') }}
                        </span>
                    @endif
                </div>
            @endif
        </form>
    </div>

    <!-- Staff Accounts Table Container -->
    @if($staffs->count() > 0)
        <div class="bg-white rounded-brand border border-slate-200 shadow-card overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] border-b border-slate-200 tracking-wider">
                        <tr>
                            <th class="py-3 px-4 min-w-[220px]">Nama Staf &amp; Kontak</th>
                            <th class="py-3 px-3">Divisi / Pos Kerja</th>
                            <th class="py-3 px-3">Kewenangan Akses Sistem</th>
                            <th class="py-3 px-3 text-center">Status Akun</th>
                            <th class="py-3 px-3">Terdaftar Sejak</th>
                            <th class="py-3 px-4 text-right">Aksi Kelola</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-800">
                        @foreach($staffs as $stf)
                            <tr class="hover:bg-slate-50/80 transition">
                                
                                <!-- 1. Nama Staf & Kontak Personal -->
                                <td class="py-3.5 px-4 align-middle">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-brand bg-[#001A72] text-white flex items-center justify-center font-black text-xs shrink-0 shadow-2xs mt-0.5">
                                            {{ strtoupper(substr($stf->full_name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0 max-w-[240px]">
                                            <span class="font-black text-slate-900 block text-xs sm:text-sm leading-snug break-words">
                                                {{ $stf->full_name }}
                                            </span>
                                            <div class="text-[11px] text-slate-500 space-y-0.5 mt-0.5">
                                                <span class="block truncate font-mono text-slate-600">{{ $stf->email }}</span>
                                                <span class="block text-slate-500">{{ $stf->phone_number }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- 2. Divisi / Role Badge -->
                                <td class="py-3.5 px-3 whitespace-nowrap align-middle">
                                    @if($stf->role === \App\Enums\UserRole::MARKETING)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-black bg-indigo-50 text-[#001A72] border border-indigo-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#001A72]"></span>
                                            <span>Marketing &amp; Kurasi</span>
                                        </span>
                                    @elseif($stf->role === \App\Enums\UserRole::FRONT_OFFICE)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-black bg-blue-50 text-blue-800 border border-blue-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                            <span>Front Office (FO)</span>
                                        </span>
                                    @elseif($stf->role === \App\Enums\UserRole::DUTY_OFFICER)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-black bg-amber-50 text-amber-800 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                            <span>Duty Officer (DO)</span>
                                        </span>
                                    @elseif($stf->role === \App\Enums\UserRole::SUPER_ADMIN)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-black bg-purple-50 text-purple-800 border border-purple-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>
                                            <span>Super Administrator</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- 3. Kewenangan Akses Sistem -->
                                <td class="py-3.5 px-3 align-middle">
                                    <div class="text-[11px] text-slate-600 space-y-0.5 max-w-[230px]">
                                        @if($stf->role === \App\Enums\UserRole::MARKETING)
                                            <p class="leading-tight font-medium">Kurasi proposal acara, penentuan skema tarif, dan inbox antrean kurasi.</p>
                                        @elseif($stf->role === \App\Enums\UserRole::FRONT_OFFICE)
                                            <p class="leading-tight font-medium">Manifes harian, QR scanner check-in, dan kustodian KTP fisik.</p>
                                        @elseif($stf->role === \App\Enums\UserRole::DUTY_OFFICER)
                                            <p class="leading-tight font-medium">Inspeksi fisik checklist aset ruangan &amp; clearance izin checkout.</p>
                                        @elseif($stf->role === \App\Enums\UserRole::SUPER_ADMIN)
                                            <p class="leading-tight font-medium">Akses penuh sistem, master ruangan, layout, dan akun staf.</p>
                                        @endif
                                    </div>
                                </td>

                                <!-- 4. Status Akun (Aktif / Nonaktif Mutasi) -->
                                <td class="py-3.5 px-3 text-center whitespace-nowrap align-middle">
                                    @if(!$stf->is_suspended)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                            <span>Aktif Bertugas</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-300"
                                              title="{{ $stf->suspension_reason ?? 'Dinonaktifkan' }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                            <span>Nonaktif / Mutasi</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- 5. Terdaftar Sejak -->
                                <td class="py-3.5 px-3 whitespace-nowrap text-[11px] text-slate-500 align-middle">
                                    {{ $stf->created_at ? $stf->created_at->translatedFormat('d M Y') : '-' }}
                                </td>

                                <!-- 6. Aksi CRUD & Reset -->
                                <td class="py-3.5 px-4 text-right whitespace-nowrap align-middle">
                                    <div class="inline-flex items-center gap-1">
                                        
                                        <!-- Reset Password Button Trigger -->
                                        <button type="button" 
                                                @click="staffToReset = '{{ addslashes($stf->full_name) }}'; resetUrl = '{{ route('admin.staff.reset_password', $stf) }}'; resetModalOpen = true"
                                                class="p-1.5 rounded-brand bg-indigo-50 hover:bg-indigo-100 text-[#001A72] transition cursor-pointer"
                                                title="Reset Password Akun">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                        </button>

                                        <!-- Edit Profil Button -->
                                        <a href="{{ route('admin.staff.edit', $stf) }}" 
                                           class="p-1.5 rounded-brand bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
                                           title="Edit Profil &amp; Role Divisi">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                        </a>

                                        @if($stf->id !== auth()->id())
                                            <!-- Toggle Status (Deactivate / Reactivate) Trigger -->
                                            <button type="button" 
                                                    @click="staffToToggle = '{{ addslashes($stf->full_name) }}'; toggleAction = '{{ $stf->is_suspended ? 'activate' : 'deactivate' }}'; toggleUrl = '{{ route('admin.staff.toggle_status', $stf) }}'; toggleModalOpen = true"
                                                    class="p-1.5 rounded-brand transition cursor-pointer {{ $stf->is_suspended ? 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700' : 'bg-amber-50 hover:bg-amber-100 text-amber-700' }}"
                                                    title="{{ $stf->is_suspended ? 'Aktifkan Kembali Akun' : 'Nonaktifkan Akun (Mutasi/Berhenti)' }}">
                                                @if($stf->is_suspended)
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                                @endif
                                            </button>

                                            <!-- Delete Button Trigger -->
                                            <button type="button" 
                                                    @click="staffToDelete = '{{ addslashes($stf->full_name) }}'; deleteUrl = '{{ route('admin.staff.destroy', $stf) }}'; deleteModalOpen = true"
                                                    class="p-1.5 rounded-brand bg-rose-50 hover:bg-rose-100 text-rose-600 transition cursor-pointer"
                                                    title="Hapus Akun Staf">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                            </button>
                                        @endif

                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div class="p-3.5 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <span class="text-[11px] text-slate-500 font-medium">
                    Menampilkan <strong>{{ $staffs->firstItem() ?? 0 }}</strong> - <strong>{{ $staffs->lastItem() ?? 0 }}</strong> dari <strong>{{ $staffs->total() }}</strong> akun staf
                </span>
                <div>
                    {{ $staffs->links() }}
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-brand border border-slate-200 p-8 text-center max-w-lg mx-auto space-y-3">
            <div class="w-12 h-12 mx-auto rounded-full bg-indigo-50 text-[#001A72] flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-sm sm:text-base font-black text-slate-900">Tidak ada akun staf ditemukan</h3>
                <p class="text-xs text-slate-500">Coba ubah kata kunci pencarian atau reset filter divisi role.</p>
            </div>
            <a href="{{ route('admin.staff.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-brand bg-[#001A72] text-white font-bold text-xs shadow-md">
                <span>Tambah Akun Staf Baru</span>
            </a>
        </div>
    @endif

    <!-- ======================================================== -->
    <!-- MODAL 1: RESET PASSWORD AKUN STAF (FR-ADM-03) -->
    <!-- ======================================================== -->
    <div x-show="resetModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         role="dialog" 
         aria-modal="true">
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity" @click="resetModalOpen = false"></div>
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-md p-5 border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <div>
                        <span class="text-[10px] font-mono font-bold text-indigo-600 block">FR-ADM-03</span>
                        <h3 class="text-sm sm:text-base font-black text-slate-900">Reset Password Akun Staf</h3>
                    </div>
                    <button type="button" @click="resetModalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <p class="text-xs text-slate-600">
                    Masukkan kata sandi baru untuk akun staf <strong class="text-slate-900" x-text="staffToReset"></strong>:
                </p>

                <form :action="resetUrl" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">
                            Kata Sandi Baru <span class="text-rose-500">*</span>:
                        </label>
                        <input type="password" 
                               name="password" 
                               required 
                               placeholder="Minimal 8 karakter (huruf besar, kecil, angka)" 
                               class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72]">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">
                            Ulangi Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>:
                        </label>
                        <input type="password" 
                               name="password_confirmation" 
                               required 
                               placeholder="Ketik ulang kata sandi baru" 
                               class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72]">
                    </div>

                    <div class="pt-2 flex items-center gap-2">
                        <button type="submit" 
                                class="flex-1 py-2 px-3 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white font-black text-xs shadow-md transition cursor-pointer">
                            Simpan Password Baru
                        </button>
                        <button type="button" 
                                @click="resetModalOpen = false" 
                                class="py-2 px-3 rounded-brand bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 2: NONAKTIFKAN / AKTIFKAN STATUS AKUN STAF (FR-ADM-03) -->
    <!-- ======================================================== -->
    <div x-show="toggleModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         role="dialog" 
         aria-modal="true">
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity" @click="toggleModalOpen = false"></div>
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-md p-5 border border-slate-200 space-y-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center mx-auto"
                     :class="toggleAction === 'deactivate' ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>

                <div class="text-center space-y-1">
                    <h3 class="text-sm sm:text-base font-black text-slate-900"
                        x-text="toggleAction === 'deactivate' ? 'Nonaktifkan Akun Staf?' : 'Aktifkan Kembali Akun Staf?'">
                    </h3>
                    <p class="text-xs text-slate-600">
                        <span x-show="toggleAction === 'deactivate'">
                            Staf <strong class="text-slate-900" x-text="staffToToggle"></strong> yang dimutasi atau berhenti bertugas tidak akan dapat login lagi ke portal MCC.
                        </span>
                        <span x-show="toggleAction === 'activate'">
                            Staf <strong class="text-slate-900" x-text="staffToToggle"></strong> akan diberikan kembali izin akses operasional sistem.
                        </span>
                    </p>
                </div>

                <form :action="toggleUrl" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')

                    <div x-show="toggleAction === 'deactivate'">
                        <label class="text-[11px] font-bold text-slate-700 block mb-1">
                            Alasan Penonaktifan / Catatan Mutasi:
                        </label>
                        <textarea name="suspension_reason" 
                                  rows="2" 
                                  placeholder="Contoh: Mutasi ke Dinas Lain, Berhenti Bertugas, dsb..." 
                                  class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72]"></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <button type="submit" 
                                :class="toggleAction === 'deactivate' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700'"
                                class="flex-1 py-2 px-3 rounded-brand text-white font-black text-xs shadow-md transition cursor-pointer">
                            <span x-text="toggleAction === 'deactivate' ? 'Konfirmasi Nonaktifkan' : 'Konfirmasi Aktifkan'"></span>
                        </button>
                        <button type="button" 
                                @click="toggleModalOpen = false" 
                                class="flex-1 py-2 px-3 rounded-brand bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 3: HAPUS AKUN STAF PERMANEN (FR-ADM-03) -->
    <!-- ======================================================== -->
    <div x-show="deleteModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         role="dialog" 
         aria-modal="true">
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity" @click="deleteModalOpen = false"></div>
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-md p-5 border border-slate-200 space-y-4">
                <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                </div>

                <div class="text-center space-y-1">
                    <h3 class="text-sm sm:text-base font-black text-slate-900">Hapus Akun Staf?</h3>
                    <p class="text-xs text-slate-600">
                        Apakah Anda yakin ingin menghapus akun staf <strong class="text-slate-900" x-text="staffToDelete"></strong>? Tindakan ini akan menghapus akses akun dari sistem secara permanen.
                    </p>
                </div>

                <form :action="deleteUrl" method="POST" class="flex items-center gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="flex-1 py-2 px-3 rounded-brand bg-rose-600 hover:bg-rose-700 text-white font-black text-xs shadow-md transition cursor-pointer">
                        Hapus Permanen
                    </button>
                    <button type="button" 
                            @click="deleteModalOpen = false" 
                            class="flex-1 py-2 px-3 rounded-brand bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
