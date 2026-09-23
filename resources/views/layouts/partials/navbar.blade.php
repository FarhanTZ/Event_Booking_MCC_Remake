<header x-data="{ mobileMenuOpen: false, userDropdownOpen: false }" class="bg-white/90 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-500 flex items-center justify-center text-white shadow-md shadow-indigo-200 group-hover:scale-105 transition-transform duration-200">
                        <i data-lucide="building-2" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="font-bold text-lg tracking-tight text-slate-900 block leading-tight">MCC Booking</span>
                        <span class="text-xs text-slate-500 font-medium">Malang Creative Center</span>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-6">
                <a href="/" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition flex items-center gap-1.5">
                    <i data-lucide="layout-grid" class="w-4 h-4"></i>
                    <span>Katalog Ruangan</span>
                </a>
                <a href="/jadwal" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                    <span>Jadwal Gedung</span>
                </a>
                <a href="/panduan" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition flex items-center gap-1.5">
                    <i data-lucide="book-open" class="w-4 h-4"></i>
                    <span>Panduan & SOP</span>
                </a>
            </nav>

            <!-- Auth / Action Buttons -->
            <div class="hidden md:flex items-center gap-3">
                @if(auth()->check())
                    <!-- Authenticated User Menu -->
                    <div class="relative" @click.away="userDropdownOpen = false">
                        <button @click="userDropdownOpen = !userDropdownOpen" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 transition text-left focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-semibold flex items-center justify-center text-sm">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="text-xs">
                                <span class="font-semibold text-slate-800 block">{{ auth()->user()->name ?? 'User Pemohon' }}</span>
                                <span class="text-slate-500">{{ auth()->user()->institution_name ?? 'Komunitas Kreatif' }}</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="userDropdownOpen" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50">
                            <a href="/dashboard" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 text-slate-400"></i>
                                <span>Dashboard Pemohon</span>
                            </a>
                            <a href="/my-bookings" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600">
                                <i data-lucide="ticket" class="w-4 h-4 text-slate-400"></i>
                                <span>Riwayat & E-Pass</span>
                            </a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <form method="POST" action="/logout">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 text-left">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Guest View -->
                    <a href="/login" class="px-3.5 py-2 text-sm font-semibold text-slate-700 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition">
                        Masuk
                    </a>
                    <a href="/register" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs hover:shadow-md transition">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>Daftar Akun</span>
                    </a>
                @endif
            </div>

            <!-- Mobile menu button -->
            <div class="flex md:hidden items-center">
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 focus:outline-none">
                    <i x-show="!mobileMenuOpen" data-lucide="menu" class="w-6 h-6"></i>
                    <i x-show="mobileMenuOpen" data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu modal/drawer -->
    <div x-show="mobileMenuOpen" x-transition class="md:hidden border-t border-slate-200 bg-white px-4 pt-2 pb-6 space-y-3">
        <a href="/" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-indigo-600 hover:bg-slate-50">Katalog Ruangan</a>
        <a href="/jadwal" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-indigo-600 hover:bg-slate-50">Jadwal Gedung</a>
        <a href="/panduan" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-indigo-600 hover:bg-slate-50">Panduan & SOP</a>
        
        <div class="border-t border-slate-200 pt-3">
            @if(auth()->check())
                <a href="/dashboard" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-50">Dashboard Pemohon</a>
                <a href="/my-bookings" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-50">Riwayat & E-Pass</a>
                <form method="POST" action="/logout" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full text-left block px-3 py-2 rounded-md text-base font-medium text-rose-600 hover:bg-rose-50">Keluar</button>
                </form>
            @else
                <div class="grid grid-cols-2 gap-3 mt-2">
                    <a href="/login" class="text-center px-4 py-2 border border-slate-300 rounded-lg font-semibold text-slate-700 hover:bg-slate-50">Masuk</a>
                    <a href="/register" class="text-center px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700">Daftar</a>
                </div>
            @endif
        </div>
    </div>
</header>