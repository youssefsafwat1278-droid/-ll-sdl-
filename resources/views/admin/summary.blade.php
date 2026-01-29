@extends('layouts.admin')

@section('title', 'الملخص العام - Admin')

@section('content')
<div class="space-y-6 animate-fade-in">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <h2 class="text-3xl font-black font-['Changa']"
            :class="darkMode ? 'text-white' : 'text-gray-900'">الملخص العام 📊</h2>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Quick Link to Stats -->
            <a href="{{ route('admin.attendance-stats') }}"
               class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-lg shadow-purple-900/50 transition transform hover:scale-105">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                إحصائيات المشاركة
            </a>

            <!-- Gameweek Filter -->
            @if(!$gameweeks->isEmpty())
                <form method="GET" class="flex gap-2">
                <div class="relative">
                    <select name="gameweek_id" onchange="this.form.submit()"
                            class="appearance-none border px-4 py-2 pr-8 rounded-xl outline-none cursor-pointer transition text-sm font-bold"
                            :class="darkMode ? 'bg-[#1a0b2e] text-white border-white/20 focus:border-[#04f5ff] hover:bg-white/5' : 'bg-white text-gray-900 border-gray-300 focus:border-blue-500 hover:bg-gray-50'">
                        <option value="">جميع الجولات</option>
                        @foreach($gameweeks as $gw)
                            <option value="{{ $gw->id }}" {{ $selectedGameweekId == $gw->id ? 'selected' : '' }}>
                                الجولة {{ $gw->gameweek_number }}{{ $gw->name ? ' - ' . $gw->name : '' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-2"
                         :class="darkMode ? 'text-white' : 'text-gray-500'">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                </form>
            @endif
        </div>
    </div>

    <!-- Selected Gameweek Info -->
    @if($currentGameweek && $selectedGameweekId)
        @php $selectedGameweek = $gameweeks->firstWhere('id', $selectedGameweekId); @endphp
        @if($selectedGameweek)
        <div class="bg-blue-900/20 border border-blue-500/30 text-blue-200 px-6 py-4 rounded-xl flex items-center gap-3 backdrop-blur-sm">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            <span class="font-bold">عرض البيانات للجولة: {{ $selectedGameweek->name }}</span>
        </div>
        @endif
    @endif

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="backdrop-blur-md border rounded-2xl p-6 text-center shadow-lg"
             :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
            <div class="text-xs font-bold uppercase mb-2"
                 :class="darkMode ? 'text-gray-400' : 'text-gray-500'">إجمالي الكشافة</div>
            <div class="text-3xl font-black"
                 :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $scouts->count() }}</div>
        </div>

        <div class="backdrop-blur-md border rounded-2xl p-6 text-center shadow-lg"
             :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
            <div class="text-xs font-bold uppercase mb-2"
                 :class="darkMode ? 'text-gray-400' : 'text-gray-500'">لديهم نقاط</div>
            <div class="text-3xl font-black text-[#00ff85]">
                {{ $scouts->filter(fn($s) => $s->performances->isNotEmpty())->count() }}
            </div>
        </div>

        <div class="backdrop-blur-md border rounded-2xl p-6 text-center shadow-lg"
             :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
            <div class="text-xs font-bold uppercase mb-2"
                 :class="darkMode ? 'text-gray-400' : 'text-gray-500'">عدد الطلائع</div>
            <div class="text-3xl font-black text-purple-400">
                {{ $scouts->pluck('patrol_id')->unique()->count() }}
            </div>
        </div>

        <div class="backdrop-blur-md border rounded-2xl p-6 text-center shadow-lg"
             :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
            <div class="text-xs font-bold uppercase mb-2"
                 :class="darkMode ? 'text-gray-400' : 'text-gray-500'">متوسط النقاط</div>
            <div class="text-3xl font-black text-yellow-500">
                @php
                    $scoutsWithPoints = $scouts->filter(fn($s) => $s->performances->isNotEmpty());
                    $avgPoints = $scoutsWithPoints->count() > 0
                        ? round($scoutsWithPoints->avg(fn($s) => $s->performances->first()?->total_points ?? 0), 1)
                        : 0;
                @endphp
                {{ $avgPoints }}
            </div>
        </div>
    </div>

    <!-- Scouts Table -->
    <div class="backdrop-blur-xl border rounded-3xl shadow-2xl overflow-hidden"
         :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full">
                <thead class="text-xs font-bold uppercase border-b"
                       :class="darkMode ? 'bg-black/20 text-gray-400 border-white/10' : 'bg-gray-50 text-gray-500 border-gray-200'">
                    <tr>
                        <th class="px-6 py-4 text-right">الصورة</th>
                        <th class="px-6 py-4 text-right">المعرف</th>
                        <th class="px-6 py-4 text-right">الاسم</th>
                        <th class="px-6 py-4 text-right">الطليعة</th>
                        <th class="px-6 py-4 text-right">السعر</th>
                        <th class="px-6 py-4 text-right">النقاط الكلية</th>
                        @if($selectedGameweekId)
                            <th class="px-6 py-4 text-right text-[#04f5ff]">نقاط الجولة</th>
                        @endif
                        <th class="px-6 py-4 text-right">الحالة</th>
                        <th class="px-6 py-4 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y"
                       :class="darkMode ? 'divide-white/5' : 'divide-gray-100'">
                        @forelse($scouts as $scout)
                            @php $performance = $scout->performances->first(); @endphp
                            <tr class="group transition-colors"
                                :class="darkMode ? 'hover:bg-white/5' : 'hover:bg-gray-50'">
                            <td class="px-6 py-4">
                                <img src="{{ $scout->photo_url ?? asset('images/default-avatar.png') }}"
                                     alt="{{ $scout->full_name }}"
                                     class="w-12 h-12 rounded-full object-cover border-2 border-white/10 group-hover:border-[#04f5ff] transition-colors">
                            </td>

                            <td class="px-6 py-4 font-mono text-sm text-gray-500">
                                {{ $scout->scout_id }}
                            </td>

                            <td class="px-6 py-4">
                                <a href="{{ route('admin.summary.show', $scout->scout_id) }}"
                                   class="font-bold transition block"
                                   :class="darkMode ? 'text-white hover:text-[#04f5ff]' : 'text-gray-900 hover:text-blue-600'">
                                    {{ $scout->full_name }}
                                </a>
                                <div class="mt-1">
                                    @if($scout->role === 'leader')
                                        <span class="text-[10px] bg-purple-500/20 text-purple-300 border border-purple-500/30 px-2 py-0.5 rounded font-bold">قائد</span>
                                    @elseif($scout->role === 'senior')
                                        <span class="text-[10px] bg-blue-500/20 text-blue-300 border border-blue-500/30 px-2 py-0.5 rounded font-bold">رائد</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                @if($scout->patrol)
                                    <div class="flex items-center gap-2">
                                        @if($scout->patrol->patrol_logo_url)
                                            <img src="{{ $scout->patrol->patrol_logo_url }}" class="w-6 h-6 object-contain">
                                        @endif
                                        <div class="text-sm font-medium"
                                             :class="darkMode ? 'text-gray-300' : 'text-gray-600'">{{ $scout->patrol->patrol_name }}</div>
                                    </div>
                                @else
                                    <span class="text-gray-500 text-sm">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-bold text-[#04f5ff]">{{ $scout->current_price }}</div>
                                @if($scout->price_change != 0)
                                    <div class="text-xs font-bold {{ $scout->price_change > 0 ? 'text-[#00ff85]' : 'text-red-500' }}">
                                        {{ $scout->price_change > 0 ? '↑' : '↓' }} {{ abs($scout->price_change) }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <span class="font-black text-lg text-[#00ff85]">{{ $scout->total_points ?? 0 }}</span>
                            </td>

                            @if($selectedGameweekId)
                                <td class="px-6 py-4"
                                    :class="darkMode ? 'bg-[#04f5ff]/5' : 'bg-blue-50'">
                                    @if($performance)
                                        <span class="font-black text-lg"
                                              :class="darkMode ? 'text-white' : 'text-blue-900'">
                                            {{ $performance->total_points ?? 0 }}
                                        </span>
                                        @if($performance->notes)
                                            <div class="text-xs text-gray-400 mt-1 truncate max-w-[150px]">{{ $performance->notes }}</div>
                                        @endif
                                    @else
                                        <span class="text-gray-600 text-sm">-</span>
                                    @endif
                                </td>
                            @endif

                            <td class="px-6 py-4">
                                @if(!$scout->is_available)
                                    <span class="inline-flex items-center gap-1.5 bg-red-500/10 text-red-500 border border-red-500/20 px-3 py-1 rounded-full text-xs font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> غير متاح
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-[#00ff85]/10 text-[#00ff85] border border-[#00ff85]/20 px-3 py-1 rounded-full text-xs font-bold">
                                         <span class="w-1.5 h-1.5 rounded-full bg-[#00ff85]"></span> متاح
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-left">
                                <a href="{{ route('admin.summary.show', $scout->scout_id) }}"
                                   class="transition p-2 rounded-lg inline-block"
                                   :class="darkMode ? 'text-gray-400 hover:text-white hover:bg-white/10' : 'text-gray-400 hover:text-gray-900 hover:bg-gray-100'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $selectedGameweekId ? '9' : '8' }}" class="px-4 py-8 text-center text-gray-500">
                                لا توجد بيانات للعرض
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
