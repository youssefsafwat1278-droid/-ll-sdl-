<!-- resources/views/team-builder.blade.php -->
@extends('layouts.app')

@section('title', 'بناء الفريق - Scout Tanzania')

@section('content')
@php
    $user = $user ?? auth()->user();
    $userPatrolId = $userPatrolId ?? ($user->patrol_id ?? ($user->scout ? $user->scout->patrol_id : null));
    $scoutsPayload = $scoutsPayload ?? [];
    $patrols = $patrols ?? [];
    $currentGameweek = $currentGameweek ?? null;
@endphp

<div x-data="teamBuilder()" x-init="init()" class="space-y-6 animate-fade-in relative min-h-screen">
    
    <!-- Top Bar: Status & Budget -->
    <div class="sticky top-20 z-30 bg-[#1a0b2e]/90 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl p-4 mb-6 transition-all duration-300">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <!-- Team Status -->
            <div class="flex items-center gap-4">
                <div class="text-center">
                    <div class="text-[10px] text-gray-400 font-bold uppercase">المختارون</div>
                    <div class="text-xl font-black text-white">
                        <span x-text="selectedScouts.length"></span>/11
                    </div>
                </div>
                 <!-- Progress Bar (Visual) -->
                <div class="hidden md:block w-32 h-2 bg-gray-700 rounded-full overflow-hidden">
                    <div class="bg-[#04f5ff] h-full transition-all duration-500" :style="`width: ${(selectedScouts.length / 11) * 100}%`"></div>
                </div>
                
                <div class="h-8 w-px bg-white/10"></div>

                <div class="text-center">
                    <div class="text-[10px] text-gray-400 font-bold uppercase">التكلفة</div>
                    <div class="text-2xl font-black transition-colors duration-300" :class="totalCost <= 100 ? 'text-[#04f5ff]' : 'text-red-500'">
                        <span x-text="totalCost.toFixed(1)"></span><span class="text-xs text-gray-500">M</span>
                    </div>
                </div>
                
                <div class="hidden md:block h-8 w-px bg-white/10"></div>
                 
                <!-- Rules Badges -->
                <div class="hidden md:flex gap-2 text-[10px] font-bold uppercase">
                    <div class="px-2 py-1 rounded border transition-colors" :class="ownPatrolCount === 4 ? 'bg-green-500/20 border-green-500/50 text-green-400' : 'bg-white/5 border-white/10 text-gray-400'">
                        طليعتي: <span x-text="`${ownPatrolCount}/4`"></span>
                    </div>
                    <div class="px-2 py-1 rounded border transition-colors" :class="otherPatrolsCount === 4 ? 'bg-green-500/20 border-green-500/50 text-green-400' : 'bg-white/5 border-white/10 text-gray-400'">
                        آخرين: <span x-text="`${otherPatrolsCount}/4`"></span>
                    </div>
                    <div class="px-2 py-1 rounded border transition-colors" :class="leadersCount === 3 ? 'bg-green-500/20 border-green-500/50 text-green-400' : 'bg-white/5 border-white/10 text-gray-400'">
                        قادة: <span x-text="`${leadersCount}/3`"></span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 w-full md:w-auto">
                {{-- Captain Select Modal Trigger (Conditional) --}}
                <div x-show="selectedScouts.length === 11" x-cloak class="flex gap-2">
                     <button @click="resetTeam" class="px-4 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-xl font-bold text-sm border border-red-500/20 transition">
                        إعادة تعيين
                    </button>
                    <button @click="showCaptainModal = true" class="px-6 py-2 bg-gradient-to-r from-[#04f5ff] to-[#00ff85] text-[#1a0b2e] rounded-xl font-black text-sm shadow-[0_0_15px_rgba(4,245,255,0.3)] hover:shadow-[0_0_25px_rgba(4,245,255,0.5)] transform hover:-translate-y-0.5 transition flex items-center gap-2">
                        <span>إنهاء الفريق</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
                <div x-show="selectedScouts.length < 11" class="text-sm font-bold text-gray-400 flex items-center gap-2">
                    <span class="animate-pulse text-[#04f5ff]">●</span> أكمل اختيار اللاعبين
                </div>
            </div>
        </div>
        
        <!-- Mobile Rules (only visible on small screens) -->
        <div class="md:hidden grid grid-cols-3 gap-2 mt-4 text-[10px] font-bold text-center">
             <div class="p-1 rounded bg-white/5 text-gray-400" :class="{'text-green-400 bg-green-500/10': ownPatrolCount === 4}">
                طليعتي: <span x-text="`${ownPatrolCount}/4`"></span>
            </div>
            <div class="p-1 rounded bg-white/5 text-gray-400" :class="{'text-green-400 bg-green-500/10': otherPatrolsCount === 4}">
                آخرين: <span x-text="`${otherPatrolsCount}/4`"></span>
            </div>
            <div class="p-1 rounded bg-white/5 text-gray-400" :class="{'text-green-400 bg-green-500/10': leadersCount === 3}">
                قادة: <span x-text="`${leadersCount}/3`"></span>
            </div>
        </div>

        <!-- Error Message -->
         <div x-show="validationErrors.length > 0" x-cloak class="mt-4 bg-red-500/10 border border-red-500/20 text-red-200 px-4 py-2 rounded-lg text-sm font-bold animate-pulse">
            <template x-for="error in validationErrors">
                <div x-text="error"></div>
            </template>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 relative">
        
        <!-- Left: Pitch View (Selected Team) -->
        <div class="lg:col-span-7 xl:col-span-8 order-2 lg:order-1">
            <div class="bg-[#1a0b2e] border border-white/10 rounded-3xl p-4 shadow-2xl relative overflow-hidden min-h-[600px] flex flex-col items-center justify-center">
                <!-- Pitch Background -->
                 <div class="absolute inset-0 bg-green-900/20">
                     <div class="absolute inset-0 opacity-20" 
                          style="background-image: repeating-linear-gradient(0deg, transparent, transparent 49px, rgba(255,255,255,0.1) 50px); background-size: 100% 50px;">
                     </div>
                     <!-- Center Circle -->
                     <div class="absolute top-1/2 left-1/2 w-32 h-32 border-2 border-white/10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                     <div class="absolute top-1/2 left-0 right-0 h-0.5 bg-white/10 -translate-y-1/2"></div>
                     <!-- Pattern Overlay -->
                     <div class="absolute inset-0 bg-[url('/images/pattern.png')] opacity-5"></div>
                 </div>

                <!-- Empty State -->
                <div x-show="selectedScouts.length === 0" class="relative z-10 text-center pointer-events-none">
                     <div class="bg-white/5 p-6 rounded-full inline-block mb-4 backdrop-blur-sm">
                        <svg class="w-16 h-16 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                     </div>
                     <h3 class="text-xl font-bold text-white mb-2">ابدأ بناء فريقك</h3>
                     <p class="text-gray-400">اختر 11 لاعباً من القائمة الجانبية</p>
                </div>

                <!-- Selected Players Grid (Pitch Layout) -->
                <div x-show="selectedScouts.length > 0" class="relative z-10 w-full h-full grid grid-cols-3 md:grid-cols-4 gap-4 p-4 content-start">
                     <template x-for="(scoutId, index) in selectedScouts" :key="scoutId">
                        <div class="relative group animate-fade-in-up" :style="`animation-delay: ${index * 50}ms`">
                            <!-- Player Card -->
                            <div class="bg-gradient-to-b from-[#1a0b2e]/90 to-[#2d1b4e]/90 backdrop-blur-md border border-[#04f5ff]/30 rounded-xl overflow-hidden shadow-lg hover:shadow-[#04f5ff]/20 hover:border-[#04f5ff] transition transform hover:-translate-y-1 group-hover:z-20">
                                
                                <!-- Remove Button -->
                                <button @click="removeScout(scoutId)" class="absolute top-1 right-1 z-20 text-red-500 hover:text-red-400 bg-black/50 hover:bg-black/80 rounded-full p-1 transition opacity-0 group-hover:opacity-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>

                                <div class="relative pt-2">
                                     <!-- Patrol Logo BG -->
                                    <template x-if="getScout(scoutId)?.patrol?.patrol_logo_url">
                                        <img :src="getScout(scoutId).patrol.patrol_logo_url" class="absolute top-0 right-0 w-12 h-12 opacity-10 pointer-events-none">
                                    </template>
                                    
                                    <img :src="getScout(scoutId)?.photo_url || defaultAvatar" class="w-16 h-16 rounded-full mx-auto border-2 border-white/10 group-hover:border-[#04f5ff] transition object-cover bg-[#1a0b2e]">
                                </div>
                                <div class="text-center p-2 bg-black/40 mt-1">
                                    <div class="text-[10px] text-[#04f5ff] font-bold truncate px-1" x-text="getScout(scoutId)?.full_name"></div>
                                    <div class="flex justify-between items-center px-2 mt-1">
                                        <div class="text-[10px] text-gray-400" x-text="getScout(scoutId)?.patrol?.patrol_name"></div>
                                        <div class="text-xs font-black text-white" x-text="getScout(scoutId)?.current_price"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                     </template>
                </div>
            </div>
        </div>

        <!-- Right: Player Pool (Filter & List) -->
        <div class="lg:col-span-5 xl:col-span-4 order-1 lg:order-2 space-y-4">
            <!-- Filter Bar -->
            <div class="bg-[#1a0b2e] border border-white/10 rounded-2xl p-4 sticky top-20 z-10">
                <div class="relative mb-3">
                    <input type="text" x-model="filters.search" @input="filterScouts" placeholder="بحث باسم اللاعب..." 
                           class="w-full bg-[#12002b] border border-white/10 rounded-xl pl-10 pr-4 py-3 text-white focus:border-[#04f5ff] outline-none transition placeholder-gray-600 text-sm">
                    <svg class="w-5 h-5 text-gray-500 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                
                <div class="grid grid-cols-2 gap-2">
                    <select x-model="filters.patrol" @change="filterScouts" class="bg-[#12002b] border border-white/10 rounded-lg px-3 py-2 text-white text-xs focus:border-[#04f5ff] outline-none">
                        <option value="">كل الطلائع</option>
                         @foreach($patrols as $patrol)
                            <option value="{{ $patrol->patrol_id }}">{{ $patrol->patrol_name }}</option>
                        @endforeach
                    </select>
                    <select x-model="filters.sort" @change="filterScouts" class="bg-[#12002b] border border-white/10 rounded-lg px-3 py-2 text-white text-xs focus:border-[#04f5ff] outline-none">
                        <option value="points">الأعلى نقاط</option>
                        <option value="price_desc">الأغلى سعراً</option>
                        <option value="price_asc">الأرخص سعراً</option>
                    </select>
                </div>
                 <div class="mt-2 flex gap-2 overflow-x-auto pb-1 custom-scrollbar">
                     <button @click="filters.role = ''; filterScouts()" :class="filters.role === '' ? 'bg-[#04f5ff] text-[#1a0b2e]' : 'bg-white/5 text-gray-400 hover:bg-white/10'" class="px-3 py-1 rounded-full text-[10px] font-bold whitespace-nowrap transition">الكل</button>
                     <button @click="filters.role = 'scout'; filterScouts()" :class="filters.role === 'scout' ? 'bg-[#04f5ff] text-[#1a0b2e]' : 'bg-white/5 text-gray-400 hover:bg-white/10'" class="px-3 py-1 rounded-full text-[10px] font-bold whitespace-nowrap transition">كشاف</button>
                     <button @click="filters.role = 'leader'; filterScouts()" :class="filters.role === 'leader' ? 'bg-[#04f5ff] text-[#1a0b2e]' : 'bg-white/5 text-gray-400 hover:bg-white/10'" class="px-3 py-1 rounded-full text-[10px] font-bold whitespace-nowrap transition">قائد</button>
                     <button @click="filters.role = 'senior'; filterScouts()" :class="filters.role === 'senior' ? 'bg-[#04f5ff] text-[#1a0b2e]' : 'bg-white/5 text-gray-400 hover:bg-white/10'" class="px-3 py-1 rounded-full text-[10px] font-bold whitespace-nowrap transition">رائد</button>
                </div>
            </div>

            <!-- List -->
            <div class="h-[600px] overflow-y-auto custom-scrollbar pr-2 space-y-2">
                <template x-for="scout in filteredScouts" :key="scout.scout_id">
                    <div @click="addScout(scout.scout_id)"
                         :class="[
                             isSelected(scout.scout_id) ? 'border-[#00ff85] bg-[#00ff85]/5 shadow-[0_0_15px_rgba(0,255,133,0.1)]' : 'border-white/5 bg-[#1a0b2e]/60',
                             !scout.can_pick ? 'opacity-80 border-red-500/30 bg-red-900/10 cursor-not-allowed' : 'hover:border-[#04f5ff]/50 hover:bg-white/5 cursor-pointer'
                         ]"
                         class="flex items-center gap-3 p-3 rounded-xl border transition relative group overflow-hidden">
                        
                         <!-- Avatar -->
                        <div class="relative">
                            <img :src="scout.photo_url || defaultAvatar" class="w-12 h-12 rounded-full object-cover border border-white/10" :class="!scout.can_pick ? 'grayscale' : ''">
                            <!-- Role Badge -->
                            <div x-show="scout.role !== 'scout'" 
                                 :class="scout.role === 'leader' ? 'bg-purple-500' : 'bg-blue-500'"
                                 class="absolute -top-1 -right-1 text-[8px] font-bold text-white px-1.5 py-0.5 rounded-full border border-[#1a0b2e] uppercase"
                                 x-text="scout.role === 'leader' ? 'C' : 'S'">
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <div class="text-sm font-bold text-white truncate" x-text="scout.full_name"></div>
                                <div x-show="isSelected(scout.scout_id)" class="text-[#00ff85]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 text-[10px] text-gray-400">
                                <span x-text="scout.patrol?.patrol_name || '-'"></span>
                                <span class="w-1 h-1 rounded-full bg-gray-600"></span>
                                <span class="" x-text="scout.total_points + ' pts'"></span>
                            </div>
                            
                            <!-- Limits Display -->
                            <div class="flex gap-2 mt-1.5 text-[9px] font-mono leading-none">
                                <template x-if="scout.role === 'leader' || scout.role === 'senior'">
                                     <div class="px-1.5 py-1 rounded bg-black/30 border border-white/10 flex items-center gap-1" 
                                          :class="scout.ownership_count >= 20 ? 'text-red-400 border-red-500/30' : 'text-gray-400'">
                                         <span>Total:</span>
                                         <span class="font-bold text-white" x-text="scout.ownership_count"></span><span class="opacity-50">/20</span>
                                     </div>
                                </template>
                                <template x-if="scout.role === 'scout'">
                                     <div class="flex gap-1.5">
                                         <!-- Local -->
                                         <div class="px-1.5 py-1 rounded border flex items-center gap-1"
                                              :class="[
                                                  scout.is_local_match ? 'bg-blue-500/10 border-blue-500/30' : 'bg-black/30 border-white/10',
                                                  scout.local_ownership_count >= 3 ? 'text-red-400 border-red-500/50' : 'text-gray-400'
                                              ]">
                                             <span :class="scout.is_local_match ? 'text-blue-400' : ''">Local:</span>
                                             <span class="font-bold text-white" x-text="scout.local_ownership_count"></span><span class="opacity-50">/3</span>
                                         </div>
                                         <!-- External -->
                                          <div class="px-1.5 py-1 rounded border flex items-center gap-1"
                                              :class="[
                                                  !scout.is_local_match ? 'bg-purple-500/10 border-purple-500/30' : 'bg-black/30 border-white/10',
                                                  scout.external_ownership_count >= 5 ? 'text-red-400 border-red-500/50' : 'text-gray-400'
                                              ]">
                                             <span :class="!scout.is_local_match ? 'text-purple-400' : ''">Ext:</span>
                                             <span class="font-bold text-white" x-text="scout.external_ownership_count"></span><span class="opacity-50">/5</span>
                                         </div>
                                     </div>
                                </template>
                            </div>
                        </div>

                        <!-- Price & Action -->
                        <div class="text-right">
                            <div class="text-sm font-black text-[#04f5ff]" x-text="scout.current_price"></div>
                            
                            <!-- Pick Button Visual -->
                            <div class="mt-1 flex justify-end">
                                <template x-if="!isSelected(scout.scout_id)">
                                    <div x-show="scout.can_pick">
                                        <div class="w-6 h-6 rounded-full bg-white/5 flex items-center justify-center text-gray-400 group-hover:bg-[#04f5ff] group-hover:text-[#1a0b2e] transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!scout.can_pick">
                                    <div class="text-[9px] font-bold text-red-500 uppercase border border-red-500/30 bg-red-500/10 px-1.5 py-0.5 rounded">
                                        مغلق
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
                 <div x-show="filteredScouts.length === 0" class="text-center py-12 text-gray-500">
                    لا توجد لاعبين مطابقين
                </div>
            </div>
        </div>
    </div>

    <!-- Finalize Modal -->
    <div x-show="showCaptainModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="showCaptainModal = false"></div>
        <div class="bg-[#1a0b2e] border border-white/10 rounded-2xl w-full max-w-md p-6 relative z-10 shadow-2xl animate-scale-up">
            <h3 class="text-xl font-bold text-white mb-6 text-center">اختيار الكابتن</h3>
            
            <div class="space-y-4">
                <div>
                     <label class="block text-xs font-bold text-gray-400 mb-2 uppercase">الكابتن (x2 نقاط)</label>
                    <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto custom-scrollbar">
                         <template x-for="scoutId in selectedScouts" :key="'cap-'+scoutId">
                             <button @click="captainId = scoutId" 
                                     :class="captainId === scoutId ? 'bg-[#04f5ff] text-[#1a0b2e] border-[#04f5ff]' : 'bg-white/5 text-gray-300 border-white/10 hover:bg-white/10'"
                                     class="flex items-center gap-2 p-2 rounded-lg border text-xs font-bold transition">
                                 <div x-text="getScout(scoutId)?.full_name" class="truncate"></div>
                                 <svg x-show="captainId === scoutId" class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                             </button>
                         </template>
                    </div>
                </div>

                <div>
                     <label class="block text-xs font-bold text-gray-400 mb-2 uppercase">نائب الكابتن</label>
                    <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto custom-scrollbar">
                         <template x-for="scoutId in selectedScouts" :key="'vice-'+scoutId">
                             <button @click="viceCaptainId = scoutId" 
                                     :disabled="scoutId === captainId"
                                     :class="[
                                        viceCaptainId === scoutId ? 'bg-[#00ff85] text-[#1a0b2e] border-[#00ff85]' : 'bg-white/5 text-gray-300 border-white/10 hover:bg-white/10',
                                        scoutId === captainId ? 'opacity-30 cursor-not-allowed' : ''
                                     ]"
                                     class="flex items-center gap-2 p-2 rounded-lg border text-xs font-bold transition">
                                 <div x-text="getScout(scoutId)?.full_name" class="truncate"></div>
                                 <svg x-show="viceCaptainId === scoutId" class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                             </button>
                         </template>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <button @click="showCaptainModal = false" class="flex-1 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 font-bold transition">إلغاء</button>
                <button @click="submitTeam" 
                        :disabled="!canSubmit"
                        :class="canSubmit ? 'bg-[#04f5ff] hover:bg-[#03d0d8] text-[#1a0b2e]' : 'bg-gray-600 text-gray-400 cursor-not-allowed'"
                        class="flex-1 py-3 rounded-xl font-black shadow-lg transition">
                    <span x-show="!loading">تأكيد وانطلاق 🚀</span>
                     <span x-show="loading">جاري الحفظ...</span>
                </button>
            </div>
        </div>
    </div>

