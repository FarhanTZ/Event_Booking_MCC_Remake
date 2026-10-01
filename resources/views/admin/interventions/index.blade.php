@extends('layouts.admin')

@section('title', 'Intervensi & Pembatalan Darurat - Super Admin MCC')
@section('page_title', 'Intervensi Darurat & Super Override')

@section('content')
<div class="space-y-4" 
     x-data="{ 
         cancelModalOpen: false, 
         bookingToCancel: null, 
         cancelUrl: '',
         category: 'agenda_pemerintah_darurat'
     }">
    
    <!-- Top Action Card: Info -->
    <div class="bg-[#001A72] text-white p-4 sm:p-5 rounded-brand border border-indigo-900 shadow-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-white/20 text-white">
                    FR-ADM-05
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-400 text-slate-950">
                    Otoritas Super Override
                </span>
            </div>
            <h2 class="text-base sm:text-lg font-black tracking-tight">
                Intervensi Reservasi &amp; Pembatalan Paksa Darurat
            </h2>
            <p class="text-indigo-200 text-xs mt-0.5 max-w-2xl">
                Kewenangan override Super Admin untuk membatalkan reservasi pada situasi darurat (agenda mendadak Forkopimda/Pemkot Malang atau kerusakan teknis gedung). Seluruh slot jadwal seketika dilepas kembali ke kalender publik.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <div class="px-3.5 py-2 rounded-brand bg-white/10 border border-white/15 text-xs font-semibold flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>{{ $metrics['total_active'] ?? 0 }} Reservasi Terjadwal</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar (Inline Single-Row) -->
    <div class="bg-white rounded-brand border border-slate-200 shadow-card p-4 sm:p-5 space-y-3 w-full">
        <form method="GET" action="{{ route('admin.interventions.index') }}" x-ref="filterForm" class="space-y-3 w-full">
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
                           placeholder="Cari kode booking, nama event, nama pemohon, atau kontak..." 
                           class="block w-full pl-10 pr-3.5 py-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] placeholder:text-slate-400 bg-white transition">
                </div>

                <!-- 2. Filter Tingkat Lantai -->
                <div class="w-full sm:w-48 shrink-0">
                    <select name="floor" 
                            @change="$refs.filterForm.submit()"
                            class="block w-full py-2.5 px-3 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-700 font-medium cursor-pointer bg-white">
                        <option value="">Semua Lantai</option>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ request('floor') == (string)$i ? 'selected' : '' }}>
                                Lantai {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- 3. Filter Status Reservasi -->
                <div class="w-full sm:w-48 shrink-0">
                    <select name="status" 
                            @change="$refs.filterForm.submit()"
                            class="block w-full py-2.5 px-3 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 font-medium cursor-pointer bg-white">
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif (Disetujui/Check-in)</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Disetujui (Confirmed)</option>
                        <option value="checked_in" {{ request('status') === 'checked_in' ? 'selected' : '' }}>Check-in Hari-H</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending / Kurasi</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>

                <!-- 4. Tombol Reset Cepat -->
                @if(request()->hasAny(['search', 'floor', 'status', 'date']) && (request('search') || request('floor') || request('date') || (request('status') && request('status') !== 'all')))
                    <div class="shrink-0">
                        <a href="{{ route('admin.interventions.index') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-brand text-xs font-bold text-rose-600 hover:text-white hover:bg-rose-600 bg-rose-50 border border-rose-200 transition shadow-2xs"
                           title="Reset Semua Filter">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            <span>Reset</span>
                        </a>
                    </div>
                @endif

            </div>

            <!-- Active Chips -->
            @if(request()->hasAny(['search', 'floor', 'status']) && (request('search') || request('floor') || (request('status') && request('status') !== 'all')))
                <div class="flex items-center gap-2 pt-2 border-t border-slate-100 text-xs flex-wrap">
                    <span class="text-slate-400 text-[11px] font-bold">Filter Aktif:</span>
                    @if(request('search'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100">
                            Pencarian: "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('floor'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100">
                            Lantai {{ request('floor') }}
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

    <!-- Bookings Table -->
    @if($bookings->count() > 0)
        <div class="bg-white rounded-brand border border-slate-200 shadow-card overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] border-b border-slate-200 tracking-wider">
                        <tr>
                            <th class="py-3 px-4 min-w-[200px]">Kode &amp; Acara</th>
                            <th class="py-3 px-3 min-w-[170px]">Ruangan &amp; Lokasi</th>
                            <th class="py-3 px-3 min-w-[160px]">Jadwal Pelaksanaan</th>
                            <th class="py-3 px-3 min-w-[180px]">Pemohon &amp; Kontak</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Otoritas Intervensi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-800">
                        @foreach($bookings as $booking)
                            @php
                                $firstSlot = $booking->slots->sortBy('event_start_at')->first();
                                $roomName = $firstSlot?->room?->name ?? 'Ruangan MCC';
                                $floorLevel = $firstSlot?->room?->floor_level ?? '-';
                                $isCancellable = in_array($booking->status, [
                                    \App\Enums\BookingStatus::CONFIRMED,
                                    \App\Enums\BookingStatus::CHECKED_IN,
                                    \App\Enums\BookingStatus::AWAITING_PAYMENT,
                                    \App\Enums\BookingStatus::AWAITING_MARKETING,
                                    \App\Enums\BookingStatus::REVISED,
                                ]);
                                $emergencyApproval = $booking->approvals->where('decision', 'emergency_cancelled')->last();
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition {{ $booking->status === \App\Enums\BookingStatus::CANCELLED ? 'bg-slate-50/40 text-slate-400' : '' }}">
                                
                                <!-- 1. Kode & Acara -->
                                <td class="py-3.5 px-4 align-middle">
                                    <div class="space-y-0.5">
                                        <div class="font-mono font-bold text-slate-900 text-xs">
                                            #{{ $booking->booking_code }}
                                        </div>
                                        <div class="font-black text-slate-900 text-xs sm:text-sm line-clamp-1">
                                            {{ $booking->eventDetail?->event_name ?? 'Acara MCC' }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 flex items-center gap-1.5">
                                            <span>{{ $booking->eventDetail?->ekraf_subsector ?? 'Ekraf' }}</span>
                                            <span>&bull;</span>
                                            <span>{{ $booking->eventDetail?->estimated_participants ?? 0 }} Peserta</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- 2. Ruangan & Lokasi -->
                                <td class="py-3 px-3 align-middle">
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-slate-900 text-xs">{{ $roomName }}</div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-[#001A72] text-white">
                                            Lantai {{ $floorLevel }}
                                        </span>
                                    </div>
                                </td>

                                <!-- 3. Jadwal Pelaksanaan -->
                                <td class="py-3 px-3 whitespace-nowrap align-middle">
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-slate-900 text-xs">
                                            {{ $firstSlot && $firstSlot->event_start_at ? \Carbon\Carbon::parse($firstSlot->event_start_at)->translatedFormat('d M Y') : '-' }}
                                        </div>
                                        <div class="text-[11px] font-mono text-slate-500">
                                            {{ $booking->slots->count() }} Sesi Selesai/Aktif
                                        </div>
                                    </div>
                                </td>

                                <!-- 4. Pemohon & Kontak -->
                                <td class="py-3 px-3 align-middle">
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-slate-900 text-xs">
                                            {{ $booking->user?->full_name ?? $booking->eventDetail?->pic_name_snapshot ?? '-' }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 truncate">
                                            {{ $booking->user?->institution_name ?? 'Komunitas/Individu' }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-mono">
                                            {{ $booking->getPicPhoneNumber() }}
                                        </div>
                                    </div>
                                </td>

                                <!-- 5. Status Reservasi -->
                                <td class="py-3 px-3 text-center whitespace-nowrap align-middle">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold {{ $booking->status->badgeClasses() }}">
                                        {{ $booking->status->label() }}
                                    </span>
                                </td>

                                <!-- 6. Otoritas Intervensi -->
                                <td class="py-3 px-4 text-right whitespace-nowrap align-middle">
                                    @if($isCancellable)
                                        <button type="button" 
                                                @click="bookingToCancel = {{ json_encode([
                                                    'id' => $booking->id,
                                                    'code' => $booking->booking_code,
                                                    'event_name' => $booking->eventDetail?->event_name ?? 'Event',
                                                    'room' => $roomName,
                                                    'date' => $firstSlot && $firstSlot->event_start_at ? \Carbon\Carbon::parse($firstSlot->event_start_at)->translatedFormat('d M Y') : '-',
                                                    'user' => $booking->user?->full_name ?? 'Pemohon',
                                                ]) }}; cancelUrl = '{{ route('admin.interventions.cancel', $booking->id) }}'; cancelModalOpen = true;"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-brand text-xs font-bold text-rose-700 hover:text-white bg-rose-50 hover:bg-rose-600 border border-rose-200 transition shadow-2xs cursor-pointer"
                                                title="Batalkan Paksa dengan Super Override">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                            <span>Batalkan Paksa</span>
                                        </button>
                                    @elseif($emergencyApproval)
                                        <div class="text-right" title="{{ $emergencyApproval->notes }}">
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                                Super Override
                                            </span>
                                            <div class="text-[9px] text-slate-400 mt-0.5 font-mono">
                                                {{ $emergencyApproval->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    @elseif($booking->status === \App\Enums\BookingStatus::COMPLETED)
                                        <div class="text-right">
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                                Acara Selesai
                                            </span>
                                            <div class="text-[9px] text-slate-400 mt-0.5">
                                                Telah Check-out
                                            </div>
                                        </div>
                                    @elseif(in_array($booking->status, [\App\Enums\BookingStatus::ABANDONED_CLEAN, \App\Enums\BookingStatus::ABANDONED_INCIDENT]))
                                        <div class="text-right">
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                                Tutup Otomatis
                                            </span>
                                            <div class="text-[9px] text-slate-400 mt-0.5">
                                                Panitia Ditinggal
                                            </div>
                                        </div>
                                    @elseif($booking->status === \App\Enums\BookingStatus::REJECTED)
                                        <div class="text-right">
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                                Ditolak Kurasi
                                            </span>
                                        </div>
                                    @elseif($booking->status === \App\Enums\BookingStatus::CANCELLED)
                                        <div class="text-right">
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                                Dibatalkan Pemohon
                                            </span>
                                        </div>
                                    @else
                                        <div class="text-right">
                                            <span class="inline-flex items-center text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                                Tidak Aktif
                                            </span>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            @if($bookings->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-brand border border-slate-200 shadow-card p-12 text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Tidak Ada Reservasi yang Sesuai Kriteria Filter</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Ubah parameter pencarian atau reset filter untuk melihat jadwal reservasi gedung lainnya.
            </p>
            <div class="pt-2">
                <a href="{{ route('admin.interventions.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-brand text-xs font-bold text-[#001A72] bg-[#EEF2FF] hover:bg-indigo-100 transition shadow-2xs">
                    Reset Filter Pencarian
                </a>
            </div>
        </div>
    @endif

    <!-- ======================================================================== -->
    <!-- MODAL: PEMBATALAN PAKSA DARURAT (SUPER OVERRIDE) -->
    <!-- ======================================================================== -->
    <div x-show="cancelModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="cancelModalOpen = false" 
             class="bg-white rounded-brand border border-slate-200 shadow-2xl max-w-lg w-full p-5 sm:p-6 space-y-4">
            
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-900">Pembatalan Paksa Darurat (Super Override)</h4>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Tindakan ini seketika <span class="text-rose-600 font-bold">melepas seluruh slot jadwal</span> dan mencatat riwayat pembatalan permanen ke audit log.
                    </p>
                </div>
            </div>

            <!-- Target Summary -->
            <div class="p-3 bg-slate-50 rounded-brand border border-slate-200 text-xs space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 text-[11px]">Kode Booking:</span>
                    <span class="font-mono font-bold text-slate-900" x-text="'#' + bookingToCancel?.code"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 text-[11px]">Nama Event:</span>
                    <span class="font-bold text-slate-900" x-text="bookingToCancel?.event_name"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 text-[11px]">Ruangan &amp; Tanggal:</span>
                    <span class="font-medium text-slate-700" x-text="bookingToCancel?.room + ' (' + bookingToCancel?.date + ')'"></span>
                </div>
            </div>

            <form :action="cancelUrl" method="POST" class="space-y-4">
                @csrf

                <!-- Kategori Alasan Darurat -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kategori Alasan Darurat <span class="text-rose-600">*</span>
                    </label>
                    <select name="emergency_category" 
                            x-model="category"
                            required
                            class="w-full text-xs rounded-brand border-slate-300 focus:border-rose-600 focus:ring-rose-600">
                        <option value="agenda_pemerintah_darurat">Agenda Mendadak Forkopimda / Pemkot Malang</option>
                        <option value="kerusakan_teknis_gedung">Kerusakan Teknis / Struktural Gedung Darurat</option>
                        <option value="kondisi_kahar_force_majeure">Kondisi Kahar (Force Majeure)</option>
                        <option value="pelanggaran_berat_keamanan">Pelanggaran Berat Protokol Keamanan Gedung</option>
                        <option value="lainnya">Intervensi Khusus Super Administrator</option>
                    </select>
                </div>

                <!-- Input Catatan Alasan Pembatalan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Catatan Resmi Alasan Pembatalan <span class="text-rose-600">*</span>
                    </label>
                    <textarea name="cancellation_reason" 
                              rows="3" 
                              required 
                              minlength="15"
                              placeholder="Tuliskan keterangan detail alasan pembatalan paksa (contoh: Kunjungan mendadak Pj Walikota Malang dan Forkopimda untuk agenda kenegaraan)..."
                              class="w-full text-xs rounded-brand border-slate-300 focus:border-rose-600 focus:ring-rose-600 placeholder:text-slate-400"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Catatan ini tersimpan permanen pada riwayat kurasi dan audit trail transaksi.</p>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                    <button type="button" 
                            @click="cancelModalOpen = false" 
                            class="px-4 py-2 rounded-brand text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 rounded-brand text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition cursor-pointer">
                        Eksekusi Super Override
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
