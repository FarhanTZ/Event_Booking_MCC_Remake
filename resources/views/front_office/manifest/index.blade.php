<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale() ?? 'id') }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() ?? '' }}">

    <title>Manifes Acara Harian - Front Office MCC</title>

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
      x-data="frontOfficeManifest()">

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
        
        <!-- 1. SISI KIRI: Sidebar Navigasi & Menu Front Office -->
        @include('front_office.partials.sidebar-left')

        <!-- 2. BAGIAN TENGAH: Workspace Utama Manifes Acara -->
        <main class="flex-1 min-w-0 w-full space-y-4">
            
            <!-- Top Control Bar: Title & Sidebar Toggles -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 sm:p-5 rounded-brand border border-slate-200 shadow-card">
                
                <!-- Left Section: Toggle Left Sidebar & Manifes Title -->
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
                                Manifes Acara Harian
                            </h1>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Jadwal sesi permohonan aktif untuk hari ini ({{ $targetDate->translatedFormat('d F Y') }})
                        </p>
                    </div>
                </div>

                <!-- Right Section: Check-in CTA & Toggle Right Sidebar -->
                <div class="flex items-center gap-2.5 self-end sm:self-auto">


                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-brand bg-[#EEF2FF] text-xs text-[#001A72] font-semibold border border-indigo-100 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        <span>{{ $targetDate->translatedFormat('l, d M Y') }}</span>
                    </div>

                    <button type="button"
                            @click="toggleRight()"
                            :class="sidebarRight ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-[#001A72] text-white hover:bg-[#001250]'"
                            class="w-9 h-9 inline-flex items-center justify-center rounded-brand transition cursor-pointer shrink-0 shadow-2xs"
                            title="Buka / Tutup Profil & Metrik Kanan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M15 3v18"/></svg>
                    </button>
                </div>
            </div>

            <!-- Multi-Parameter Instant Search & Filter Panel Component -->
            @include('front_office.partials.filter-bar')

            <!-- Tabel Manifes Acara Harian Component -->
            @include('front_office.partials.manifest-table')

        </main>

        <!-- 3. SISI KANAN: Profil Petugas & Kartu KPI Metrik Manifes -->
        @include('front_office.partials.sidebar-right')

    </div>

    <!-- Modal Detail Manifes Component -->
    @include('front_office.partials.modal-detail')

    <!-- Modal Validasi Check-In Dual-Method Component (FR-FO-02) -->
    @include('front_office.partials.modal-checkin')

    <!-- Modal Disposisi Pengawas Ruangan Duty Officer Component (FR-FO-04) -->
    @include('front_office.partials.modal-duty-officer')

    <!-- Modal Inisiasi Checkout & Pemilihan Ulang DO Shift Berjalan (FR-FO-06) -->
    @include('front_office.partials.modal-checkout-do')

    <!-- Modal Protokol Panitia Pulang Tanpa Lapor (FR-FO-08) -->
    @include('front_office.partials.modal-abandoned')

    <!-- Alpine.js Store & Handlers -->
    <script>
        function frontOfficeManifest() {
            return {
                sidebarLeft: true,
                sidebarRight: true,
                showModal: false,
                showCheckinModal: false,
                checkinMethod: 'manual', // 'scanner' | 'manual'
                manualBookingCode: '',
                checkinToken: '',
                isVerifying: false,
                isSubmittingCheckin: false,
                checkinError: '',
                verifiedBooking: null,
                isEarlyArrival: false,
                earlyWarningMessage: '',
                confirmEarlyTolerance: false,
                isCameraStreaming: false,
                mediaStream: null,
                ktpHolderName: '',
                picWhatsappNumber: '',
                checkinDutyOfficerId: '',

                // FR-FO-08 Abandoned Protocol State
                showAbandonedModal: false,
                isSubmittingAbandoned: false,
                abandonedError: '',
                unclaimedBoxId: '',
                abandonedNotes: '',
                abandonedTargetBooking: {
                    id: '',
                    booking_code: '',
                    event_name: '',
                    ktp_holder_name: '',
                    storage_drawer_number: '',
                    clearance_status: 'pending',
                    slot_id: null
                },

                // FR-FO-04 & FR-FO-05 Duty Officer Assignment & WhatsApp Quick Dispatch State
                showDutyOfficerModal: false,
                isSubmittingDO: false,
                isDispatching: false,
                sendWhatsAppDispatch: true,
                doAssignmentError: '',
                selectedDutyOfficerId: '',
                dutyOfficerNotes: '',
                doTargetBooking: {
                    id: '',
                    booking_code: '',
                    event_name: '',
                    room_name: '',
                    floor_level: 1,
                    slot_id: null,
                    event_start_at: '',
                    status_label: 'Aktif (Checked-In)',
                    initial_do_id: '',
                    duty_officer_id: ''
                },

                // FR-FO-06 Checkout Duty Officer Assignment & Shift Handoff State
                showCheckoutDoModal: false,
                isSubmittingCheckoutDO: false,
                isDispatchingCheckoutDO: false,
                sendCheckoutWhatsApp: true,
                checkoutDoError: '',
                selectedCheckoutDoId: '',
                checkoutDoNotes: '',
                checkoutTargetBooking: {
                    id: '',
                    booking_code: '',
                    event_name: '',
                    room_name: '',
                    floor_level: 1,
                    slot_id: null,
                    initial_do_id: '',
                    initial_do_name: ''
                },

                activeSlot: {
                    id: '',
                    slot_start: '',
                    slot_end: '',
                    duration: '',
                    room_name: '',
                    floor_level: 1,
                    capacity: 0,
                    booking_id: '',
                    booking_code: '',
                    status_value: '',
                    status_label: '',
                    status_badge: '',
                    event_name: '',
                    event_category: '',
                    ekraf_subsector: '',
                    estimated_participants: 0,
                    event_description: '',
                    equipment_needed: '',
                    requested_facilities: [],
                    pic_name: '',
                    pic_phone: '',
                    emergency_phone: '',
                    institution_name: '',
                    pricing_scheme: '',
                    duty_officer_id: '',
                    duty_officer_name: '',
                    duty_officer_phone: '',
                    duty_officer_notes: '',
                    do_notified_at: null,
                    checkout_do_id: '',
                    checkout_do_name: '',
                    checkout_do_phone: '',
                    checkout_do_notes: '',
                    checkout_do_notified_at: null,
                    clearance_status: 'pending',
                    ktp_holder_name: '',
                    storage_drawer_number: null,
                    ktp_returned_at: null,
                    incident: null,
                    all_slots: []
                },

                // FR-FO-07 Release KTP State
                isSubmittingReleaseKtp: false,

                toggleLeft() {
                    this.sidebarLeft = !this.sidebarLeft;
                },

                toggleRight() {
                    this.sidebarRight = !this.sidebarRight;
                },

                openManifestModal(slot) {
                    this.activeSlot = slot;
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                },

                openCheckinModal(bookingCode = '') {
                    this.resetCheckinState();
                    if (bookingCode) {
                        this.manualBookingCode = bookingCode;
                        this.checkinMethod = 'manual';
                    }
                    this.showCheckinModal = true;
                    if (this.checkinMethod === 'scanner') {
                        this.$nextTick(() => this.startQrCamera());
                    }
                },

                triggerManualCheckin(bookingCode) {
                    this.openCheckinModal(bookingCode);
                    this.verifyBookingCheckin();
                },

                closeCheckinModal() {
                    this.stopQrCamera();
                    this.showCheckinModal = false;
                    this.resetCheckinState();
                },

                setCheckinMethod(method) {
                    this.checkinMethod = method;
                    this.checkinError = '';
                    if (method === 'scanner') {
                        this.$nextTick(() => this.startQrCamera());
                    } else {
                        this.stopQrCamera();
                    }
                },

                resetCheckinState() {
                    this.manualBookingCode = '';
                    this.checkinToken = '';
                    this.checkinError = '';
                    this.verifiedBooking = null;
                    this.isEarlyArrival = false;
                    this.earlyWarningMessage = '';
                    this.confirmEarlyTolerance = false;
                    this.isVerifying = false;
                    this.isSubmittingCheckin = false;
                    this.ktpHolderName = '';
                    this.picWhatsappNumber = '';
                    this.checkinDutyOfficerId = '';
                },

                cancelVerification() {
                    this.resetCheckinState();
                },

                async startQrCamera() {
                    if (this.isCameraStreaming) return;
                    try {
                        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                            this.checkinError = 'Perangkat peramban web ini tidak mendukung akses kamera langsung. Silakan gunakan metode input manual.';
                            return;
                        }
                        this.mediaStream = await navigator.mediaDevices.getUserMedia({
                            video: { facingMode: 'environment' }
                        });
                        const video = this.$refs.videoFeed;
                        if (video) {
                            video.srcObject = this.mediaStream;
                            await video.play();
                            this.isCameraStreaming = true;
                        }
                    } catch (err) {
                        console.error('Camera access error:', err);
                        this.checkinError = 'Tidak dapat mengakses kamera: ' + (err.message || 'Izin kamera ditolak oleh peramban.');
                        this.isCameraStreaming = false;
                    }
                },

                stopQrCamera() {
                    if (this.mediaStream) {
                        this.mediaStream.getTracks().forEach(track => track.stop());
                        this.mediaStream = null;
                    }
                    this.isCameraStreaming = false;
                },

                async verifyBookingCheckin(token = null) {
                    this.isVerifying = true;
                    this.checkinError = '';
                    this.verifiedBooking = null;

                    const payload = {
                        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        booking_code: this.manualBookingCode ? this.manualBookingCode.trim() : null,
                        checkin_token: token || this.checkinToken || null
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
                            const errMessage = data.errors?.checkin?.[0] || data.errors?.identifier?.[0] || data.message || 'Gagal memverifikasi tiket booking.';
                            this.checkinError = errMessage;
                            this.isVerifying = false;
                            return;
                        }

                        this.verifiedBooking = data.booking;
                        this.isEarlyArrival = !!data.is_early;
                        this.earlyWarningMessage = data.warning_message || '';
                        this.confirmEarlyTolerance = !data.is_early; // Otomatis true jika bukan early
                        this.stopQrCamera();
                    } catch (e) {
                        this.checkinError = 'Terjadi kesalahan komunikasi dengan server MCC: ' + e.message;
                    } finally {
                        this.isVerifying = false;
                    }
                },

                async submitFinalCheckin() {
                    if (!this.verifiedBooking) return;
                    this.isSubmittingCheckin = true;
                    this.checkinError = '';

                    const payload = {
                        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        booking_code: this.verifiedBooking.booking_code,
                        checkin_token: this.checkinToken || null,
                        confirm_early: this.confirmEarlyTolerance,
                        ktp_holder_name: this.ktpHolderName,
                        pic_whatsapp_number: this.picWhatsappNumber,
                        duty_officer_id: this.checkinDutyOfficerId || null
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
                            const errMessage = data.errors?.early_warning?.[0] || data.errors?.ktp_holder_name?.[0] || data.errors?.pic_whatsapp_number?.[0] || data.errors?.duty_officer_id?.[0] || data.errors?.checkin?.[0] || data.message || 'Gagal memproses check-in.';
                            this.checkinError = errMessage;
                            this.isSubmittingCheckin = false;
                            return;
                        }

                        // Check-in berhasil -> tutup modal dan muat ulang halaman manifes
                        this.closeCheckinModal();
                        window.location.reload();
                    } catch (e) {
                        this.checkinError = 'Terjadi kesalahan saat memproses check-in: ' + e.message;
                        this.isSubmittingCheckin = false;
                    }
                },

                // FR-FO-04 & FR-FO-05 Modal Handlers & Dispatch Logic
                openDutyOfficerModal(bookingData) {
                    this.doTargetBooking = {
                        id: bookingData.id,
                        booking_code: bookingData.booking_code,
                        event_name: bookingData.event_name,
                        room_name: bookingData.room_name,
                        floor_level: bookingData.floor_level || 1,
                        slot_id: bookingData.slot_id || null,
                        event_start_at: bookingData.event_start_at || '-',
                        status_label: bookingData.status_label || 'Aktif (Checked-In)',
                        initial_do_id: bookingData.duty_officer_id || '',
                        duty_officer_id: bookingData.duty_officer_id || ''
                    };

                    this.selectedDutyOfficerId = bookingData.duty_officer_id ? String(bookingData.duty_officer_id) : '';
                    this.dutyOfficerNotes = bookingData.duty_officer_notes || '';
                    this.sendWhatsAppDispatch = true;
                    this.doAssignmentError = '';
                    this.isSubmittingDO = false;
                    this.showDutyOfficerModal = true;
                },

                closeDutyOfficerModal() {
                    this.showDutyOfficerModal = false;
                    this.doAssignmentError = '';
                },

                skipDutyOfficerAssignment() {
                    this.closeDutyOfficerModal();
                    window.location.reload();
                },

                async submitDutyOfficerAssignment() {
                    if (!this.selectedDutyOfficerId || !this.doTargetBooking.id) return;
                    this.isSubmittingDO = true;
                    this.doAssignmentError = '';

                    const payload = {
                        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        booking_id: this.doTargetBooking.id,
                        slot_id: this.doTargetBooking.slot_id || null,
                        duty_officer_id: this.selectedDutyOfficerId,
                        notes: this.dutyOfficerNotes || null
                    };

                    try {
                        const response = await fetch('{{ route('front_office.assign_duty_officer') }}', {
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
                            const errMessage = data.errors?.duty_officer_id?.[0] || data.message || 'Gagal menyimpan disposisi Duty Officer.';
                            this.doAssignmentError = errMessage;
                            this.isSubmittingDO = false;
                            return;
                        }

                        // FR-FO-05: Jika dicentang untuk kirim notifikasi WhatsApp langsung
                        if (this.sendWhatsAppDispatch) {
                            try {
                                const notifyPayload = {
                                    _token: payload._token,
                                    booking_id: this.doTargetBooking.id,
                                    slot_id: this.doTargetBooking.slot_id || null
                                };
                                const notifyRes = await fetch('{{ route('front_office.notify_duty_officer') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': payload._token
                                    },
                                    body: JSON.stringify(notifyPayload)
                                });
                                const notifyData = await notifyRes.json();
                                if (notifyData.success && notifyData.whatsapp_url) {
                                    window.open(notifyData.whatsapp_url, '_blank');
                                }
                            } catch (notifyErr) {
                                console.warn('Dispatch notification warning:', notifyErr);
                            }
                        }

                        // Berhasil disposisi -> tutup modal dan refresh halaman
                        this.closeDutyOfficerModal();
                        window.location.reload();
                    } catch (e) {
                        this.doAssignmentError = 'Terjadi kesalahan komunikasi dengan server: ' + e.message;
                        this.isSubmittingDO = false;
                    }
                },

                // FR-FO-05 Quick Dispatch Action
                async notifyDutyOfficer(bookingId, slotId = null) {
                    if (!bookingId) return;
                    this.isDispatching = true;

                    const payload = {
                        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        booking_id: bookingId,
                        slot_id: slotId
                    };

                    try {
                        const response = await fetch('{{ route('front_office.notify_duty_officer') }}', {
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
                            const errMessage = data.errors?.duty_officer?.[0] || data.errors?.phone_number?.[0] || data.message || 'Gagal menyiapkan notifikasi WhatsApp.';
                            alert(errMessage);
                            return;
                        }

                        if (this.activeSlot) {
                            this.activeSlot.do_notified_at = data.notified_at || 'Baru saja';
                        }

                        if (data.whatsapp_url) {
                            window.open(data.whatsapp_url, '_blank');
                        }
                    } catch (e) {
                        alert('Terjadi kesalahan komunikasi dengan server: ' + e.message);
                    } finally {
                        this.isDispatching = false;
                    }
                },

                // FR-FO-06 Checkout Duty Officer Modal Handlers & WhatsApp Dispatch Logic
                openCheckoutDoModal(bookingData) {
                    this.checkoutTargetBooking = {
                        id: bookingData.id,
                        booking_code: bookingData.booking_code,
                        event_name: bookingData.event_name,
                        room_name: bookingData.room_name,
                        floor_level: bookingData.floor_level || 1,
                        slot_id: bookingData.slot_id || null,
                        initial_do_id: bookingData.initial_do_id || '',
                        initial_do_name: bookingData.initial_do_name || ''
                    };

                    // Default to initial DO if available, or current checkout DO
                    this.selectedCheckoutDoId = bookingData.current_checkout_do_id || bookingData.initial_do_id || '';
                    this.checkoutDoNotes = '';
                    this.sendCheckoutWhatsApp = true;
                    this.checkoutDoError = '';
                    this.isSubmittingCheckoutDO = false;
                    this.showCheckoutDoModal = true;
                },

                closeCheckoutDoModal() {
                    this.showCheckoutDoModal = false;
                    this.checkoutDoError = '';
                },

                async submitCheckoutDoAssignment() {
                    if (!this.selectedCheckoutDoId || !this.checkoutTargetBooking.id) return;
                    this.isSubmittingCheckoutDO = true;
                    this.checkoutDoError = '';

                    const payload = {
                        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        booking_id: this.checkoutTargetBooking.id,
                        slot_id: this.checkoutTargetBooking.slot_id || null,
                        assigned_checkout_do_id: this.selectedCheckoutDoId,
                        notes: this.checkoutDoNotes || null,
                        send_whatsapp: this.sendCheckoutWhatsApp
                    };

                    try {
                        const response = await fetch('{{ route('front_office.checkout.initiate_do') }}', {
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
                            const errMessage = data.errors?.assigned_checkout_do_id?.[0] || data.errors?.duty_officer_id?.[0] || data.message || 'Gagal menyimpan penugasan DO checkout.';
                            this.checkoutDoError = errMessage;
                            this.isSubmittingCheckoutDO = false;
                            return;
                        }

                        // Jika opsi kirim WhatsApp aktif dan URL tersedia, buka tab WhatsApp
                        if (this.sendCheckoutWhatsApp && data.whatsapp_url) {
                            window.open(data.whatsapp_url, '_blank');
                        }

                        // Berhasil -> tutup modal dan refresh halaman
                        this.closeCheckoutDoModal();
                        window.location.reload();
                    } catch (e) {
                        this.checkoutDoError = 'Terjadi kesalahan komunikasi dengan server: ' + e.message;
                        this.isSubmittingCheckoutDO = false;
                    }
                },

                async notifyCheckoutDutyOfficer(bookingId, slotId = null) {
                    if (!bookingId) return;
                    this.isDispatchingCheckoutDO = true;

                    const payload = {
                        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        booking_id: bookingId,
                        slot_id: slotId
                    };

                    try {
                        const response = await fetch('{{ route('front_office.checkout.notify_do') }}', {
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
                            const errMessage = data.errors?.duty_officer?.[0] || data.errors?.phone_number?.[0] || data.message || 'Gagal menyiapkan notifikasi WhatsApp checkout.';
                            alert(errMessage);
                            return;
                        }

                        if (this.activeSlot) {
                            this.activeSlot.checkout_do_notified_at = data.notified_at || 'Baru saja';
                        }

                        if (data.whatsapp_url) {
                            window.open(data.whatsapp_url, '_blank');
                        }
                    } catch (e) {
                        alert('Terjadi kesalahan komunikasi dengan server: ' + e.message);
                    } finally {
                        this.isDispatchingCheckoutDO = false;
                    }
                },

                // FR-FO-07 Pemrosesan Pelepasan KTP Berdasarkan Sinyal Clearance DO
                async submitReleaseKtp() {
                    if (!this.activeSlot || !this.activeSlot.booking_id) return;

                    const holderName = this.activeSlot.ktp_holder_name || 'Penanggung Jawab';
                    const drawerInfo = this.activeSlot.storage_drawer_number ? ` di laci ${this.activeSlot.storage_drawer_number}` : '';
                    
                    if (!confirm(`Konfirmasi penyerahan KTP fisik kepada ${holderName}${drawerInfo} dan tandai acara Selesai (Completed)?`)) {
                        return;
                    }

                    this.isSubmittingReleaseKtp = true;

                    const payload = {
                        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        booking_id: this.activeSlot.booking_id,
                        slot_id: this.activeSlot.id || null
                    };

                    try {
                        const response = await fetch('{{ route('front_office.checkout.release_ktp') }}', {
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
                            const errMessage = data.errors?.clearance?.[0] || data.message || 'Gagal memproses pelepasan KTP.';
                            alert(errMessage);
                            this.isSubmittingReleaseKtp = false;
                            return;
                        }

                        alert(data.message || 'Check-Out selesai! KTP fisik telah diserahkan kembali ke panitia.');
                        this.closeModal();
                        window.location.reload();
                    } catch (e) {
                        alert('Terjadi kesalahan komunikasi dengan server: ' + e.message);
                        this.isSubmittingReleaseKtp = false;
                    }
                },

                // FR-FO-08 Protokol Panitia Pulang Tanpa Lapor Handlers
                openAbandonedModal(slotData) {
                    this.abandonedTargetBooking = {
                        id: slotData.booking_id || slotData.id,
                        booking_code: slotData.booking_code,
                        event_name: slotData.event_name,
                        ktp_holder_name: slotData.ktp_holder_name || '',
                        storage_drawer_number: slotData.storage_drawer_number || '',
                        clearance_status: slotData.clearance_status || 'pending',
                        slot_id: slotData.id || null
                    };

                    this.unclaimedBoxId = slotData.unclaimed_box_id || '';
                    this.abandonedNotes = '';
                    this.abandonedError = '';
                    this.isSubmittingAbandoned = false;
                    this.showAbandonedModal = true;
                },

                closeAbandonedModal() {
                    this.showAbandonedModal = false;
                    this.abandonedError = '';
                },

                async submitMarkAbandoned() {
                    if (!this.unclaimedBoxId) {
                        this.abandonedError = 'Nomor kotak arsip (Unclaimed Box ID) wajib diisi.';
                        return;
                    }

                    this.isSubmittingAbandoned = true;
                    this.abandonedError = '';

                    const payload = {
                        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        booking_id: this.abandonedTargetBooking.id,
                        slot_id: this.abandonedTargetBooking.slot_id || null,
                        unclaimed_box_id: this.unclaimedBoxId,
                        notes: this.abandonedNotes || null
                    };

                    try {
                        const response = await fetch('{{ route('front_office.abandoned.mark') }}', {
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
                            const errMessage = data.errors?.unclaimed_box_id?.[0] || data.message || 'Gagal menandai user pulang tanpa lapor.';
                            this.abandonedError = errMessage;
                            this.isSubmittingAbandoned = false;
                            return;
                        }

                        alert(data.message || 'Protokol panitia pulang tanpa lapor berhasil diproses.');
                        this.closeAbandonedModal();
                        if (this.showModal) this.closeModal();
                        window.location.reload();
                    } catch (e) {
                        this.abandonedError = 'Terjadi kesalahan komunikasi dengan server: ' + e.message;
                        this.isSubmittingAbandoned = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
