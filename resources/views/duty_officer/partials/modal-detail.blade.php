<!-- ==================================================================== -->
<!-- MODAL DETAIL OPERASIONAL & PERALATAN MANDIRI (DUTY OFFICER) -->
<!-- ==================================================================== -->
<div x-show="detailModalOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="detailModalOpen"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="detailModalOpen = false"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
        <div x-show="detailModalOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-2xl border border-slate-200 my-8">
            
            <!-- Modal Header (MCC Brand #001A72) -->
            <div class="bg-[#001A72] text-white p-4 sm:p-5 flex items-start justify-between gap-3 border-b border-white/10">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-white/15 text-white" x-text="activeTask.bookingCode">
                            #MCC-BOOKING
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-500 text-white" x-text="activeTask.statusLabel">
                            Status
                        </span>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-white tracking-tight leading-snug" x-text="activeTask.eventName">
                        Nama Acara
                    </h3>
                    <p class="text-xs text-indigo-200 mt-0.5" x-text="activeTask.institutionName">
                        Lembaga / Komunitas
                    </p>
                </div>

                <button type="button" 
                        @click="detailModalOpen = false"
                        class="p-1.5 rounded-brand bg-white/10 hover:bg-white/20 text-white transition cursor-pointer shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-4 sm:p-6 space-y-4 max-h-[70vh] overflow-y-auto custom-scrollbar">
                
                <!-- Info Grid Ruangan & Jadwal -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3 rounded-brand bg-slate-50 border border-slate-200">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Ruangan &amp; Lokasi</span>
                        <p class="text-xs sm:text-sm font-black text-slate-900 mt-0.5" x-text="activeTask.roomName">
                            -
                        </p>
                        <p class="text-[11px] text-slate-500" x-text="`Lantai ${activeTask.floorLevel}`">
                            -
                        </p>
                    </div>

                    <div class="p-3 rounded-brand bg-slate-50 border border-slate-200">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Jadwal Sesi Ruangan</span>
                        <p class="text-xs sm:text-sm font-black text-slate-900 mt-0.5" x-text="activeTask.schedule">
                            -
                        </p>
                        <p class="text-[11px] font-mono text-teal-700 font-bold" x-text="activeTask.countdownText">
                            -
                        </p>
                    </div>
                </div>

                <!-- Kontak PIC Lapangan & Laci KTP -->
                <div class="p-3.5 rounded-brand bg-[#EEF2FF] border border-indigo-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-[#001A72] block">Penanggung Jawab Lapangan (PIC)</span>
                        <p class="text-sm font-black text-slate-900 mt-0.5" x-text="activeTask.picName">-</p>
                        <p class="text-xs font-mono text-slate-600" x-text="activeTask.picPhone">-</p>
                        <template x-if="activeTask.storageDrawer">
                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-100 text-amber-900 border border-amber-200" x-text="`Laci Front Office: ${activeTask.storageDrawer}`"></span>
                        </template>
                    </div>

                    <template x-if="activeTask.whatsappUrl">
                        <a :href="activeTask.whatsappUrl" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-brand bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-xs transition active:scale-95 shrink-0 cursor-pointer">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.275.072.376-.044c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.42-.099.825zm-3.392-12.416c-5.514 0-10 4.486-10 10 0 1.761.459 3.417 1.264 4.864l-1.303 4.764 4.887-1.282c1.398.765 2.993 1.194 4.692 1.194 5.514 0 10-4.486 10-10s-4.486-10-10-10z"/>
                            </svg>
                            <span>Chat WA PIC</span>
                        </a>
                    </template>
                </div>

                <!-- Rekapitulasi Peralatan Eksternal Mandiri (event_details.equipment_brought) -->
                <div class="p-4 rounded-brand bg-slate-50 border border-slate-200 space-y-2">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-800">
                            Rekapitulasi Peralatan Eksternal Mandiri (Bawaan Pemohon)
                        </h4>
                    </div>

                    <div class="pt-1">
                        <template x-if="activeTask.equipmentItems && activeTask.equipmentItems.length > 0">
                            <div class="flex flex-wrap gap-2">
                                <template x-for="item in activeTask.equipmentItems" :key="item">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-brand bg-white border border-slate-200 text-xs font-semibold text-slate-800 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#001A72]"></span>
                                        <span x-text="item"></span>
                                    </span>
                                </template>
                            </div>
                        </template>

                        <template x-if="!activeTask.equipmentItems || activeTask.equipmentItems.length === 0">
                            <p class="text-xs text-slate-500 italic">
                                Pemohon tidak mencantumkan peralatan eksternal mandiri yang dibawa masuk.
                            </p>
                        </template>
                    </div>
                </div>

                <!-- Deskripsi Acara -->
                <div class="p-3.5 rounded-brand bg-slate-50 border border-slate-200 space-y-1 text-xs">
                    <span class="font-bold text-slate-700 block">Deskripsi Kegiatan:</span>
                    <p class="text-slate-600 whitespace-pre-line" x-text="activeTask.description || 'Tidak ada deskripsi tambahan.'"></p>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="bg-slate-50 p-4 border-t border-slate-200 flex items-center justify-end gap-2.5">
                <button type="button" 
                        @click="detailModalOpen = false" 
                        class="px-4 py-2 rounded-brand bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-300 transition shadow-2xs cursor-pointer"
                        style="min-height: 40px;">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>
