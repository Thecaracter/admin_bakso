<header class="bg-white/90 backdrop-blur-sm shadow-md border-b border-white/20">
    <div class="flex items-center justify-between px-4 md:px-6 py-4">
        <div class="flex items-center space-x-3">
            <!-- Mobile menu button -->
            <button
                class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-amber-50 hover:text-amber-600 transition-colors"
                @click="sidebarOpen = !sidebarOpen">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800 font-serif">@yield('header')</h2>
        </div>

        <div class="flex items-center">
            <!-- User Info -->
            <div class="flex items-center space-x-3 px-3 py-2 bg-amber-50 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-500" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="hidden md:block text-gray-700 font-medium">{{ Auth::user()->name }}</span>
            </div>
        </div>
    </div>
</header>
