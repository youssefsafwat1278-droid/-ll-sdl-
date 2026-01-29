@extends('layouts.admin')

@section('title', 'إحصائيات المشاركة والحضور - Admin')

@section('content')
<div class="space-y-6 animate-fade-in">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <h2 class="text-3xl font-black text-white font-['Changa']">إحصائيات المشاركة 📋</h2>
        <a href="{{ route('admin.summary') }}"
           class="inline-flex items-center gap-2 bg-white/5 hover:bg-white/10 border border-white/10 text-white px-4 py-2 rounded-xl transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            العودة للملخص العام
        </a>
    </div>

    <!-- Info Card -->
    <div class="bg-blue-900/20 border border-blue-500/30 text-blue-200 px-6 py-4 rounded-xl flex items-center gap-3 backdrop-blur-sm">
        <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        <span class="font-bold">ملاحظة: هذه الصفحة تعرض عدد مرات المشاركة في كل نشاط (وليس مجموع النقاط)</span>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-[#1a0b2e]/60 backdrop-blur-md border border-white/10 rounded-2xl p-6 text-center">
            <div class="text-xs text-gray-400 font-bold uppercase mb-2">إجمالي الكشافة</div>
            <div class="text-3xl font-black text-white">{{ $scoutsStats->count() }}</div>
        </div>

        <div class="bg-[#1a0b2e]/60 backdrop-blur-md border border-white/10 rounded-2xl p-6 text-center">
            <div class="text-xs text-gray-400 font-bold uppercase mb-2">أكثر حضور</div>
            @php $mostActive = $scoutsStats->first(); @endphp
            <div class="text-3xl font-black text-[#00ff85]">
                {{ $mostActive['total_gameweeks'] ?? 0 }}
            </div>
            @if($mostActive)
                <div class="text-[10px] text-gray-500 mt-1 font-bold">{{ $mostActive['scout']->full_name }}</div>
            @endif
        </div>

        <div class="bg-[#1a0b2e]/60 backdrop-blur-md border border-white/10 rounded-2xl p-6 text-center">
            <div class="text-xs text-gray-400 font-bold uppercase mb-2">متوسط الحضور</div>
            <div class="text-3xl font-black text-purple-400">
                {{ $scoutsStats->count() > 0 ? round($scoutsStats->avg('total_gameweeks'), 1) : 0 }}
            </div>
            <div class="text-[10px] text-gray-500 mt-1 font-bold">جولة</div>
        </div>

        <div class="bg-[#1a0b2e]/60 backdrop-blur-md border border-white/10 rounded-2xl p-6 text-center">
            <div class="text-xs text-gray-400 font-bold uppercase mb-2">إجمالي الجولات</div>
            <div class="text-3xl font-black text-yellow-500">
                {{ \App\Models\Gameweek::count() }}
            </div>
        </div>
    </div>

    <!-- Statistics Table -->
    <div class="bg-[#1a0b2e]/80 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-sm">
                <thead class="bg-black/40 text-gray-400 text-xs font-bold uppercase sticky top-0 backdrop-blur-md z-10">
                    <tr>
                        <th class="px-3 py-4 text-right sticky right-0 bg-[#1a0b2e] z-20 border-b border-white/10 min-w-[180px]">الكشاف</th>
                        <th class="px-3 py-4 text-right border-b border-white/10 min-w-[120px]">الطليعة</th>
                        <th class="px-3 py-4 text-center border-b border-white/10 text-white bg-white/5">الجولات</th>
                        <th class="px-3 py-4 text-center border-b border-white/10 text-[#00ff85]">حضور</th>
                        <th class="px-3 py-4 text-center border-b border-white/10 text-red-500">غياب</th>
                        <th class="px-3 py-4 text-center border-b border-white/10">تفاعل</th>
                        <th class="px-3 py-4 text-center border-b border-white/10">زي</th>
                        <th class="px-3 py-4 text-center border-b border-white/10">نشاط</th>
                        <th class="px-3 py-4 text-center border-b border-white/10">خدمة</th>
                        <th class="px-3 py-4 text-center border-b border-white/10">لجان</th>
                        <th class="px-3 py-4 text-center border-b border-white/10">قداس</th>
                        <th class="px-3 py-4 text-center border-b border-white/10">اعتراف</th>
                        <th class="px-3 py-4 text-center border-b border-white/10">ق.جماعة</th>
                        <th class="px-3 py-4 text-center border-b border-white/10">ق.قبيلة</th>
                        <th class="px-3 py-4 text-center border-b border-white/10">أسود</th>
                        <th class="px-3 py-4 text-center border-b border-white/10">م.أولى</th>
                        <th class="px-3 py-4 text-center border-b border-white/10">أ.طليعة</th>
                        <th class="px-3 py-4 text-center border-b border-white/10 text-red-400">جزاءات</th>
                        <th class="px-3 py-4 text-center border-b border-white/10 bg-[#04f5ff]/10 text-[#04f5ff]">إجمالي النقاط</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($scoutsStats as $stat)
                        <tr class="group hover:bg-white/5 transition-colors">
                            <!-- Scout Info -->
                            <td class="px-3 py-3 sticky right-0 bg-[#1a0b2e] border-l border-white/5 z-10 group-hover:bg-[#251042] transition-colors">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $stat['scout']->photo_url ?? asset('images/default-avatar.png') }}"
                                         alt="{{ $stat['scout']->full_name }}"
                                         class="w-8 h-8 rounded-full object-cover border border-white/20">
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.summary.show', $stat['scout']->scout_id) }}"
                                           class="font-bold text-white hover:text-[#04f5ff] transition truncate block max-w-[120px]">
                                            {{ $stat['scout']->full_name }}
                                        </a>
                                        <div class="text-[10px] text-gray-500 font-mono">{{ $stat['scout']->scout_id }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Patrol -->
                            <td class="px-3 py-3">
                                @if($stat['scout']->patrol)
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full" style="background-color: {{ $stat['scout']->patrol->patrol_color }}"></div>
                                        <span class="text-xs text-gray-300">{{ $stat['scout']->patrol->patrol_name }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-600 text-xs">-</span>
                                @endif
                            </td>

                            <!-- Stats Columns -->
                            <td class="px-3 py-3 text-center font-bold text-white bg-white/5">{{ $stat['total_gameweeks'] }}</td>
                            
                            <td class="px-3 py-3 text-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md {{ $stat['attendance_count'] > 0 ? 'bg-[#00ff85]/20 text-[#00ff85] font-bold' : 'text-gray-600' }}">
                                    {{ $stat['attendance_count'] }}
                                </span>
                            </td>
                            
                            <td class="px-3 py-3 text-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md {{ $stat['attendance_absent'] > 0 ? 'bg-red-500/20 text-red-500 font-bold' : 'text-gray-600' }}">
                                    {{ $stat['attendance_absent'] }}
                                </span>
                            </td>

                            @foreach(['interaction_count', 'uniform_count', 'activity_count', 'service_count', 'committee_count', 'mass_count', 'confession_count', 'group_mass_count', 'tribe_mass_count', 'aswad_count', 'first_group_count', 'largest_patrol_count'] as $key)
                                <td class="px-3 py-3 text-center">
                                    <span class="{{ $stat[$key] > 0 ? 'font-bold text-white' : 'text-gray-700' }}">{{ $stat[$key] }}</span>
                                </td>
                            @endforeach

                            <td class="px-3 py-3 text-center">
                                <span class="{{ $stat['penalty_count'] > 0 ? 'font-bold text-red-500' : 'text-gray-700' }}">{{ $stat['penalty_count'] }}</span>
                            </td>

                            <td class="px-3 py-3 text-center bg-[#04f5ff]/5">
                                <span class="font-black text-[#04f5ff] text-lg">{{ $stat['total_points'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="19" class="px-4 py-8 text-center text-gray-500">لا توجد بيانات للعرض</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Export Options -->
    <div class="bg-[#1a0b2e]/60 border border-white/10 rounded-xl p-4 flex justify-between items-center">
        <div class="text-sm text-gray-400">إجمالي {{ $scoutsStats->count() }} كشاف</div>
        <button onclick="window.print()" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition font-bold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            طباعة التقرير
        </button>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .no-print, nav, aside, button, a[href], .bg-gradient-to-r { display: none !important; }
        body, .pl-theme { background: white !important; color: black !important; }
        table { font-size: 8px; color: black; }
        th, td { border: 1px solid #ddd !important; color: black !important; }
        .sticky { position: static !important; }
        th { background: #eee !important; }
        .custom-scrollbar { overflow: visible !important; }
    }
</style>
@endpush
@endsection
