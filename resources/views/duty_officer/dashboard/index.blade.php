<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale() ?? 'id') }}" class="h-full bg-slate-200/60">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() ?? '' }}">
    <meta name="theme-color" content="#001A72">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>Duty Officer - Mobile MCC</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html, body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
            overflow-x: hidden;
        }
        [x-cloak] { display: none !important; }

        /* Hide Scrollbar for Horizontal Swipeable Containers */
        .no-scrollbar::-webkit-scrollbar {
            display: none !important;
        }
        .no-scrollbar {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
            -webkit-overflow-scrolling: touch;
        }

        /* Smooth horizontal touch scroll */
        .touch-pan-x {
            touch-action: pan-x;
            -webkit-overflow-scrolling: touch;
        }

        /* Custom Scrollbar for Bottom Sheets */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #CBD5E1 #F8FAFC;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }

        /* Safe area bottom padding for iPhone home indicator */
        .pb-safe {
            padding-bottom: env(safe-area-inset-bottom, 0.5rem);
        }
    </style>
</head>
<body class="bg-slate-200/50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-[#001A72] selection:text-white"
      x-data="dutyOfficerMobileApp()"
      @open-room-opening.window="openRoomOpeningModal($event.detail)"
      @open-detail-modal.window="openDetailModal($event.detail)">

    <!-- Flash Message Notification -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="fixed top-4 inset-x-4 max-w-md mx-auto z-50 bg-emerald-600 text-white px-4 py-3 rounded-2xl shadow-xl flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 text-emerald-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span class="text-xs font-bold leading-tight line-clamp-2">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-200 hover:text-white p-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- ======================================================================== -->
    <!-- MOBILE APP CONTAINER (NATIVE APP FEEL) -->
    <!-- ======================================================================== -->
    <div class="w-full max-w-lg mx-auto min-h-screen bg-slate-100 flex flex-col pb-24 shadow-2xl md:my-0 md:border-x md:border-slate-300 relative">
        
        <!-- 1. STICKY TOP APP HEADER -->
        <header class="sticky top-0 z-30 bg-[#001A72] text-white px-4 py-3 shadow-md flex items-center justify-between gap-3">
            <!-- Brand & App Identity -->
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center p-1.5 shadow-inner shrink-0">
                    <img src="{{ asset('assets/images/media/branding/mcc-aset-logo blue 1.png') }}" 
                         alt="MCC Logo" 
                         class="h-full w-auto object-contain brightness-0 invert">
                </div>
                <div class="min-w-0">
                    <span class="font-black text-sm tracking-tight text-white block leading-tight">
                        MCC Duty Officer
                    </span>
                    <!-- Live Clock & Date -->
                    <div class="flex items-center gap-2 text-[11px] text-indigo-200 font-medium leading-tight mt-0.5">
                        <span class="font-mono font-bold text-emerald-400" x-text="currentTimeString">--:--:-- WIB</span>
                        <span>•</span>
                        <span class="truncate">{{ $target_date->translatedFormat('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Profile & Quick Sheet Trigger Button -->
            <button type="button" 
                    @click="profileSheetOpen = true"
                    class="flex items-center gap-2 p-1.5 pr-2.5 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/15 transition cursor-pointer shrink-0">
                <div class="w-7 h-7 rounded-xl bg-white text-[#001A72] flex items-center justify-center font-black text-xs shadow-xs">
                    {{ strtoupper(substr(auth()->user()->full_name ?? 'D', 0, 1)) }}
                </div>
                <div class="hidden sm:block text-left text-xs leading-tight">
                    <span class="font-bold block text-white truncate max-w-[80px]">
                        {{ auth()->user()->full_name ?? 'DO' }}
                    </span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </button>
        </header>

        <!-- 2. MAIN APP CONTENT FEED -->
        <main class="flex-1 p-4 sm:p-5 space-y-5 min-w-0">

            <!-- Mobile Instant Search & Horizontal Drag/Swipe Chips Filter -->
            @include('duty_officer.partials.filter-bar')

            <!-- Section Title & Counter -->
            <div class="flex items-center justify-between pt-1 px-1">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-800">
                        {{ $filter->scope === 'assigned' ? 'Penugasan Saya' : 'Daftar Tugas Lapangan' }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-[#001A72] text-white shadow-2xs">
                        {{ count($tasks) }} Acara
                    </span>
                </div>

                <div class="text-xs font-bold text-slate-500">
                    {{ $target_date->translatedFormat('d F Y') }}
                </div>
            </div>

            <!-- Task Cards Feed -->
            <div class="space-y-4">
                @forelse($tasks as $task)
                    @include('duty_officer.partials.task-card', ['task' => $task])
                @empty
                    <!-- Mobile Empty State (Spacious & Extended Downwards) -->
                    <div class="bg-white rounded-3xl border border-slate-200/90 py-12 sm:py-16 px-6 text-center shadow-card space-y-4 my-2 flex flex-col items-center justify-center">
                        <div class="w-16 h-16 rounded-3xl bg-[#EEF2FF] text-[#001A72] border border-indigo-100 flex items-center justify-center shadow-2xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="m9 14 2 2 4-4"/></svg>
                        </div>
                        <div class="space-y-1 max-w-xs">
                            <h3 class="text-base font-black text-slate-800 tracking-tight">
                                Tidak Ada Jadwal Tugas
                            </h3>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Tidak ditemukan penugasan sesi ruangan yang cocok untuk kriteria filter saat ini.
                            </p>
                        </div>
                        <div class="pt-3 pb-2">
                            <a href="{{ route('duty_officer.dashboard') }}" 
                               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#001A72] hover:bg-[#001250] text-white font-black text-xs sm:text-sm rounded-2xl transition shadow-md active:scale-95 cursor-pointer"
                               style="min-height: 46px;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg>
                                <span>Reset Semua Filter</span>
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

        </main>

        <!-- 3. FIXED BOTTOM NAVIGATION BAR -->
        @include('duty_officer.partials.bottom-nav')

    </div>

    <!-- 4. PROFILE & SOP BOTTOM SHEET -->
    @include('duty_officer.partials.mobile-profile-sheet')

    <!-- 5. MODAL DETAIL OPERASIONAL & PERALATAN MANDIRI (BOTTOM SHEET) -->
    @include('duty_officer.partials.modal-detail')

    <!-- 6. MODAL BUKA AKSES RUANGAN & CHECKLIST (FR-DO-02) (BOTTOM SHEET) -->
    @include('duty_officer.partials.modal-room-opening')

    <!-- Alpine.js Store & Timing Handlers -->
    <script>
        function horizontalScrollDrag() {
            return {
                isDown: false,
                startX: 0,
                scrollLeft: 0,
                startDrag(e) {
                    this.isDown = true;
                    const container = this.$refs.scrollContainer;
                    this.startX = e.pageX - container.offsetLeft;
                    this.scrollLeft = container.scrollLeft;
                },
                stopDrag() {
                    this.isDown = false;
                },
                onDrag(e) {
                    if (!this.isDown) return;
                    e.preventDefault();
                    const container = this.$refs.scrollContainer;
                    const x = e.pageX - container.offsetLeft;
                    const walk = (x - this.startX) * 1.5;
                    container.scrollLeft = this.scrollLeft - walk;
                }
            };
        }

        function dutyOfficerMobileApp() {
            return {
                profileSheetOpen: false,
                currentTimeString: '',
                detailModalOpen: false,
                openingModalOpen: false,
                openingActionUrl: '',
                activeTask: {
                    bookingId: null,
                    bookingCode: '',
                    statusLabel: '',
                    eventName: '',
                    institutionName: '',
                    roomName: '',
                    floorLevel: '',
                    schedule: '',
                    countdownText: '',
                    picName: '',
                    picPhone: '',
                    storageDrawer: null,
                    whatsappUrl: '',
                    equipmentItems: [],
                    facilities: [],
                    description: ''
                },

                init() {
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);
                },

                updateTime() {
                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    this.currentTimeString = `${hours}:${minutes}:${seconds} WIB`;
                },

                openDetailModal(data) {
                    this.activeTask = data;
                    this.detailModalOpen = true;
                },

                openRoomOpeningModal(data) {
                    this.activeTask = data;
                    this.openingActionUrl = '/duty-officer/bookings/' + data.bookingId + '/confirm-opening';
                    this.openingModalOpen = true;
                }
            };
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('operationalCountdownCard', (config) => ({
                startIso: config.startIso,
                endIso: config.endIso,
                statusCode: config.statusCode,
                totalSeconds: config.totalSeconds || 1,
                remainingSeconds: config.remainingSeconds || 0,
                elapsedSeconds: config.elapsedSeconds || 0,
                progressPercent: config.progressPercent || 0,
                countdownText: config.initialCountdownText || '',
                statusState: config.initialStatusState || 'unknown',
                timerInterval: null,

                initTimer() {
                    if (!this.startIso || !this.endIso) return;
                    this.calculate();
                    this.timerInterval = setInterval(() => this.calculate(), 1000);
                },

                calculate() {
                    const now = new Date();
                    const start = new Date(this.startIso);
                    const end = new Date(this.endIso);

                    if (isNaN(start.getTime()) || isNaN(end.getTime())) return;

                    const total = Math.max(1, Math.floor((end - start) / 1000));
                    this.totalSeconds = total;

                    if (now < start) {
                        const diff = Math.floor((start - now) / 1000);
                        const hours = Math.floor(diff / 3600);
                        const mins = Math.floor((diff % 3600) / 60);
                        this.countdownText = hours > 0 ? `Mulai ${hours}j ${mins}m` : `Mulai ${mins}m`;
                        this.progressPercent = 0;
                        this.statusState = 'upcoming';
                        return;
                    }

                    if (now > end) {
                        const overdue = Math.floor((now - end) / 1000);
                        const hours = Math.floor(overdue / 3600);
                        const mins = Math.floor((overdue % 3600) / 60);
                        this.progressPercent = 100;
                        
                        if (this.statusCode === 'checked_in' && overdue > 1800) {
                            this.statusState = 'overdue';
                            this.countdownText = `Lewat (${hours > 0 ? hours + 'j ' : ''}${mins}m)`;
                        } else {
                            this.statusState = 'ended';
                            this.countdownText = `Selesai (${hours > 0 ? hours + 'j ' : ''}${mins}m lalu)`;
                        }
                        return;
                    }

                    // Active ongoing session
                    const elapsed = Math.floor((now - start) / 1000);
                    const rem = Math.max(0, Math.floor((end - now) / 1000));
                    this.elapsedSeconds = elapsed;
                    this.remainingSeconds = rem;
                    this.progressPercent = Math.min(100, Math.round((elapsed / total) * 100));

                    const remHours = Math.floor(rem / 3600);
                    const remMins = Math.floor((rem % 3600) / 60);
                    const remSecs = rem % 60;

                    this.countdownText = remHours > 0 
                        ? `Sisa ${String(remHours).padStart(2, '0')}:${String(remMins).padStart(2, '0')}:${String(remSecs).padStart(2, '0')}`
                        : `Sisa ${String(remMins).padStart(2, '0')}:${String(remSecs).padStart(2, '0')}`;

                    this.statusState = rem <= 900 ? 'ending_soon' : 'active';
                }
            }));
        });
    </script>
</body>
</html>
