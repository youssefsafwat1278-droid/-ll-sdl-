<!-- resources/views/components/navbar.blade.php -->
<nav class="fixed top-0 inset-x-0 z-[200] transition-all duration-300"
     x-data="{ scrolled: false }"
     @scroll.window="scrolled = (window.pageYOffset > 20)"
     :class="scrolled ? 'py-4' : 'py-0 md:py-4'"
     style="z-index: 200 !important;">
    
    <div class="max-w-7xl mx-auto px-4 md:px-6">
        <div class="rounded-2xl transition-all duration-300 backdrop-blur-xl border shadow-2xl relative"
             :class="darkMode 
                ? 'bg-[#1a0b2e]/80 border-white/10 shadow-[#04f5ff]/10' 
                : 'bg-white/90 border-gray-200 shadow-gray-200/50'">
            
            <!-- Glow Effect (Dark Mode only) -->
            <div x-show="darkMode" class="absolute top-0 left-1/2 -translate-x-1/2 w-1/2 h-1 bg-gradient-to-r from-transparent via-[#04f5ff] to-transparent opacity-50 blur-sm"></div>

            <div class="flex items-center justify-between h-16 px-4 md:px-6">
                
                <!-- Left: Logo & Sidebar Trigger -->
                <div class="flex items-center gap-4">
                    <button type="button" @click="$dispatch('sidebar-toggle')" 
                            class="lg:hidden p-2 rounded-xl transition-colors"
                            :class="darkMode ? 'text-white hover:bg-white/10' : 'text-gray-600 hover:bg-gray-100'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>

                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <div class="relative w-8 h-8 flex items-center justify-center bg-gradient-to-tr from-[#04f5ff] to-[#00ff85] rounded-lg shadow-lg group-hover:rotate-12 transition-transform">
                            <span class="text-xl">⚽</span>
                        </div>
                        <h1 class="text-xl font-black tracking-tight font-['Changa'] uppercase"
                            :class="darkMode ? 'text-white' : 'text-gray-900'">
                            Scout <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#04f5ff] to-[#00ff85]">Fantasy</span>
                        </h1>
                    </a>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center gap-3 md:gap-4">
                    
                    <!-- Theme Toggle -->
                    <button @click="darkMode = !darkMode" 
                            class="p-2 rounded-xl transition-all duration-300 relative overflow-hidden group"
                            :class="darkMode ? 'bg-white/5 text-[#fbbf24] hover:bg-white/10' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                        <!-- Sun Icon -->
                        <svg x-show="!darkMode" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="rotate-90 opacity-0" x-transition:enter-end="rotate-0 opacity-100" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <!-- Moon Icon -->
                        <svg x-show="darkMode" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-rotate-90 opacity-0" x-transition:enter-end="rotate-0 opacity-100" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </button>

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center gap-3 pl-2 pr-1 py-1 rounded-full border transition-all duration-300"
                                :class="darkMode 
                                    ? 'border-white/10 hover:border-[#04f5ff]/50 bg-white/5 hover:bg-white/10' 
                                    : 'border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50'">
                            
                            <span class="hidden md:block text-xs font-bold px-2"
                                  :class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                                {{ auth()->user()->first_name }}
                            </span>
                            
                            <img src="{{ auth()->user()->photo_url ?? asset('images/default-avatar.png') }}"
                                 alt="Profile"
                                 class="w-8 h-8 rounded-full border-2 transition-colors object-cover"
                                 :class="darkMode ? 'border-[#04f5ff]/30' : 'border-gray-200'">
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open"
                             @click.away="open = false"
                             x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-2"
                             class="absolute left-0 mt-3 w-56 rounded-2xl shadow-xl overflow-hidden border z-50 p-2"
                             :class="darkMode ? 'bg-[#1a0b2e]/95 backdrop-blur-xl border-white/10' : 'bg-white/95 backdrop-blur-xl border-gray-100'">
                            
                            <!-- Header -->
                            <div class="px-4 py-3 border-b mb-2 rounded-xl"
                                 :class="darkMode ? 'bg-white/5 border-white/5' : 'bg-gray-50 border-gray-100'">
                                <p class="text-xs font-bold uppercase tracking-wider"
                                   :class="darkMode ? 'text-gray-400' : 'text-gray-500'">الحساب</p>
                                <p class="text-sm font-black truncate mt-1"
                                   :class="darkMode ? 'text-white' : 'text-gray-900'">{{ auth()->user()->full_name }}</p>
                            </div>

                            <!-- Links -->
                            <a href="{{ route('settings') }}" 
                               class="flex items-center gap-3 px-4 py-2 text-sm rounded-lg font-bold transition-colors mb-1"
                               :class="darkMode ? 'text-gray-300 hover:bg-white/10 hover:text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'">
                                <span>⚙️</span> الإعدادات
                            </a>
                            
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full flex items-center gap-3 px-4 py-2 text-sm rounded-lg font-bold transition-colors"
                                        :class="darkMode ? 'text-red-400 hover:bg-red-500/10 hover:text-red-300' : 'text-red-500 hover:bg-red-50 hover:text-red-600'">
                                    <span>🚪</span> تسجيل الخروج
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</nav>
