@extends('layouts.app')

@section('title', 'ترتيبي - Scout Tanzania')

@section('content')
<div class="space-y-6 animate-fade-in">
    <!-- Hero Card -->
    <div class="bg-gradient-to-r from-[#1a0b2e] to-[#251042] border border-white/10 rounded-2xl p-8 relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-[#04f5ff]/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
        
        <div class="relative z-10 flex flex-col items-center justify-center text-center">
             <div class="bg-white/10 p-4 rounded-full mb-4 ring-1 ring-white/20 shadow-[0_0_20px_rgba(255,255,255,0.1)]">
                <svg class="w-10 h-10 text-[#00ff85]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
             </div>
             <h2 class="text-3xl font-black text-white font-['Changa'] mb-2">ترتيبي الحالي</h2>
             <div class="flex items-baseline gap-2">
                 <span class="text-sm text-gray-400 font-bold uppercase">المركز</span>
                 <span class="text-5xl font-black text-[#04f5ff] drop-shadow-[0_0_10px_rgba(4,245,255,0.5)]">
                     #{{ $currentGameweekRank ?? '-' }}
                 </span>
             </div>
        </div>
    </div>

    <!-- Rankings Table -->
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-white/10">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                سجل النقاط عبر الجولات
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-center">
                <thead class="bg-black/20 text-gray-400 text-xs font-bold uppercase border-b border-white/10">
                    <tr>
                        <th class="px-6 py-4 text-right">الجولة</th>
                        <th class="px-6 py-4">الترتيب العام</th>
                        <th class="px-6 py-4">نقاط الجولة</th>
                        <th class="px-6 py-4 text-right">إجمالي النقاط</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($rankings as $ranking)
                        <tr class="group hover:bg-white/5 transition">
                            <td class="px-6 py-4 text-right font-bold text-white">
                                {{ $ranking->gameweek?->name ?? 'GW'.$ranking->gameweek_id }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-purple-500/10 text-purple-300 border border-purple-500/20 px-3 py-1 rounded-full font-black text-xs">
                                    #{{ $ranking->overall_rank }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[#00ff85] font-black text-lg">{{ $ranking->gameweek_points }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-white font-bold">{{ $ranking->total_points }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($rankings->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="bg-white/5 rounded-full p-4 mb-4">
                    <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-white">لا توجد جولات بعد</h3>
                <p class="text-gray-400 mt-2">سيبدأ ترتيبك بالظهور بعد انتهاء الجولة الأولى.</p>
            </div>
        @endif
    </div>
</div>
@endsection
