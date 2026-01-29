<!-- resources/views/admin/points/index.blade.php -->
@extends('layouts.admin')

@section('title', 'إدخال النقاط - Admin')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <h2 class="text-3xl font-black text-white font-['Changa']">إدخال النقاط 🎯</h2>
        @if(!$gameweeks->isEmpty())
            <form method="GET" class="w-full md:w-auto">
                <div class="relative group">
                    <select name="gameweek_id" onchange="this.form.submit()" class="w-full md:min-w-[300px] appearance-none bg-[#1a0b2e]/60 backdrop-blur-md border border-white/20 rounded-xl px-5 py-3 text-white focus:border-[#04f5ff] focus:ring-1 focus:ring-[#04f5ff] outline-none cursor-pointer transition text-sm font-bold shadow-lg">
                        @foreach($gameweeks as $gw)
                            <option value="{{ $gw->id }}" {{ $gameweekId == $gw->id ? 'selected' : '' }}>
                                الجولة {{ $gw->gameweek_number }}{{ $gw->name ? ' - ' . $gw->name : '' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-4 text-[#04f5ff]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </form>
        @endif
    </div>

    @if($gameweeks->isEmpty())
        <div class="bg-red-500/10 border border-red-500/30 text-red-200 px-6 py-4 rounded-xl backdrop-blur-sm text-center font-bold">
            {{ $message ?? 'لا يوجد جولات متاحة حالياً' }}
        </div>
    @else

    <!-- Selected Gameweek Info -->
    @php $selectedGameweek = $gameweeks->firstWhere('id', $gameweekId); @endphp
    @if($selectedGameweek)
    <div class="bg-blue-900/20 border border-blue-500/30 text-blue-200 px-6 py-4 rounded-xl flex items-center gap-3 backdrop-blur-sm">
        <svg class="w-6 h-6 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        <span class="font-bold text-lg">أدخل النقاط للجولة: <span class="text-[#04f5ff]">{{ $selectedGameweek->name }}</span></span>
    </div>
    @endif

    <!-- Progress -->
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-xl p-6 relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-r from-green-500/5 to-transparent group-hover:from-green-500/10 transition"></div>
        <div class="flex justify-between items-center mb-3 relative z-10">
            <span class="text-lg font-black text-white flex items-center gap-2">
                <span>📊</span> التقدم: <span class="text-[#00ff85]">{{ $enteredCount }}</span> <span class="text-gray-500">/</span> {{ $totalCount }}
            </span>
            <span class="text-xl font-black text-[#04f5ff]">{{ $progress }}%</span>
        </div>
        <div class="w-full bg-black/30 rounded-full h-4 border border-white/5 overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-emerald-400 h-full rounded-full transition-all duration-1000 ease-out shadow-[0_0_10px_#10b981]" style="width: {{ $progress }}%"></div>
        </div>
    </div>

    <!-- Scouts List -->
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-white/10 flex justify-between items-center">
             <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                قائمة الكشافة
             </h3>
             <input type="text" placeholder="بحث سريع..." class="bg-[#12002b] border border-white/10 rounded-lg px-3 py-1.5 text-xs text-white focus:border-[#04f5ff] outline-none">
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-black/20 text-gray-400 text-xs font-bold uppercase border-b border-white/10">
                    <tr>
                        <th class="px-6 py-4 text-right">رقم الكشاف</th>
                        <th class="px-6 py-4 text-right">الاسم</th>
                        <th class="px-6 py-4 text-right">الطليعة</th>
                        <th class="px-6 py-4 text-right">الحالة</th>
                        <th class="px-6 py-4 text-right">النقاط</th>
                        <th class="px-6 py-4 text-right">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($scouts as $scout)
                        <tr class="group hover:bg-white/5 transition border-l-4 border-transparent {{ $scout->has_points ? 'hover:border-[#00ff85]' : 'hover:border-yellow-500' }}">
                            <td class="px-6 py-4 font-mono text-gray-400">{{ $scout->scout_id }}</td>
                            <td class="px-6 py-4 font-bold text-white">{{ $scout->full_name }}</td>
                            <td class="px-6 py-4">
                                @if($scout->patrol)
                                    <span class="bg-white/5 px-2 py-1 rounded text-xs text-gray-300 font-bold border border-white/5">{{ $scout->patrol->patrol_name }}</span>
                                @else
                                    <span class="text-gray-600 font-bold">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($scout->has_points)
                                    <span class="bg-[#00ff85]/20 text-[#00ff85] px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider shadow-[0_0_10px_rgba(0,255,133,0.2)]">Done</span>
                                @else
                                    <span class="bg-yellow-500/20 text-yellow-500 px-3 py-1 rounded-full text-xs font-bold border border-yellow-500/30">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-lg {{ $scout->has_points ? 'text-[#04f5ff]' : 'text-gray-600' }}">
                                    {{ $scout->current_gameweek_points ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.points.show', ['scout' => $scout->scout_id, 'gameweek_id' => $gameweekId]) }}" 
                                   class="inline-block px-4 py-2 rounded-lg text-xs font-bold shadow-lg transform active:scale-95 transition {{ $scout->has_points ? 'bg-blue-600/20 text-blue-300 hover:bg-blue-600 hover:text-white border border-blue-500/30' : 'bg-[#00ff85] text-[#1a0b2e] hover:bg-[#00e676]' }}">
                                    {{ $scout->has_points ? '✏️ تعديل' : '➕ إدخال' }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