</div>

    <!-- Toast Notification -->
    <div x-show="toast.show" x-transition.opacity.duration.300ms
         x-cloak
         class="fixed bottom-6 left-1/2 -translate-x-1/2 md:translate-x-0 md:left-6 z-[60] px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3 font-bold text-sm tracking-wide"
         :class="toast.type === 'error' ? 'bg-red-500 text-white shadow-red-500/20' : 'bg-[#00ff85] text-[#1a0b2e] shadow-[#00ff85]/20'">
        <span x-text="toast.message"></span>
    </div>

@push('scripts')
<script>
function teamBuilder() {
    return {
        scouts: @json($scoutsPayload),
        defaultAvatar: '{{ asset('images/default-avatar.png') }}',
        userScoutId: '{{ $user->scout_id ?? "" }}',
        userPatrolId: {{ $userPatrolId ?? 'null' }},
        selectedScouts: [],
        captainId: '',
        viceCaptainId: '',
        showCaptainModal: false,
        filters: {
            patrol: '',
            role: '',
            sort: 'points',
            search: ''
        },
        filteredScouts: [],
        validationErrors: [],
        loading: false,
        toast: { show: false, message: '', type: 'error' },

        showToast(message, type = 'error') {
            this.toast.message = message;
            this.toast.type = type;
            this.toast.show = true;
            setTimeout(() => this.toast.show = false, 3000);
        },

        init() {
            if (!Array.isArray(this.scouts)) {
                this.scouts = Object.values(this.scouts || {});
            }
            this.filterScouts();
        },

        resetTeam() {
            if(confirm('هل أنت متأكد من إعادة تعيين اختيار الفريق؟')) {
                this.selectedScouts = [];
                this.captainId = '';
                this.viceCaptainId = '';
            }
        },

        get totalCost() {
            return this.selectedScouts.reduce((sum, id) => {
                const scout = this.scouts.find(s => s.scout_id === id);
                return sum + (scout ? parseFloat(scout.current_price) : 0);
            }, 0);
        },

        get budgetRemaining() {
            return 100 - this.totalCost;
        },

        get ownPatrolCount() {
            return this.selectedScouts.filter(id => {
                const scout = this.scouts.find(s => s.scout_id === id);
                // فقط الكشافة العاديين (مش قادة/رواد) من طليعتك
                return scout && scout.role === 'scout' && scout.patrol_id == this.userPatrolId;
            }).length;
        },

        get otherPatrolsCount() {
            return this.selectedScouts.filter(id => {
                const scout = this.scouts.find(s => s.scout_id === id);
                // فقط الكشافة العاديين (مش قادة/رواد) من طلائع أخرى
                return scout && scout.role === 'scout' && scout.patrol_id != this.userPatrolId;
            }).length;
        },

        get leadersCount() {
            return this.selectedScouts.filter(id => {
                const scout = this.scouts.find(s => s.scout_id === id);
                return scout && (scout.role === 'leader' || scout.role === 'senior');
            }).length;
        },

        get canSubmit() {
            return this.selectedScouts.length === 11 &&
                   this.captainId &&
                   this.viceCaptainId &&
                   this.captainId !== this.viceCaptainId &&
                   !this.loading;
        },

        getScout(id) {
            return this.scouts.find(s => s.scout_id === id);
        },

        isSelected(id) {
            return this.selectedScouts.includes(id);
        },

        addScout(id) {
            if (this.isSelected(id)) {
                this.removeScout(id);
                return;
            }

            if (this.selectedScouts.length >= 11) {
                this.showToast('لقد اكتمل عدد اللاعبين (11)', 'error');
                return;
            }

            const scout = this.getScout(id);
            if (scout && !scout.can_pick) {
                this.showToast('هذا اللاعب غير متاح للاختيار', 'error');
                return;
            }

            if (id === this.userScoutId) {
                this.showToast('لا يمكنك اختيار نفسك في الفريق', 'error');
                return;
            }

            this.selectedScouts.push(id);
            this.validateTeam();
        },

        removeScout(id) {
            this.selectedScouts = this.selectedScouts.filter(s => s !== id);
            if (this.captainId === id) this.captainId = '';
            if (this.viceCaptainId === id) this.viceCaptainId = '';
            this.validateTeam();
        },

        validateTeam() {
            this.validationErrors = [];

            if (this.selectedScouts.length > 0) {
                if (this.totalCost > 100) {
                    this.validationErrors.push('تجاوزت الميزانية (100M).');
                }

                if (this.selectedScouts.length === 11) {
                    if (this.ownPatrolCount !== 4) {
                        this.validationErrors.push('يجب اختيار 4 كشافة من طليعتك.');
                    }
                    if (this.otherPatrolsCount !== 4) {
                        this.validationErrors.push('يجب اختيار 4 كشافة من طلائع أخرى.');
                    }
                    if (this.leadersCount !== 3) {
                        this.validationErrors.push('يجب اختيار 3 قادة/رواد.');
                    }
                }
            }
        },

        filterScouts() {
            this.filteredScouts = this.scouts.filter(scout => {
                if (scout.scout_id === this.userScoutId) return false;
                // We show not-pickable players but greyed out? Or hide them? 
                // Previous logic showed them but disabled. Keeping consistent.
                
                if (this.filters.patrol && scout.patrol_id != this.filters.patrol) return false;
                if (this.filters.role && scout.role !== this.filters.role) return false;
                if (this.filters.search) {
                    const search = this.filters.search.toLowerCase();
                    const name = (scout.first_name + ' ' + scout.last_name).toLowerCase();
                    if (!name.includes(search)) return false;
                }
                return true;
            });

            if (this.filters.sort === 'points') {
                this.filteredScouts.sort((a, b) => b.total_points - a.total_points);
            } else if (this.filters.sort === 'price_asc') {
                this.filteredScouts.sort((a, b) => a.current_price - b.current_price);
            } else if (this.filters.sort === 'price_desc') {
                this.filteredScouts.sort((a, b) => b.current_price - a.current_price);
            }
        },

        async submitTeam() {
            this.validateTeam();
            if (this.validationErrors.length > 0) return;
            if (!this.canSubmit) return;

            this.loading = true;

            try {
                const response = await fetch('{{ route("team.select") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        scouts: this.selectedScouts,
                        captain_id: this.captainId,
                        vice_captain_id: this.viceCaptainId
                    })
                });

                const data = await response.json().catch(() => ({}));

                if (response.status === 422 && data.errors) {
                    this.validationErrors = Object.values(data.errors).flat();
                    this.showCaptainModal = false; 
                    return;
                }

                if (!response.ok) {
                    this.validationErrors = [data.error || 'حدث خطأ غير متوقع.'];
                    this.showCaptainModal = false;
                    return;
                }

                if (data.success) {
                    window.location.href = data.redirect;
                    return;
                }
                 this.validationErrors = [data.error || 'حدث خطأ.'];
                 this.showCaptainModal = false;

            } catch (error) {
                this.validationErrors = ['تعذر الاتصال بالخادم.'];
                this.showCaptainModal = false;
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.4); }
</style>
@endpush
@endsection
