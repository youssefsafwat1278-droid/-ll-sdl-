<!-- resources/views/gameweeks/index.blade.php -->
@extends('layouts.app')

@section('title', 'الأسابيع - Scout Tanzania')

@section('content')
<div class="space-y-8 animate-fade-in pb-20 sm:pb-0">
    <div class="flex items-center justify-between">
         <h2 class="text-3xl font-black text-white font-['Changa']">جدول الأسابيع 🗓️</h2>
    </div>

    <!-- Timeline / Grid -->
    <div class="space-y-6 relative">
        <!-- Vertical Line (Desktop) -->
        <div class="hidden lg:block absolute right-1/2 top-0 bottom-0 w-1 bg-gradient-to-b from-[#00ff85] via-[#04f5ff] to-purple-600 opacity-30 -mr-0.5 rounded-full"></div>

        @foreach($gameweeks as $index => $gameweek)
            <div class="relative flex flex-col lg:flex-row items-center gap-8 {{ $index % 2 == 0 ? 'lg:flex-row-reverse' : '' }}">
                
                <!-- Center Node (Desktop) -->
                <div class="hidden lg:flex absolute right-1/2 -mr-3 w-6 h-6 rounded-full border-4 border-[#1a0b2e] z-10 
                            {{ $gameweek->is_current ? 'bg-[#00ff85] shadow-[0_0_20px_#00ff85]' : ($gameweek->is_finished ? 'bg-gray-500' : 'bg-[#04f5ff]') }}">
                </div>

                <!-- Date Badge Mobile -->
                <div class="lg:w-1/2 flex {{ $index % 2 == 0 ? 'lg:justify-end' : 'lg:justify-start' }} w-full">
                    <div class="text-center lg:text-right">
                        <div class="text-[#04f5ff] font-bold text-lg hidden lg:block">{{ $gameweek->date->format('Y-m-d') }}</div>
                        <div class="text-gray-500 text-sm hidden lg:block">{{ $gameweek->location }}</div>
                    </div>
                </div>

                <!-- Card -->
                <div class="lg:w-1/2 w-full">
                    <div class="relative group bg-[#1a0b2e]/60 backdrop-blur-xl border rounded-3xl overflow-hidden shadow-xl hover:-translate-y-1 transition duration-300
                                {{ $gameweek->is_current ? 'border-[#00ff85]/50 shadow-[0_0_20px_rgba(0,255,133,0.15)]' : 'border-white/10 hover:border-white/30' }}">
                        
                        <!-- Status Badge -->
                        <div class="absolute top-4 left-4 z-20">
                            @if($gameweek->is_current)
                                <span class="bg-[#00ff85] text-[#1a0b2e] px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest animate-pulse">Live</span>
                            @elseif($gameweek->is_finished)
                                <span class="bg-gray-600/80 backdrop-blur-md text-white px-3 py-1 rounded-full text-xs font-bold border border-white/10">Finished</span>
                            @else
                                <span class="bg-[#04f5ff] text-[#1a0b2e] px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest">Upcoming</span>
                            @endif
                        </div>

                        <!-- Image / Graphic -->
                        <div class="h-32 bg-cover bg-center relative" 
                             style="background-image: url('{{ $gameweek->photo_url ? asset(ltrim($gameweek->photo_url, '/')) : asset('images/default-bg.jpg') }}');">
                             <div class="absolute inset-0 bg-gradient-to-t from-[#1a0b2e] to-transparent"></div>
                             
                             @if(!$gameweek->photo_url)
                                 <div class="absolute inset-0 flex items-center justify-center opacity-10">
                                     <span class="text-8xl font-black text-white">GW{{ $gameweek->gameweek_number }}</span>
                                 </div>
                             @endif
                        </div>

                        <div class="p-6 relative -mt-6">
                            <h3 class="text-2xl font-black text-white mb-2">{{ $gameweek->name }}</h3>
                            
                            <!-- Mobile Date info -->
                            <div class="lg:hidden flex items-center gap-4 text-sm text-gray-400 mb-4 border-b border-white/5 pb-4">
                                <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> {{ $gameweek->date->format('M d') }}</span>
                                <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> {{ $gameweek->location }}</span>
                            </div>
                            
                            @if($gameweek->description)
                                <p class="text-sm text-gray-400 mb-4 line-clamp-2">{{ $gameweek->description }}</p>
                            @endif

                            <div class="flex items-center justify-between mt-4">
                                <div class="text-xs text-gray-500 font-mono">
                                    Deadline: <span class="text-[#04f5ff]">{{ $gameweek->deadline->format('H:i') }}</span>
                                </div>
                                <a href="{{ route('gameweeks.show', $gameweek->id) }}" class="text-[#00ff85] hover:text-white font-bold text-sm flex items-center gap-1 transition-colors">
                                    التفاصيل <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
