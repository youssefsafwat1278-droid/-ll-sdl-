<!-- resources/views/patrols/show.blade.php -->
@extends('layouts.app')

@section('title', $patrol->patrol_name . ' - Scout Tanzania')

@section('content')
<div class="space-y-8 animate-fade-in pb-20 sm:pb-0">
    
    <!-- Patrol Header -->
    <div class="relative rounded-3xl p-8 overflow-hidden shadow-2xl border border-white/10 group" 
         style="background: linear-gradient(to right, #1a0b2e, #12002b);">
         
         <!-- Dynamic Glow based on Patrol Color -->
         <div class="absolute inset-0 opacity-20" 
              style="background: radial-gradient(circle at top right, {{ $patrol->patrol_color }} 0%, transparent 70%);">
         </div>
         
        <div class="flex flex-col md:flex-row items-center gap-8 relative z-10">
            <div class="relative w-32 h-32 md:w-40 md:h-40 flex-shrink-0">
                 <div class="w-full h-full rounded-full bg-[#1a0b2e] border-4 border-white/10 flex items-center justify-center shadow-2xl">
                    @if($patrol->patrol_logo_url)
                        <img src="{{ $patrol->patrol_logo_url }}" alt="{{ $patrol->patrol_name }}" class="w-20 h-20 md:w-24 md:h-24 object-contain">
                    @else
                        <span class="text-6xl">🦅</span>
                    @endif
                 </div>
                 <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-white text-[#1a0b2e] px-4 py-1 rounded-full text-sm font-black shadow-lg whitespace-nowrap">
                     Rank #{{ $patrol->rank ?? '-' }}
                 </div>
            </div>

            <div class="flex-1 text-center md:text-right">
                <h2 class="text-4xl md:text-5xl font-black text-white mb-3 drop-shadow-lg font-['Changa']">{{ $patrol->patrol_name }}</h2>
                @if($patrol->description)
                    <p class="text-lg text-gray-300 max-w-2xl">{{ $patrol->description }}</p>
                @endif
                
                <div class="flex flex-wrap justify-center md:justify-start gap-4 mt-6">
                    <div class="bg-white/5 border border-white/10 rounded-xl px-4 py-2 flex flex-col items-center min-w-[100px]">
                        <span class="text-[10px] text-gray-400 font-bold uppercase">النقاط</span>
                        <span class="text-2xl font-black text-[#00ff85]">{{ $patrol->total_points }}</span>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-xl px-4 py-2 flex flex-col items-center min-w-[100px]">
                         <span class="text-[10px] text-gray-400 font-bold uppercase">الكشافة</span>
                         <span class="text-2xl font-black text-white">{{ $patrol->scouts->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Dashboard -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-[#1a0b2e]/60 backdrop-blur-md border border-white/10 rounded-2xl p-4 text-center">
            <div class="text-gray-400 text-xs font-bold uppercase mb-1">متوسط النقاط</div>
            <div class="text-2xl font-black text-[#04f5ff]">
                {{ $patrol->scouts->count() > 0 ? round($patrol->total_points / $patrol->scouts->count(), 1) : 0 }}
            </div>
        </div>
        <div class="bg-[#1a0b2e]/60 backdrop-blur-md border border-white/10 rounded-2xl p-4 text-center">
            <div class="text-gray-400 text-xs font-bold uppercase mb-1">أغلى كشاف</div>
            <div class="text-2xl font-black text-yellow-400">
                {{ $patrol->scouts->max('current_price') ?? 0 }}
            </div>
        </div>
        <div class="bg-[#1a0b2e]/60 backdrop-blur-md border border-white/10 rounded-2xl p-4 text-center">
            <div class="text-gray-400 text-xs font-bold uppercase mb-1">نسبة المساهمة</div>
            <div class="text-2xl font-black text-purple-400">
                {{ $patrol->contribution_percentage ?? 0 }}%
            </div>
        </div>
        <div class="bg-[#1a0b2e]/60 backdrop-blur-md border border-white/10 rounded-2xl p-4 text-center">
            <div class="text-gray-400 text-xs font-bold uppercase mb-1">إجمالي المالكين</div>
            <div class="text-2xl font-black text-orange-400">
                {{ $patrol->scouts->sum('ownership_count') }}
            </div>
        </div>
    </div>

    <!-- Scouts Grid -->
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-3xl p-6 md:p-8">
        <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
            <span>🛡️</span> كشافة الطليعة
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($patrol->scouts as $scout)
                <a href="{{ route('scouts.show', $scout->scout_id) }}" 
                   class="group bg-white/5 hover:bg-white/10 border border-white/5 rounded-2xl p-4 transition-all hover:-translate-y-1">
                    <div class="flex flex-col items-center text-center">
                        <div class="relative w-16 h-16 mb-3">
                             <img src="{{ $scout->photo_url ?? asset('images/default-avatar.png') }}" 
                                 class="w-full h-full rounded-full border-2 border-white/10 group-hover:border-[#04f5ff] transition-colors object-cover">
                             @if($scout->role === 'leader')
                                 <span class="absolute -bottom-1 -right-1 bg-purple-600 text-[10px] text-white px-1.5 rounded font-bold">CPT</span>
                             @endif
                        </div>

                        <h4 class="text-white font-bold text-sm mb-1 truncate w-full">{{ $scout->full_name }}</h4>
                        
                        <div class="flex items-center gap-2 text-xs">
                             <span class="text-[#00ff85] font-bold">{{ $scout->total_points }} pts</span>
                             <span class="text-gray-500">|</span>
                             <span class="text-[#04f5ff] font-bold">{{ $scout->current_price }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
