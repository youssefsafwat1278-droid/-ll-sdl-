<!DOCTYPE html>
<html lang="ar" dir="rtl" 
      x-data="{ 
          sidebarOpen: false, 
          darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
      }"
      x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Scout Tanzania')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Changa:wght@200;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="font-arabic min-h-screen overflow-x-hidden selection:bg-[#04f5ff] selection:text-[#1a0b2e] transition-colors duration-300"
      :class="darkMode ? 'bg-[#0f0518] text-white' : 'bg-gray-50 text-gray-900'">

    <!-- Fantasy Background -->
    <!-- Fantasy Background -->
    <div class="fixed inset-0 z-[-1] pointer-events-none transition-opacity duration-500"
         :class="darkMode ? 'opacity-100' : 'opacity-0'">
        <div class="absolute inset-0 bg-[#0f0518]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-purple-900/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-900/10 rounded-full blur-[100px]"></div>
    </div>
    
    <!-- Admin Navbar -->
    <nav class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
         x-data="{ scrolled: false }"
         @scroll.window="scrolled = (window.pageYOffset > 20)"
         :class="scrolled ? 'py-4' : 'py-0 md:py-4'">
        
        <div class="max-w-7xl mx-auto px-4 md:px-6">
            <div class="rounded-2xl transition-all duration-300 backdrop-blur-xl border shadow-2xl relative"
                 :class="darkMode 
                    ? 'bg-[#1a0b2e]/90 border-white/10 shadow-purple-500/10' 
                    : 'bg-white/90 border-gray-200 shadow-gray-200/50'">
                
                <div class="flex items-center justify-between h-16 px-4">
                    <div class="flex items-center gap-4">
                        <button type="button"
                                @click="sidebarOpen = !sidebarOpen"
                                class="lg:hidden p-2 rounded-xl transition-colors"
                                :class="darkMode ? 'text-white hover:bg-white/10' : 'text-gray-600 hover:bg-gray-100'">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        
                        <div class="flex items-center gap-3">
                            <h1 class="text-xl font-black font-['Changa'] uppercase tracking-wide"
                                :class="darkMode ? 'text-white' : 'text-gray-900'">
                                لوحة <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-600">التحكم</span>
                            </h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- Theme Toggle -->
                        <button @click="darkMode = !darkMode" 
                                class="p-2 rounded-xl transition-all duration-300 relative overflow-hidden group"
                                :class="darkMode ? 'bg-white/5 text-[#fbbf24] hover:bg-white/10' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                            <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        </button>

                        <a href="{{ route('home') }}" 
                           class="hidden md:flex items-center gap-2 text-sm font-bold transition px-3 py-1.5 rounded-lg"
                           :class="darkMode ? 'text-gray-300 hover:text-white hover:bg-white/5' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            العودة للموقع
                        </a>
                        
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-red-500/10 hover:bg-red-500/20 text-red-500 hover:text-red-400 px-4 py-2 rounded-xl text-sm font-bold transition border border-red-500/10">خروج</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex pt-16">
        <div x-show="sidebarOpen"
             x-cloak
             x-transition.opacity.duration.200ms
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm z-30 lg:hidden"></div>
             
        <!-- Admin Sidebar -->
        <aside x-cloak
               class="fixed right-0 top-16 h-[calc(100vh-4rem)] w-72 backdrop-blur-xl shadow-2xl border-l transform transition-transform duration-300 ease-out z-40 overflow-y-auto custom-scrollbar"
               :class="[
                   sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0',
                   darkMode ? 'bg-[#1a0b2e]/95 border-white/10 text-white' : 'bg-white/95 border-gray-200 text-gray-900'
               ]">
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                           @click="sidebarOpen = false"
                           class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all"
                           :class="darkMode 
                                ? ({{ request()->routeIs('admin.dashboard') ? 'true' : 'false' }} ? 'bg-gradient-to-r from-purple-600/20 to-purple-600/5 text-[#04f5ff] border border-purple-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5')
                                : ({{ request()->routeIs('admin.dashboard') ? 'true' : 'false' }} ? 'bg-purple-100 text-purple-700 border border-purple-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span>الرئيسية</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.summary') }}"
                           @click="sidebarOpen = false"
                           class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all"
                           :class="darkMode 
                                ? ({{ request()->routeIs('admin.summary') && !request()->routeIs('admin.summary.show') ? 'true' : 'false' }} ? 'bg-gradient-to-r from-blue-600/20 to-blue-600/5 text-blue-400 border border-blue-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5')
                                : ({{ request()->routeIs('admin.summary') && !request()->routeIs('admin.summary.show') ? 'true' : 'false' }} ? 'bg-blue-50 text-blue-600 border border-blue-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>الملخص العام</span>
                        </a>
                    </li>

                    <li>
                    <li>
                        <a href="{{ route('admin.attendance-stats') }}"
                           class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all"
                           :class="darkMode 
                                ? ({{ request()->routeIs('admin.attendance-stats') ? 'true' : 'false' }} ? 'bg-gradient-to-r from-green-600/20 to-green-600/5 text-green-400 border border-green-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5')
                                : ({{ request()->routeIs('admin.attendance-stats') ? 'true' : 'false' }} ? 'bg-green-50 text-green-600 border border-green-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span>إحصائيات المشاركة</span>
                        </a>
                    </li>
                    </li>

                    <li>
                    <li>
                        <a href="{{ route('admin.points.index') }}"
                           class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all"
                           :class="darkMode 
                                ? ({{ request()->routeIs('admin.points*') ? 'true' : 'false' }} ? 'bg-gradient-to-r from-yellow-600/20 to-yellow-600/5 text-yellow-400 border border-yellow-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5')
                                : ({{ request()->routeIs('admin.points*') ? 'true' : 'false' }} ? 'bg-yellow-50 text-yellow-600 border border-yellow-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>إدخال النقاط</span>
                        </a>
                    </li>
                    </li>

                    <li>
                    <li>
                        <a href="{{ route('admin.scout-images.index') }}"
                           class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all"
                           :class="darkMode 
                                ? ({{ request()->routeIs('admin.scout-images*') || request()->routeIs('admin.patrol-images*') ? 'true' : 'false' }} ? 'bg-gradient-to-r from-pink-600/20 to-pink-600/5 text-pink-400 border border-pink-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5')
                                : ({{ request()->routeIs('admin.scout-images*') || request()->routeIs('admin.patrol-images*') ? 'true' : 'false' }} ? 'bg-pink-50 text-pink-600 border border-pink-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>إدارة الصور</span>
                        </a>
                    </li>
                    </li>

                    <li>
                    <li>
                        <a href="{{ route('admin.gameweeks.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all"
                           :class="darkMode 
                                ? ({{ request()->routeIs('admin.gameweeks*') ? 'true' : 'false' }} ? 'bg-gradient-to-r from-indigo-600/20 to-indigo-600/5 text-indigo-400 border border-indigo-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5')
                                : ({{ request()->routeIs('admin.gameweeks*') ? 'true' : 'false' }} ? 'bg-indigo-50 text-indigo-600 border border-indigo-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>الجولات</span>
                        </a>
                    </li>
                    </li>

                    <li>
                    <li>
                        <a href="{{ route('admin.news.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all"
                           :class="darkMode 
                                ? ({{ request()->routeIs('admin.news*') ? 'true' : 'false' }} ? 'bg-gradient-to-r from-teal-600/20 to-teal-600/5 text-teal-400 border border-teal-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5')
                                : ({{ request()->routeIs('admin.news*') ? 'true' : 'false' }} ? 'bg-teal-50 text-teal-600 border border-teal-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                            <span>الأخبار</span>
                        </a>
                    </li>
                    </li>

                    <li>
                    <li>
                        <a href="{{ route('admin.notifications.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all"
                           :class="darkMode 
                                ? ({{ request()->routeIs('admin.notifications*') ? 'true' : 'false' }} ? 'bg-gradient-to-r from-orange-600/20 to-orange-600/5 text-orange-400 border border-orange-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5')
                                : ({{ request()->routeIs('admin.notifications*') ? 'true' : 'false' }} ? 'bg-orange-50 text-orange-600 border border-orange-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0h6z"></path>
                            </svg>
                            <span>الإشعارات</span>
                        </a>
                    </li>
                    </li>

                    <li>
                    <li>
                        <a href="{{ route('admin.statistics') }}" 
                           class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all"
                           :class="darkMode 
                                ? ({{ request()->routeIs('admin.statistics') ? 'true' : 'false' }} ? 'bg-gradient-to-r from-cyan-600/20 to-cyan-600/5 text-cyan-400 border border-cyan-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5')
                                : ({{ request()->routeIs('admin.statistics') ? 'true' : 'false' }} ? 'bg-cyan-50 text-cyan-600 border border-cyan-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span>الإحصائيات</span>
                        </a>
                    </li>
                     <li>
                        <a href="{{ route('admin.user-registrations.index') }}"
                           class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all"
                           :class="darkMode 
                                ? ({{ request()->routeIs('admin.user-registrations*') ? 'true' : 'false' }} ? 'bg-gradient-to-r from-lime-600/20 to-lime-600/5 text-lime-400 border border-lime-500/30' : 'text-gray-400 hover:text-white hover:bg-white/5')
                                : ({{ request()->routeIs('admin.user-registrations*') ? 'true' : 'false' }} ? 'bg-lime-50 text-lime-600 border border-lime-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 14a4 4 0 10-8 0m8 0v4m-8-4v4m2-10a2 2 0 114 0 2 2 0 01-4 0z"></path>
                            </svg>
                            <span>Registrations</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="flex-1 lg:mr-72 min-h-screen">
            <div class="container mx-auto px-4 py-8">
                @if(session('success'))
                    <script>
                        window.__flash = { message: @json(session('success')), type: 'success' };
                    </script>
                @elseif(session('error'))
                    <script>
                        window.__flash = { message: @json(session('error')), type: 'error' };
                    </script>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @stack('scripts')

    <div x-data="toast"
         x-init="if (window.__flash) { showToast(window.__flash.message, window.__flash.type); window.__flash = null; }"
         x-show="show"
         x-transition
         class="fixed top-20 left-6 z-50">
        <div class="px-6 py-4 rounded-xl shadow-2xl text-white backdrop-blur-md border border-white/10"
             x-bind:class="{
                'bg-green-600/90': type === 'success',
                'bg-red-600/90': type === 'error',
                'bg-blue-600/90': type === 'info'
             }">
            <span x-text="message" class="font-bold"></span>
        </div>
    </div>
</body>
</html>
