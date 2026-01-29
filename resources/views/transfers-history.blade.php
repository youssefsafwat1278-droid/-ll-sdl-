@extends('layouts.app')

@section('title', 'سجل التبديلات - Scout Tanzania')

@section('content')
<div class="space-y-8 animate-fade-in">
    <div class="flex items-center justify-between">
        <h2 class="text-3xl font-black text-white font-['Changa']">سجل التبديلات 🔄</h2>
    </div>

    @if($transfers->count() > 0)
        <!-- Stats Summary -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl p-4 text-center">
                <div class="text-[10px] text-gray-500 font-bold uppercase mb-1">إجمالي التبديلات</div>
                <div class="text-3xl font-black text-[#04f5ff]">{{ $transfers->total() }}</div>
            </div>
             <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl p-4 text-center">
                <div class="text-[10px] text-gray-500 font-bold uppercase mb-1">نقاط مخصومة</div>
                <div class="text-3xl font-black text-red-500">{{ $transfers->sum('transfer_cost') }}</div>
            </div>
        </div>

        <div class="space-y-4">
            @foreach($transfers as $transfer)
                <div class="bg-[#1a0b2e]/80 backdrop-blur-md border border-white/10 rounded-2xl p-6 relative overflow-hidden group hover:border-white/20 transition">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-[#04f5ff]/5 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2"></div>
                    
                    <div class="flex items-center justify-between mb-6 relative z-10 border-b border-white/5 pb-4">
                        <div class="flex items-center gap-2">
                             <span class="bg-white/5 text-white px-3 py-1 rounded-lg text-xs font-bold border border-white/5">
                                GW {{ $transfer->gameweek->gameweek_number }}
                             </span>
                             <span class="text-gray-400 text-xs font-mono">{{ $transfer->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        @if($transfer->transfer_cost > 0)
                            <span class="bg-red-500/20 text-red-400 px-3 py-1 rounded-lg text-xs font-bold border border-red-500/20">
                                -{{ $transfer->transfer_cost }} pts
                            </span>
                        @else
                            <span class="bg-[#00ff85]/20 text-[#00ff85] px-3 py-1 rounded-lg text-xs font-bold border border-[#00ff85]/20">مجاني</span>
                        @endif
                    </div>

                    <div class="flex flex-col md:flex-row items-center gap-4 relative z-10">
                        <!-- OUT -->
                        <div class="flex-1 w-full bg-red-500/10 border border-red-500/20 rounded-xl p-4 flex items-center gap-4 group-hover:bg-red-500/20 transition">
                            <div class="relative">
                                <img src="{{ $transfer->scoutOut->photo_url ?? asset('images/default-avatar.png') }}" class="w-12 h-12 rounded-full border-2 border-red-500/50 object-cover">
                                <div class="absolute -bottom-1 -right-1 bg-red-500 text-[#1a0b2e] rounded-full p-0.5 border border-[#1a0b2e]">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-red-400 font-bold uppercase mb-0.5">OUT</div>
                                <div class="font-bold text-white">{{ $transfer->scoutOut->full_name }}</div>
                                <div class="text-xs text-gray-400">{{ $transfer->scoutOut->patrol->patrol_name }}</div>
                            </div>
                            <div class="mr-auto font-mono font-bold text-red-400">{{ $transfer->price_out }}</div>
                        </div>

                         <!-- IN -->
                        <div class="flex-1 w-full bg-[#00ff85]/10 border border-[#00ff85]/20 rounded-xl p-4 flex items-center gap-4 group-hover:bg-[#00ff85]/20 transition">
                            <div class="relative">
                                <img src="{{ $transfer->scoutIn->photo_url ?? asset('images/default-avatar.png') }}" class="w-12 h-12 rounded-full border-2 border-[#00ff85]/50 object-cover">
                                <div class="absolute -bottom-1 -right-1 bg-[#00ff85] text-[#1a0b2e] rounded-full p-0.5 border border-[#1a0b2e]">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-[#00ff85] font-bold uppercase mb-0.5">IN</div>
                                <div class="font-bold text-white">{{ $transfer->scoutIn->full_name }}</div>
                                <div class="text-xs text-gray-400">{{ $transfer->scoutIn->patrol->patrol_name }}</div>
                            </div>
                            <div class="mr-auto font-mono font-bold text-[#00ff85]">{{ $transfer->price_in }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $transfers->links() }}
        </div>

    @else
        <div class="flex flex-col items-center justify-center py-20 bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl text-center">
            <div class="bg-white/5 rounded-full p-6 mb-6">
                <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">لم تقم بأي تبديلات بعد</h3>
            <p class="text-gray-400 max-w-md mx-auto">سجل التبديلات الخاص بك فارغ. قم بزيارة صفحة الانتقالات لإجراء تغييرات على تشكيلتك.</p>
            <a href="{{ route('transfers') }}" class="mt-6 bg-[#04f5ff] text-[#1a0b2e] px-8 py-3 rounded-xl font-black hover:bg-[#03d0d8] transition">
                الذهاب للانتقالات
            </a>
        </div>
    @endif
</div>
@endsection
