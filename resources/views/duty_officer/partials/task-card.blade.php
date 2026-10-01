@php
    $firstSlot = $task->slots->sortBy('event_start_at')->first();
    $lastSlot = $task->slots->sortByDesc('event_end_at')->first();
    $room = $firstSlot?->room;
    $eventDetail = $task->eventDetail;
    $timing = $task->getOperationalCountdownInfo();
    $picName = $task->getPicName();
    $picPhone = $task->getPicPhoneNumber();
    $whatsappUrl = $task->getPicWhatsAppUrl(auth()->user()->full_name ?? 'Duty Officer');
    $equipmentItems = $eventDetail?->equipment_brought_items ?? [];
    $equipmentText = $eventDetail?->equipment_brought;

    $startIso = $firstSlot?->event_start_at ? $firstSlot->event_start_at->toIso8601String() : null;
    $endIso = $lastSlot?->event_end_at ? $lastSlot->event_end_at->toIso8601String() : null;
    $isCheckedIn = $task->isCheckedIn();
    $isConfirmed = $task->isConfirmed();
    $isCompleted = $task->isCompleted() || $task->isAbandoned();
    $needsOpening = $task->needsRoomOpening();
    $isRoomOpened = $task->isRoomOpened();
    $roomFacilities = $room ? $room->facilities->map(fn($f) => [
        'id' => $f->id,
        'facility_name' => $f->facility_name,
        'quantity' => $f->quantity,
        'unit' => $f->unit,
    ])->values()->toArray() : [];

    $taskPayload = [
        'bookingId' => $task->id,
        'bookingCode' => $task->booking_code,
        'statusLabel' => $task->status->label(),
        'eventName' => $eventDetail?->event_name ?? 'Kegiatan Komunitas MCC',
        'institutionName' => $eventDetail?->institution_name_snapshot ?? ($task->user?->institution_name ?? '-'),
        'roomName' => $room ? $room->name : '-',
        'floorLevel' => $room ? (string)$room->floor_level : '-',
        'schedule' => $timing['schedule_formatted'],
        'countdownText' => $timing['countdown_text'],
        'picName' => $picName,
        'picPhone' => $picPhone,
        'storageDrawer' => $task->latestKtpVaultLog?->storage_drawer_number,
        'whatsappUrl' => $whatsappUrl,
        'equipmentItems' => $equipmentItems,
        'facilities' => $roomFacilities,
        'description' => $eventDetail?->event_description ?? '',
    ];
@endphp

