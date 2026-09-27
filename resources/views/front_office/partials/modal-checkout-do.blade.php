<!-- ==================================================================== -->
<!-- MODAL INISIASI CHECKOUT & PEMILIHAN DO SHIFT BERJALAN (FR-FO-06) -->
<!-- ==================================================================== -->
<div x-show="showCheckoutDoModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-checkout-do-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="showCheckoutDoModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeCheckoutDoModal()" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

    <!-- Centered Modal Wrapper -->
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6 relative z-10 pointer-events-none">
        <div x-show="showCheckoutDoModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop
             class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-lg border border-slate-200 pointer-events-auto my-8">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-[#001A72] to-indigo-900 text-white px-6 py-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-brand bg-white/10 flex items-center justify-center text-white shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-white tracking-tight leading-snug" id="modal-checkout-do-title">
                            Panggil DO Inspeksi Checkout
                        </h3>
                        <p class="text-[11px] text-indigo-200">
                            Penanganan pergantian shift &amp; tugas inspeksi fisik (FR-FO-06)
                        </p>
                    </div>
                </div>

                <button @click="closeCheckoutDoModal()" class="p-1.5 rounded-brand text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer shrink-0" aria-label="Tutup Modal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                
                <!-- Error Banner -->
                <template x-if="checkoutDoError">
                    <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-brand flex items-start gap-2.5 text-rose-700 text-xs animate-shake">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 text-rose-600 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                        <div class="flex-1 font-semibold leading-relaxed" x-text="checkoutDoError"></div>
                    </div>
                </template>

                <!-- Info Box Sesi Booking yang Mengajukan Checkout -->
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-brand space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase tracking-wider font-extrabold text-slate-400">Pengajuan Checkout Acara</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>Inspeksi Lapangan</span>
                        </span>
                    </div>
                    <div>
                        <span class="font-extrabold text-slate-900 text-sm block" x-text="checkoutTargetBooking.event_name"></span>
                        <span class="text-xs text-[#001A72] font-mono font-bold" x-text="checkoutTargetBooking.booking_code"></span>
                    </div>
                    <div class="pt-2 border-t border-slate-200/80 grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-slate-400 text-[10px] block">Ruangan &amp; Lantai:</span>
                            <span class="font-bold text-[#001A72]" x-text="checkoutTargetBooking.room_name + ' (Lt. ' + (checkoutTargetBooking.floor_level || 1) + ')'"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] block">DO Check-in Awal:</span>
                            <span class="font-semibold text-slate-700" x-text="checkoutTargetBooking.initial_do_name || 'Belum Ditugaskan'"></span>
                        </div>
                    </div>
                </div>

                <!-- Petunjuk Operasional Shift Handoff -->
                <div class="p-3 bg-[#EEF2FF] border border-indigo-100 rounded-brand text-xs text-[#001A72] flex items-start gap-2.5 leading-relaxed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72] shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                    <div>
                        <span class="font-bold block text-slate-900 mb-0.5">Penanganan Pergantian Shift:</span>
                        <span>Pilih nama staf Duty Officer yang <strong>sedang aktif pada shift jam tersebut</strong>. Staf ini fleksibel: bisa sama dengan DO check-in jika masih shift yang sama, atau staf pengganti jika sudah pergantian shift operasional.</span>
                    </div>
                </div>

                <!-- Form Pemilihan Duty Officer Checkout -->
                <div class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5">
                            Pilih Staf Duty Officer Shift Berjalan (assigned_checkout_do_id) <span class="text-rose-500">*</span>
                        </label>
                        <select x-model="selectedCheckoutDoId" 
                                class="block w-full py-2.5 px-3 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 font-medium">
                            <option value="">-- Pilih Staf DO Shift Berjalan --</option>
                            @foreach($dutyOfficers as $do)
                                <option value="{{ $do->id }}" 
                                        :selected="selectedCheckoutDoId === '{{ $do->id }}'">
                                    {{ $do->full_name }} • {{ $do->phone_number ?? '-' }}
                                    @if(isset($activeSlot['duty_officer_id']) && $activeSlot['duty_officer_id'] === $do->id)
                                        (DO Check-in Awal)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1">
                            DO bertugas menginspeksi kebersihan ruangan, inventaris fasilitas, dan menginput sinyal clearance di sistem.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Catatan Khusus Inspeksi Checkout (Opsional)
                        </label>
                        <textarea x-model="checkoutDoNotes" 
                                  rows="2" 
                                  placeholder="Contoh: Periksa inventaris mixer audio, kelengkapan mic wireless, dan kebersihan karpet ruangan."
                                  class="block w-full p-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] placeholder:text-slate-400"></textarea>
                    </div>

                    <!-- FR-FO-06 Instant WhatsApp Task Dispatch Checkbox -->
                    <div class="p-3 bg-emerald-50/80 border border-emerald-200 rounded-brand">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" x-model="sendCheckoutWhatsApp" class="rounded text-[#001A72] focus:ring-[#001A72] w-4 h-4 mt-0.5 shrink-0">
                            <div class="text-xs">
                                <span class="font-bold text-emerald-950 block">Kirim Tugas Inspeksi Ruangan (WhatsApp)</span>
                                <span class="text-[11px] text-emerald-800 leading-relaxed block mt-0.5">
                                    Otomatis membuka tautan WhatsApp ke staf DO dengan format pesan resmi:
                                </span>
                                <div class="mt-2 p-2 bg-white/90 border border-emerald-200 rounded text-[11px] text-slate-700 italic leading-relaxed">
                                    "Halo [Nama DO], acara <span class="font-semibold text-slate-900" x-text="checkoutTargetBooking.event_name"></span> di <span class="font-semibold text-slate-900" x-text="checkoutTargetBooking.room_name"></span> telah selesai dan panitia mengajukan checkout. Mohon segera inspeksi fisik ruangan (kebersihan &amp; inventaris fasilitas) serta input sinyal clearance di sistem."
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="bg-slate-50 px-6 py-3.5 border-t border-slate-200 flex items-center justify-between text-xs">
                <button type="button" 
                        @click="closeCheckoutDoModal()" 
                        class="px-3.5 py-2 rounded-brand bg-white border border-slate-300 text-slate-600 hover:bg-slate-100 font-bold transition cursor-pointer">
                    Batal
                </button>

                <button type="button" 
                        @click="submitCheckoutDoAssignment()" 
                        :disabled="!selectedCheckoutDoId || isSubmittingCheckoutDO"
                        class="px-4 py-2 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="!isSubmittingCheckoutDO" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                    <svg x-show="isSubmittingCheckoutDO" class="w-4 h-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span x-text="isSubmittingCheckoutDO ? 'Memproses Disposisi...' : 'Panggil DO & Kirim Tugas (WhatsApp)'"></span>
                </button>
            </div>

        </div>
    </div>
</div>
