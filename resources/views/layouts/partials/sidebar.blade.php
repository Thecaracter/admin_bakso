{{-- layouts/partials/sidebar.blade.php --}}
<div x-cloak x-show="sidebarOpen" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm lg:hidden"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false">
</div>

<aside x-cloak :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full lg:translate-x-0': !sidebarOpen }"
    class="fixed inset-y-0 left-0 w-64 bg-white/90 backdrop-blur-sm shadow-xl border-r border-white/20 transition-transform duration-300 z-20">
    <div class="flex flex-col h-full">
        <!-- Logo & Brand Section -->
        <div class="p-6 border-b border-amber-100">
            <div class="flex items-center space-x-4 group">
                <img src="{{ asset('assets/images/bakso.jpeg') }}"
                    class="w-12 h-12 rounded-full object-cover border-2 border-amber-500 shadow-lg group-hover:scale-110 transition-transform duration-300">
                <div>
                    <h1
                        class="font-bold text-gray-800 text-lg font-serif group-hover:text-amber-600 transition-colors duration-300">
                        Bakso Boled</h1>
                    <p class="text-sm text-gray-600 italic">Sistem Manajemen</p>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 p-4 space-y-3 overflow-y-auto">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-500/30' : 'text-gray-700 hover:bg-gradient-to-r hover:from-amber-50 hover:to-orange-50 hover:text-amber-600' }}"
                @click="sidebarOpen = false">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <!-- Menu -->
            <div x-data="{ menuOpen: {{ request()->routeIs('produk.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="menuOpen = !menuOpen"
                    class="flex items-center justify-between w-full px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('produk.*') ? 'bg-gradient-to-r from-amber-50 to-orange-50 text-amber-600' : 'text-gray-700 hover:bg-gradient-to-r hover:from-amber-50 hover:to-orange-50 hover:text-amber-600' }} group">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span class="font-medium">Menu</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': menuOpen }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="menuOpen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2" class="pl-12">
                    <a href="{{ route('produk.index') }}" @click="sidebarOpen = false"
                        class="block px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-300 {{ request()->routeIs('produk.index') ? 'bg-amber-100 text-amber-700' : 'text-amber-600 bg-amber-50/50 hover:bg-amber-100/80' }} hover:pl-6 hover:shadow-sm">
                        Daftar Menu
                    </a>
                </div>
            </div>

            <!-- Transaksi -->
            <div x-data="{ transactionOpen: {{ request()->routeIs('kasir.*') || request()->routeIs('transaksi.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="transactionOpen = !transactionOpen"
                    class="flex items-center justify-between w-full px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('kasir.*') || request()->routeIs('transaksi.*') ? 'bg-gradient-to-r from-amber-50 to-orange-50 text-amber-600' : 'text-gray-700 hover:bg-gradient-to-r hover:from-amber-50 hover:to-orange-50 hover:text-amber-600' }} group">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">Transaksi</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': transactionOpen }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="transactionOpen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2" class="pl-12 space-y-1">
                    <a href="{{ route('kasir.index') }}" @click="sidebarOpen = false"
                        class="block px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-300 {{ request()->routeIs('kasir.index') ? 'bg-amber-100 text-amber-700' : 'text-amber-600 bg-amber-50/50 hover:bg-amber-100/80' }} hover:pl-6 hover:shadow-sm">
                        Kasir
                    </a>
                    <a href="{{ route('transaksi.index') }}" @click="sidebarOpen = false"
                        class="block px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-300 {{ request()->routeIs('transaksi.index') ? 'bg-amber-100 text-amber-700' : 'text-amber-600 bg-amber-50/50 hover:bg-amber-100/80' }} hover:pl-6 hover:shadow-sm">
                        Riwayat Transaksi
                    </a>
                </div>
            </div>

            <!-- Reports Section -->
            <div class="pt-4 border-t border-amber-100/50">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Laporan</p>

                <div x-data="{ reportsOpen: {{ request()->routeIs('laporan.*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="reportsOpen = !reportsOpen"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('laporan.*') ? 'bg-gradient-to-r from-amber-50 to-orange-50 text-amber-600' : 'text-gray-700 hover:bg-gradient-to-r hover:from-amber-50 hover:to-orange-50 hover:text-amber-600' }} group">
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="font-medium">Laporan</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': reportsOpen }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="reportsOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" class="pl-12 space-y-1">
                        <a href="{{ route('laporan.penjualan') }}" @click="sidebarOpen = false"
                            class="block px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-300 {{ request()->routeIs('laporan.penjualan') ? 'bg-amber-100 text-amber-700' : 'text-amber-600 bg-amber-50/50 hover:bg-amber-100/80' }} hover:pl-6 hover:shadow-sm">
                            Penjualan
                        </a>
                        <a href="{{ route('laporan.laba-rugi') }}" @click="sidebarOpen = false"
                            class="block px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-300 {{ request()->routeIs('laporan.laba-rugi') ? 'bg-amber-100 text-amber-700' : 'text-amber-600 bg-amber-50/50 hover:bg-amber-100/80' }} hover:pl-6 hover:shadow-sm">
                            Laba Rugi
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Logout Section -->
        <div class="p-4 border-t border-amber-100">
            <form action="{{ route('logout') }}" method="POST" @click="sidebarOpen = false">
                @csrf
                <button type="submit"
                    class="flex items-center justify-center space-x-3 px-4 py-3 w-full rounded-xl text-gray-700 hover:bg-red-50 hover:text-red-600 transition-all duration-300 group">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 transition-transform duration-300 group-hover:-translate-x-1" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="font-medium">Keluar</span>
                </button>
            </form>
        </div>
    </div>
</aside>
