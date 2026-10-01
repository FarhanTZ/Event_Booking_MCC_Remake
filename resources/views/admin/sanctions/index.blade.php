@extends('layouts.admin')

@section('title', 'Penegakan Sanksi & Blacklist Pemohon - Super Admin MCC')
@section('page_title', 'Penegakan Sanksi & Blacklist Pemohon')

@section('content')
<div class="space-y-4" 
     x-data="{ 
         suspendModalOpen: false, 
         userToSuspend: null, 
         suspendUrl: '',
         liftModalOpen: false,
         userToLift: null,
         liftUrl: '',
         detailModalOpen: false,
         applicantDetail: null
     }">
    
    <!-- Top Action Card: Info -->
    <div class="bg-[#001A72] text-white p-4 sm:p-5 rounded-brand border border-indigo-900 shadow-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-white/20 text-white">
                    FR-ADM-04
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-400 text-slate-950">
                    Kedisiplinan &amp; Sanksi
                </span>
            </div>
            <h2 class="text-base sm:text-lg font-black tracking-tight">
                Penegakan Sanksi &amp; Daftar Hitam (Blacklist) Pemohon
            </h2>
            <p class="text-indigo-200 text-xs mt-0.5 max-w-2xl">
                Pengawasan kedisiplinan pemohon, pembekuan akun pelanggar tata tertib / panitia kabur (<span class="font-mono text-white">abandoned incident</span>), dan pemulihan akses setelah penyelesaian ganti rugi resmi.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <div class="px-3.5 py-2 rounded-brand bg-white/10 border border-white/15 text-xs font-semibold flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-rose-400 animate-pulse"></span>
                <span>{{ $metrics['total_suspended'] ?? 0 }} Akun Dibekukan</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar (Inline Single-Row) -->
    <div class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 space-y-3 w-full">
        <form method="GET" action="{{ route('admin.sanctions.index') }}" x-ref="filterForm" class="space-y-3 w-full">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full">
                
                <!-- 1. Search Bar -->
                <div class="relative flex-1 min-w-0">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           @input.debounce.400ms="$refs.filterForm.submit()"
                           placeholder="Cari nama pemohon, instansi, email, nomor HP, atau alasan sanksi..." 
                           class="block w-full pl-10 pr-3.5 py-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] placeholder:text-slate-400 bg-white transition">
                </div>

                <!-- 2. Filter Status Kasus / Sanksi (Sebelah Kanan Searchbar) -->
                <div class="w-full sm:w-48 shrink-0">
                    <select name="status" 
                            @change="$refs.filterForm.submit()"
                            class="block w-full py-2.5 px-3 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 font-medium cursor-pointer bg-white">
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status ({{ $metrics['total_applicants'] ?? 0 }})</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Blacklist ({{ $metrics['total_suspended'] ?? 0 }})</option>
                        <option value="abandoned" {{ request('status') === 'abandoned' ? 'selected' : '' }}>Abandoned ({{ $metrics['pending_abandoned'] ?? 0 }})</option>
                        <option value="incident" {{ request('status') === 'incident' ? 'selected' : '' }}>Kerusakan ({{ $metrics['active_incidents'] ?? 0 }})</option>
                    </select>
                </div>

                <!-- 3. Tombol Reset Cepat -->
                @if(request()->hasAny(['search', 'status']) && (request('search') || (request('status') && request('status') !== 'all')))
                    <div class="shrink-0">
                        <a href="{{ route('admin.sanctions.index') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-brand text-xs font-bold text-rose-600 hover:text-white hover:bg-rose-600 bg-rose-50 border border-rose-200 transition shadow-2xs"
                           title="Reset Semua Filter">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            <span>Reset</span>
                        </a>
                    </div>
                @endif

            </div>

            <!-- Active Chips -->
            @if(request()->hasAny(['search', 'status']) && (request('search') || (request('status') && request('status') !== 'all')))
                <div class="flex items-center gap-2 pt-2 border-t border-slate-100 text-xs flex-wrap">
                    <span class="text-slate-400 text-[11px] font-bold">Filter Aktif:</span>
                    @if(request('search'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100">
                            Pencarian: "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('status') && request('status') !== 'all')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100 uppercase">
                            Filter: {{ request('status') }}
                        </span>
                    @endif
                </div>
            @endif
        </form>
    </div>

    <!-- Applicants Queue Table -->
    @if($applicants->count() > 0)
        <div class="bg-white rounded-brand border border-slate-200 shadow-card overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] border-b border-slate-200 tracking-wider">
                        <tr>
                            <th class="py-3 px-4 min-w-[240px]">Pemohon &amp; Entitas</th>
                            <th class="py-3 px-3">Kontak / WhatsApp</th>
                            <th class="py-3 px-3 text-center">Status Akses</th>
                            <th class="py-3 px-3 text-center">Riwayat Kasus</th>
                            <th class="py-3 px-3 min-w-[200px]">Catatan Sanksi / Alasan</th>
                            <th class="py-3 px-4 text-right">Aksi Kedisiplinan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-800">
                        @foreach($applicants as $applicant)
                            @php
                                $abandonedCount = $applicant->bookings->where('status', \App\Enums\BookingStatus::ABANDONED_INCIDENT)->count();
                                $unresolvedIncidents = $applicant->bookings->flatMap->incidents->where('is_resolved', false)->count();
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition {{ $applicant->is_suspended ? 'bg-rose-50/20' : '' }}">
                                
                                <!-- 1. Pemohon & Entitas -->
                                <td class="py-3.5 px-4 align-middle">
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-full {{ $applicant->is_suspended ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-indigo-50 text-[#001A72] border border-indigo-100' }} flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                            {{ strtoupper(substr($applicant->full_name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-black text-slate-900 text-xs sm:text-sm">
                                                {{ $applicant->full_name }}
                                            </div>
                                            <div class="text-[11px] text-slate-500 truncate">
                                                {{ $applicant->institution_name ?? 'Individu / Komunitas' }}
                                            </div>
                                            <div class="text-[10px] font-mono text-slate-400 mt-0.5">
                                                {{ $applicant->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- 2. Kontak WhatsApp -->
                                <td class="py-3 px-3 whitespace-nowrap align-middle">
                                    <div class="text-slate-800 font-semibold">{{ $applicant->phone_number }}</div>
                                    @php
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $applicant->phone_number);
                                        if (str_starts_with($cleanPhone, '0')) {
                                            $cleanPhone = '62' . substr($cleanPhone, 1);
                                        }
                                    @endphp
                                    @if($cleanPhone)
                                        <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] text-emerald-600 font-bold hover:underline mt-0.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                            Hubungi WhatsApp
                                        </a>
                                    @endif
                                </td>

                                <!-- 3. Status Akses Akun -->
                                <td class="py-3 px-3 text-center whitespace-nowrap align-middle">
                                    @if($applicant->is_suspended)
                                        <div class="inline-flex flex-col items-center gap-0.5">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200 shadow-2xs">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                                Dibekukan (Blacklist)
                                            </span>
                                            @if($applicant->suspended_at)
                                                <span class="text-[9px] text-rose-600 font-mono">
                                                    {{ $applicant->suspended_at->format('d/m/Y H:i') }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Akun Normal
                                        </span>
                                    @endif
                                </td>

                                <!-- 4. Riwayat Kasus -->
                                <td class="py-3 px-3 text-center whitespace-nowrap align-middle">
                                    <div class="flex flex-col items-center gap-1">
                                        @if($abandonedCount > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                {{ $abandonedCount }}x Panitia Kabur
                                            </span>
                                        @endif
                                        @if($unresolvedIncidents > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                {{ $unresolvedIncidents }} Kerusakan Aktif
                                            </span>
                                        @endif
                                        @if($abandonedCount === 0 && $unresolvedIncidents === 0)
                                            <span class="text-[10px] text-slate-400 font-medium">Nihil Pelanggaran</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- 5. Alasan Sanksi / Catatan -->
                                <td class="py-3 px-3 align-middle text-xs">
                                    @if($applicant->is_suspended && $applicant->suspension_reason)
                                        <p class="text-rose-900 font-medium text-[11px] leading-relaxed line-clamp-2" title="{{ $applicant->suspension_reason }}">
                                            {{ $applicant->suspension_reason }}
                                        </p>
                                    @else
                                        <span class="text-slate-400 text-[11px]">-</span>
                                    @endif
                                </td>

                                <!-- 6. Aksi Kedisiplinan -->
                                <td class="py-3 px-4 text-right whitespace-nowrap align-middle">
                                    <div class="inline-flex items-center gap-1.5">
                                        @if($applicant->is_suspended)
                                            <!-- Tombol Pulihkan Akun (Lift Suspension) -->
                                            <button type="button" 
                                                    @click="userToLift = {{ json_encode(['id' => $applicant->id, 'name' => $applicant->full_name, 'reason' => $applicant->suspension_reason]) }}; liftUrl = '{{ route('admin.sanctions.lift', $applicant->id) }}'; liftModalOpen = true;"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-brand text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-2xs transition cursor-pointer"
                                                    title="Pulihkan Hak Akses Akun">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                                <span>Pulihkan</span>
                                            </button>
                                        @else
                                            <!-- Tombol Bekukan Akun (Suspend / Blacklist) -->
                                            <button type="button" 
                                                    @click="userToSuspend = {{ json_encode(['id' => $applicant->id, 'name' => $applicant->full_name, 'email' => $applicant->email]) }}; suspendUrl = '{{ route('admin.sanctions.suspend', $applicant->id) }}'; suspendModalOpen = true;"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-brand text-xs font-bold text-rose-700 hover:text-white bg-rose-50 hover:bg-rose-600 border border-rose-200 transition shadow-2xs cursor-pointer"
                                                    title="Bekukan / Blacklist Akun Pemohon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                                <span>Bekukan</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            @if($applicants->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50">
                    {{ $applicants->links() }}
                </div>
            @endif
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-brand border border-slate-200 shadow-card p-12 text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Tidak Ada Data Pemohon yang Sesuai Filter</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Gunakan kata kunci lain atau reset filter untuk menampilkan daftar seluruh pemohon gedung MCC.
            </p>
            <div class="pt-2">
                <a href="{{ route('admin.sanctions.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-brand text-xs font-bold text-[#001A72] bg-[#EEF2FF] hover:bg-indigo-100 transition shadow-2xs">
                    Reset Filter Pencarian
                </a>
            </div>
        </div>
    @endif

    <!-- ======================================================================== -->
    <!-- MODAL 1: BEKUKAN AKUN (SUSPEND / BLACKLIST PEMOHON) -->
    <!-- ======================================================================== -->
    <div x-show="suspendModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="suspendModalOpen = false" 
             class="bg-white rounded-brand border border-slate-200 shadow-2xl max-w-lg w-full p-5 sm:p-6 space-y-4">
            
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-900">Bekukan Akun Pemohon (Blacklist)</h4>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pemohon yang dibekukan <span class="text-rose-600 font-bold">otomatis ditolak</span> saat memilih jadwal atau mengajukan proposal di kalender publik MCC.
                    </p>
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-brand border border-slate-200 text-xs space-y-1">
                <div class="text-slate-500 text-[11px]">Target Pemohon:</div>
                <div class="font-bold text-slate-900" x-text="userToSuspend?.name"></div>
                <div class="font-mono text-slate-500 text-[11px]" x-text="userToSuspend?.email"></div>
            </div>

            <form :action="suspendUrl" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alasan Pembekuan Akun / Catatan Pelanggaran <span class="text-rose-600">*</span>
                    </label>
                    <textarea name="suspension_reason" 
                              rows="3" 
                              required 
                              minlength="10"
                              placeholder="Tuliskan dasar penetapan sanksi (contoh: Panitia kabur dan merusak fasilitas audio di Lantai 5 tanpa checkout resmi)..."
                              class="w-full text-xs rounded-brand border-slate-300 focus:border-rose-600 focus:ring-rose-600 placeholder:text-slate-400"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Alasan ini tersimpan permanen pada profil pemohon dan audit log.</p>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                    <button type="button" 
                            @click="suspendModalOpen = false" 
                            class="px-4 py-2 rounded-brand text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 rounded-brand text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition cursor-pointer">
                        Konfirmasi Pembekuan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================================== -->
    <!-- MODAL 2: PULIHKAN AKUN (LIFT SUSPENSION / REACTIVATE) -->
    <!-- ======================================================================== -->
    <div x-show="liftModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="liftModalOpen = false" 
             class="bg-white rounded-brand border border-slate-200 shadow-2xl max-w-lg w-full p-5 sm:p-6 space-y-4">
            
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-900">Pulihkan Akses Akun Pemohon</h4>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Mencabut status blacklist dan mengaktifkan kembali hak pemesanan jadwal kalender publik bagi pemohon.
                    </p>
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-brand border border-slate-200 text-xs space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 text-[11px]">Nama Pemohon:</span>
                    <span class="font-bold text-slate-900" x-text="userToLift?.name"></span>
                </div>
                <div class="text-[11px] text-rose-700 bg-rose-50 p-2 rounded border border-rose-100">
                    <span class="font-bold">Alasan Sanksi Sebelumnya:</span>
                    <span x-text="userToLift?.reason"></span>
                </div>
            </div>

            <form :action="liftUrl" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Catatan Penyelesaian Kewajiban (Opsional)
                    </label>
                    <textarea name="settlement_notes" 
                              rows="2" 
                              placeholder="Contoh: Telah melunasi ganti rugi kerusakan sarana dan menyerahkan surat pertanggungjawaban resmi..."
                              class="w-full text-xs rounded-brand border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 placeholder:text-slate-400"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                    <button type="button" 
                            @click="liftModalOpen = false" 
                            class="px-4 py-2 rounded-brand text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 rounded-brand text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition cursor-pointer">
                        Cabut Sanksi &amp; Aktifkan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
