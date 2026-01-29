<!-- resources/views/components/sidebar.blade.php -->
<aside 
       class="fixed right-0 top-16 h-[calc(100vh-4rem)] w-72 bg-[#1a0b2e]/90 backdrop-blur-xl shadow-[0_0_40px_rgba(0,0,0,0.5)] border-l border-white/5 transform transition-transform duration-300 ease-out z-40 lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
       x-cloak>
    
    <nav class="p-4 h-full overflow-y-auto custom-scrollbar">
        <ul class="space-y-2">
            <li>
                <a href="{{ route('home') }}"
                   @click="sidebarOpen = false"
                   class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('home') ? 'bg-gradient-to-r from-[#00ff85]/20 to-[#00ff85]/5 border border-[#00ff85]/30 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="p-2 rounded-lg {{ request()->routeIs('home') ? 'bg-[#00ff85]/20 text-[#00ff85]' : 'bg-white/5 text-gray-400 group-hover:text-white group-hover:bg-white/10' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <span class="font-bold tracking-wide">الرئيسية</span>
                    @if(request()->routeIs('home'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-[#00ff85] shadow-[0_0_10px_#00ff85]"></div>
                    @endif
                </a>
            </li>

            <li>
                <a href="{{ route('my-team') }}"
                   @click="sidebarOpen = false"
                   class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('my-team') ? 'bg-gradient-to-r from-[#04f5ff]/20 to-[#04f5ff]/5 border border-[#04f5ff]/30 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="p-2 rounded-lg {{ request()->routeIs('my-team') ? 'bg-[#04f5ff]/20 text-[#04f5ff]' : 'bg-white/5 text-gray-400 group-hover:text-white group-hover:bg-white/10' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <span class="font-bold tracking-wide">فريقي</span>
                    @if(request()->routeIs('my-team'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-[#04f5ff] shadow-[0_0_10px_#04f5ff]"></div>
                    @endif
                </a>
            </li>

            <li>
                <a href="{{ route('transfers') }}"
                   @click="sidebarOpen = false"
                   class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('transfers*') ? 'bg-gradient-to-r from-[#e90052]/20 to-[#e90052]/5 border border-[#e90052]/30 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="p-2 rounded-lg {{ request()->routeIs('transfers*') ? 'bg-[#e90052]/20 text-[#e90052]' : 'bg-white/5 text-gray-400 group-hover:text-white group-hover:bg-white/10' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    <span class="font-bold tracking-wide">التبديلات</span>
                    @if(request()->routeIs('transfers*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-[#e90052] shadow-[0_0_10px_#e90052]"></div>
                    @endif
                </a>
            </li>

            <li>
                <a href="{{ route('scouts.index') }}"
                   @click="sidebarOpen = false"
                   class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('scouts*') ? 'bg-gradient-to-r from-purple-500/20 to-purple-500/5 border border-purple-500/30 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="p-2 rounded-lg {{ request()->routeIs('scouts*') ? 'bg-purple-500/20 text-purple-400' : 'bg-white/5 text-gray-400 group-hover:text-white group-hover:bg-white/10' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <span class="font-bold tracking-wide">الكشافة</span>
                </a>
            </li>

            <li>
                <a href="{{ route('patrols.index') }}"
                   @click="sidebarOpen = false"
                   class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('patrols*') ? 'bg-gradient-to-r from-yellow-500/20 to-yellow-500/5 border border-yellow-500/30 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="p-2 rounded-lg {{ request()->routeIs('patrols*') ? 'bg-yellow-500/20 text-yellow-400' : 'bg-white/5 text-gray-400 group-hover:text-white group-hover:bg-white/10' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <span class="font-bold tracking-wide">الطلائع</span>
                </a>
            </li>

            <li>
                <a href="{{ route('rankings') }}"
                   @click="sidebarOpen = false"
                   class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('rankings*') ? 'bg-gradient-to-r from-blue-500/20 to-blue-500/5 border border-blue-500/30 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="p-2 rounded-lg {{ request()->routeIs('rankings*') ? 'bg-blue-500/20 text-blue-400' : 'bg-white/5 text-gray-400 group-hover:text-white group-hover:bg-white/10' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <span class="font-bold tracking-wide">الترتيب</span>
                </a>
            </li>

            <li>
                <a href="{{ route('gameweeks.index') }}"
                   @click="sidebarOpen = false"
                   class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('gameweeks*') ? 'bg-gradient-to-r from-pink-500/20 to-pink-500/5 border border-pink-500/30 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="p-2 rounded-lg {{ request()->routeIs('gameweeks*') ? 'bg-pink-500/20 text-pink-400' : 'bg-white/5 text-gray-400 group-hover:text-white group-hover:bg-white/10' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="font-bold tracking-wide">الأسابيع</span>
                </a>
            </li>
        </ul>

        <div class="mt-8 relative overflow-hidden rounded-2xl p-4 border border-white/10 group">
             <div class="absolute inset-0 bg-gradient-to-br from-[#00ff85]/10 to-[#04f5ff]/10 group-hover:from-[#00ff85]/20 group-hover:to-[#04f5ff]/20 transition-all duration-500"></div>
             <div class="relative z-10">
                 <div class="text-[10px] uppercase tracking-wider text-[#00ff85] font-bold mb-1">نقاطك الكلية</div>
                 <div class="text-3xl font-black text-white mb-2">{{ auth()->user()->total_points }}</div>
                 <div class="flex items-center gap-2 text-xs text-gray-400">
                     <svg class="w-3 h-3 text-[#04f5ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                     </svg>
                     <span>الترتيب: <strong class="text-white">#{{ auth()->user()->rankings()->latest()->first()?->overall_rank ?? '-' }}</strong></span>
                 </div>
             </div>
        </div>
    </nav>
</aside>
