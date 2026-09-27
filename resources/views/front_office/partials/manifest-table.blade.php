<!-- ==================================================================== -->
<!-- TABEL MANIFES ACARA HARIAN (FR-FO-01 MULTI-SLOT FILTER) -->
<!-- ==================================================================== -->
<div class="bg-white rounded-brand border border-slate-200 shadow-card overflow-hidden">
    <!-- Card Header -->
    <div class="px-6 py-4 border-b border-slate-100 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-brand bg-[#EEF2FF] text-[#001A72] flex items-center justify-center font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="m9 16 2 2 4-4"/></svg>
            </div>
            <div>
                <h3 class="font-extrabold text-sm text-slate-900">
                    Jadwal Manifes Acara Hari Ini
                </h3>
                <p class="text-[11px] text-slate-500">
                    Menampilkan sesi slot operasional yang aktif pada tanggal <span class="font-semibold text-slate-700">{{ $targetDate->translatedFormat('d F Y') }}</span>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                {{ $manifestSlots->total() }} Sesi Terjadwal
            </span>
        </div>
    </div>

    <!-- Status Tabs Bar (FR-FO-01: Menunggu Kedatangan, Sedang Berlangsung, Selesai Digunakan) -->
    <div class="px-6 py-2.5 bg-slate-50/70 border-b border-slate-200/80 flex items-center gap-2 overflow-x-auto custom-scrollbar">
        <a href="{{ route('front_office.manifest', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}" 
           class="px-3 py-1.5 rounded-brand text-xs font-bold transition whitespace-nowrap {{ $currentStatus === 'all' ? 'bg-[#001A72] text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Semua Sesi ({{ $metrics['total_slots_today'] }})
        </a>

        <a href="{{ route('front_office.manifest', array_merge(request()->except('status', 'page'), ['status' => 'confirmed'])) }}" 
           class="px-3 py-1.5 rounded-brand text-xs font-bold transition whitespace-nowrap inline-flex items-center gap-1.5 {{ $currentStatus === 'confirmed' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-amber-800 hover:bg-amber-50 border border-amber-200' }}">
            <span class="w-2 h-2 rounded-full {{ $currentStatus === 'confirmed' ? 'bg-white' : 'bg-amber-500' }}"></span>
            <span>Menunggu Kedatangan ({{ $metrics['confirmed_slots'] }})</span>
        </a>

        <a href="{{ route('front_office.manifest', array_merge(request()->except('status', 'page'), ['status' => 'checked_in'])) }}" 
           class="px-3 py-1.5 rounded-brand text-xs font-bold transition whitespace-nowrap inline-flex items-center gap-1.5 {{ $currentStatus === 'checked_in' ? 'bg-teal-600 text-white shadow-xs' : 'bg-white text-teal-800 hover:bg-teal-50 border border-teal-200' }}">
            <span class="w-2 h-2 rounded-full {{ $currentStatus === 'checked_in' ? 'bg-white' : 'bg-teal-500' }}"></span>
            <span>Sedang Berlangsung ({{ $metrics['checked_in_slots'] }})</span>
        </a>

        <a href="{{ route('front_office.manifest', array_merge(request()->except('status', 'page'), ['status' => 'completed'])) }}" 
           class="px-3 py-1.5 rounded-brand text-xs font-bold transition whitespace-nowrap inline-flex items-center gap-1.5 {{ $currentStatus === 'completed' ? 'bg-slate-100 text-slate-800 border border-slate-300 shadow-2xs font-extrabold' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            <span class="w-2 h-2 rounded-full {{ $currentStatus === 'completed' ? 'bg-slate-500' : 'bg-slate-400' }}"></span>
            <span>Selesai Digunakan ({{ $metrics['completed_slots'] }})</span>
        </a>
    </div>

    <!-- Table Container -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 font-extrabold border-b border-slate-200">
                <tr>
                    <th scope="col" class="px-5 py-3.5">Waktu Sesi</th>
                    <th scope="col" class="px-5 py-3.5">Ruangan &amp; Lantai</th>
                    <th scope="col" class="px-5 py-3.5">Nama Acara &amp; Penyelenggara</th>
                    <th scope="col" class="px-5 py-3.5">Penanggung Jawab (PIC)</th>
                    <th scope="col" class="px-5 py-3.5 text-center">Status Sesi</th>
                    <th scope="col" class="px-5 py-3.5 text-center">Aksi Resepsionis</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white font-normal">
                @forelse($manifestSlots as $slot)
                    @php
                        $booking = $slot->booking;
                        $eventDetail = $booking->eventDetail;
                        $room = $slot->room;
                        $isMultiSlot = $booking->slots->count() > 1;
                        
                        $startCarbon = \Carbon\Carbon::parse($slot->event_start_at);
                        $endCarbon = \Carbon\Carbon::parse($slot->event_end_at);
                        $durationHours = round($startCarbon->diffInMinutes($endCarbon) / 60, 1);
                        
                        // Siapkan payload JSON yang bersih untuk modal detail
                        $slotPayload = [
                            'id' => $slot->id,
                            'slot_start' => $startCarbon->translatedFormat('d M Y, H:i'),
                            'slot_end' => $endCarbon->translatedFormat('H:i') . ' WIB',
                            'duration' => $durationHours . ' Jam',
                            'room_name' => $room->name ?? 'Ruangan MCC',
                            'floor_level' => $room->floor_level ?? 1,
                            'capacity' => $room->capacity ?? 0,
                            'booking_id' => $booking->id,
                            'booking_code' => $booking->booking_code,
                            'status_value' => $booking->status->value,
                            'status_label' => $booking->status->label(),
                            'status_badge' => $booking->status->badgeClasses(),
                            'event_name' => $eventDetail->event_name ?? 'Acara MCC',
                            'event_category' => $eventDetail->event_category ?? '-',
                            'ekraf_subsector' => $eventDetail->ekraf_subsector ?? '-',
                            'estimated_participants' => $eventDetail->estimated_participants ?? 0,
                            'event_description' => $eventDetail->event_description ?? '-',
                            'equipment_needed' => $eventDetail->equipment_needed ?? '-',
                            'requested_facilities' => $eventDetail->requested_facilities ?? [],
                            'pic_name' => $eventDetail->pic_name_snapshot ?? ($booking->user->full_name ?? '-'),
                            'pic_phone' => $eventDetail->pic_phone_snapshot ?? ($booking->user->phone_number ?? '-'),
                            'emergency_phone' => $eventDetail->pic_emergency_phone ?? '-',
                            'institution_name' => $eventDetail->institution_name_snapshot ?? ($booking->user->institution_name ?? '-'),
                            'pricing_scheme' => $booking->pricing_scheme === 'subsidy' ? 'Subsidi Pemkot (Gratis)' : 'Komersial / Retribusi',
                            'all_slots' => $booking->slots->map(function ($s) {
                                return [
                                    'id' => $s->id,
                                    'room_name' => $s->room->name ?? 'Ruangan',
                                    'floor_level' => $s->room->floor_level ?? 1,
                                    'start' => \Carbon\Carbon::parse($s->event_start_at)->translatedFormat('d M Y, H:i'),
                                    'end' => \Carbon\Carbon::parse($s->event_end_at)->translatedFormat('H:i') . ' WIB',
                                    'is_today' => \Carbon\Carbon::parse($s->event_start_at)->isToday(),
                                ];
                            })->values()->all(),
                        ];
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition group">
                        
                        <!-- 1. Waktu Sesi (Jam Mulai - Jam Selesai & Durasi) -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="font-extrabold text-slate-900 text-xs flex items-center gap-1.5">
                                <span>{{ $startCarbon->format('H:i') }} - {{ $endCarbon->format('H:i') }}</span>
                                <span class="text-slate-400 font-normal text-[10px]">({{ $durationHours }} jam)</span>
                            </div>
                            <div class="text-[10px] text-slate-500 font-semibold mt-0.5">
                                {{ $startCarbon->translatedFormat('l, d F Y') }}
                            </div>
                        </td>

                        <!-- 2. Ruangan & Lantai -->
                        <td class="px-5 py-4">
                            <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                <span>{{ $room->name }}</span>
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-2">
                                <span class="inline-flex items-center px-1.5 py-0.2 rounded bg-slate-100 text-slate-700 font-semibold text-[10px] border border-slate-200">
                                    Lantai {{ $room->floor_level }}
                                </span>
                                <span>Kapasitas: {{ $room->capacity }} org</span>
                            </div>
                        </td>

                        <!-- 3. Nama Acara & Penyelenggara -->
                        <td class="px-5 py-4 max-w-xs sm:max-w-sm">
                            <div class="font-bold text-slate-900 line-clamp-1 leading-snug group-hover:text-[#001A72] transition" title="{{ $eventDetail->event_name ?? 'Acara' }}">
                                {{ $eventDetail->event_name ?? 'Acara MCC' }}
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-2">
                                <span class="font-mono font-bold text-[#001A72]">{{ $booking->booking_code }}</span>
                                <span class="text-slate-300">•</span>
                                <span class="truncate">{{ $eventDetail->institution_name_snapshot ?? ($booking->user->institution_name ?? '-') }}</span>
                            </div>
                            
                            @if($isMultiSlot)
                                <div class="mt-1.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-[#001A72] border border-indigo-100" title="Permohonan memiliki {{ $booking->slots->count() }} sesi slot di beberapa jam/ruangan">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                        <span>Multi-Slot (Total {{ $booking->slots->count() }} Sesi)</span>
                                    </span>
                                </div>
                            @endif
                        </td>

                        <!-- 4. PIC & Kontak -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="font-bold text-slate-900">
                                {{ $eventDetail->pic_name_snapshot ?? ($booking->user->full_name ?? '-') }}
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1.5 font-mono">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                <span>{{ $eventDetail->pic_phone_snapshot ?? ($booking->user->phone_number ?? '-') }}</span>
                            </div>
                        </td>

                        <!-- 5. Status Sesi -->
                        <td class="px-5 py-4 text-center whitespace-nowrap">
                            @if($booking->status === \App\Enums\BookingStatus::CONFIRMED)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold shadow-2xs inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span>Menunggu Kedatangan</span>
                                </span>
                            @elseif($booking->status === \App\Enums\BookingStatus::CHECKED_IN)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold shadow-2xs inline-flex items-center gap-1.5 bg-teal-50 text-teal-700 border border-teal-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                                    <span>Sedang Berlangsung</span>
                                </span>
                            @elseif($booking->status === \App\Enums\BookingStatus::COMPLETED)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold shadow-2xs inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    <span>Selesai Digunakan</span>
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold shadow-2xs inline-flex items-center gap-1 {{ $booking->status->badgeClasses() }}">
                                    <span>{{ $booking->status->label() }}</span>
                                </span>
                            @endif
                        </td>

                        <!-- 6. Aksi Resepsionis -->
                        <td class="px-5 py-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center">
                                <button type="button"
                                        @click="openManifestModal({{ json_encode($slotPayload) }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-brand bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition shadow-2xs cursor-pointer border border-slate-200"
                                        title="Lihat Rincian Manifes">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <span>Detail</span>
                                </button>
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 sm:py-28 text-center bg-white">
                            <div class="max-w-sm mx-auto flex flex-col items-center justify-center space-y-4">
                                <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 shadow-2xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                </div>
                                <div class="space-y-1.5">
                                    <p class="text-sm font-bold text-slate-800">
                                        @if($currentStatus === 'completed')
                                            Belum ada sesi yang selesai digunakan hari ini
                                        @elseif($currentStatus === 'checked_in')
                                            Belum ada sesi yang sedang berlangsung saat ini
                                        @elseif($currentStatus === 'confirmed')
                                            Tidak ada sesi yang menunggu kedatangan
                                        @else
                                            Tidak ada sesi acara pada manifes hari ini
                                        @endif
                                    </p>
                                    <p class="text-xs text-slate-500 leading-relaxed">
                                        Tidak ditemukan jadwal permohonan untuk filter status yang dipilih.
                                    </p>
                                </div>
                                @if($currentStatus !== 'all')
                                    <div class="pt-2 pb-1">
                                        <a href="{{ route('front_office.manifest') }}" 
                                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white text-xs font-bold transition shadow-xs hover:shadow cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                            <span>Tampilkan Semua Sesi</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    @if($manifestSlots->hasPages())
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $manifestSlots->links() }}
        </div>
    @endif
</div>
