<!-- ==================================================================== -->
<!-- MODAL VALIDASI CHECK-IN DUAL-METHOD (SCANNER QR & INPUT KODE MANUAL) -->
<!-- ==================================================================== -->
<div x-show="showCheckinModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-checkin-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="showCheckinModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeCheckinModal()" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

    <!-- Centered Modal Wrapper -->
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6 relative z-10 pointer-events-none">
        <div x-show="showCheckinModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop
             class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-xl border border-slate-200 pointer-events-auto my-8">
            
            <!-- Modal Header -->
            <div class="bg-[#001A72] text-white px-6 py-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-brand bg-white/10 flex items-center justify-center text-white shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-white tracking-tight leading-snug">
                            Validasi Check-In Kedatangan
                        </h3>
                        <p class="text-[11px] text-indigo-200">
                            Pindai QR Code E-Pass atau masukkan Kode Booking manual
                        </p>
                    </div>
                </div>

                <button @click="closeCheckinModal()" class="p-1.5 rounded-brand text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer shrink-0" aria-label="Tutup Modal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <!-- Method Switcher Tab (Scanner vs Manual Input) -->
            <div class="px-6 pt-4 bg-slate-50 border-b border-slate-200 flex items-center gap-2">
                <button type="button" 
                        @click="setCheckinMethod('scanner')"
                        :class="checkinMethod === 'scanner' ? 'border-[#001A72] text-[#001A72] font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                        class="px-4 py-2.5 text-xs rounded-t-brand border-b-2 transition flex items-center gap-2 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect width="10" height="10" x="7" y="7" rx="1"/></svg>
                    <span>Metode 1: QR Scanner</span>
                </button>

                <button type="button" 
                        @click="setCheckinMethod('manual')"
                        :class="checkinMethod === 'manual' ? 'border-[#001A72] text-[#001A72] font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                        class="px-4 py-2.5 text-xs rounded-t-brand border-b-2 transition flex items-center gap-2 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    <span>Metode 2: Input Manual</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                
                <!-- Error Banner -->
                <template x-if="checkinError">
                    <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-brand flex items-start gap-2.5 text-rose-700 text-xs animate-shake">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 text-rose-600 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                        <div class="flex-1 font-semibold leading-relaxed" x-text="checkinError"></div>
                    </div>
                </template>

                <!-- Step 1: Input Mode (Scanner atau Form Teks) -->
                <template x-if="!verifiedBooking">
                    <div class="space-y-4">
                        
                        <!-- TAB A: SCANNER QR CODE KAMERA -->
                        <div x-show="checkinMethod === 'scanner'" class="space-y-3">
                            <div class="relative bg-slate-900 rounded-brand overflow-hidden flex flex-col items-center justify-center min-h-[220px] p-4 text-center border border-slate-700">
                                
                                <video id="qr-video-feed" 
                                       x-ref="videoFeed" 
                                       class="w-full max-h-[260px] object-cover rounded" 
                                       playsinline 
                                       autoplay 
                                       x-show="isCameraStreaming"></video>
                                
                                <!-- Scanner Viewfinder Overlay -->
                                <div x-show="isCameraStreaming" 
                                     class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="w-44 h-44 border-2 border-emerald-400 rounded-lg relative animate-pulse shadow-[0_0_15px_rgba(52,211,153,0.4)]">
                                        <div class="absolute -top-1 -left-1 w-4 h-4 border-t-2 border-l-2 border-emerald-300"></div>
                                        <div class="absolute -top-1 -right-1 w-4 h-4 border-t-2 border-r-2 border-emerald-300"></div>
                                        <div class="absolute -bottom-1 -left-1 w-4 h-4 border-b-2 border-l-2 border-emerald-300"></div>
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 border-b-2 border-r-2 border-emerald-300"></div>
                                    </div>
                                </div>

                                <!-- Camera Inactive State Placeholder -->
                                <div x-show="!isCameraStreaming" class="text-white space-y-2.5 p-4">
                                    <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center mx-auto text-indigo-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                                    </div>
                                    <div class="font-bold text-xs">Kamera Pemindai E-Pass</div>
                                    <p class="text-[11px] text-slate-400 max-w-xs leading-relaxed">
                                        Klik tombol di bawah untuk mengaktifkan webcam/kamera dan arahkan ke QR Code E-Pass pemohon.
                                    </p>
                                    <button type="button" 
                                            @click="startQrCamera()" 
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-brand bg-white text-[#001A72] hover:bg-slate-100 text-xs font-bold transition shadow cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                        <span>Nyalakan Kamera Scanner</span>
                                    </button>
                                </div>
                            </div>

                            <p class="text-[11px] text-slate-500 text-center">
                                Pastikan izin akses kamera (*Camera Permission*) telah diizinkan pada peramban web meja depan.
                            </p>
                        </div>

                        <!-- TAB B: FORM INPUT KODE BOOKING MANUAL -->
                        <div x-show="checkinMethod === 'manual'" class="space-y-3">
                            <label class="block text-xs font-bold text-slate-700">
                                Kode Booking Tiket E-Pass:
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       x-model="manualBookingCode" 
                                       @keydown.enter.prevent="verifyBookingCheckin()"
                                       placeholder="Contoh: MCC-2026-X89B" 
                                       class="block w-full px-3.5 py-2.5 text-xs uppercase font-mono font-bold rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] placeholder:normal-case placeholder:font-normal placeholder:text-slate-400">
                            </div>
                            <p class="text-[11px] text-slate-500">
                                Masukkan kode booking alfanumerik yang tertera pada proposal atau kartu digital E-Pass pemohon.
                            </p>

                            <button type="button" 
                                    @click="verifyBookingCheckin()"
                                    :disabled="!manualBookingCode || isVerifying"
                                    class="w-full py-2.5 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white text-xs font-bold transition shadow-xs flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg x-show="!isVerifying" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                <svg x-show="isVerifying" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span x-text="isVerifying ? 'Memverifikasi Data...' : 'Verifikasi Tiket Booking'"></span>
                            </button>
                        </div>

                    </div>
                </template>

                <!-- Step 2: Konfirmasi Data Booking & Toleransi Waktu -->
                <template x-if="verifiedBooking">
                    <div class="space-y-4">
                        
                        <!-- Header Kartu Terverifikasi -->
                        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-brand flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                                <span class="font-bold text-xs text-emerald-900">Tiket Terverifikasi Sah</span>
                            </div>
                            <span class="font-mono font-extrabold text-xs text-[#001A72]" x-text="verifiedBooking.booking_code"></span>
                        </div>

                        <!-- Card Detail Acara & PIC -->
                        <div class="border border-slate-200 rounded-brand p-4 space-y-2.5 text-xs bg-slate-50/50">
                            <div>
                                <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Nama Acara</span>
                                <span class="font-extrabold text-slate-900 text-sm block" x-text="verifiedBooking.event_name"></span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-200">
                                <div>
                                    <span class="text-slate-500 text-[11px] block">Ruangan:</span>
                                    <span class="font-bold text-slate-800" x-text="verifiedBooking.room_name + ' (Lt. ' + verifiedBooking.floor_level + ')'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-500 text-[11px] block">Jadwal Sesi:</span>
                                    <span class="font-bold text-slate-800 font-mono" x-text="verifiedBooking.event_start_at + ' - ' + verifiedBooking.event_end_at"></span>
                                </div>
                                <div>
                                    <span class="text-slate-500 text-[11px] block">Penyelenggara:</span>
                                    <span class="font-bold text-slate-800" x-text="verifiedBooking.institution_name"></span>
                                </div>
                                <div>
                                    <span class="text-slate-500 text-[11px] block">PIC Lapangan:</span>
                                    <span class="font-bold text-slate-800" x-text="verifiedBooking.pic_name + ' (' + verifiedBooking.pic_phone + ')'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Form Serah Terima Jaminan Fisik KTP (FR-FO-03) -->
                        <div class="p-3.5 bg-indigo-50/70 border border-indigo-200 rounded-brand space-y-3">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                                <span class="font-bold text-xs text-[#001A72]">Pencatatan Jaminan Fisik KTP (FR-FO-03)</span>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                        Nama Pemilik KTP Fisik <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" 
                                           x-model="ktpHolderName" 
                                           placeholder="Nama penyerah KTP asli" 
                                           class="block w-full px-3 py-2 text-xs rounded-brand border border-slate-300 bg-white font-semibold focus:border-[#001A72] focus:ring-[#001A72]">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                        WhatsApp Hari-H <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" 
                                           x-model="picWhatsappNumber" 
                                           placeholder="Contoh: 081234567890" 
                                           class="block w-full px-3 py-2 text-xs font-mono font-bold rounded-brand border border-slate-300 bg-white focus:border-[#001A72] focus:ring-[#001A72]">
                                </div>
                            </div>
                            <div class="text-[10px] text-slate-500 flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                <span>KTP fisik langsung disimpan aman oleh FO tanpa memindai foto / NIK digital.</span>
                            </div>
                        </div>

                        <!-- Disposisi / Ganti Staf Duty Officer (DO) Langsung -->
                        <div class="p-3.5 bg-emerald-50/60 border border-emerald-200 rounded-brand space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-[11px] font-bold text-emerald-950">
                                    Penugasan / Ganti Duty Officer (DO) Lapangan
                                </label>
                                <span class="text-[10px] text-emerald-700 font-semibold bg-emerald-100/80 px-1.5 py-0.2 rounded">
                                    Fleksibel / On-Duty
                                </span>
                            </div>
                            <select x-model="checkinDutyOfficerId" 
                                    class="block w-full py-2 px-3 text-xs rounded-brand border border-slate-300 bg-white shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 font-medium">
                                <option value="">-- Disposisikan nanti setelah check-in --</option>
                                @foreach($dutyOfficers as $do)
                                    <option value="{{ $do->id }}">
                                        {{ $do->full_name }} • {{ $do->phone_number ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Peringatan Toleransi Awal (>60 Menit Sebelum Acara) -->
                        <template x-if="isEarlyArrival">
                            <div class="p-3.5 bg-amber-50 border border-amber-300 rounded-brand space-y-2">
                                <div class="flex items-start gap-2 text-amber-800 text-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 text-amber-600 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                                    <div>
                                        <span class="font-bold block">Peringatan Check-in Lebih Awal</span>
                                        <span class="text-[11px] text-amber-900 leading-relaxed block" x-text="earlyWarningMessage"></span>
                                    </div>
                                </div>
                                <label class="flex items-center gap-2 pt-2 border-t border-amber-200 text-xs text-amber-900 font-semibold cursor-pointer">
                                    <input type="checkbox" x-model="confirmEarlyTolerance" class="rounded text-[#001A72] focus:ring-[#001A72]">
                                    <span>Saya mengonfirmasi bahwa pemohon diizinkan masuk dan persiapan ruangan lebih awal.</span>
                                </label>
                            </div>
                        </template>

                        <!-- Tombol Aksi Final Check-in -->
                        <div class="flex items-center gap-2 pt-2">
                            <button type="button" 
                                    @click="cancelVerification()"
                                    class="flex-1 py-2.5 rounded-brand bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition cursor-pointer">
                                Batal / Reset
                            </button>
                            <button type="button" 
                                    @click="submitFinalCheckin()"
                                    :disabled="isSubmittingCheckin || !ktpHolderName || !picWhatsappNumber || (isEarlyArrival && !confirmEarlyTolerance)"
                                    class="flex-2 py-2.5 rounded-brand bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg x-show="!isSubmittingCheckin" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <svg x-show="isSubmittingCheckin" class="w-4 h-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span x-text="isSubmittingCheckin ? 'Memproses Check-In...' : 'Konfirmasi Check-In Resmi'"></span>
                            </button>
                        </div>

                    </div>
                </template>

            </div>

            <!-- Modal Footer -->
            <div class="bg-slate-50 px-6 py-3.5 border-t border-slate-200 flex items-center justify-between text-xs">
                <span class="text-slate-500 text-[11px]">Sistem Validasi Meja Depan MCC</span>
                <button type="button" 
                        @click="closeCheckinModal()" 
                        class="px-3 py-1.5 rounded-brand bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold transition cursor-pointer">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>
