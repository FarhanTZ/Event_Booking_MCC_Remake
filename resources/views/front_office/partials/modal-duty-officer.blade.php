<!-- ==================================================================== -->
<!-- MODAL DISPOSISI PENGAWAS RUANGAN (DUTY OFFICER - FR-FO-04) -->
<!-- ==================================================================== -->
<div x-show="showDutyOfficerModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-do-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="showDutyOfficerModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeDutyOfficerModal()" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

    <!-- Centered Modal Wrapper -->
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6 relative z-10 pointer-events-none">
        <div x-show="showDutyOfficerModal" 
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-white tracking-tight leading-snug" id="modal-do-title"
                            x-text="doTargetBooking.initial_do_id ? 'Ubah / Ganti Pengawas Ruangan (DO)' : 'Disposisi Pengawas Ruangan (DO)'">
                            Disposisi Pengawas Ruangan (DO)
                        </h3>
                        <p class="text-[11px] text-indigo-200">
                            Penetapan &amp; pembaruan penanggung jawab inspeksi lapangan (FR-FO-04)
                        </p>
                    </div>
                </div>

                <button @click="closeDutyOfficerModal()" class="p-1.5 rounded-brand text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer shrink-0" aria-label="Tutup Modal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                
                <!-- Error Banner -->
                <template x-if="doAssignmentError">
                    <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-brand flex items-start gap-2.5 text-rose-700 text-xs animate-shake">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 text-rose-600 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                        <div class="flex-1 font-semibold leading-relaxed" x-text="doAssignmentError"></div>
                    </div>
                </template>

                <!-- Info Box Sesi Booking yang Baru Check-In -->
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-brand space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase tracking-wider font-extrabold text-slate-400">Sesi Ruangan Terpilih</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800" x-text="doTargetBooking.status_label || 'Aktif (Checked-In)'"></span>
                    </div>
                    <div>
                        <span class="font-extrabold text-slate-900 text-sm block" x-text="doTargetBooking.event_name"></span>
                        <span class="text-xs text-slate-500 font-mono font-bold" x-text="doTargetBooking.booking_code"></span>
                    </div>
                    <div class="pt-2 border-t border-slate-200/80 grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-slate-400 text-[10px] block">Ruangan & Lantai:</span>
                            <span class="font-bold text-[#001A72]" x-text="doTargetBooking.room_name + ' (Lt. ' + (doTargetBooking.floor_level || 1) + ')'"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] block">Waktu Sesi:</span>
                            <span class="font-semibold text-slate-700 font-mono" x-text="doTargetBooking.event_start_at"></span>
                        </div>
                    </div>
                </div>

                <!-- Form Pemilihan Duty Officer (Staf On-Duty) -->
                <div class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5">
                            Pilih Staf Duty Officer (DO) On-Duty <span class="text-rose-500">*</span>
                        </label>
                        <select x-model="selectedDutyOfficerId" 
                                class="block w-full py-2.5 px-3 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 font-medium">
                            <option value="">-- Pilih Staf Pengawas Ruangan --</option>
                            @foreach($dutyOfficers as $do)
                                <option value="{{ $do->id }}" 
                                        :selected="selectedDutyOfficerId === '{{ $do->id }}'">
                                    {{ $do->full_name }} • {{ $do->phone_number ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1">
                            DO yang dipilih akan bertugas melakukan inspeksi aset, pendampingan fasilitas, dan pengawasan ruangan.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Catatan Khusus Disposisi (Opsional)
                        </label>
                        <textarea x-model="dutyOfficerNotes" 
                                  rows="2" 
                                  placeholder="Contoh: Koordinasikan AC tambahan & sound system mic wireless"
                                  class="block w-full p-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] placeholder:text-slate-400"></textarea>
                    </div>

                    <!-- FR-FO-05 WhatsApp Auto Dispatch Option -->
                    <div class="p-3 bg-emerald-50/70 border border-emerald-200/80 rounded-brand">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" x-model="sendWhatsAppDispatch" class="rounded text-[#001A72] focus:ring-[#001A72] w-4 h-4 mt-0.5 shrink-0">
                            <div class="text-xs">
                                <span class="font-bold text-emerald-950 block">Kirim Notifikasi Tugas via WhatsApp (FR-FO-05)</span>
                                <span class="text-[11px] text-emerald-800 leading-relaxed block mt-0.5">
                                    Buka tautan pesan WhatsApp otomatis ke DO berisi ringkasan sesi, nama ruangan, dan kontak PIC lapangan.
                                </span>
                            </div>
                        </label>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="bg-slate-50 px-6 py-3.5 border-t border-slate-200 flex items-center justify-between text-xs">
                <button type="button" 
                        @click="skipDutyOfficerAssignment()" 
                        class="px-3.5 py-2 rounded-brand bg-white border border-slate-300 text-slate-600 hover:bg-slate-100 font-bold transition cursor-pointer">
                    Tugaskan Nanti (Skip)
                </button>

                <button type="button" 
                        @click="submitDutyOfficerAssignment()" 
                        :disabled="!selectedDutyOfficerId || isSubmittingDO"
                        class="px-4 py-2 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="!isSubmittingDO" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
                    <svg x-show="isSubmittingDO" class="w-4 h-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span x-text="isSubmittingDO ? 'Menyimpan Perubahan...' : (doTargetBooking.initial_do_id ? 'Simpan Perubahan DO' : 'Simpan & Disposisikan DO')"></span>
                </button>
            </div>

        </div>
    </div>
</div>
