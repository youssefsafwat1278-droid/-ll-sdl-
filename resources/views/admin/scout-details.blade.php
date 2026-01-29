@extends('layouts.admin')

@section('title', 'تفاصيل الكشاف: ' . $scout->full_name . ' - Admin')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Back Button & Title -->
    <div class="flex items-center justify-between">
         <h2 class="text-3xl font-black text-white font-['Changa']">ملف الكشاف 📁</h2>
         <a href="{{ route('admin.summary') }}"
            class="inline-flex items-center gap-2 bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white px-4 py-2 rounded-xl transition border border-white/5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            العودة للملخص
         </a>
    </div>

    <!-- Hero Header -->
    <div class="relative bg-gradient-to-r from-[#1a0b2e] to-[#2d1b4e] rounded-3xl overflow-hidden shadow-2xl border border-white/10 p-8 md:p-12">
        <div class="absolute top-0 right-0 w-64 h-64 bg-[#04f5ff]/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-8 md:gap-12">
            
            <!-- Avatar -->
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-br from-[#04f5ff] to-[#00ff85] rounded-full blur opacity-30 group-hover:opacity-60 transition duration-500"></div>
                <img src="{{ $scout->photo_url ?? asset('images/default-avatar.png') }}"
                     alt="{{ $scout->full_name }}"
                     class="relative w-40 h-40 md:w-48 md:h-48 rounded-full border-4 border-[#1a0b2e] object-cover shadow-2xl z-10">
                
                <div class="absolute bottom-2 right-2 z-20 bg-[#1a0b2e] text-white text-sm font-bold px-3 py-1 rounded-full border border-white/10 shadow-lg">
                    #{{ $scout->scout_id }}
                </div>
            </div>

            <!-- Info -->
            <div class="flex-1 text-center md:text-right space-y-4">
                <div>
                     <h1 class="text-4xl md:text-5xl font-black text-white font-['Changa'] mb-2">{{ $scout->full_name }}</h1>
                     @if($scout->patrol)
                        <div class="flex items-center justify-center md:justify-start gap-3">
                            @if($scout->patrol->patrol_logo_url)
                                <img src="{{ $scout->patrol->patrol_logo_url }}" class="w-8 h-8 rounded-full border border-white/20">
                            @endif
                            <span class="text-xl text-gray-300 font-bold">{{ $scout->patrol->patrol_name }}</span>
                        </div>
                     @else 
                        <span class="text-xl text-gray-500 font-bold">بدون طليعة</span>
                     @endif
                </div>

                <div class="flex flex-wrap justify-center md:justify-start gap-3">
                    @if($scout->role === 'leader')
                        <span class="bg-purple-600/20 text-purple-300 border border-purple-500/30 px-4 py-1.5 rounded-lg text-sm font-bold uppercase tracking-wider">Captain</span>
                    @elseif($scout->role === 'senior')
                        <span class="bg-blue-600/20 text-blue-300 border border-blue-500/30 px-4 py-1.5 rounded-lg text-sm font-bold uppercase tracking-wider">Senior</span>
                    @endif

                    @if($scout->status === 'available')
                        <span class="bg-[#00ff85]/20 text-[#00ff85] border border-[#00ff85]/30 px-4 py-1.5 rounded-lg text-sm font-bold uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#00ff85] animate-pulse"></span> Available
                        </span>
                    @elseif($scout->status === 'injured')
                        <span class="bg-red-500/20 text-red-400 border border-red-500/30 px-4 py-1.5 rounded-lg text-sm font-bold uppercase tracking-wider">Injured</span>
                    @elseif($scout->status === 'suspended')
                        <span class="bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 px-4 py-1.5 rounded-lg text-sm font-bold uppercase tracking-wider">Suspended</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Key Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-[#1a0b2e]/60 backdrop-blur-md border border-white/10 rounded-2xl p-6 text-center">
            <div class="text-xs text-gray-400 font-bold uppercase mb-2">إجمالي النقاط</div>
            <div class="text-4xl font-black text-[#00ff85]">{{ $scout->total_points ?? 0 }}</div>
        </div>

        <div class="bg-[#1a0b2e]/60 backdrop-blur-md border border-white/10 rounded-2xl p-6 text-center">
            <div class="text-xs text-gray-400 font-bold uppercase mb-2">متوسط النقاط</div>
            <div class="text-4xl font-black text-[#04f5ff]">{{ $avgPoints }}</div>
            <div class="text-[10px] text-gray-500 font-bold mt-1">من {{ $totalGames }} جولة</div>
        </div>

        <div class="bg-[#1a0b2e]/60 backdrop-blur-md border border-white/10 rounded-2xl p-6 text-center">
            <div class="text-xs text-gray-400 font-bold uppercase mb-2">أفضل أداء</div>
            <div class="text-4xl font-black text-purple-400">{{ $bestPerformance?->total_points ?? 0 }}</div>
            @if($bestPerformance)
                <div class="text-[10px] text-gray-500 font-bold mt-1 text-purple-300">GW{{ $bestPerformance->gameweek->gameweek_number }}</div>
            @endif
        </div>

        <div class="bg-[#1a0b2e]/60 backdrop-blur-md border border-white/10 rounded-2xl p-6 text-center">
            <div class="text-xs text-gray-400 font-bold uppercase mb-2">السعر الحالي</div>
            <div class="text-4xl font-black text-yellow-500">{{ $scout->current_price }}</div>
            @if($scout->price_change != 0)
                <div class="text-xs font-bold {{ $scout->price_change > 0 ? 'text-[#00ff85]' : 'text-red-500' }} mt-1">
                    {{ $scout->price_change > 0 ? '▲' : '▼' }} {{ abs($scout->price_change) }}
                </div>
            @endif
        </div>
    </div>

    <!-- Detailed Breakdown -->
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-3xl p-8">
        <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
            <svg class="w-6 h-6 text-[#04f5ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            تفصيل النقاط (تراكمي)
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @php
                 $categories = [
                    'attendance_points' => ['label' => 'الحضور', 'icon' => '✅'],
                    'interaction_points' => ['label' => 'التفاعل', 'icon' => '💬'],
                    'uniform_points' => ['label' => 'الزي', 'icon' => '👔'],
                    'activity_points' => ['label' => 'النشاط', 'icon' => '⚡'],
                    'service_points' => ['label' => 'الخدمة', 'icon' => '🤝'],
                    'committee_points' => ['label' => 'اللجان', 'icon' => '📋'],
                    'mass_points' => ['label' => 'القداس', 'icon' => '⛪'],
                    'confession_points' => ['label' => 'الاعتراف', 'icon' => '🙏'],
                    'group_mass_points' => ['label' => 'ق.جماعة', 'icon' => '⛪'],
                    'tribe_mass_points' => ['label' => 'ق.قبيلة', 'icon' => '⛪'],
                    'aswad_points' => ['label' => 'أسود', 'icon' => '🦁'],
                    'first_group_points' => ['label' => 'م.أولى', 'icon' => '🥇'],
                    'largest_patrol_points' => ['label' => 'أ.طليعة', 'icon' => '👥'],
                    'penalty_points' => ['label' => 'الجزاءات', 'icon' => '⚠️'],
                ];
            @endphp

            @foreach($categories as $key => $category)
                @php
                    $value = $categoryTotals[$key] ?? 0;
                    $isNegative = $value < 0;
                    $colorClass = $isNegative ? 'text-red-500' : 'text-white';
                    $bgClass = $isNegative ? 'bg-red-500/10 border-red-500/20' : 'bg-white/5 border-white/5';
                @endphp
                <div class="{{ $bgClass }} border rounded-xl p-4 flex flex-col items-center justify-center text-center hover:bg-white/10 transition">
                    <div class="text-2xl mb-1">{{ $category['icon'] }}</div>
                    <div class="text-[10px] uppercase font-bold text-gray-500 mb-1">{{ $category['label'] }}</div>
                    <div class="text-xl font-black {{ $colorClass }}">{{ $value > 0 ? '+' : '' }}{{ $value }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-3xl p-6 md:p-8 overflow-hidden">
        <h3 class="text-2xl font-bold text-white mb-6">سجل الأداء</h3>
        
        @if($performances->isEmpty())
             <div class="text-center py-12 text-gray-500 font-bold">لا توجد بيانات أداء حتى الآن</div>
        @else
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm text-left">
                    <thead class="bg-black/20 text-gray-400 text-xs font-bold uppercase border-b border-white/10">
                        <tr>
                            <th class="px-4 py-3 text-right">الجولة</th>
                            <th class="px-4 py-3 text-center">حضور</th>
                            <th class="px-4 py-3 text-center">تفاعل</th>
                            <th class="px-4 py-3 text-center">زي</th>
                            <th class="px-4 py-3 text-center">نشاط</th>
                            <th class="px-4 py-3 text-center">خدمة</th>
                            <th class="px-4 py-3 text-center text-red-400">خصم</th>
                            <th class="px-4 py-3 text-center text-[#04f5ff]">إجمالي</th>
                            <th class="px-4 py-3 text-right">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($performances as $perf)
                            <tr class="group hover:bg-white/5 transition">
                                <td class="px-4 py-3 font-bold text-white">GW{{ $perf->gameweek->gameweek_number }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="{{ $perf->attendance_points > 0 ? 'text-[#00ff85] font-bold' : ($perf->attendance_points < 0 ? 'text-red-500 font-bold' : 'text-gray-600') }}">
                                        {{ $perf->attendance_points }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $perf->interaction_points }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $perf->uniform_points }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $perf->activity_points }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $perf->service_points }}</td>
                                <td class="px-4 py-3 text-center text-red-400 font-bold">{{ $perf->penalty_points }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="bg-[#04f5ff]/10 text-[#04f5ff] px-2 py-1 rounded font-black border border-[#04f5ff]/20">{{ $perf->total_points }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs truncate max-w-[200px]">{{ $perf->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar { height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); rounded: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
</style>
@endpush
@endsection