<div class="bg-white rounded-3xl border border-slate-200/90 shadow-card hover:shadow-lg transition-all duration-200 overflow-hidden"
     x-data="operationalCountdownCard({
         startIso: '{{ $startIso }}',
         endIso: '{{ $endIso }}',
         statusCode: '{{ $task->status->value }}',
         totalSeconds: {{ $timing['total_seconds'] }},
         remainingSeconds: {{ $timing['remaining_seconds'] }},
         elapsedSeconds: {{ $timing['elapsed_seconds'] }},
         progressPercent: {{ $timing['progress_percent'] }},
         initialCountdownText: '{{ addslashes($timing['countdown_text']) }}',
         initialStatusState: '{{ $timing['status_state'] }}'
     })"
     x-init="initTimer()">

    <!-- Card Top Header: Room, Floor, Code & Status -->
    <div class="p-4 sm:p-5 border-b border-slate-100 bg-slate-50/80 flex items-start justify-between gap-3">
        <div class="min-w-0 flex-1 space-y-1">
            <!-- Room & Floor Pill -->
            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-[#001A72] text-white shadow-2xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-indigo-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <span class="truncate max-w-[170px] sm:max-w-[220px]">{{ $room ? $room->name : 'Ruangan MCC' }}</span>
                </span>
                
                <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-bold bg-white text-slate-700 border border-slate-200 shadow-2xs">
                    Lantai {{ $room ? $room->floor_level : '-' }}
                </span>

                <span class="text-[11px] font-mono text-slate-400 font-bold">
                    #{{ $task->booking_code }}
                </span>
            </div>

            <!-- Event Title -->
            <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-snug">
                {{ $eventDetail?->event_name ?? 'Kegiatan Komunitas MCC' }}
            </h2>

            <!-- Institution Name -->
            <p class="text-xs sm:text-sm font-semibold text-[#001A72] truncate">
                {{ $eventDetail?->institution_name_snapshot ?? ($task->user?->institution_name ?? 'Komunitas') }}
            </p>
        </div>

        <!-- Status Pill -->
        <div class="shrink-0 text-right space-y-1">
            @if($needsOpening)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black bg-amber-100 text-amber-950 border border-amber-300 shadow-2xs animate-pulse">
                    <span class="w-2 h-2 rounded-full bg-amber-600 animate-ping"></span>
                    <span>Perlu Dibuka</span>
                </span>
            @elseif($isCheckedIn)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black bg-teal-50 text-teal-900 border border-teal-300 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                    <span>Berlangsung</span>
                </span>
            @elseif($isConfirmed)
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs">
                    Disetujui
                </span>
            @elseif($task->isAbandoned())
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200 shadow-2xs">
                    Ditinggal
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold {{ $task->status->badgeClasses() }}">
                    {{ $task->status->label() }}
                </span>
            @endif

            @if($isRoomOpened)
                <div class="text-[11px] text-emerald-700 font-bold flex items-center justify-end gap-1 pt-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    <span>Terbuka {{ $task->room_opened_at->format('H:i') }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- FR-DO-02: Big Mobile Action CTA Banner (If Room Needs Opening) -->
    @if($needsOpening)
        <div class="p-3.5 sm:p-4 bg-gradient-to-r from-amber-500 via-amber-500 to-amber-600 text-slate-950 flex flex-col gap-3 shadow-inner">
            <div class="flex items-center gap-2.5 text-xs">
                <div class="w-8 h-8 rounded-full bg-slate-950 text-amber-400 flex items-center justify-center shrink-0 shadow-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m17 5-5-3-5 3"/><path d="m17 19-5 3-5-3"/><path d="M2 12h20"/><path d="m5 7-3 5 3 5"/><path d="m19 7 3 5-3 5"/></svg>
                </div>
                <div class="min-w-0 leading-tight">
                    <span class="font-black block uppercase text-xs text-slate-950">Panitia Telah Check-In di FO</span>
                    <span class="text-[11px] sm:text-xs text-slate-900 font-medium">Buka pintu ruangan &amp; lakukan serah terima fasilitas.</span>
                </div>
            </div>

            <!-- Big Action Touch Target Button (min 48px) -->
            <button type="button"
                    @click='$dispatch("open-room-opening", @json($taskPayload))'
                    class="w-full flex items-center justify-center gap-2 px-5 py-3.5 bg-slate-950 hover:bg-slate-900 text-amber-300 hover:text-amber-200 rounded-2xl font-black text-xs sm:text-sm shadow-md transition active:scale-98 cursor-pointer"
                    style="min-height: 50px;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m17 5-5-3-5 3"/><path d="m17 19-5 3-5-3"/><path d="M2 12h20"/><path d="m5 7-3 5 3 5"/><path d="m19 7 3 5-3 5"/></svg>
                <span>Buka Akses Ruangan</span>
            </button>
        </div>
    @endif

    <!-- Card Body: Schedule, Countdown & Actions -->
    <div class="p-4 sm:p-5 space-y-4">
        
        <!-- Schedule & Real-time Progress Countdown -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-2.5">
            <div class="flex items-center justify-between gap-2">
                <!-- Clock / Time Schedule -->
                <div class="flex items-center gap-2 text-xs sm:text-sm font-black text-slate-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>{{ $timing['schedule_formatted'] }}</span>
                </div>

                <!-- Digital Countdown Counter Pill -->
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-black shadow-2xs"
                     :class="{
                         'bg-teal-100 text-teal-950 border border-teal-300': statusState === 'active',
                         'bg-amber-100 text-amber-950 border border-amber-300 animate-pulse': statusState === 'ending_soon',
                         'bg-rose-100 text-rose-950 border border-rose-300 font-black': statusState === 'overdue',
                         'bg-slate-200 text-slate-800': statusState === 'ended' || statusState === 'upcoming'
                     }">
                    <span class="w-2 h-2 rounded-full"
                          :class="{
                              'bg-teal-600 animate-pulse': statusState === 'active',
                              'bg-amber-600 animate-ping': statusState === 'ending_soon',
                              'bg-rose-600 animate-bounce': statusState === 'overdue',
                              'bg-slate-400': statusState === 'ended' || statusState === 'upcoming'
                          }"></span>
                    <span x-text="countdownText">{{ $timing['countdown_text'] }}</span>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-1.5">
                <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                    <div class="h-2.5 rounded-full transition-all duration-500"
                         :style="`width: ${progressPercent}%`"
                         :class="{
                             'bg-emerald-500': progressPercent < 75 && statusState === 'active',
                             'bg-amber-500': progressPercent >= 75 && statusState === 'active' || statusState === 'ending_soon',
                             'bg-rose-600': statusState === 'overdue',
                             'bg-slate-400': statusState === 'ended',
                             'bg-indigo-300': statusState === 'upcoming'
                         }">
                    </div>
                </div>
                <div class="flex justify-between items-center text-[10px] sm:text-[11px] text-slate-500 font-mono">
                    <span>Mulai: {{ $firstSlot?->event_start_at ? $firstSlot->event_start_at->format('H:i') : '00:00' }}</span>
                    <span x-text="`${progressPercent}% terpakai`" class="font-bold text-slate-700">{{ $timing['progress_percent'] }}%</span>
                    <span>Selesai: {{ $lastSlot?->event_end_at ? $lastSlot->event_end_at->format('H:i') : '00:00' }}</span>
                </div>
            </div>
        </div>

        <!-- PIC Info & Direct Touch-friendly WhatsApp Button -->
        <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-full bg-[#EEF2FF] text-[#001A72] border border-indigo-100 flex items-center justify-center shrink-0 font-black text-sm shadow-2xs">
                    {{ strtoupper(substr($picName, 0, 1)) }}
                </div>
                <div class="min-w-0 space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">PIC Lapangan</span>
                        @if($task->latestKtpVaultLog?->storage_drawer_number)
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                Laci: {{ $task->latestKtpVaultLog->storage_drawer_number }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm font-black text-slate-900 truncate">
                        {{ $picName }}
                    </p>
                    <p class="text-xs font-mono text-slate-500 truncate">
                        {{ $picPhone }}
                    </p>
                </div>
            </div>

            <!-- Direct WA CTA Button -->
            @if(!empty($whatsappUrl))
                <a href="{!! $whatsappUrl !!}" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs sm:text-sm shadow-xs transition active:scale-95 shrink-0 cursor-pointer"
                   style="min-height: 46px;">
                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.275.072.376-.044c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.42-.099.825zm-3.392-12.416c-5.514 0-10 4.486-10 10 0 1.761.459 3.417 1.264 4.864l-1.303 4.764 4.887-1.282c1.398.765 2.993 1.194 4.692 1.194 5.514 0 10-4.486 10-10s-4.486-10-10-10z"/>
                    </svg>
                    <span>WA PIC</span>
                </a>
            @endif
        </div>

        <!-- Rekapitulasi Peralatan Bawaan Pemohon (Accordion) -->
        <div class="rounded-2xl border border-slate-200 overflow-hidden" x-data="{ expanded: true }">
            <button type="button" 
                    @click="expanded = !expanded"
                    class="w-full px-4 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-left cursor-pointer select-none">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                    <span class="text-xs sm:text-sm font-bold text-slate-800">
                        Peralatan Bawaan Pemohon
                    </span>
                    @if(!empty($equipmentItems))
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72]">
                            {{ count($equipmentItems) }} Item
                        </span>
                    @endif
                </div>

                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 transform transition-transform" :class="expanded ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </button>

            <div x-show="expanded" x-collapse class="p-3.5 sm:p-4 bg-white">
                @if(!empty($equipmentItems))
                    <div class="flex flex-wrap gap-2">
                        @foreach($equipmentItems as $item)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#001A72]"></span>
                                <span>{{ $item }}</span>
                            </span>
                        @endforeach
                    </div>
                @elseif(!empty($equipmentText))
                    <p class="text-xs sm:text-sm text-slate-700 whitespace-pre-line bg-slate-50 p-3 rounded-2xl border border-slate-200">
                        {{ $equipmentText }}
                    </p>
                @else
                    <p class="text-xs text-slate-400 italic">Tidak membawa peralatan eksternal tambahan.</p>
                @endif
            </div>
        </div>

    </div>

    <!-- Card Footer: Assignment Info & Detail Button -->
    <div class="px-4 py-3 bg-slate-50/90 border-t border-slate-100 flex items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-1.5 text-xs text-slate-500 truncate">
            <span class="font-semibold text-slate-600">DO:</span>
            <span class="font-bold text-[#001A72] truncate">
                {{ $task->dutyOfficer?->full_name ?? 'Belum terdisposisi' }}
            </span>
        </div>

        <button type="button" 
                @click='$dispatch("open-detail-modal", @json($taskPayload))'
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white hover:bg-[#EEF2FF] text-[#001A72] border border-slate-200 hover:border-indigo-200 font-bold text-xs transition shadow-2xs cursor-pointer active:scale-95"
                style="min-height: 40px;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
            <span>Detail Lengkap</span>
        </button>
    </div>
</div>
