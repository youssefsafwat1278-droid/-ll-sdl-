<!DOCTYPE html>
<html lang="ar" dir="rtl" 
      x-data="{ 
          sidebarOpen: false, 
          darkMode:localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
      }"
      x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Scout Tanzania Fantasy League')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Changa:wght@200;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-arabic min-h-screen overflow-x-hidden selection:bg-[#04f5ff] selection:text-[#1a0b2e] transition-colors duration-300"
      :class="darkMode ? 'bg-[#12002b] text-white' : 'bg-gray-50 text-gray-900'"
      @sidebar-toggle.window="sidebarOpen = !sidebarOpen">

    <!-- Fantasy Background -->
    <div class="fixed inset-0 z-[-1] pointer-events-none transition-opacity duration-500"
         :class="darkMode ? 'opacity-100' : 'opacity-0'">
        <div class="absolute inset-0 bg-[#12002b]"></div>
        <div class="absolute top-0 left-0 w-full h-[800px] bg-gradient-to-b from-[#37003c] via-[#1a0b2e] to-transparent opacity-60"></div>
        <div class="absolute -top-[200px] -left-[200px] w-[600px] h-[600px] bg-[#00ff85] rounded-full mix-blend-screen filter blur-[150px] opacity-10"></div>
        <div class="absolute top-[20%] right-[-100px] w-[500px] h-[500px] bg-[#04f5ff] rounded-full mix-blend-screen filter blur-[150px] opacity-10"></div>
    </div>
    
    <x-navbar />
    
    <div class="flex pt-16">
        <!-- Backdrop -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm z-30 lg:hidden"
             x-cloak></div>

        <x-sidebar />
        
        <main class="flex-1 lg:mr-72 min-h-screen pt-16">
            <div class="container mx-auto px-4 py-6">
                @if(session('success'))
                    <script>
                        window.__flash = { message: @json(session('success')), type: 'success' };
                    </script>
                @elseif(session('error'))
                    <script>
                        window.__flash = { message: @json(session('error')), type: 'error' };
                    </script>
                @elseif(session('info'))
                    <script>
                        window.__flash = { message: @json(session('info')), type: 'info' };
                    </script>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <x-footer />

    <div x-data="toast"
         x-init="if (window.__flash) { showToast(window.__flash.message, window.__flash.type); window.__flash = null; }"
         x-show="show"
         x-transition
         class="fixed top-20 left-6 z-50">
        <div class="px-4 py-3 rounded-lg shadow-lg text-white"
             x-bind:class="{
                'bg-success-600': type === 'success',
                'bg-danger-600': type === 'error',
                'bg-blue-600': type === 'info'
             }">
            <span x-text="message"></span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @stack('scripts')
</body>
</html>
