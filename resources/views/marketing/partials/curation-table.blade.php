<!-- ==================================================================== -->
<!-- TABEL LENGKAP ANTREAN TRIASE PROPOSAL -->
<!-- ==================================================================== -->
<div class="bg-white rounded-brand border border-slate-200 shadow-card overflow-hidden">
    <!-- Card Header -->
    <div class="px-6 py-4 border-b border-slate-100 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-brand bg-[#EEF2FF] text-[#001A72] flex items-center justify-center font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><line x1="10" x2="8" y1="9" y2="9"/></svg>
            </div>
            <div>
                <h3 class="font-extrabold text-sm text-slate-900">
                    Daftar Antrean Proposal Masuk
                </h3>
                <p class="text-[11px] text-slate-500">
                    Diurutkan berdasarkan urgensi waktu pelaksanaan &amp; antrean FIFO
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                {{ $curations->total() }} Data Ditemukan
            </span>
        </div>
    </div>

    <!-- Table Container -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-700">
            <thead class="bg-slate-50/80 text-slate-600 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200/80">
                <tr>
                    <th scope="col" class="px-5 py-3.5">Kode &amp; Pembiayaan</th>
                    <th scope="col" class="px-5 py-3.5">Pemohon &amp; PIC</th>
                    <th scope="col" class="px-5 py-3.5">Detail Acara &amp; Ekraf</th>
                    <th scope="col" class="px-5 py-3.5">Jadwal &amp; Ruangan</th>
                    <th scope="col" class="px-5 py-3.5">Status</th>
                    <th scope="col" class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($curations as $booking)
                    @php
                        $statusEnum = $booking->status instanceof \App\Enums\BookingStatus ? $booking->status : \App\Enums\BookingStatus::tryFrom($booking->status) ?? \App\Enums\BookingStatus::AWAITING_MARKETING;
                        $urgencyInfo = $booking->getUrgencyBadgeInfo();
                        $userAccountType = $booking->user->account_type ?? \App\Enums\AccountType::KOMUNITAS;
                        $accountTypeLabel = is_object($userAccountType) && method_exists($userAccountType, 'label') ? $userAccountType->label() : ucfirst((string)$userAccountType);

                        $user = $booking->user;
                        $isSuspended = (bool) ($user->is_suspended ?? false);
                        $suspensionReason = $user->suspension_reason ?? null;
                        $suspendedAt = $user->suspended_at ? \Carbon\Carbon::parse($user->suspended_at)->translatedFormat('d F Y') : null;
                        
                        $addressParts = array_filter([
                            $user->address ?? null,
                            $user->district ? 'Kec. ' . $user->district : null,
                            $user->city ?? null,
                        ]);
                        $addressFull = !empty($addressParts) ? implode(', ', $addressParts) : 'Belum mencantumkan alamat detail';

                        $socialMediaList = [];
                        if (!empty($user->social_media)) {
                            if (is_array($user->social_media)) {
                                foreach ($user->social_media as $platform => $handle) {
                                    $socialMediaList[] = is_numeric($platform) ? $handle : ucfirst($platform) . ': ' . $handle;
                                }
                            } else {
                                $socialMediaList[] = (string) $user->social_media;
                            }
                        }

                        $estimatedParticipants = (int) ($booking->eventDetail->estimated_participants ?? 0);
                        $totalMinutes = 0;
                        $hasOvercapacity = false;

                        $mappedSlots = $booking->slots->map(function($s) use ($estimatedParticipants, &$totalMinutes, &$hasOvercapacity) {
                            $start = \Carbon\Carbon::parse($s->event_start_at);
                            $end = \Carbon\Carbon::parse($s->event_end_at);
                            $diffMins = max(0, $start->diffInMinutes($end));
                            $totalMinutes += $diffMins;

                            $roomCapacity = (int) ($s->room->capacity ?? 0);
                            $isOver = $roomCapacity > 0 && $estimatedParticipants > $roomCapacity;
                            if ($isOver) {
                                $hasOvercapacity = true;
                            }

                            $bufferEnd = (clone $end)->addMinutes(30);

                            return [
                                'room_name' => $s->room->name ?? 'Ruangan MCC',
                                'floor' => 'Lantai ' . ($s->room->floor_level ?? 1),
                                'capacity' => $roomCapacity,
                                'capacity_formatted' => $roomCapacity . ' Orang',
                                'date' => $start->translatedFormat('l, d F Y'),
                                'time' => $start->format('H:i') . ' - ' . $end->format('H:i') . ' WIB',
                                'duration_label' => (floor($diffMins / 60) > 0 ? floor($diffMins / 60) . ' Jam ' : '') . ($diffMins % 60 > 0 ? ($diffMins % 60) . ' Menit' : ''),
                                'buffer_time' => $end->format('H:i') . ' - ' . $bufferEnd->format('H:i') . ' WIB',
                                'is_overcapacity' => $isOver,
                                'overcapacity_diff' => $isOver ? ($estimatedParticipants - $roomCapacity) : 0,
                            ];
                        })->values()->toArray();

                        $totalHours = floor($totalMinutes / 60);
                        $remainingMins = $totalMinutes % 60;
                        $totalDurationFormatted = ($totalHours > 0 ? "{$totalHours} Jam " : '') . ($remainingMins > 0 ? "{$remainingMins} Menit" : ($totalHours > 0 ? '' : '0 Menit'));
                        $totalMinutesWithBuffer = $totalMinutes + ($booking->slots->count() * 30);
                        $totalBufferHours = floor($totalMinutesWithBuffer / 60);
                        $remainingBufferMins = $totalMinutesWithBuffer % 60;
                        $totalWithBufferFormatted = ($totalBufferHours > 0 ? "{$totalBufferHours} Jam " : '') . ($remainingBufferMins > 0 ? "{$remainingBufferMins} Menit" : ($totalBufferHours > 0 ? '' : '0 Menit'));

                        $modalData = [
                            'code' => $booking->booking_code,
                            'status' => $statusEnum->label(),
                            'status_badge' => $statusEnum->badgeClasses(),
                            'status_raw' => $booking->status instanceof \App\Enums\BookingStatus ? $booking->status->value : (string) $booking->status,
                            'urgency_label' => $urgencyInfo['label'] ?? null,
                            'created_at' => $booking->created_at->translatedFormat('l, d F Y - H:i') . ' WIB',
                            'institution' => $booking->eventDetail->institution_name_snapshot ?? ($user->institution_name ?? $user->full_name),
                            'account_type' => $accountTypeLabel,
                            'user_name' => $user->full_name ?? '-',
                            'user_email' => $user->email ?? '-',
                            'user_phone' => $user->phone_number ?? '-',
                            'user_address' => $addressFull,
                            'user_city' => $user->city ?? '-',
                            'user_district' => $user->district ?? '-',
                            'social_media' => !empty($socialMediaList) ? implode(' • ', $socialMediaList) : '-',
                            'website' => $user->website ?? '-',
                            'is_suspended' => $isSuspended,
                            'suspension_reason' => $suspensionReason,
                            'suspended_at' => $suspendedAt,
                            'incident_count' => 0,
                            'pic_name' => $booking->eventDetail->pic_name_snapshot ?? $user->full_name,
                            'pic_phone' => $booking->eventDetail->pic_phone_snapshot ?? $user->phone_number,
                            'event_name' => $booking->eventDetail->event_name ?? '-',
                            'event_category' => $booking->eventDetail->event_category ?? '-',
                            'ekraf_subsector' => $booking->eventDetail->ekraf_subsector ?? '-',
                            'event_type' => $booking->eventDetail->event_type ?? '-',
                            'participants' => $estimatedParticipants,
                            'description' => $booking->eventDetail->event_description ?? '-',
                            'equipment' => $booking->eventDetail->equipment_needed ?? 'Tidak membawa peralatan khusus',
                            'proposal_url' => $booking->eventDetail && $booking->eventDetail->proposal_file_url ? asset('storage/' . $booking->eventDetail->proposal_file_url) : null,
                            'pricing_scheme' => $booking->pricing_scheme === 'commercial' ? 'Komersial' : 'Subsidi Pemkot Malang (Gratis)',
                            'slots' => $mappedSlots,
                            'slots_count' => $booking->slots->count(),
                            'total_duration' => $totalDurationFormatted,
                            'total_duration_with_buffer' => $totalWithBufferFormatted,
                            'has_overcapacity' => $hasOvercapacity,
                        ];
                    @endphp
                    <tr class="hover:bg-slate-50/90 transition-colors {{ $urgencyInfo ? 'bg-rose-50/20' : '' }}">
                        
                        <!-- 1. Kode, Tanggal Submit & Skema -->
                        <td class="px-5 py-4 align-top space-y-1">
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-extrabold text-sm text-[#001A72] block">{{ $booking->booking_code }}</span>
                                
                                <!-- Urgency Warning Icon beside Booking Code with Alpine Hover Tooltip -->
                                @if($urgencyInfo)
                                    <div class="relative inline-flex items-center"
                                         x-data="{ showTooltip: false }"
                                         @mouseenter="showTooltip = true"
                                         @mouseleave="showTooltip = false">
                                        <button type="button"
                                                class="inline-flex items-center justify-center text-rose-600 hover:text-rose-700 animate-pulse cursor-pointer p-0.5 rounded focus:outline-none"
                                                aria-label="{{ $urgencyInfo['label'] }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/></svg>
                                        </button>
                                        
                                        <!-- Tooltip Popover with Alpine Transition (Slate Charcoal Gray) -->
                                        <div x-show="showTooltip"
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 translate-x-1"
                                             x-transition:enter-end="opacity-100 translate-x-0"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 translate-x-0"
                                             x-transition:leave-end="opacity-0 translate-x-1"
                                             style="display: none;"
                                             class="absolute left-full top-1/2 -translate-y-1/2 ml-2 z-50 pointer-events-none flex items-center drop-shadow-xl">
                                            <div class="w-2 h-2 bg-slate-800 rotate-45 -mr-1 border-l border-b border-slate-600"></div>
                                            <div class="bg-slate-800 text-white text-[11px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap flex items-center gap-1.5 border border-slate-600 shadow-2xl">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-ping shrink-0"></span>
                                                <span>{{ $urgencyInfo['label'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <span class="text-[11px] text-slate-500 block">{{ $booking->created_at->translatedFormat('d M Y, H:i') }} WIB</span>

                            <!-- Skema Pembiayaan (Rounded Full Pill with spacing) -->
                            <div class="pt-2">
                                @if($booking->pricing_scheme === 'commercial')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">Komersial</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Subsidi Pemkot</span>
                                @endif
                            </div>
                        </td>

                        <!-- 2. Pemohon, Lembaga & PIC -->
                        <td class="px-5 py-4 align-top space-y-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-bold text-slate-900 text-xs block truncate max-w-56">
                                    {{ $booking->eventDetail->institution_name_snapshot ?? ($booking->user->institution_name ?? $booking->user->full_name) }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $accountTypeLabel }}
                                </span>
                            </div>

                            <div class="text-[11px] text-slate-600 flex items-center gap-1.5 pt-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <span class="font-medium text-slate-800">{{ $booking->eventDetail->pic_name_snapshot ?? $booking->user->full_name }}</span>
                            </div>
                            <div class="text-[11px] font-mono font-semibold text-emerald-700 flex items-center gap-1.5 pt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                <span>{{ $booking->eventDetail->pic_phone_snapshot ?? $booking->user->phone_number }}</span>
                            </div>
                        </td>

                        <!-- 3. Detail Acara & Sub-Sektor Ekraf -->
                        <td class="px-5 py-4 align-top space-y-1.5 max-w-64">
                            <span class="font-bold text-slate-900 text-xs block leading-snug">
                                {{ $booking->eventDetail->event_name ?? 'Event MCC' }}
                            </span>
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100">
                                    {{ $booking->eventDetail->ekraf_subsector ?? 'Ekraf Umum' }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">
                                    {{ $booking->eventDetail->estimated_participants ?? 0 }} Peserta
                                </span>
                            </div>
                        </td>

                        <!-- 4. Rekapitulasi Jadwal & Ruangan -->
                        <td class="px-5 py-4 align-top">
                            <div class="space-y-1.5 max-w-60">
                                @foreach($booking->slots->take(2) as $slot)
                                    <div class="text-[11px] bg-slate-50/90 p-2 rounded-brand border border-slate-200/80">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="font-bold text-slate-900 truncate">{{ $slot->room->name ?? 'Ruangan MCC' }}</span>
                                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-slate-200 text-slate-800 font-bold shrink-0">Lt. {{ $slot->room->floor_level ?? 1 }}</span>
                                        </div>
                                        <span class="text-slate-500 font-mono text-[10px] block mt-0.5">
                                            {{ \Carbon\Carbon::parse($slot->event_start_at)->translatedFormat('d M Y') }} &bull; {{ \Carbon\Carbon::parse($slot->event_start_at)->format('H:i') }}-{{ \Carbon\Carbon::parse($slot->event_end_at)->format('H:i') }}
                                        </span>
                                    </div>
                                @endforeach
                                @if($booking->slots->count() > 2)
                                    <span class="text-[10px] text-[#001A72] font-extrabold block pl-1">
                                        +{{ $booking->slots->count() - 2 }} sesi jadwal lainnya
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- 5. Status Kurasi -->
                        <td class="px-5 py-4 align-top">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $statusEnum->badgeClasses() }}">
                                {{ $statusEnum->label() }}
                            </span>
                        </td>

                        <!-- 6. Aksi Review Modal Button (Circular Eye Icon) -->
                        <td class="px-5 py-4 align-top text-right">
                            <button type="button"
                                    @click='openModal(@json($modalData))'
                                    title="Lihat Detail & Kurasi"
                                    aria-label="Lihat Detail & Kurasi"
                                    class="w-9 h-9 inline-flex items-center justify-center rounded-full bg-[#001A72] hover:bg-[#001250] text-white transition shadow-sm hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-slate-500">
                            <div class="max-w-sm mx-auto space-y-3">
                                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </div>
                                <p class="font-extrabold text-sm text-slate-800">Tidak ada permohonan ditemukan</p>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Tidak ada antrean proposal dengan parameter pencarian atau filter yang aktif.
                                </p>
                                <a href="{{ route('marketing.dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-brand bg-[#001A72] text-white text-xs font-bold hover:bg-[#001250] transition">
                                    Reset Semua Filter
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    @if($curations->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50">
            {{ $curations->links() }}
        </div>
    @endif
</div>
