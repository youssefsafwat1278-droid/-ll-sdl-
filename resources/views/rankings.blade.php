<!-- resources/views/rankings.blade.php -->
@extends('layouts.app')

@section('title', 'الترتيب العام - Scout Tanzania')

@section('content')
<div class="space-y-6 animate-fade-in pb-20 sm:pb-0">
    <div class="bg-gradient-to-r from-purple-800 to-indigo-900 rounded-2xl shadow-[0_0_30px_rgba(88,28,135,0.4)] p-6 md:p-8 flex items-center justify-between">
        <div>
            <h2 class="text-3xl md:text-5xl font-black text-white font-['Changa'] mb-2">لوحة الشرف 🏆</h2>
            <p class="text-purple-200">تنافس لتكون الأفضل في العشيرة</p>
        </div>
        <div class="hidden md:block text-5xl">📊</div>
    </div>

    <!-- Top 3 Podium (Optional Idea for future, sticking to table for now but styling it premium) -->

    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full">
                <thead>
                    <tr class="bg-white/5 text-gray-400 text-xs md:text-sm uppercase font-bold text-right border-b border-white/10">
                        <th class="px-4 py-4 md:px-6">#</th>
                        <th class="px-4 py-4 md:px-6">الفريق</th>
                        <th class="px-4 py-4 md:px-6 hidden md:table-cell">الطليعة</th>
                        <th class="px-4 py-4 md:px-6">النقاط</th>
                        <th class="px-4 py-4 md:px-6 hidden sm:table-cell">الجولة</th>
                        <th class="px-4 py-4 md:px-6 hidden lg:table-cell">ترتيب الجولة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($users as $index => $user)
                        @php 
                            $rank = $users->firstItem() + $index;
                            $isTop3 = $rank <= 3;
                            $medal = match($rank) { 1 => '🥇', 2 => '🥈', 3 => '🥉', default => $rank };
                        @endphp
                        <tr class="group transition-colors {{ auth()->id() == $user->id ? 'bg-[#00ff85]/10 hover:bg-[#00ff85]/20' : 'hover:bg-white/5' }}">
                            <td class="px-4 py-4 md:px-6 font-bold text-white text-lg">
                                <span class="{{ $isTop3 ? 'text-2xl drop-shadow-md' : 'text-gray-400' }}">{{ $medal }}</span>
                            </td>
                            <td class="px-4 py-4 md:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="relative w-10 h-10 md:w-12 md:h-12 flex-shrink-0">
                                        <img src="{{ $user->photo_url ?? asset('images/default-avatar.png') }}"
                                             class="w-full h-full rounded-full object-cover border-2 {{ auth()->id() == $user->id ? 'border-[#00ff85]' : 'border-white/20' }}">
                                        @if(auth()->id() == $user->id)
                                            <div class="absolute -bottom-1 -right-1 bg-[#00ff85] text-[#1a0b2e] text-[10px] font-bold px-1 rounded-sm">YOU</div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-white text-sm md:text-base truncate group-hover:text-[#04f5ff] transition-colors">{{ $user->team_name }}</div>
                                        <div class="text-xs text-gray-400 md:hidden">{{ $user->patrol->patrol_name ?? '' }}</div>
                                        <div class="text-xs text-gray-500 hidden md:block">{{ $user->scout->full_name ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 md:px-6 hidden md:table-cell">
                                <span class="bg-white/10 text-gray-300 px-2 py-1 rounded text-xs">{{ $user->patrol->patrol_name ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-4 md:px-6">
                                <span class="font-black text-xl md:text-2xl text-[#00ff85] drop-shadow-[0_0_10px_rgba(0,255,133,0.3)]">
                                    {{ $totalPointsMap[$user->id] ?? $user->total_points }}
                                </span>
                            </td>
                            <td class="px-4 py-4 md:px-6 hidden sm:table-cell">
                                <span class="font-bold text-[#04f5ff]">
                                    +{{ $gameweekPointsMap[$user->id] ?? ($user->gameweek_points ?? 0) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 md:px-6 hidden lg:table-cell text-gray-400 font-mono">
                                #{{ $gameweekRanks[$user->id] ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 flex justify-center">
        <!-- Pagination Styling would go here or rely on default Tailwind pagination styled in AppServiceProvider/Config -->
        {{ $users->links() }}
    </div>
</div>
@endsection
