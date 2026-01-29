@extends('layouts.admin')

@section('title', 'إدخال نقاط - ' . $scout->full_name)

@section('content')
<div class="max-w-5xl mx-auto animate-fade-in">
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
        
        <!-- Player Header -->
        <div class="p-8 bg-gradient-to-r from-[#12002b] to-[#1a0b2e] border-b border-white/10 flex flex-col md:flex-row items-center gap-6">
            <div class="relative">
                <img src="{{ $scout->photo_url ?? asset('images/default-avatar.png') }}" class="w-24 h-24 rounded-full border-4 border-white/10 object-cover shadow-lg">
                <div class="absolute -bottom-2 -right-2 bg-[#04f5ff] text-[#1a0b2e] text-xs font-black px-2 py-1 rounded-full border border-[#1a0b2e]">
                    {{ $scout->scout_id }}
                </div>
            </div>
            <div class="text-center md:text-right flex-1">
                <h2 class="text-3xl font-black text-white font-['Changa'] mb-2">{{ $scout->full_name }}</h2>
                <div class="flex flex-wrap justify-center md:justify-start gap-3">
                    <span class="bg-white/5 text-gray-300 px-3 py-1 rounded-lg text-sm font-bold border border-white/5">{{ $scout->patrol->patrol_name ?? 'بدون طليعة' }}</span>
                    <span class="bg-[#00ff85]/10 text-[#00ff85] px-3 py-1 rounded-lg text-sm font-bold border border-[#00ff85]/20">{{ $gameweek->name }}</span>
                </div>
            </div>
            
            <!-- Live Total -->
            <div class="bg-[#12002b] rounded-2xl p-4 border border-[#04f5ff]/30 shadow-[0_0_20px_rgba(4,245,255,0.1)] text-center min-w-[150px]">
                <div class="text-gray-400 text-xs font-bold uppercase mb-1">إجمالي النقاط</div>
                <div class="text-5xl font-black text-[#04f5ff]" x-text="total">0</div>
            </div>
        </div>

        <div class="p-8">
            <form action="{{ route('admin.points.store') }}" method="POST" x-data="pointsForm()" x-init="calculateTotal()" class="space-y-8">
                @csrf
                <input type="hidden" name="scout_id" value="{{ $scout->scout_id }}">
                <input type="hidden" name="gameweek_id" value="{{ $gameweek->id }}">

                <!-- Section 1: Attendance -->
                <div class="bg-white/5 rounded-xl p-6 border border-white/5">
                    <h3 class="text-[#00ff85] font-bold text-lg mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        الحضور والانضباط
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 mb-2 uppercase">حالة الحضور</label>
                            <select name="attendance_points" x-model.number="points.attendance" @change="calculateTotal" class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#00ff85] outline-none transition appearance-none font-bold">
                                <option value="2">✅ في الموعد (+2)</option>
                                <option value="1">⏰ متأخر (+1)</option>
                                <option value="-2">❌ غائب (-2)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 mb-2 uppercase">الزي الكشفي</label>
                            <input type="number" name="uniform_points" x-model.number="points.uniform" @input="calculateTotal" min="0" class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#00ff85] outline-none transition font-mono font-bold text-center">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 mb-2 uppercase">الخصومات (قيمة سالبة)</label>
                            <input type="number" name="penalty_points" x-model.number="points.penalty" @input="calculateTotal" max="0" class="w-full bg-red-900/10 border border-red-500/20 rounded-xl px-4 py-3 text-red-400 focus:border-red-500 outline-none transition font-mono font-bold text-center">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Spiritual & Social -->
                <div class="bg-white/5 rounded-xl p-6 border border-white/5">
                    <h3 class="text-purple-400 font-bold text-lg mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        الروحيات والاجتماعيات
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach([
                            'mass' => 'حضور القداس',
                            'confession' => 'الاعتراف',
                            'group_mass' => 'قداس المجموعة',
                            'tribe_mass' => 'قداس القبيلة'
                        ] as $key => $label)
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">{{ $label }}</label>
                            <input type="number" name="{{ $key }}_points" x-model.number="points.{{ $key }}" @input="calculateTotal" min="0" class="w-full bg-[#12002b] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-purple-500 outline-none transition text-center font-bold">
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Section 3: Activities & Performance -->
                <div class="bg-white/5 rounded-xl p-6 border border-white/5">
                    <h3 class="text-yellow-400 font-bold text-lg mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        النشاط والأداء
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                         @foreach([
                            'interaction' => 'التفاعل',
                            'activity' => 'المشاركة',
                            'service' => 'الخدمة',
                            'committee' => 'اللجان',
                            'aswad' => 'أسود',
                            'first_group' => 'مركز أول',
                            'largest_patrol' => 'أكبر طليعة'
                        ] as $key => $label)
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">{{ $label }}</label>
                            <input type="number" name="{{ $key }}_points" x-model.number="points.{{ $key }}" @input="calculateTotal" min="0" class="w-full bg-[#12002b] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-yellow-500 outline-none transition text-center font-bold">
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Notes -->
                <div class="group">
                    <label class="block text-sm font-bold text-gray-400 mb-2 uppercase">ملاحظات إضافية</label>
                    <textarea name="notes" rows="2" class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#04f5ff] outline-none transition placeholder-gray-600" placeholder="أي ملاحظات خاصة بهذا الكشاف...">{{ $performance->notes ?? '' }}</textarea>
                </div>

                <!-- Actions -->
                <div class="flex gap-4 pt-4 border-t border-white/5">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-black py-4 rounded-xl shadow-lg transform hover:-translate-y-1 transition text-lg flex items-center justify-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        حفظ النقاط
                    </button>
                    <a href="{{ route('admin.points.index', ['gameweek_id' => $gameweek->id]) }}" class="px-8 bg-white/5 hover:bg-white/10 text-gray-300 font-bold py-4 rounded-xl text-center transition border border-white/10 hover:text-white">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function pointsForm() {
    return {
        points: {
            attendance: {{ $performance->attendance_points ?? 2 }},
            interaction: {{ $performance->interaction_points ?? 0 }},
            uniform: {{ $performance->uniform_points ?? 0 }},
            activity: {{ $performance->activity_points ?? 0 }},
            service: {{ $performance->service_points ?? 0 }},
            committee: {{ $performance->committee_points ?? 0 }},
            mass: {{ $performance->mass_points ?? 0 }},
            confession: {{ $performance->confession_points ?? 0 }},
            group_mass: {{ $performance->group_mass_points ?? 0 }},
            tribe_mass: {{ $performance->tribe_mass_points ?? 0 }},
            aswad: {{ $performance->aswad_points ?? 0 }},
            first_group: {{ $performance->first_group_points ?? 0 }},
            largest_patrol: {{ $performance->largest_patrol_points ?? 0 }},
            penalty: {{ $performance->penalty_points ?? 0 }}
        },
        total: 0,

        calculateTotal() {
            this.total = this.points.attendance +
                        this.points.interaction +
                        this.points.uniform +
                        this.points.activity +
                        this.points.service +
                        this.points.committee +
                        this.points.mass +
                        this.points.confession +
                        this.points.group_mass +
                        this.points.tribe_mass +
                        this.points.aswad +
                        this.points.first_group +
                        this.points.largest_patrol +
                        this.points.penalty;
        }
    }
}
</script>
@endpush
@endsection
