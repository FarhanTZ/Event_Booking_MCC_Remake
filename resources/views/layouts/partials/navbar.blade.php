<header x-data="{ mobileMenuOpen: false, userDropdownOpen: false }" class="bg-white text-slate-900 border-b border-slate-200 sticky top-0 z-40 shadow-xs">
    <div class="max-w-7xl 2xl:max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- SISI KIRI: Brand Logo & Navigation Links -->
            <div class="flex items-center gap-6 lg:gap-8">
                <a href="/" class="flex items-center gap-3 group shrink-0">
                    <img src="{{ asset('assets/images/media/branding/mcc-aset-logo blue 1.png') }}" 
                         alt="Malang Creative Center Logo" 
                         class="h-9 w-auto object-contain group-hover:opacity-90 transition-opacity">
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-5 lg:gap-6">
                    <a href="/" class="text-sm font-semibold text-slate-700 hover:text-[#001A72] transition-colors flex items-center gap-1.5 {{ request()->is('/') ? 'text-[#001A72] font-bold' : '' }}">
                        <i data-lucide="home" class="w-4 h-4 text-slate-500"></i>
                        <span>Beranda</span>
                    </a>
                    <a href="/katalog" class="text-sm font-semibold text-slate-700 hover:text-[#001A72] transition-colors flex items-center gap-1.5 {{ request()->is('katalog*') ? 'text-[#001A72] font-bold' : '' }}">
                        <i data-lucide="layout-grid" class="w-4 h-4 text-slate-500"></i>
                        <span>Katalog Ruangan</span>
                    </a>
                    <a href="/jadwal" class="text-sm font-semibold text-slate-700 hover:text-[#001A72] transition-colors flex items-center gap-1.5 {{ request()->is('jadwal*') ? 'text-[#001A72] font-bold' : '' }}">
                        <i data-lucide="calendar" class="w-4 h-4 text-slate-500"></i>
                        <span>Jadwal Gedung</span>
                    </a>
                    <a href="/panduan" class="text-sm font-semibold text-slate-700 hover:text-[#001A72] transition-colors flex items-center gap-1.5 {{ request()->is('panduan*') ? 'text-[#001A72] font-bold' : '' }}">
                        <i data-lucide="book-open" class="w-4 h-4 text-slate-500"></i>
                        <span>Panduan & SOP</span>
                    </a>
                </nav>
            </div>

            <!-- SISI KANAN: User Profile / Auth Action Buttons -->
            <div class="hidden md:flex items-center gap-3 shrink-0">
                @if(auth()->check())
                    <!-- Authenticated User Menu -->
                    <div class="relative" @click.away="userDropdownOpen = false">
                        <button @click="userDropdownOpen = !userDropdownOpen" class="flex items-center gap-2.5 p-1.5 rounded-brand hover:bg-slate-100 transition-colors text-left focus:outline-none focus:ring-2 focus:ring-[#001A72] cursor-pointer">
                            <div class="w-8 h-8 rounded-full bg-[#001A72] text-white font-bold flex items-center justify-center text-sm shadow-2xs">
                                {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="text-xs">
                                <span class="font-bold text-slate-900 block truncate max-w-[140px]">{{ auth()->user()->full_name ?? auth()->user()->name ?? 'User Pemohon' }}</span>
                                <span class="text-slate-500 block truncate max-w-[140px] text-[10px]">{{ auth()->user()->institution_name ?? 'Komunitas Kreatif' }}</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="userDropdownOpen" 
                             x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-60 bg-white rounded-brand shadow-elevated border border-slate-200 py-2 z-50 text-slate-900">
                            @if(auth()->user()->isMarketing() || auth()->user()->isSuperAdmin())
                                <a href="{{ route('marketing.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-[#001A72] bg-[#EEF2FF] hover:bg-indigo-100">
                                    <i data-lucide="shield-check" class="w-4 h-4 text-[#001A72]"></i>
                                    <span>Portal Kurasi Marketing</span>
                                </a>
                                <div class="border-t border-slate-100 my-1"></div>
                            @endif
                            <a href="/dashboard" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-[#EEF2FF] hover:text-[#001A72]">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 text-slate-400"></i>
                                <span>Dashboard Pemohon</span>
                            </a>
                            <a href="/jadwal" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-[#EEF2FF] hover:text-[#001A72]">
                                <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                <span>Cek Slot & Keranjang</span>
                            </a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <form method="POST" action="/logout">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 text-left cursor-pointer">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Guest View -->
                    <a href="/login" class="px-4 py-2 text-sm font-semibold text-slate-700 hover:text-[#001A72] hover:bg-slate-100 rounded-brand transition-colors">
                        Masuk
                    </a>
                    <a href="/register" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-[#001A72] hover:bg-[#001250] rounded-brand shadow-sm transition-colors">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>Daftar Akun</span>
                    </a>
                @endif
            </div>

            <!-- Mobile menu button -->
            <div class="flex md:hidden items-center">
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="p-2 rounded-brand text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none">
                    <i x-show="!mobileMenuOpen" data-lucide="menu" class="w-6 h-6"></i>
                    <i x-show="mobileMenuOpen" data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu drawer -->
    <div x-show="mobileMenuOpen" x-cloak x-transition class="md:hidden border-t border-slate-200 bg-white px-4 pt-2 pb-6 space-y-2">
        <a href="/" class="block px-3 py-2 rounded-brand text-base font-semibold text-slate-700 hover:text-[#001A72] hover:bg-slate-50">Beranda</a>
        <a href="/katalog" class="block px-3 py-2 rounded-brand text-base font-semibold text-slate-700 hover:text-[#001A72] hover:bg-slate-50">Katalog Ruangan</a>
        <a href="/jadwal" class="block px-3 py-2 rounded-brand text-base font-semibold text-slate-700 hover:text-[#001A72] hover:bg-slate-50">Jadwal Gedung</a>
        <a href="/panduan" class="block px-3 py-2 rounded-brand text-base font-semibold text-slate-700 hover:text-[#001A72] hover:bg-slate-50">Panduan & SOP</a>
        
        <div class="border-t border-slate-200 pt-3">
            @if(auth()->check())
                @if(auth()->user()->isMarketing() || auth()->user()->isSuperAdmin())
                    <a href="{{ route('marketing.dashboard') }}" class="block px-3 py-2 rounded-brand text-base font-bold text-[#001A72] bg-[#EEF2FF] mb-1">Portal Kurasi Marketing</a>
                @endif
                <a href="/dashboard" class="block px-3 py-2 rounded-brand text-base font-semibold text-slate-700 hover:bg-slate-50">Dashboard Pemohon</a>
                <form method="POST" action="/logout" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full text-left block px-3 py-2 rounded-brand text-base font-semibold text-rose-600 hover:bg-rose-50">Keluar</button>
                </form>
            @else
                <div class="grid grid-cols-2 gap-3 mt-2">
                    <a href="/login" class="text-center px-4 py-2 border border-slate-300 rounded-brand font-semibold text-slate-700 hover:bg-slate-50">Masuk</a>
                    <a href="/register" class="text-center px-4 py-2 bg-[#001A72] text-white rounded-brand font-semibold hover:bg-[#001250]">Daftar</a>
                </div>
            @endif
        </div>
    </div>
</header>