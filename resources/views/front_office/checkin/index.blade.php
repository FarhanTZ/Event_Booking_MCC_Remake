<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale() ?? 'id') }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() ?? '' }}">

    <title>Validasi Check-In Kedatangan - Front Office MCC</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- HTML5 QR Code Scanner Library for Robust Cross-Browser Camera & File Scanning -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <style>
        html, body {
            overflow-x: hidden !important;
            max-width: 100vw;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        [x-cloak] { display: none !important; }

        #qr-reader {
            width: 100% !important;
            border: none !important;
            border-radius: 0.75rem;
            overflow: hidden;
        }
        #qr-reader video {
            border-radius: 0.75rem;
            object-fit: cover;
            width: 100% !important;
            max-height: 320px !important;
        }
        #qr-reader__scan_region {
            background: #020617;
            border-radius: 0.75rem;
        }
        #qr-reader__dashboard_section {
            padding: 0 !important;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-[#001A72] selection:text-white"
      x-data="checkinValidator('{{ $initialBookingCode ?? '' }}')">

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

    <!-- TOP STATUS BAR / BRAND HEADER -->
    <header class="w-full bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 transition-all">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/images/media/branding/mcc-aset-logohorizontal.png') }}" 
                     alt="Malang Creative Center Logo" 
                     class="h-8 w-auto object-contain shrink-0">
                <div class="h-6 w-px bg-slate-200"></div>
                <div>
                    <h1 class="text-sm font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <span>Validasi Check-In Kedatangan</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#EEF2FF] text-[#001A72] border border-indigo-100">
                            FR-FO-02
                        </span>
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-[11px] text-slate-600 font-semibold border border-slate-200/60">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                <span>{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>
    </header>

    <!-- MAIN KIOSK CONTAINER -->
    <main class="flex-1 flex flex-col justify-center items-center px-4 py-8 sm:py-12">
        <div class="w-full max-w-xl space-y-5">

            <!-- TAB SELECTOR (CLEAN SEGMENTED CONTROL) -->
            <div class="bg-slate-200/70 p-1 rounded-xl flex items-center gap-1 shadow-inner" x-show="!verifiedBooking">
                <button type="button" 
                        @click="setMethod('scanner')"
                        :class="method === 'scanner' ? 'bg-white text-[#001A72] shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                        class="flex-1 py-2.5 px-4 rounded-lg text-xs transition flex items-center justify-center gap-2 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect width="10" height="10" x="7" y="7" rx="1"/></svg>
                    <span>Scan QR E-Pass</span>
                </button>

                <button type="button" 
                        @click="setMethod('manual')"
                        :class="method === 'manual' ? 'bg-white text-[#001A72] shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                        class="flex-1 py-2.5 px-4 rounded-lg text-xs transition flex items-center justify-center gap-2 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    <span>Input Kode Booking</span>
                </button>
            </div>

            <!-- ERROR ALERT -->
            <template x-if="errorMessage">
                <div class="p-4 bg-rose-50 border border-rose-200/80 rounded-2xl flex items-start gap-3 text-rose-800 text-xs shadow-sm animate-shake">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 text-rose-600 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                    <div class="flex-1 font-semibold leading-relaxed" x-text="errorMessage"></div>
                    <button type="button" @click="errorMessage = ''" class="text-rose-400 hover:text-rose-700 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
            </template>

            <!-- SUCCESS ALERT -->
            <template x-if="successMessage">
                <div class="p-4 bg-emerald-50 border border-emerald-200/80 rounded-2xl flex items-start gap-3 text-emerald-900 text-xs shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 text-emerald-600 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <div class="flex-1 font-semibold leading-relaxed" x-text="successMessage"></div>
                </div>
            </template>

            <!-- CARD UTAMA -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                
                <!-- STATE 1: SCANNER ATAU INPUT KODE -->
                <div x-show="!verifiedBooking" class="p-6 sm:p-8">
                    
                    <!-- TAB 1: SCANNER KAMERA -->
                    <div x-show="method === 'scanner'" class="space-y-4">
                        <div class="relative bg-slate-950 rounded-xl overflow-hidden min-h-[280px] border border-slate-800 flex flex-col items-center justify-center">
                            
                            <!-- Container untuk HTML5 QR Code Scanner -->
                            <div id="qr-reader" class="w-full" x-show="isCameraStreaming"></div>

                            <!-- Kamera Belum Aktif Placeholder -->
                            <div x-show="!isCameraStreaming" class="text-center p-6 space-y-3.5">
                                <div class="w-12 h-12 rounded-2xl bg-white/10 text-white flex items-center justify-center mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-white">Arahkan Kamera ke QR Code E-Pass</div>
                                    <p class="text-[11px] text-slate-400 mt-1 max-w-xs mx-auto">
                                        Pindai barcode/QR dari layar smartphone atau cetakan tiket
                                    </p>
                                </div>
                                <button type="button" 
                                        @click="startCamera()" 
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white text-[#001A72] hover:bg-slate-100 text-xs font-bold transition shadow-sm cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                    <span>Aktifkan Scanner Kamera</span>
                                </button>
                            </div>
                        </div>

                        <!-- Opsi Upload Gambar QR jika Kamera Fisik Bermasalah -->
                        <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1">
                            <label class="inline-flex items-center gap-1.5 text-[#001A72] hover:underline font-semibold cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                                <span>Upload File Gambar QR E-Pass</span>
                                <input type="file" accept="image/*" class="hidden" @change="scanQrFile($event)">
                            </label>

                            <button type="button" x-show="isCameraStreaming" @click="stopCamera()" class="text-rose-600 hover:underline font-semibold cursor-pointer">
                                Matikan Kamera
                            </button>
                        </div>
                    </div>

                    <!-- TAB 2: INPUT MANUAL KODE BOOKING -->
                    <div x-show="method === 'manual'" class="space-y-4">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700">
                                Kode Booking Pemohon
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-mono font-bold text-sm">
                                    #
                                </div>
                                <input type="text" 
                                       x-model="bookingCode" 
                                       @keydown.enter.prevent="verifyBooking()"
                                       placeholder="Contoh: MCC-2026-X89B" 
                                       class="block w-full pl-8 pr-4 py-3 text-sm uppercase font-mono font-bold rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:border-[#001A72] focus:ring-2 focus:ring-[#001A72]/20 transition placeholder:normal-case placeholder:font-normal placeholder:text-slate-400">
                            </div>
                            <p class="text-[11px] text-slate-400">
                                Masukkan 12-16 digit kode booking unik pada bukti registrasi.
                            </p>
                        </div>

                        <button type="button" 
                                @click="verifyBooking()"
                                :disabled="!bookingCode || isVerifying"
                                class="w-full py-3 px-4 rounded-xl bg-[#001A72] hover:bg-[#001250] text-white text-xs font-bold transition shadow-sm flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg x-show="!isVerifying" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            <svg x-show="isVerifying" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-text="isVerifying ? 'Memverifikasi...' : 'Verifikasi Tiket Booking'"></span>
                        </button>
                    </div>

                </div>

                <!-- STATE 2: HASIL VERIFIKASI & KONFIRMASI CHECK-IN -->
                <div x-show="verifiedBooking" class="p-6 sm:p-8 space-y-6">
                    
                    <!-- Verified Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <div>
                                <h2 class="text-xs font-bold text-emerald-900 leading-tight">Tiket Terverifikasi Sah</h2>
                                <span class="text-[11px] text-slate-500 font-medium">Status: Disetujui (Confirmed)</span>
                            </div>
                        </div>
                        <span class="font-mono font-bold text-xs text-[#001A72] bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100" x-text="verifiedBooking?.booking_code"></span>
                    </div>

                    <!-- Event Detail Card -->
                    <div class="space-y-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Informasi Kegiatan</span>
                        <h3 class="text-base font-extrabold text-slate-900 leading-snug" x-text="verifiedBooking?.event_name"></h3>
                        
                        <div class="grid grid-cols-2 gap-3 pt-2 text-xs">
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <span class="text-[10px] text-slate-400 block font-medium">Ruangan &amp; Lantai</span>
                                <span class="font-bold text-slate-800 text-xs block mt-0.5" x-text="verifiedBooking?.room_name"></span>
                                <span class="text-[10px] text-slate-500" x-text="'Lantai ' + verifiedBooking?.floor_level"></span>
                            </div>

                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <span class="text-[10px] text-slate-400 block font-medium">Jadwal Sesi Hari Ini</span>
                                <span class="font-mono font-bold text-slate-800 text-xs block mt-0.5" x-text="(verifiedBooking?.event_start_at || '') + ' - ' + (verifiedBooking?.event_end_at || '')"></span>
                                <span class="text-[10px] text-indigo-600 font-medium">Sesi Berjalan</span>
                            </div>

                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <span class="text-[10px] text-slate-400 block font-medium">Penyelenggara</span>
                                <span class="font-bold text-slate-800 text-xs block mt-0.5 truncate" x-text="verifiedBooking?.institution_name"></span>
                            </div>

                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <span class="text-[10px] text-slate-400 block font-medium">Penanggung Jawab (PIC)</span>
                                <span class="font-bold text-slate-800 text-xs block mt-0.5 truncate" x-text="verifiedBooking?.pic_name"></span>
                                <span class="text-[10px] font-mono text-slate-500" x-text="verifiedBooking?.pic_phone"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Early Warning Banner -->
                    <template x-if="isEarlyArrival">
                        <div class="p-4 bg-amber-50 border border-amber-200/80 rounded-xl space-y-2.5">
                            <div class="flex items-start gap-2.5 text-amber-900 text-xs">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 text-amber-600 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                                <div>
                                    <span class="font-bold block text-xs">Peringatan Kedatangan Awal</span>
                                    <span class="text-[11px] text-amber-800 leading-relaxed block mt-0.5" x-text="earlyWarningMessage"></span>
                                </div>
                            </div>
                            <label class="flex items-center gap-2 pt-2 border-t border-amber-200 text-xs text-amber-950 font-semibold cursor-pointer">
                                <input type="checkbox" x-model="confirmEarlyTolerance" class="rounded text-[#001A72] focus:ring-[#001A72] w-3.5 h-3.5">
                                <span class="text-[11px]">Izinkan pemohon masuk lebih awal untuk persiapan ruangan.</span>
                            </label>
                        </div>
                    </template>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-3 pt-2">
                        <button type="button" 
                                @click="resetVerification()"
                                class="flex-1 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">
                            Batal
                        </button>
                        
                        <button type="button" 
                                @click="processFinalCheckin()"
                                :disabled="isSubmitting || (isEarlyArrival && !confirmEarlyTolerance)"
                                class="flex-2 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg x-show="!isSubmitting" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            <svg x-show="isSubmitting" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-text="isSubmitting ? 'Memproses...' : 'Aktivasi Check-In'"></span>
                        </button>
                    </div>

                </div>

            </div>

        </div>
    </main>

    <!-- Alpine.js Store & Logic -->
    <script>
        function checkinValidator(initialCode = '') {
            return {
                method: 'manual', // 'scanner' | 'manual'
                bookingCode: initialCode,
                checkinToken: '',
                isVerifying: false,
                isSubmitting: false,
                errorMessage: '',
                successMessage: '',
                verifiedBooking: null,
                isEarlyArrival: false,
                earlyWarningMessage: '',
                confirmEarlyTolerance: false,
                isCameraStreaming: false,
                html5QrCode: null,

                init() {
                    if (this.bookingCode) {
                        this.verifyBooking();
                    }
                },

                setMethod(m) {
                    this.method = m;
                    this.errorMessage = '';
                    if (m === 'scanner') {
                        this.$nextTick(() => this.startCamera());
                    } else {
                        this.stopCamera();
                    }
                },

                async startCamera() {
                    if (this.isCameraStreaming) return;
                    this.errorMessage = '';

                    try {
                        if (typeof Html5Qrcode === 'undefined') {
                            this.errorMessage = 'Pustaka scanner QR belum termuat sempurna. Periksa koneksi internet Anda atau gunakan input manual.';
                            return;
                        }

                        if (!this.html5QrCode) {
                            this.html5QrCode = new Html5Qrcode("qr-reader");
                        }

                        const config = {
                            fps: 10,
                            qrbox: { width: 250, height: 250 },
                            aspectRatio: 1.0
                        };

                        await this.html5QrCode.start(
                            { facingMode: "environment" },
                            config,
                            (decodedText) => {
                                this.onQrSuccess(decodedText);
                            },
                            (errorMessage) => {
                                // Scan loop silent ignore
                            }
                        );

                        this.isCameraStreaming = true;
                    } catch (e) {
                        console.warn('Environment camera failed, trying fallback to any camera:', e);
                        try {
                            // Fallback jika tidak ada kamera 'environment' (misal: webcam laptop)
                            const cameras = await Html5Qrcode.getCameras();
                            if (cameras && cameras.length > 0) {
                                const cameraId = cameras[0].id;
                                await this.html5QrCode.start(
                                    cameraId,
                                    { fps: 10, qrbox: { width: 250, height: 250 } },
                                    (decodedText) => this.onQrSuccess(decodedText),
                                    () => {}
                                );
                                this.isCameraStreaming = true;
                                return;
                            }
                        } catch (fallbackErr) {
                            console.error('Fallback camera error:', fallbackErr);
                        }

                        console.error('Camera startup error:', e);
                        if (e.name === 'NotAllowedError' || String(e).includes('Permission denied')) {
                            this.errorMessage = 'Izin kamera ditolak oleh browser/sistem. Klik ikon kunci di address bar browser untuk mengizinkan (Allow) kamera.';
                        } else if (e.name === 'NotFoundError') {
                            this.errorMessage = 'Tidak ditemukan perangkat webcam pada komputer ini.';
                        } else {
                            this.errorMessage = 'Tidak dapat mengakses kamera: ' + (e.message || e);
                        }
                        this.isCameraStreaming = false;
                    }
                },

                async stopCamera() {
                    if (this.html5QrCode && this.isCameraStreaming) {
                        try {
                            await this.html5QrCode.stop();
                        } catch (e) {
                            console.warn('Error stopping camera:', e);
                        }
                    }
                    this.isCameraStreaming = false;
                },

                async scanQrFile(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    try {
                        if (!this.html5QrCode) {
                            this.html5QrCode = new Html5Qrcode("qr-reader");
                        }
                        const decodedText = await this.html5QrCode.scanFile(file, true);
                        this.onQrSuccess(decodedText);
                    } catch (err) {
                        this.errorMessage = 'QR Code tidak terdeteksi pada gambar tersebut. Pastikan gambar jelas.';
                    }
                },

                onQrSuccess(decodedText) {
                    this.stopCamera();
                    // Parsing decodedText (bisa berisi token langsung atau kode booking)
                    if (decodedText.startsWith('MCC-') || decodedText.length < 20) {
                        this.bookingCode = decodedText.trim();
                        this.checkinToken = '';
                    } else {
                        this.checkinToken = decodedText.trim();
                        this.bookingCode = '';
                    }
                    this.verifyBooking();
                },

                resetVerification() {
                    this.verifiedBooking = null;
                    this.errorMessage = '';
                    this.successMessage = '';
                    this.isEarlyArrival = false;
                    this.earlyWarningMessage = '';
                    this.confirmEarlyTolerance = false;
                },

                async verifyBooking() {
                    this.isVerifying = true;
                    this.errorMessage = '';
                    this.successMessage = '';
                    this.verifiedBooking = null;

                    const payload = {
                        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        booking_code: this.bookingCode ? this.bookingCode.trim() : null,
                        checkin_token: this.checkinToken ? this.checkinToken.trim() : null
                    };

                    try {
                        const response = await fetch('{{ route('front_office.checkin.inspect') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': payload._token
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (!response.ok || !data.success) {
                            this.errorMessage = data.errors?.checkin?.[0] || data.errors?.identifier?.[0] || data.message || 'Gagal memverifikasi tiket booking.';
                            return;
                        }

                        this.verifiedBooking = data.booking;
                        this.isEarlyArrival = !!data.is_early;
                        this.earlyWarningMessage = data.warning_message || '';
                        this.confirmEarlyTolerance = !data.is_early;
                        this.stopCamera();
                    } catch (err) {
                        this.errorMessage = 'Terjadi gangguan jaringan dengan server: ' + err.message;
                    } finally {
                        this.isVerifying = false;
                    }
                },

                async processFinalCheckin() {
                    if (!this.verifiedBooking) return;
                    this.isSubmitting = true;
                    this.errorMessage = '';

                    const payload = {
                        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        booking_code: this.verifiedBooking.booking_code,
                        checkin_token: this.checkinToken || null,
                        confirm_early: this.confirmEarlyTolerance
                    };

                    try {
                        const response = await fetch('{{ route('front_office.checkin.process') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': payload._token
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (!response.ok || !data.success) {
                            this.errorMessage = data.errors?.early_warning?.[0] || data.errors?.checkin?.[0] || data.message || 'Gagal mengaktifkan check-in.';
                            return;
                        }

                        this.successMessage = data.message || 'Check-in berhasil diaktifkan!';
                        this.verifiedBooking = null;
                        this.bookingCode = '';
                        this.checkinToken = '';
                    } catch (err) {
                        this.errorMessage = 'Terjadi kesalahan sistem saat memproses check-in: ' + err.message;
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
