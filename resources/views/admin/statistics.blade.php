@extends('layouts.admin')

@section('title', 'الإحصائيات - Admin')

@section('content')
<div class="space-y-8 animate-fade-in">
    <h2 class="text-3xl font-black text-white font-['Changa']">تحليلات وإحصائيات 📈</h2>

    <!-- Transfer Stats (If Active) -->
    @if($currentGameweek && isset($transferStats['total']))
        <div class="bg-gradient-to-r from-[#1a0b2e] to-[#251042] border border-white/10 rounded-2xl p-8 relative overflow-hidden shadow-2xl">
            <div class="absolute top-0 right-0 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
            
            <div class="relative z-10">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    سوق الانتقالات - <span class="text-[#04f5ff]">{{ $currentGameweek->name }}</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                     <div class="bg-white/5 rounded-xl p-5 text-center border border-white/5">
                        <div class="text-xs text-gray-400 font-bold uppercase mb-2">إجمالي التبديلات</div>
                        <div class="text-4xl font-black text-white">{{ $transferStats['total'] }}</div>
                    </div>
                    <div class="bg-white/5 rounded-xl p-5 text-center border border-white/5">
                        <div class="text-xs text-gray-400 font-bold uppercase mb-2">متوسط تبديلات المدربين</div>
                        <div class="text-4xl font-black text-[#00ff85]">{{ round($transferStats['average'], 1) }}</div>
                    </div>
                    <div class="bg-white/5 rounded-xl p-5 text-center border border-white/5">
                        <div class="text-xs text-gray-400 font-bold uppercase mb-2">الجولة الحالية</div>
                        <div class="text-4xl font-black text-purple-400">GW{{ $currentGameweek->gameweek_number }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Most In -->
                    <div>
                        <h4 class="font-bold text-gray-300 mb-4 flex items-center gap-2 text-sm uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-[#00ff85]"></span> الأكثر شراءً
                        </h4>
                        <div class="space-y-3">
                            @foreach($transferStats['most_in'] ?? [] as $item)
                                <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg border border-white/5 hover:bg-white/10 transition">
                                    <span class="text-white font-bold text-sm">{{ $item->scoutIn->full_name ?? 'غير معروف' }}</span>
                                    <span class="bg-[#00ff85]/20 text-[#00ff85] px-2 py-1 rounded text-xs font-black">+{{ $item->count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Most Out -->
                    <div>
                        <h4 class="font-bold text-gray-300 mb-4 flex items-center gap-2 text-sm uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span> الأكثر بيعاً
                        </h4>
                        <div class="space-y-3">
                            @foreach($transferStats['most_out'] ?? [] as $item)
                                <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg border border-white/5 hover:bg-white/10 transition">
                                    <span class="text-white font-bold text-sm">{{ $item->scoutOut->full_name ?? 'غير معروف' }}</span>
                                    <span class="bg-red-500/20 text-red-500 px-2 py-1 rounded text-xs font-black">-{{ $item->count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Most Owned Scouts -->
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-xl p-6">
        <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
            الكشافة الأكثر امتلاكاً (Top Owned)
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-black/20 text-gray-400 text-xs font-bold uppercase border-b border-white/10">
                    <tr>
                        <th class="px-6 py-4 text-right">اللاعب</th>
                        <th class="px-6 py-4 text-right">الطليعة</th>
                        <th class="px-6 py-4 text-right">الملكية</th>
                        <th class="px-6 py-4 text-right">النسبة</th>
                        <th class="px-6 py-4 text-right">السعر</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($mostOwnedScouts as $scout)
                        <tr class="group hover:bg-white/5 transition">
                            <td class="px-6 py-4 font-bold text-white">{{ $scout->full_name }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $scout->patrol->patrol_name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="bg-white/5 rounded px-2 py-1 text-xs font-mono text-gray-300 inline-block border border-white/5">
                                    {{ $scout->ownership_count }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                     <div class="w-16 h-1.5 bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-yellow-500" style="width: {{ $scout->ownership_percentage }}%"></div>
                                     </div>
                                     <span class="text-xs text-yellow-500 font-bold">{{ round($scout->ownership_percentage, 1) }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-black text-[#04f5ff]">{{ $scout->current_price }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Price Movers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Risers -->
        <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                <span class="text-[#00ff85] bg-[#00ff85]/20 p-1.5 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg></span>
                صاعدون (Risers)
            </h3>
            <div class="space-y-3">
                @foreach($priceRisers as $scout)
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-xl border border-white/5 hover:border-[#00ff85]/30 transition group">
                        <div>
                            <div class="font-bold text-white text-sm group-hover:text-[#00ff85] transition">{{ $scout->full_name }}</div>
                            <div class="text-[10px] text-gray-500">{{ $scout->patrol->patrol_name ?? '-' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-black text-white text-sm">{{ $scout->current_price }}</div>
                            <div class="text-xs text-[#00ff85] font-bold">+{{ $scout->price_change }}</div>
                        </div>
                    </div>
                @endforeach
                @if($priceRisers->isEmpty())
                    <p class="text-gray-500 text-center text-sm py-4">لا توجد تغييرات</p>
                @endif
            </div>
        </div>

        <!-- Fallers -->
        <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                <span class="text-red-500 bg-red-500/20 p-1.5 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg></span>
                هابطون (Fallers)
            </h3>
            <div class="space-y-3">
                @foreach($priceFallers as $scout)
                     <div class="flex items-center justify-between p-3 bg-white/5 rounded-xl border border-white/5 hover:border-red-500/30 transition group">
                        <div>
                            <div class="font-bold text-white text-sm group-hover:text-red-500 transition">{{ $scout->full_name }}</div>
                            <div class="text-[10px] text-gray-500">{{ $scout->patrol->patrol_name ?? '-' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-black text-white text-sm">{{ $scout->current_price }}</div>
                            <div class="text-xs text-red-500 font-bold">{{ $scout->price_change }}</div>
                        </div>
                    </div>
                @endforeach
                 @if($priceFallers->isEmpty())
                    <p class="text-gray-500 text-center text-sm py-4">لا توجد تغييرات</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Differentials (Constraint) -->
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-xl p-6">
        <h3 class="text-xl font-bold text-white mb-6">جواهر مخفية (أقل امتلاكاً) 💎</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach($leastOwnedScouts as $scout)
                <div class="bg-white/5 rounded-xl p-4 text-center border border-white/5 hover:bg-white/10 transition group">
                    <div class="text-sm font-bold text-gray-300 group-hover:text-white truncate mb-1">{{ $scout->full_name }}</div>
                    <div class="text-[10px] text-gray-600 mb-2">{{ $scout->patrol->patrol_name ?? '-' }}</div>
                    <div class="text-xs font-mono text-[#04f5ff] bg-[#04f5ff]/10 inline-block px-2 py-0.5 rounded border border-[#04f5ff]/20">
                         {{ $scout->ownership_count }} / 20
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
