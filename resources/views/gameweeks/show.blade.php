<!-- resources/views/gameweeks/show.blade.php -->
@extends('layouts.app')

@section('title', $gameweek->name . ' - Scout Tanzania')

@section('content')
<div class="space-y-8 animate-fade-in pb-20 sm:pb-0">
    
    <!-- Hero Header -->
    <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-white/10 group min-h-[300px] flex items-end">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105"
             style="background-image: url('{{ $gameweek->photo_url ? asset(ltrim($gameweek->photo_url, '/')) : asset('images/default-bg.jpg') }}');">
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#1a0b2e] via-[#1a0b2e]/80 to-transparent"></div>
        
        <div class="relative z-10 p-8 w-full">
            <div class="flex flex-col md:flex-row justify-between items-end gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        @if($gameweek->is_current)
                            <span class="bg-[#00ff85] text-[#1a0b2e] px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest animate-pulse">Live Now</span>
                        @elseif($gameweek->is_finished)
                            <span class="bg-white/20 backdrop-blur-md text-white px-3 py-1 rounded-full text-xs font-bold border border-white/10">Completed</span>
                        @else
                            <span class="bg-[#04f5ff] text-[#1a0b2e] px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest">Upcoming</span>
                        @endif
                        <span class="text-[#04f5ff] font-bold text-sm tracking-wider uppercase">Gameweek {{ $gameweek->gameweek_number }}</span>
                    </div>
                    <h2 class="text-4xl md:text-6xl font-black text-white font-['Changa'] mb-4 leading-tight">{{ $gameweek->name }}</h2>
                    @if($gameweek->description)
                        <p class="text-gray-300 max-w-2xl text-lg">{{ $gameweek->description }}</p>
                    @endif
                </div>

                <!-- Quick Info Cards -->
                <div class="flex gap-4">
                     <div class="bg-[#1a0b2e]/80 backdrop-blur-md border border-white/10 rounded-xl p-4 text-center min-w-[100px]">
                         <div class="text-[10px] text-gray-400 font-bold uppercase mb-1">الموعد</div>
                         <div class="text-white font-bold text-sm">{{ $gameweek->date->format('M d') }}</div>
                     </div>
                     <div class="bg-[#1a0b2e]/80 backdrop-blur-md border border-white/10 rounded-xl p-4 text-center min-w-[100px]">
                         <div class="text-[10px] text-gray-400 font-bold uppercase mb-1">المكان</div>
                         <div class="text-white font-bold text-sm">{{ $gameweek->location }}</div>
                     </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats / Top Performers (If Finished or Active) -->
    @if($gameweek->is_finished || $gameweek->is_current)
        <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-3xl p-6 md:p-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-bold text-white flex items-center gap-2">
                    <span>🔥</span> نجوم الأسبوع
                </h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                @foreach($gameweek->performances()->with('scout')->orderBy('total_points', 'desc')->limit(5)->get() as $index => $perf)
                    <a href="{{ route('scouts.show', $perf->scout->scout_id) }}" 
                       class="group relative bg-white/5 hover:bg-white/10 border border-white/5 hover:border-[#04f5ff]/50 rounded-2xl p-6 pt-10 text-center transition-all hover:-translate-y-2">
                        
                        <!-- Rank Badge -->
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-8 h-8 rounded-full flex items-center justify-center font-black text-xs border-2 border-[#1a0b2e]
                                    {{ $index == 0 ? 'bg-yellow-400 text-[#1a0b2e]' : ($index == 1 ? 'bg-gray-300 text-[#1a0b2e]' : ($index == 2 ? 'bg-orange-400 text-[#1a0b2e]' : 'bg-[#1a0b2e] text-white border-white/20')) }}">
                            {{ $index + 1 }}
                        </div>

                        <div class="relative w-20 h-20 mx-auto mb-4">
                            <img src="{{ $perf->scout->photo_url ?? asset('images/default-avatar.png') }}" 
                                 class="w-full h-full rounded-full object-cover border-2 border-white/10 group-hover:border-[#04f5ff] transition-colors">
                        </div>

                        <h4 class="text-white font-bold text-sm mb-1 truncate">{{ $perf->scout->full_name }}</h4>
                        <div class="text-xs text-gray-500 mb-3">{{ $perf->scout->patrol->patrol_name }}</div>
                        
                        <div class="inline-block bg-[#00ff85]/10 text-[#00ff85] border border-[#00ff85]/20 px-4 py-1 rounded-lg font-black text-xl">
                            {{ $perf->total_points }}
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Deadline Warning (If Upcoming) -->
    @if(!$gameweek->is_finished && !$gameweek->is_current)
         <div class="bg-gradient-to-r from-blue-900 to-indigo-900 rounded-2xl p-8 text-center border border-white/10 shadow-lg">
             <h3 class="text-2xl font-bold text-white mb-2">الموعد النهائي للانتقالات</h3>
             <div class="text-4xl md:text-6xl font-black text-[#04f5ff] font-mono tracking-widest my-4">
                 {{ $gameweek->deadline->format('H:i') }}
                 <span class="text-base md:text-2xl text-gray-400 block mt-2">{{ $gameweek->deadline->format('l, d M Y') }}</span>
             </div>
             <p class="text-gray-300">تأكد من إتمام تشكيلتك قبل إغلاق السوق!</p>
         </div>
    @endif

</div>
@endsection
