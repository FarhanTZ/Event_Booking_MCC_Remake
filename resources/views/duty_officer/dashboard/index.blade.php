<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale() ?? 'id') }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() ?? '' }}">

    <title>Dasbor Tugas Lapangan - Duty Officer MCC</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html, body {
            overflow-x: hidden !important;
            max-width: 100vw;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        [x-cloak] { display: none !important; }

        /* Custom MCC Scrollbar */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #94A3B8 #F1F5F9;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #F1F5F9;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #94A3B8;
            border-radius: 9999px;
            border: 1px solid #F1F5F9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #64748B;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-[#001A72] selection:text-white"
      x-data="dutyOfficerDashboard()">

    <!-- Flash Messages Notification -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
             class="bg-emerald-600 text-white px-4 py-3 shadow-lg fixed top-6 right-6 z-50 rounded-brand flex items-center gap-3 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span class="text-xs sm:text-sm font-semibold">{{ session('success') }}</span>
            <button @click="show = false" class="ml-2 hover:opacity-80 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- ======================================================================== -->
    <!-- 3-COLUMN WORKSPACE WRAPPER (FIXED SIDES, SCROLLABLE CENTER) -->
    <!-- ======================================================================== -->
    <div class="flex-1 w-full max-w-[1720px] 2xl:max-w-[1920px] mx-auto p-3 sm:p-4 lg:p-5 flex flex-col lg:flex-row gap-4 lg:gap-5 items-start min-h-screen">
        
        <!-- 1. SISI KIRI: Sidebar Navigasi & Menu Duty Officer -->
        @include('duty_officer.partials.sidebar-left')

        <!-- 2. BAGIAN TENGAH: Workspace Utama Dasbor Tugas Lapangan -->
        <main class="flex-1 min-w-0 w-full space-y-4">
            
            <!-- Top Control Bar: Title & Sidebar Toggles -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 sm:p-5 rounded-brand border border-slate-200 shadow-card">
                
                <!-- Left Section: Toggle Left Sidebar & Dashboard Title -->
                <div class="flex items-center gap-3">
                    <button type="button" 
                            @click="toggleLeft()"
                            :class="sidebarLeft ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-[#001A72] text-white hover:bg-[#001250]'"
                            class="w-9 h-9 inline-flex items-center justify-center rounded-brand transition cursor-pointer shrink-0 shadow-2xs"
                            title="Buka / Tutup Menu Navigasi Kiri">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/></svg>
                    </button>

                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight leading-tight">
                                Dasbor Tugas Duty Officer
                            </h1>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100">
                                FR-DO-01
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Panel kontrol pengawasan sesi ruangan dan pendampingan lapangan ({{ $target_date->translatedFormat('d F Y') }})
                        </p>
                    </div>
                </div>

                <!-- Right Section: Live Shift & Toggle Right Sidebar -->
                <div class="flex items-center gap-2.5 self-end sm:self-auto">
                    <!-- Shift Pill -->
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-brand bg-[#EEF2FF] text-xs text-[#001A72] font-semibold border border-indigo-100 shrink-0">
                        <span class="w-2 h-2 rounded-full bg-[#001A72] animate-pulse"></span>
                        @if($filter->shift === 'morning')
                            <span>Shift Pagi (08:00 - 16:00)</span>
                        @elseif($filter->shift === 'evening')
                            <span>Shift Sore (16:00 - 22:00)</span>
                        @else
                            <span>Semua Shift Operasional</span>
                        @endif
                    </div>

                    <!-- Right Sidebar Toggle -->
                    <button type="button" 
                            @click="toggleRight()"
                            :class="sidebarRight ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-[#001A72] text-white hover:bg-[#001250]'"
                            class="w-9 h-9 inline-flex items-center justify-center rounded-brand transition cursor-pointer shrink-0 shadow-2xs"
                            title="Buka / Tutup Panel Profil & Metrik Kanan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M15 3v18"/></svg>
                    </button>
                </div>
            </div>

            <!-- Multi-Parameter Realtime Search & Filter Panel Component -->
            @include('duty_officer.partials.filter-bar')

            <!-- Daftar Penugasan Lapangan Cards -->
            <div class="space-y-4">
                <div class="flex items-center justify-between px-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black uppercase tracking-wider text-slate-700">
                            Daftar Penugasan Lapangan
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-[#001A72] text-white">
                            {{ count($tasks) }} Acara
                        </span>
                    </div>

                    @if($filter->floor)
                        <span class="text-xs font-bold text-[#001A72] px-2.5 py-0.5 rounded-full bg-indigo-50 border border-indigo-100">
                            Terfilter: Lantai {{ $filter->floor }}
                        </span>
                    @endif
                </div>

                @forelse($tasks as $task)
                    @include('duty_officer.partials.task-card', ['task' => $task])
                @empty
                    <!-- Empty State -->
                    <div class="bg-white rounded-brand border border-slate-200 p-8 sm:p-14 text-center shadow-card space-y-3">
                        <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="m9 14 2 2 4-4"/></svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-black text-slate-800">
                            Tidak Ada Jadwal Tugas Lapangan
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                            Tidak ditemukan penugasan sesi ruangan yang cocok dengan kriteria filter untuk tanggal {{ $target_date->translatedFormat('d F Y') }}.
                        </p>
                        <div class="pt-2">
                            <a href="{{ route('duty_officer.dashboard') }}" 
                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#001A72] text-white font-bold text-xs sm:text-sm rounded-brand hover:bg-[#001250] transition cursor-pointer shadow-xs"
                               style="min-height: 40px;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg>
                                <span>Reset Filter</span>
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

        </main>

        <!-- 3. SISI KANAN: Profil Petugas & Kartu KPI Metrik Lapangan -->
        @include('duty_officer.partials.sidebar-right')

    </div>

    <!-- Modal Detail Operasional & Peralatan Mandiri Component -->
    @include('duty_officer.partials.modal-detail')

    <!-- Alpine.js Store & Timing Handlers -->
    <script>
        function dutyOfficerDashboard() {
            return {
                sidebarLeft: true,
                sidebarRight: true,
                currentTimeString: '',
                detailModalOpen: false,
                activeTask: {
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

                toggleLeft() {
                    this.sidebarLeft = !this.sidebarLeft;
                },

                toggleRight() {
                    this.sidebarRight = !this.sidebarRight;
                },

                openDetailModal(data) {
                    this.activeTask = data;
                    this.detailModalOpen = true;
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
                        this.countdownText = hours > 0 ? `Mulai dlm ${hours}j ${mins}m` : `Mulai dlm ${mins} menit`;
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
                            this.countdownText = `Lewat Jadwal (${hours > 0 ? hours + 'j ' : ''}${mins}m)`;
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
