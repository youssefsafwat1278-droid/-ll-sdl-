@extends('layouts.admin')

@section('title', 'User Registrations - Admin')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <h2 class="text-3xl font-black text-white font-['Changa']">تسجيلات الكشافة 📝</h2>
        <div class="flex gap-3 bg-[#1a0b2e] p-1.5 rounded-xl border border-white/10">
            <div class="px-4 py-2 rounded-lg bg-white/5 text-center min-w-[100px]">
                <div class="text-[10px] text-gray-500 font-bold uppercase">إجمالي</div>
                <div class="text-xl font-black text-white">{{ $totalCount }}</div>
            </div>
            <div class="px-4 py-2 rounded-lg bg-[#00ff85]/10 text-center min-w-[100px] border border-[#00ff85]/20">
                <div class="text-[10px] text-[#00ff85]/70 font-bold uppercase">مسجل</div>
                <div class="text-xl font-black text-[#00ff85]">{{ $registeredCount }}</div>
            </div>
            <div class="px-4 py-2 rounded-lg bg-red-500/10 text-center min-w-[100px] border border-red-500/20">
                <div class="text-[10px] text-red-500/70 font-bold uppercase">غير مسجل</div>
                <div class="text-xl font-black text-red-500">{{ $totalCount - $registeredCount }}</div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-sm text-left">
                <thead class="bg-black/20 text-gray-400 text-xs font-bold uppercase border-b border-white/10">
                    <tr>
                        <th class="px-6 py-4 text-right">الاسم</th>
                        <th class="px-6 py-4 text-right">Scout ID</th>
                        <th class="px-6 py-4 text-right">الطليعة</th>
                        <th class="px-6 py-4 text-right">الدور</th>
                        <th class="px-6 py-4 text-right">البريد الإلكتروني</th>
                        <th class="px-6 py-4 text-right">الحالة</th>
                        <th class="px-6 py-4 text-right">تاريخ التسجيل</th>
                        <th class="px-6 py-4 text-right">آخر دخول</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($scouts as $scout)
                        <tr class="group hover:bg-white/5 transition border-l-4 border-transparent {{ $scout->user ? 'hover:border-[#00ff85]' : 'hover:border-red-500 bg-red-500/5' }}">
                            <!-- الاسم -->
                            <td class="px-6 py-4 font-bold text-white group-hover:text-[#04f5ff] transition">
                                {{ $scout->full_name }}
                            </td>

                            <!-- Scout ID -->
                            <td class="px-6 py-4 font-mono text-gray-500">
                                {{ $scout->scout_id }}
                            </td>

                            <!-- الطليعة -->
                            <td class="px-6 py-4 text-gray-300">
                                {{ $scout->patrol->patrol_name ?? '-' }}
                            </td>

                            <!-- الدور -->
                            <td class="px-6 py-4">
                                @if($scout->role === 'leader')
                                    <span class="bg-purple-500/20 text-purple-300 border border-purple-500/30 px-2 py-0.5 rounded text-xs font-bold uppercase">Captain</span>
                                @elseif($scout->role === 'senior')
                                    <span class="bg-blue-500/20 text-blue-300 border border-blue-500/30 px-2 py-0.5 rounded text-xs font-bold uppercase">Senior</span>
                                @else
                                    <span class="text-gray-500 text-xs">Scout</span>
                                @endif
                            </td>

                            <!-- البريد -->
                            <td class="px-6 py-4 text-gray-400 font-mono text-xs">
                                {{ $scout->user->email ?? '-' }}
                            </td>

                            <!-- الحالة -->
                            <td class="px-6 py-4">
                                @if($scout->user)
                                    <span class="bg-[#00ff85]/20 text-[#00ff85] border border-[#00ff85]/30 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1 w-fit">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        مسجل
                                    </span>
                                @else
                                    <span class="bg-red-500/20 text-red-500 border border-red-500/30 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1 w-fit">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        غير مسجل
                                    </span>
                                @endif
                            </td>

                            <!-- التاريخ -->
                            <td class="px-6 py-4 text-gray-500 text-xs font-mono">
                                {{ $scout->user?->created_at?->format('Y-m-d') ?? '-' }}
                            </td>

                            <!-- آخر دخول -->
                            <td class="px-6 py-4 text-gray-500 text-xs font-mono">
                                {{ $scout->user?->last_login?->format('Y-m-d H:i') ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                لا يوجد كشافة.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
