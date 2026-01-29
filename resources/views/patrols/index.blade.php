<!-- resources/views/patrols/index.blade.php -->
@extends('layouts.app')

@section('title', 'الطلائع - Scout Tanzania')

@section('content')
<div class="space-y-8 animate-fade-in pb-20 sm:pb-0">
    <div class="flex items-center justify-between">
         <h2 class="text-3xl font-black text-white font-['Changa']">ترتيب الطلائع 🦅</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($patrols as $patrol)
            @php
                 $isTop3 = $patrol->rank <= 3;
                 $medal = match($patrol->rank) { 1 => '🥇', 2 => '🥈', 3 => '🥉', default => null };
                 $patrolColor = $patrol->patrol_color ?? '#6b7280';
            @endphp

            <a href="{{ route('patrols.show', $patrol->patrol_id) }}" 
               class="group relative bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-3xl overflow-hidden shadow-xl hover:shadow-[0_0_30px_rgba(255,255,255,0.1)] hover:-translate-y-2 transition-all duration-300">
                
                <!-- Background Grad -->
                <div class="absolute inset-x-0 top-0 h-32 opacity-20 transition-opacity group-hover:opacity-30" 
                     style="background: linear-gradient(180deg, {{ $patrolColor }} 0%, transparent 100%);"></div>

                <!-- Content -->
                <div class="p-6 relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-16 h-16 rounded-2xl bg-[#1a0b2e] border-2 border-white/10 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                            @if($patrol->patrol_logo_url)
                                <img src="{{ $patrol->patrol_logo_url }}" alt="{{ $patrol->patrol_name }}" class="w-10 h-10 object-contain">
                            @else
                                <span class="text-3xl">🦅</span>
                            @endif
                        </div>
                        
                        <div class="flex flex-col items-end">
                            @if($medal)
                                <span class="text-3xl drop-shadow-md mb-1">{{ $medal }}</span>
                            @endif
                            <span class="bg-white/10 text-white px-3 py-1 rounded-lg text-sm font-bold border border-white/5">
                                #{{ $patrol->rank ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <h3 class="text-2xl font-black text-white mb-2 group-hover:text-[#04f5ff] transition-colors">{{ $patrol->patrol_name }}</h3>
                    
                    @if($patrol->description)
                        <p class="text-sm text-gray-400 mb-6 line-clamp-2">{{ $patrol->description }}</p>
                    @endif

                    <!-- Mini Stats -->
                    <div class="grid grid-cols-3 gap-2">
                         <div class="bg-[#12002b]/50 rounded-xl p-2 text-center border border-white/5">
                             <div class="text-[10px] text-gray-400 font-bold uppercase">النقاط</div>
                             <div class="text-lg font-black text-[#00ff85]">{{ $patrol->total_points }}</div>
                         </div>
                         <div class="bg-[#12002b]/50 rounded-xl p-2 text-center border border-white/5">
                             <div class="text-[10px] text-gray-400 font-bold uppercase">الكشافة</div>
                             <div class="text-lg font-black text-white">{{ $patrol->scouts->count() }}</div>
                         </div>
                         <div class="bg-[#12002b]/50 rounded-xl p-2 text-center border border-white/5">
                             <div class="text-[10px] text-gray-400 font-bold uppercase">المعدل</div>
                             <div class="text-lg font-black text-purple-400">
                                 {{ $patrol->scouts->count() > 0 ? round($patrol->total_points / $patrol->scouts->count(), 1) : 0 }}
                             </div>
                         </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
