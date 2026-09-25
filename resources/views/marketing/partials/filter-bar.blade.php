<!-- ==================================================================== -->
<!-- MULTI-PARAMETER REALTIME SEARCH & FILTER BAR -->
<!-- ==================================================================== -->
<div class="bg-white rounded-brand border border-slate-200 shadow-card p-5 space-y-4">
    <form method="GET" 
          action="{{ route('marketing.dashboard') }}" 
          x-ref="filterForm"
          class="space-y-3.5">

        <!-- Row 1: Search, Status Dropdown, Kategori Acara -->
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <!-- 1. Text Search Input (Realtime debounced) -->
            <div class="sm:col-span-5 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
                <input type="text" 
                       name="q" 
                       value="{{ $currentSearch }}" 
                       @input.debounce.500ms="$refs.filterForm.submit()"
                       placeholder="Cari kode (MCC-...), PIC, acara, atau lembaga..." 
                       class="block w-full pl-10 pr-3 py-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] placeholder:text-slate-400">
            </div>

            <!-- 2. Status Dropdown Filter -->
            <div class="sm:col-span-4">
                <select name="status" 
                        @change="$refs.filterForm.submit()"
                        class="block w-full py-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 font-medium">
                    <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>
                        Semua Status ({{ $metrics['total_all'] }})
                    </option>
                    <option value="awaiting_marketing" {{ $currentStatus === 'awaiting_marketing' ? 'selected' : '' }}>
                        Menunggu Kurasi ({{ $metrics['awaiting_curation'] }})
                    </option>
                    <option value="revised" {{ $currentStatus === 'revised' ? 'selected' : '' }}>
                        Perlu Revisi ({{ $metrics['revised'] }})
                    </option>
                    <option value="awaiting_payment" {{ $currentStatus === 'awaiting_payment' ? 'selected' : '' }}>
                        Menunggu Pembayaran ({{ $metrics['awaiting_payment'] }})
                    </option>
                    <option value="confirmed" {{ $currentStatus === 'confirmed' ? 'selected' : '' }}>
                        Disetujui ({{ $metrics['total_confirmed'] }})
                    </option>
                    <option value="rejected" {{ $currentStatus === 'rejected' ? 'selected' : '' }}>
                        Ditolak ({{ $metrics['rejected'] }})
                    </option>
                </select>
            </div>

            <!-- 3. Kategori Acara Dropdown -->
            <div class="sm:col-span-3">
                <select name="category" 
                        @change="$refs.filterForm.submit()"
                        class="block w-full py-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-700">
                    <option value="all" {{ $currentCategory === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                    <option value="Dinas" {{ $currentCategory === 'Dinas' ? 'selected' : '' }}>Dinas / Pemkot</option>
                    <option value="Kampus" {{ $currentCategory === 'Kampus' ? 'selected' : '' }}>Kampus / Akademisi</option>
                    <option value="Komunitas" {{ $currentCategory === 'Komunitas' ? 'selected' : '' }}>Komunitas</option>
                    <option value="Komersial" {{ $currentCategory === 'Komersial' ? 'selected' : '' }}>Komersial</option>
                    <option value="Lainnya" {{ $currentCategory === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
        </div>

        <!-- Row 2: Sub-Sektor, Lantai, Rentang Tanggal Popover, & Reset Action -->
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- 4. Sub-Sektor Ekraf Dropdown -->
            <div class="sm:col-span-4">
                <select name="sub_sector" 
                        @change="$refs.filterForm.submit()"
                        class="block w-full py-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-700">
                    <option value="all" {{ $currentSubSector === 'all' ? 'selected' : '' }}>Semua Sub-Sektor Ekraf</option>
                    @php
                        $ekrafList = [
                            'Aplikasi', 'Arsitektur', 'Desain Interior', 'DKV', 'Fashion', 'Film', 'Fotografi', 
                            'Game', 'Konten Digital', 'Kriya', 'Kuliner', 'Modifikasi Otomotif', 'Musik', 
                            'Penerbitan', 'Periklanan', 'Pertunjukan', 'Produk', 'Seni Rupa', 'Sulih Suara', 
                            'Teknologi Baru', 'Televisi & Radio'
                        ];
                    @endphp
                    @foreach($ekrafList as $ekraf)
                        <option value="{{ $ekraf }}" {{ $currentSubSector === $ekraf ? 'selected' : '' }}>{{ $ekraf }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 5. Lantai Ruangan -->
            <div class="sm:col-span-3">
                <select name="floor_level" 
                        @change="$refs.filterForm.submit()"
                        class="block w-full py-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-700">
                    <option value="all" {{ $currentFloor === 'all' ? 'selected' : '' }}>Semua Lantai (1 - 8)</option>
                    @for($f = 1; $f <= 8; $f++)
                        <option value="{{ $f }}" {{ (string)$currentFloor === (string)$f ? 'selected' : '' }}>Lantai {{ $f }}</option>
                    @endfor
                </select>
            </div>

            <!-- 6. Date Range Picker with Clean Calendar Icon Popover -->
            <div class="sm:col-span-4 relative" 
                 x-data="{
                     init() {
                         flatpickr(this.$refs.dateRangeInput, {
                             mode: 'range',
                             dateFormat: 'd M Y',
                             altInput: false,
                             locale: {
                                 rangeSeparator: ' s.d. '
                             },
                             defaultDate: [
                                 @if($currentDateFrom) '{{ $currentDateFrom }}', @endif
                                 @if($currentDateTo) '{{ $currentDateTo }}', @endif
                             ],
                             onChange: (selectedDates, dateStr, instance) => {
                                 if (selectedDates.length === 2) {
                                     this.$refs.dateFrom.value = instance.formatDate(selectedDates[0], 'Y-m-d');
                                     this.$refs.dateTo.value = instance.formatDate(selectedDates[1], 'Y-m-d');
                                     this.$refs.filterForm.submit();
                                 }
                             },
                             onClose: (selectedDates, dateStr, instance) => {
                                 if (selectedDates.length === 2) {
                                     this.$refs.dateFrom.value = instance.formatDate(selectedDates[0], 'Y-m-d');
                                     this.$refs.dateTo.value = instance.formatDate(selectedDates[1], 'Y-m-d');
                                     this.$refs.filterForm.submit();
                                 }
                             }
                         });
                     }
                 }">
                <input type="hidden" name="date_from" x-ref="dateFrom" value="{{ $currentDateFrom }}">
                <input type="hidden" name="date_to" x-ref="dateTo" value="{{ $currentDateTo }}">
                
                <div class="relative flex items-center">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#001A72] z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    </div>
                    <input type="text" 
                           x-ref="dateRangeInput"
                           readonly
                           placeholder="Pilih Rentang Tanggal..." 
                           class="block w-full pl-10 pr-3 py-2.5 text-xs rounded-brand border border-slate-300 shadow-2xs focus:border-[#001A72] focus:ring-[#001A72] text-slate-800 font-medium cursor-pointer bg-white">
                </div>
            </div>

            <!-- 7. Reset Action Icon Button (Only when filters are active) -->
            <div class="sm:col-span-1 flex items-center justify-end">
                @if($currentSearch || $currentCategory !== 'all' || $currentSubSector !== 'all' || $currentFloor !== 'all' || $currentDateFrom || $currentDateTo || $currentStatus !== 'all')
                    <a href="{{ route('marketing.dashboard') }}" 
                       class="w-9 h-9 sm:w-[38px] sm:h-[38px] inline-flex items-center justify-center rounded-full border border-slate-300 bg-white hover:bg-rose-50 hover:text-rose-600 hover:border-rose-300 text-slate-500 transition cursor-pointer shadow-2xs shrink-0"
                       title="Reset Semua Filter">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </a>
                @endif
            </div>
        </div>

        <!-- Row 3: Export Rekapitulasi Operasional Gedung Bar (REQ-MKT-12) -->
        <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                <span class="font-bold text-slate-700">Ekspor Rekap Agenda Gedung (Confirmed):</span>
                <span class="text-[11px] text-slate-400 hidden sm:inline">Rujukan Front Office, Security, &amp; Teknisi Gedung</span>
            </div>

            <div class="flex items-center gap-2">
                <!-- Export PDF Button -->
                <a href="{{ route('marketing.schedule.export.pdf', request()->only(['date_from', 'date_to', 'floor_level'])) }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-brand bg-white border border-slate-300 hover:bg-rose-50 hover:border-rose-300 text-slate-700 hover:text-rose-700 font-bold transition shadow-2xs cursor-pointer text-xs"
                   title="Unduh format PDF untuk cetak / serah terima operasional">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="10" x2="8" y1="12" y2="12"/><line x1="16" x2="8" y1="16" y2="16"/></svg>
                    <span>Unduh PDF</span>
                </a>

                <!-- Export Excel/CSV Button -->
                <a href="{{ route('marketing.schedule.export.csv', request()->only(['date_from', 'date_to', 'floor_level'])) }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-brand bg-white border border-slate-300 hover:bg-emerald-50 hover:border-emerald-300 text-slate-700 hover:text-emerald-700 font-bold transition shadow-2xs cursor-pointer text-xs"
                   title="Unduh format Excel / CSV untuk olah data spreadsheet">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="M8 13h2"/><path d="M8 17h2"/><path d="M14 13h2"/><path d="M14 17h2"/></svg>
                    <span>Unduh Excel (CSV)</span>
                </a>
            </div>
        </div>
    </form>
</div>
