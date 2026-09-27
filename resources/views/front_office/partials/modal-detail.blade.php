<!-- ==================================================================== -->
<!-- MODAL DETAIL MANIFES SESI ACARA (FRONT OFFICE) -->
<!-- ==================================================================== -->
<div x-show="showModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="showModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeModal()" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

    <!-- Centered Modal Wrapper -->
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6 relative z-10 pointer-events-none">
        <div x-show="showModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop
             class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-2xl border border-slate-200 pointer-events-auto my-8">
            
            <!-- Modal Header -->
            <div class="bg-[#001A72] text-white px-6 py-4 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-indigo-200 block">Detail Manifes Sesi Acara</span>
                    <h3 class="text-base font-bold text-white tracking-tight leading-snug truncate" x-text="activeSlot.event_name"></h3>
                </div>

                <button @click="closeModal()" class="p-1.5 rounded-brand text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer shrink-0" aria-label="Tutup Modal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                
                <!-- Strip Header Status & Kode Booking -->
                <div class="bg-slate-50 p-3.5 rounded-brand border border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Kode Booking</span>
                        <span class="font-mono font-extrabold text-[#001A72] text-sm" x-text="activeSlot.booking_code"></span>
                    </div>

                    <div class="text-right">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider mb-0.5">Status Tiket</span>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold inline-block shadow-2xs" 
                              :class="activeSlot.status_badge" 
                              x-text="activeSlot.status_label"></span>
                    </div>
                </div>

                <!-- Informasi Sesi Hari Ini -->
                <div class="bg-[#EEF2FF] border border-indigo-100 rounded-brand p-4 space-y-2">
                    <div class="flex items-center gap-2 text-xs font-bold text-[#001A72]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        <span>Sesi Operasional Terpilih</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-xs pt-1">
                        <div>
                            <span class="text-slate-500 text-[11px] block">Ruangan:</span>
                            <span class="font-extrabold text-slate-900 text-sm" x-text="activeSlot.room_name"></span>
                            <span class="text-[11px] text-slate-500 block" x-text="'Lantai ' + activeSlot.floor_level + ' (Kapasitas ' + activeSlot.capacity + ' Orang)'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[11px] block">Waktu Sesi:</span>
                            <span class="font-extrabold text-slate-900 text-sm font-mono" x-text="activeSlot.slot_start + ' - ' + activeSlot.slot_end"></span>
                            <span class="text-[11px] text-[#001A72] font-semibold block" x-text="'Durasi: ' + activeSlot.duration"></span>
                        </div>
                    </div>
                </div>

                <!-- Penanggung Jawab Pengawas Ruangan (FR-FO-04 Duty Officer & FR-FO-05 WhatsApp Dispatch) -->
                <!-- Hanya aktif dan muncul setelah pemohon/acara berstatus Checked-In / Selesai -->
                <template x-if="activeSlot.status_value === 'checked_in' || activeSlot.status_value === 'completed'">
                    <div class="border rounded-brand p-4 space-y-3"
                         :class="activeSlot.duty_officer_name ? 'bg-emerald-50/60 border-emerald-200' : 'bg-amber-50/60 border-amber-200'">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-xs font-bold"
                                 :class="activeSlot.duty_officer_name ? 'text-emerald-900' : 'text-amber-900'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
                                <span>Pengawas Ruangan (Duty Officer)</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <template x-if="activeSlot.duty_officer_name && activeSlot.do_notified_at">
                                    <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-300 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span>Tugas Terkirim ke WA</span>
                                    </span>
                                </template>
                                <template x-if="activeSlot.duty_officer_name && !activeSlot.do_notified_at">
                                    <span class="text-[10px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-full border border-amber-300">
                                        Belum Di-WhatsApp
                                    </span>
                                </template>
                                <template x-if="!activeSlot.duty_officer_name">
                                    <span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full border border-amber-300">
                                        Belum Disposisi
                                    </span>
                                </template>
                            </div>
                        </div>

                        <template x-if="activeSlot.duty_officer_name">
                            <div class="space-y-2.5 pt-1">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                    <div>
                                        <span class="text-slate-500 text-[11px] block">Petugas Lapangan:</span>
                                        <span class="font-extrabold text-slate-900 text-sm" x-text="activeSlot.duty_officer_name"></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 text-[11px] block">Kontak DO:</span>
                                        <span class="font-bold text-[#001A72] font-mono" x-text="activeSlot.duty_officer_phone || '-'"></span>
                                    </div>
                                    <template x-if="activeSlot.duty_officer_notes">
                                        <div class="sm:col-span-2 pt-1 border-t border-emerald-200/60">
                                            <span class="text-slate-500 text-[10px] block">Catatan Disposisi:</span>
                                            <span class="text-slate-700 italic text-[11px]" x-text="activeSlot.duty_officer_notes"></span>
                                        </div>
                                    </template>
                                </div>

                                <!-- FR-FO-04 & FR-FO-05 Action Ganti DO & WhatsApp Quick Dispatch -->
                                <div class="pt-2 border-t border-emerald-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div class="text-[11px]">
                                        <template x-if="activeSlot.do_notified_at">
                                            <span class="text-emerald-700 font-medium">
                                                Terkirim: <span class="font-bold font-mono" x-text="activeSlot.do_notified_at"></span>
                                            </span>
                                        </template>
                                        <template x-if="!activeSlot.do_notified_at">
                                            <span class="text-slate-500">
                                                Kirim ringkasan sesi &amp; kontak PIC ke WA DO.
                                            </span>
                                        </template>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button type="button" 
                                                @click="closeModal(); openDutyOfficerModal({
                                                    id: activeSlot.booking_id,
                                                    booking_code: activeSlot.booking_code,
                                                    event_name: activeSlot.event_name,
                                                    room_name: activeSlot.room_name,
                                                    floor_level: activeSlot.floor_level,
                                                    slot_id: activeSlot.id,
                                                    event_start_at: activeSlot.slot_start,
                                                    status_label: activeSlot.status_label,
                                                    duty_officer_id: activeSlot.duty_officer_id,
                                                    duty_officer_notes: activeSlot.duty_officer_notes
                                                })"
                                                class="px-2.5 py-1.5 rounded-brand bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-bold transition cursor-pointer">
                                            Ganti DO
                                        </button>

                                        <button type="button"
                                                @click="notifyDutyOfficer(activeSlot.booking_id, activeSlot.id)"
                                                :disabled="isDispatching"
                                                class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-brand bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-2xs cursor-pointer disabled:opacity-50">
                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.301-.15-1.78-.878-2.056-.979-.276-.1-.476-.15-.677.15-.2.301-.777.98-1.002 1.23-.226.25-.451.276-.752.126-.301-.151-1.272-.469-2.423-1.496-.896-.799-1.501-1.786-1.677-2.087-.175-.301-.019-.464.132-.614.136-.135.301-.351.451-.527.15-.175.2-.301.301-.501.1-.2.05-.376-.025-.526-.075-.15-.677-1.63-.927-2.233-.244-.587-.492-.508-.677-.517l-.577-.01c-.2 0-.526.075-.802.376-.276.301-1.053 1.028-1.053 2.507 0 1.479 1.078 2.908 1.228 3.109.15.2 2.122 3.24 5.141 4.544.718.31 1.279.496 1.716.635.722.23 1.379.197 1.9.12.58-.087 1.78-.727 2.03-1.43.25-.702.25-1.304.175-1.43-.075-.125-.276-.201-.577-.351z"/></svg>
                                            <span x-text="activeSlot.do_notified_at ? 'Kirim Ulang Tugas (WA)' : 'Kirim Tugas ke DO (WhatsApp)'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="!activeSlot.duty_officer_name">
                            <div class="flex items-center justify-between pt-1">
                                <span class="text-xs text-amber-800">Belum ada Duty Officer yang ditugaskan ke sesi ruangan ini.</span>
                                <button type="button" 
                                        @click="closeModal(); openDutyOfficerModal({
                                            id: activeSlot.booking_id,
                                            booking_code: activeSlot.booking_code,
                                            event_name: activeSlot.event_name,
                                            room_name: activeSlot.room_name,
                                            floor_level: activeSlot.floor_level,
                                            slot_id: activeSlot.id,
                                            event_start_at: activeSlot.slot_start,
                                            status_label: activeSlot.status_label
                                        })"
                                        class="px-2.5 py-1 rounded bg-[#001A72] text-white text-xs font-bold hover:bg-[#001250] transition cursor-pointer">
                                    Disposisi DO
                                </button>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- FR-FO-06 Inisiasi Checkout & Pemilihan Ulang DO Shift Berjalan -->
                <!-- Muncul saat acara Checked-In / Selesai ketika panitia melapor checkout & ingin mengambil kembali KTP -->
                <template x-if="activeSlot.status_value === 'checked_in' || activeSlot.status_value === 'completed'">
                    <div class="border rounded-brand p-4 space-y-3"
                         :class="activeSlot.checkout_do_name ? 'bg-indigo-50/70 border-indigo-200' : 'bg-amber-50/70 border-amber-200'">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-xs font-bold"
                                 :class="activeSlot.checkout_do_name ? 'text-[#001A72]' : 'text-amber-900'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                <span>Inspeksi Checkout (DO Shift Berjalan)</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <template x-if="activeSlot.checkout_do_name && activeSlot.checkout_do_notified_at">
                                    <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-300 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span>Tugas Checkout Terkirim ke WA</span>
                                    </span>
                                </template>
                                <template x-if="activeSlot.checkout_do_name && !activeSlot.checkout_do_notified_at">
                                    <span class="text-[10px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-full border border-amber-300">
                                        Belum Di-WhatsApp
                                    </span>
                                </template>
                                <template x-if="!activeSlot.checkout_do_name">
                                    <span class="text-[10px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-full border border-amber-300">
                                        Belum Diinisiasi
                                    </span>
                                </template>
                            </div>
                        </div>

                        <!-- Jika DO Checkout Sudah Ditetapkan -->
                        <template x-if="activeSlot.checkout_do_name">
                            <div class="space-y-2.5 pt-1">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                    <div>
                                        <span class="text-slate-500 text-[11px] block">DO Shift Berjalan:</span>
                                        <span class="font-extrabold text-slate-900 text-sm" x-text="activeSlot.checkout_do_name"></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 text-[11px] block">Kontak DO:</span>
                                        <span class="font-bold text-[#001A72] font-mono" x-text="activeSlot.checkout_do_phone || '-'"></span>
                                    </div>
                                    <template x-if="activeSlot.checkout_do_notes">
                                        <div class="sm:col-span-2 pt-1 border-t border-indigo-200/60">
                                            <span class="text-slate-500 text-[10px] block">Catatan Inspeksi:</span>
                                            <span class="text-slate-700 italic text-[11px]" x-text="activeSlot.checkout_do_notes"></span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Action Buttons: Kirim Tugas WhatsApp & Ganti DO Shift -->
                                <div class="pt-2 border-t border-indigo-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div class="text-[11px]">
                                        <template x-if="activeSlot.checkout_do_notified_at">
                                            <span class="text-emerald-700 font-medium">
                                                Terkirim: <span class="font-bold font-mono" x-text="activeSlot.checkout_do_notified_at"></span>
                                            </span>
                                        </template>
                                        <template x-if="!activeSlot.checkout_do_notified_at">
                                            <span class="text-slate-500">
                                                Kirim tugas inspeksi fisik &amp; sinyal clearance ke WA DO.
                                            </span>
                                        </template>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                                @click="closeModal(); openCheckoutDoModal({
                                                    id: activeSlot.booking_id,
                                                    booking_code: activeSlot.booking_code,
                                                    event_name: activeSlot.event_name,
                                                    room_name: activeSlot.room_name,
                                                    floor_level: activeSlot.floor_level,
                                                    slot_id: activeSlot.id,
                                                    initial_do_id: activeSlot.duty_officer_id,
                                                    initial_do_name: activeSlot.duty_officer_name,
                                                    current_checkout_do_id: activeSlot.checkout_do_id
                                                })"
                                                class="px-2.5 py-1.5 rounded-brand bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-bold transition cursor-pointer">
                                            Ganti DO
                                        </button>

                                        <button type="button"
                                                @click="notifyCheckoutDutyOfficer(activeSlot.booking_id, activeSlot.id)"
                                                :disabled="isDispatchingCheckoutDO"
                                                class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-brand bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-2xs cursor-pointer disabled:opacity-50">
                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.301-.15-1.78-.878-2.056-.979-.276-.1-.476-.15-.677.15-.2.301-.777.98-1.002 1.23-.226.25-.451.276-.752.126-.301-.151-1.272-.469-2.423-1.496-.896-.799-1.501-1.786-1.677-2.087-.175-.301-.019-.464.132-.614.136-.135.301-.351.451-.527.15-.175.2-.301.301-.501.1-.2.05-.376-.025-.526-.075-.15-.677-1.63-.927-2.233-.244-.587-.492-.508-.677-.517l-.577-.01c-.2 0-.526.075-.802.376-.276.301-1.053 1.028-1.053 2.507 0 1.479 1.078 2.908 1.228 3.109.15.2 2.122 3.24 5.141 4.544.718.31 1.279.496 1.716.635.722.23 1.379.197 1.9.12.58-.087 1.78-.727 2.03-1.43.25-.702.25-1.304.175-1.43-.075-.125-.276-.201-.577-.351z"/></svg>
                                            <span x-text="activeSlot.checkout_do_notified_at ? 'Kirim Ulang Tugas (WA)' : 'Kirim Tugas Inspeksi Ruangan (WhatsApp)'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Jika DO Checkout Belum Ditetapkan -->
                        <template x-if="!activeSlot.checkout_do_name">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
                                <span class="text-xs text-amber-900 leading-relaxed">
                                    Panitia melapor acara selesai &amp; ingin mengambil KTP? Panggil DO aktif pada shift jam ini untuk inspeksi ruangan.
                                </span>
                                <button type="button" 
                                        @click="closeModal(); openCheckoutDoModal({
                                            id: activeSlot.booking_id,
                                            booking_code: activeSlot.booking_code,
                                            event_name: activeSlot.event_name,
                                            room_name: activeSlot.room_name,
                                            floor_level: activeSlot.floor_level,
                                            slot_id: activeSlot.id,
                                            initial_do_id: activeSlot.duty_officer_id,
                                            initial_do_name: activeSlot.duty_officer_name,
                                            current_checkout_do_id: null
                                        })"
                                        class="px-3.5 py-1.5 rounded-brand bg-[#001A72] text-white text-xs font-bold hover:bg-[#001250] transition shadow-xs cursor-pointer shrink-0 flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                    <span>Panggil DO Inspeksi Checkout</span>
                                </button>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Detail Penanggung Jawab & Kontak -->
                <div class="border border-slate-200 rounded-brand p-4 space-y-3">
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span>Identitas Penyelenggara &amp; PIC Lapangan</span>
                    </h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-slate-500 text-[11px] block">Lembaga / Komunitas:</span>
                            <span class="font-bold text-slate-800" x-text="activeSlot.institution_name"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[11px] block">Skema Pemakaian:</span>
                            <span class="font-bold text-slate-800" x-text="activeSlot.pricing_scheme"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[11px] block">Nama PIC Acara:</span>
                            <span class="font-bold text-slate-800" x-text="activeSlot.pic_name"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[11px] block">Kontak WhatsApp PIC:</span>
                            <span class="font-bold text-[#001A72] font-mono" x-text="activeSlot.pic_phone"></span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-slate-500 text-[11px] block">Kontak Darurat Alternatif:</span>
                            <span class="font-bold text-slate-700 font-mono" x-text="activeSlot.emergency_phone"></span>
                        </div>
                    </div>
                </div>

                <!-- Daftar Seluruh Sesi Slot Permohonan (Khusus Multi-Slot / Multi-Day) -->
                <template x-if="activeSlot.all_slots && activeSlot.all_slots.length > 1">
                    <div class="border border-slate-200 rounded-brand p-4 space-y-3 bg-slate-50/60">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                <span>Seluruh Rangkaian Sesi Permohonan (<span x-text="activeSlot.all_slots.length"></span> Sesi)</span>
                            </h4>
                            <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded">
                                Multi-Slot / Multi-Day
                            </span>
                        </div>
                        <div class="space-y-2">
                            <template x-for="(s, idx) in activeSlot.all_slots" :key="s.id">
                                <div class="p-2.5 rounded-brand border text-xs flex items-center justify-between"
                                     :class="s.id === activeSlot.id ? 'bg-[#EEF2FF] border-[#001A72] text-[#001A72] font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-700'">
                                    <div class="flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-mono font-bold"
                                              :class="s.id === activeSlot.id ? 'bg-[#001A72] text-white' : 'bg-slate-100 text-slate-600'"
                                              x-text="idx + 1"></span>
                                        <div>
                                            <span x-text="s.room_name + ' (Lt. ' + s.floor_level + ')'"></span>
                                            <span class="text-[10px] block" :class="s.id === activeSlot.id ? 'text-indigo-800' : 'text-slate-500'" x-text="s.start + ' - ' + s.end"></span>
                                        </div>
                                    </div>
                                    <div>
                                        <template x-if="s.id === activeSlot.id">
                                            <span class="px-2 py-0.5 rounded bg-[#001A72] text-white text-[10px] font-extrabold">
                                                Sesi Aktif Hari Ini
                                            </span>
                                        </template>
                                        <template x-if="s.id !== activeSlot.id">
                                            <span class="text-[10px] text-slate-500 font-semibold" x-text="s.is_today ? 'Hari ini di jam lain' : 'Hari Lain'"></span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Detail Acara & Estimasi Peserta -->
                <div class="border border-slate-200 rounded-brand p-4 space-y-2.5">
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                        Deskripsi &amp; Estimasi Peserta
                    </h4>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-slate-500 text-[11px] block">Kategori Acara:</span>
                            <span class="font-bold text-slate-800" x-text="activeSlot.event_category"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[11px] block">Subsektor Ekraf:</span>
                            <span class="font-bold text-slate-800" x-text="activeSlot.ekraf_subsector"></span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-slate-500 text-[11px] block">Estimasi Peserta:</span>
                            <span class="font-bold text-slate-800" x-text="activeSlot.estimated_participants + ' Orang'"></span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-slate-500 text-[11px] block">Deskripsi Acara:</span>
                            <p class="text-slate-700 mt-1 leading-relaxed whitespace-pre-line bg-slate-50 p-2.5 rounded border border-slate-200" x-text="activeSlot.event_description"></p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex items-center justify-end">
                <button type="button"
                        @click="closeModal()"
                        class="px-4 py-2 rounded-brand bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs transition cursor-pointer">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>
