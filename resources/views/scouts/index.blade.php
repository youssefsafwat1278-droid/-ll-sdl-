<!-- resources/views/scouts/index.blade.php -->
@extends('layouts.app')

@section('title', 'الكشافة - Scout Tanzania')

@section('content')
<div class="space-y-6 animate-fade-in pb-20 sm:pb-0">
    <div class="flex items-center justify-between">
        <h2 class="text-3xl font-black text-white font-['Changa']">الكشافة 🔭</h2>
    </div>

    <!-- Filters Panel -->
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl p-4 sm:p-6 shadow-xl relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-purple-900/10 to-transparent pointer-events-none"></div>
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 relative z-10">
            <div class="space-y-1">
                <label class="text-xs text-gray-400 font-bold uppercase">تصفية حسب الطليعة</label>
                <select name="patrol" onchange="this.form.submit()" class="w-full bg-[#12002b] border border-white/20 text-white rounded-xl px-4 py-2.5 focus:border-[#04f5ff] focus:ring-1 focus:ring-[#04f5ff] outline-none appearance-none cursor-pointer hover:bg-white/5 transition">
                    <option value="">كل الطلائع</option>
                    @foreach($patrols as $patrol)
                        <option value="{{ $patrol->patrol_id }}" {{ request('patrol') == $patrol->patrol_id ? 'selected' : '' }}>
                            {{ $patrol->patrol_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-xs text-gray-400 font-bold uppercase">الدو</label>
                <select name="role" onchange="this.form.submit()" class="w-full bg-[#12002b] border border-white/20 text-white rounded-xl px-4 py-2.5 focus:border-[#04f5ff] focus:ring-1 focus:ring-[#04f5ff] outline-none appearance-none cursor-pointer hover:bg-white/5 transition">
                    <option value="">كل الأدوار</option>
                    <option value="scout" {{ request('role') == 'scout' ? 'selected' : '' }}>كشاف</option>
                    <option value="leader" {{ request('role') == 'leader' ? 'selected' : '' }}>قائد</option>
                    <option value="senior" {{ request('role') == 'senior' ? 'selected' : '' }}>رائد</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-xs text-gray-400 font-bold uppercase">الحالة</label>
                <select name="availability" onchange="this.form.submit()" class="w-full bg-[#12002b] border border-white/20 text-white rounded-xl px-4 py-2.5 focus:border-[#04f5ff] focus:ring-1 focus:ring-[#04f5ff] outline-none appearance-none cursor-pointer hover:bg-white/5 transition">
                    <option value="">الكل</option>
                    <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>متاح</option>
                    <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>غير متاح</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-xs text-gray-400 font-bold uppercase">ترتيب حسب</label>
                <select name="sort" onchange="this.form.submit()" class="w-full bg-[#12002b] border border-white/20 text-white rounded-xl px-4 py-2.5 focus:border-[#04f5ff] focus:ring-1 focus:ring-[#04f5ff] outline-none appearance-none cursor-pointer hover:bg-white/5 transition">
                    <option value="points_desc" {{ request('sort') == 'points_desc' ? 'selected' : '' }}>الأعلى نقاطاً</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>الأرخص سعراً</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>الأغلى سعراً</option>
                    <option value="form_desc" {{ request('sort') == 'form_desc' ? 'selected' : '' }}>الأفضل شكلاً</option>
                </select>
            </div>

            <div class="flex items-end">
                <a href="{{ route('scouts.index') }}" class="w-full bg-white/10 hover:bg-white/20 text-white font-bold py-2.5 rounded-xl text-center transition border border-white/5">
                    ↺ إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    <!-- Scouts Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
        @foreach($scouts as $scout)
            <a href="{{ route('scouts.show', $scout->scout_id) }}" class="group bg-[#1a0b2e]/60 backdrop-blur-md rounded-2xl p-4 sm:p-5 border border-white/5 hover:border-[#04f5ff]/50 hover:bg-[#1a0b2e]/80 transition-all duration-300 hover:-translate-y-1 shadow-lg hover:shadow-[0_0_30px_rgba(4,245,255,0.15)] relative overflow-hidden">
                
                <!-- Glow Effect -->
                <div class="absolute top-0 right-0 w-20 h-20 bg-white/5 rounded-full blur-2xl -mr-10 -mt-10 group-hover:bg-[#04f5ff]/20 transition-colors"></div>

                <div class="text-center relative z-10">
                    <div class="relative w-24 h-24 mx-auto mb-4">
                        <img src="{{ $scout->photo_url ?? asset('images/default-avatar.png') }}"
                             alt="{{ $scout->full_name }}"
                             class="w-full h-full rounded-full object-cover border-4 border-[#12002b] shadow-xl group-hover:border-[#04f5ff] transition-colors">
                        @if($scout->role === 'leader')
                             <div class="absolute -bottom-2 -right-2 bg-purple-600 text-white text-[10px] sm:text-xs font-black px-2 py-0.5 rounded-md border border-[#1a0b2e] shadow-lg">القائد</div>
                        @elseif($scout->role === 'senior')
                             <div class="absolute -bottom-2 -right-2 bg-blue-500 text-white text-[10px] sm:text-xs font-black px-2 py-0.5 rounded-md border border-[#1a0b2e] shadow-lg">رائد</div>
                        @endif
                    </div>

                    <h3 class="font-bold text-white mb-1 truncate group-hover:text-[#04f5ff] transition-colors">{{ $scout->full_name }}</h3>
                    <p class="text-xs text-gray-400 mb-4 truncate">{{ $scout->patrol->patrol_name ?? 'غير محدد' }}</p>

                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div class="bg-[#12002b]/50 rounded-lg p-2 border border-white/5">
                            <div class="text-[10px] text-gray-500 uppercase font-bold">السعر</div>
                            <div class="text-base font-black text-[#04f5ff]">{{ $scout->current_price }}</div>
                        </div>
                        <div class="bg-[#12002b]/50 rounded-lg p-2 border border-white/5">
                            <div class="text-[10px] text-gray-500 uppercase font-bold">النقاط</div>
                            <div class="text-base font-black text-[#00ff85]">{{ $scout->total_points }}</div>
                        </div>
                    </div>

                    <!-- Ownership / Availability -->
                    @if($scout->isLeaderOrSenior())
                         <div class="w-full bg-[#12002b] rounded-full h-1.5 mb-1 overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-500 to-indigo-500 h-full" style="width: {{ ($scout->ownership_count / 20) * 100 }}%"></div>
                        </div>
                        <div class="flex justify-between text-[10px] text-gray-500 font-medium">
                            <span>الملكية</span>
                            <span>{{ $scout->ownership_count }}/20</span>
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <div class="w-full bg-[#12002b] rounded-full h-1.5 mb-1 overflow-hidden">
                                    <div class="bg-blue-500 h-full" style="width: {{ ($scout->local_ownership_count / 7) * 100 }}%"></div>
                                </div>
                                <div class="text-[9px] text-gray-500 flex justify-between"><span>محلي</span><span>{{ $scout->local_ownership_count }}/7</span></div>
                            </div>
                            <div>
                                <div class="w-full bg-[#12002b] rounded-full h-1.5 mb-1 overflow-hidden">
                                    <div class="bg-green-500 h-full" style="width: {{ ($scout->external_ownership_count / 5) * 100 }}%"></div>
                                </div>
                                <div class="text-[9px] text-gray-500 flex justify-between"><span>خارجي</span><span>{{ $scout->external_ownership_count }}/5</span></div>
                            </div>
                        </div>
                    @endif

                    @if(!$scout->is_available)
                        <div class="absolute inset-x-0 bottom-0 bg-red-500/90 text-white text-[10px] font-bold py-1 backdrop-blur-sm">
                            غير متاح حالياً
                        </div>
                    @endif
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8 flex justify-center">
        {{ $scouts->links() }}
    </div>
</div>
@endsection
