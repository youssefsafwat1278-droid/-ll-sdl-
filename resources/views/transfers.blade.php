<!-- resources/views/transfers.blade.php -->
@extends('layouts.app')

@section('title', 'الانتقالات - Scout Tanzania')

@section('content')
@php
    $freeHitActive = $freeHitActive ?? false;
@endphp

<div x-data="transferManager()" x-init="init()" class="relative">
    
    <!-- Fixed Bottom Action Bar for Mobile -->
    <div class="fixed bottom-0 left-0 right-0 p-4 border-t z-50 lg:hidden shadow-[0_-5px_20px_rgba(0,0,0,0.5)]" 
         :class="darkMode ? 'bg-[#1a0b2e]/95 backdrop-blur-xl border-white/10' : 'bg-white/95 backdrop-blur-xl border-gray-200'"
         x-show="transfers.length > 0" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         x-cloak>
          <div class="flex justify-between items-center mb-3 text-xs" :class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                <span>التكلفة: <span :class="transferCost > 0 ? 'text-red-500 pl-1' : (darkMode ? 'text-white pl-1' : 'text-gray-900 pl-1')" x-text="transferCost"></span></span>
                <span>الرصيد: <span :class="newBalance >= 0 ? (darkMode ? 'text-[#00ff85]' : 'text-green-600') : 'text-red-500'" x-text="newBalance.toFixed(1)"></span></span>
          </div>
          <div class="flex gap-3">
              <button @click="showMobileDraft = true"
                      class="flex-1 font-bold py-3 rounded-lg border transition-all flex justify-center items-center gap-2"
                      :class="darkMode ? 'border-white/20 text-white hover:bg-white/5' : 'border-gray-300 text-gray-700 hover:bg-gray-50'">
                  <span>👁️ مراجعة</span>
              </button>
              <button @click="confirmTransfers"
                    :disabled="!canConfirm || loading"
                    :class="canConfirm && !loading ? (darkMode ? 'bg-[#00ff85] hover:bg-[#00e676] text-[#1a0b2e]' : 'bg-green-500 hover:bg-green-600 text-white') : 'bg-gray-600 text-gray-400 cursor-not-allowed'"
                    class="flex-[2] font-black py-3 rounded-lg shadow-lg transition-all flex justify-center items-center gap-2">
                <span x-show="!loading">تأكيد (<span x-text="transfers.length"></span>)</span>
                <span x-show="loading" class="animate-spin text-xl">⏳</span>
            </button>
          </div>
    </div>
    
    <!-- Animated Content Wrapper -->
    <div class="space-y-6 pb-24 sm:pb-6 animate-fade-in">
        @if(!$hasGameweek)
            <div class="bg-red-500/10 border border-red-500/30 text-red-500 px-6 py-4 rounded-xl backdrop-blur-md">
                {{ $message ?? 'لا يوجد أسبوع نشط حالياً' }}
            </div>
        @else

            <!-- Header & Deadline -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-[0_0_30px_rgba(37,99,235,0.3)] p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
                    <div>
                        <h2 class="text-3xl font-black font-['Changa'] mb-1">الانتقالات 🔄</h2>
                        <div class="text-blue-100 font-medium">{{ $currentGameweek->name }}</div>
                    </div>
                    <div class="w-full sm:w-auto">
                        @if(!$deadlinePassed)
                            <div class="bg-black/20 backdrop-blur-sm rounded-lg px-4 py-2 border border-white/10 text-center sm:text-left">
                                <div class="text-xs text-blue-200 mb-1">الموعد النهائي</div>
                                 <div class="text-2xl font-mono font-bold tracking-wider text-[#04f5ff]" x-data="countdown('{{ $currentGameweek->deadline }}')" x-init="init()" x-text="timeLeft"></div>
                            </div>
                        @else
                            <div class="bg-red-500/80 backdrop-blur-sm px-4 py-2 rounded-lg font-bold border border-red-400/50">
                                🔒 السوق مغلق
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($deadlinePassed)
                <div class="bg-red-500/10 border border-red-500/30 text-red-500 px-6 py-4 rounded-xl text-center font-bold">
                    لا يمكنك إجراء الانتقالات الآن. انتظر فتح الجولة القادمة.
                </div>
            @else

                @if($freeHitActive)
                    <div class="bg-gradient-to-r from-[#00ff85] to-[#04f5ff] p-[1px] rounded-2xl">
                        <div class="bg-[#1a0b2e]/90 backdrop-blur-xl rounded-2xl p-6 flex items-center gap-4">
                            <div class="text-4xl animate-spin-slow">💎</div>
                            <div>
                               <div class="text-xl font-black text-white uppercase">Free Hit Enabled</div>
                               <div class="text-gray-300 text-sm">تبديلات غير محدودة. لن يتم خصم نقاط.</div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Stats Dashboard -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="backdrop-blur-md border rounded-xl p-4 flex flex-col items-center justify-center gap-1 shadow-lg"
                         :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
                        <span class="text-xs font-bold uppercase" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">التبديلات المجانية</span>
                        <span class="text-3xl font-black" :class="darkMode ? 'text-[#00ff85]' : 'text-green-600'">{{ $user->free_transfers }}</span>
                    </div>
                    <div class="backdrop-blur-md border rounded-xl p-4 flex flex-col items-center justify-center gap-1 shadow-lg"
                         :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
                        <span class="text-xs font-bold uppercase" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">المطلوبة</span>
                        <span class="text-3xl font-black" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="transfers.length"></span>
                    </div>
                    <div class="backdrop-blur-md border rounded-xl p-4 flex flex-col items-center justify-center gap-1 shadow-lg"
                         :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
                        <span class="text-xs font-bold uppercase" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">التكلفة (نقاط)</span>
                        <span class="text-3xl font-black" :class="transferCost > 0 ? 'text-red-500' : (darkMode ? 'text-gray-300' : 'text-gray-400')" x-text="transferCost"></span>
                    </div>
                    <div class="backdrop-blur-md border rounded-xl p-4 flex flex-col items-center justify-center gap-1 shadow-lg"
                         :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
                        <span class="text-xs font-bold uppercase" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">الميزانية المتبقية</span>
                        <span class="text-3xl font-black" :class="newBalance >= 0 ? (darkMode ? 'text-[#04f5ff]' : 'text-blue-600') : 'text-red-500'" x-text="newBalance.toFixed(1)"></span>
                    </div>
                </div>

                <div x-show="!freeHitActive && transferCost > 0" x-cloak
                     class="bg-red-500/10 border border-red-500/30 text-red-400 px-6 py-4 rounded-xl text-sm font-bold">
                    تم تجاوز عدد التبديلات المجانية. سيتم خصم
                    <span class="font-black" x-text="transferCost"></span>
                    نقطة من نقاط الجولة.
                </div>

                <!-- Desktop Draft List -->
                <div x-show="transfers.length > 0" x-cloak 
                     class="hidden lg:block backdrop-blur-xl border rounded-2xl p-6 shadow-2xl"
                     :class="darkMode ? 'bg-[#1a0b2e]/80 border-white/10' : 'bg-white border-gray-200'">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-2" :class="darkMode ? 'text-white' : 'text-gray-900'">
                        <span>📝</span><span>مسودة الانتقالات</span>
                    </h3>
                    <div class="space-y-3">
                        <template x-for="(transfer, index) in transfers" :key="index">
                            <div class="flex items-center gap-4 rounded-xl p-3 border relative group transition"
                                 :class="darkMode ? 'bg-white/5 border-white/5 hover:bg-white/10' : 'bg-gray-50 border-gray-200 hover:bg-gray-100'">
                                <div class="flex-1 flex items-center gap-3 min-w-0 opacity-60">
                                     <img :src="getScout(transfer.out)?.photo_url || defaultAvatar" class="w-10 h-10 rounded-full grayscale">
                                     <div class="truncate">
                                         <div class="text-red-400 text-xs font-bold uppercase mb-0.5">Out</div>
                                         <div class="text-sm font-bold truncate" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="getScout(transfer.out)?.full_name"></div>
                                         <div class="text-xs text-gray-500" x-text="getScout(transfer.out)?.current_price"></div>
                                     </div>
                                </div>
                                <div class="text-gray-500">➜</div>
                                <div class="flex-1 flex items-center gap-3 min-w-0">
                                     <img :src="getScout(transfer.in)?.photo_url || defaultAvatar" class="w-10 h-10 rounded-full border border-[#00ff85]">
                                     <div class="truncate">
                                         <div class="text-[#00ff85] text-xs font-bold uppercase mb-0.5">In</div>
                                         <div class="text-sm font-bold truncate" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="getScout(transfer.in)?.full_name"></div>
                                         <div class="text-xs" :class="darkMode ? 'text-[#04f5ff]' : 'text-blue-600'" x-text="getScout(transfer.in)?.current_price"></div>
                                     </div>
                                </div>
                                <button @click="removeTransfer(index)" class="absolute top-2 left-2 text-red-500 hover:text-red-400 bg-red-500/10 hover:bg-red-500/20 rounded p-1 transition">✕</button>
                            </div>
                        </template>
                    </div>
                    <button @click="confirmTransfers" :disabled="!canConfirm || loading"
                            class="hidden lg:block w-full mt-6 py-3 rounded-lg font-black text-lg shadow-lg transition-all transform hover:scale-[1.01]"
                            :class="canConfirm && !loading ? (darkMode ? 'bg-[#00ff85] hover:bg-[#00e676] text-[#1a0b2e]' : 'bg-green-500 hover:bg-green-600 text-white') : 'bg-gray-600 cursor-not-allowed text-gray-400'">
                        <span x-show="!loading">تأكيد الانتقالات</span>
                        <span x-show="loading">جاري المعالجة...</span>
                    </button>
                </div>

                <!-- Interface Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Current Team -->
                    <div class="backdrop-blur-md border rounded-2xl p-4 sm:p-6 h-fit"
                         :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/5' : 'bg-white border-gray-200 shadow-md'">
                        <h3 class="text-xl font-bold mb-6 border-b pb-4" :class="darkMode ? 'text-white border-white/10' : 'text-gray-900 border-gray-200'">فريقك الحالي</h3>
                        <div class="grid grid-cols-1 gap-3">
                            @foreach($currentTeam as $member)
                                <div class="relative group rounded-xl border transition-all duration-200 p-3 flex items-center gap-4 cursor-pointer"
                                     :class="isBeingTransferred('{{ $member->scout_id }}') 
                                        ? (darkMode ? 'bg-red-500/10 border-red-500/50 opacity-50' : 'bg-red-50 border-red-200 opacity-60') 
                                        : (darkMode ? 'bg-white/5 border-white/5 hover:bg-white/10 hover:border-white/20' : 'bg-gray-50 border-gray-200 hover:bg-gray-100 hover:border-gray-300')"
                                     @click="!isBeingTransferred('{{ $member->scout_id }}') && selectScoutOut('{{ $member->scout_id }}')">
                                    <div class="w-14 h-14 rounded-full overflow-hidden border-2 flex-shrink-0" :class="darkMode ? 'bg-gray-800 border-white/10' : 'bg-gray-200 border-gray-100'">
                                         <img src="{{ $member->scout->photo_url ?? asset('images/default-avatar.png') }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <div class="font-bold truncate" :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $member->scout->full_name }}</div>
                                            <div class="font-mono font-bold" :class="darkMode ? 'text-[#04f5ff]' : 'text-blue-600'">{{ $member->scout->current_price }}</div>
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] px-2 py-0.5 rounded" :class="darkMode ? 'bg-white/10 text-gray-300' : 'bg-gray-200 text-gray-600'">{{ $member->scout->patrol->patrol_name ?? '-' }}</span>
                                            <span class="text-[10px] uppercase" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">{{ $member->scout->role }}</span>
                                        </div>
                                    </div>
                                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors"
                                          :class="scoutOutSelected === '{{ $member->scout_id }}' ? (darkMode ? 'bg-[#00ff85] border-[#00ff85]' : 'bg-green-500 border-green-500') : (darkMode ? 'border-white/20 group-hover:border-[#00ff85]' : 'border-gray-300 group-hover:border-green-500')">
                                         <span x-show="scoutOutSelected === '{{ $member->scout_id }}'" class="font-bold text-xs" :class="darkMode ? 'text-[#1a0b2e]' : 'text-white'">✓</span>
                                         <span x-show="isBeingTransferred('{{ $member->scout_id }}')" class="text-red-500 font-bold text-xs">✕</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Available Scouts -->
                    <div class="backdrop-blur-xl border flex flex-col shadow-2xl overflow-hidden fixed inset-0 z-[60] rounded-none h-[100dvh] w-full lg:sticky lg:top-20 lg:h-[calc(100vh-6rem)] lg:rounded-2xl lg:z-auto lg:inset-auto lg:w-auto" 
                         :class="darkMode ? 'bg-[#1a0b2e]/90 border-white/10' : 'bg-white border-gray-200'"
                         x-show="scoutOutSelected" x-cloak
                         x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full lg:translate-x-0 lg:opacity-0" x-transition:enter-end="translate-x-0 lg:translate-x-0 lg:opacity-100"
                         @click.away="!mobile && cancelTransfer()">
                         <div class="p-4 border-b flex items-center justify-between" :class="darkMode ? 'bg-[#12002b] border-white/10' : 'bg-gray-50 border-gray-200'">
                             <h3 class="text-lg font-bold flex items-center gap-2" :class="darkMode ? 'text-white' : 'text-gray-900'">
                                 <span>اختيار بديل لـ</span><span :class="darkMode ? 'text-[#04f5ff]' : 'text-blue-600'" x-text="getScout(scoutOutSelected)?.first_name"></span>
                             </h3>
                             <button @click="cancelTransfer" class="p-2 rounded-full" :class="darkMode ? 'text-gray-400 hover:text-white bg-white/10' : 'text-gray-500 hover:text-gray-800 bg-gray-200'">✕</button>
                         </div>
                         <div class="p-4 space-y-3" :class="darkMode ? 'bg-[#1a0b2e]' : 'bg-white'">
                             <div class="relative">
                                 <input type="text" x-model="filters.search" @input="filterScouts" placeholder="بحث باسم اللاعب..." class="w-full border rounded-lg pl-10 pr-4 py-3 transition focus:outline-none focus:border-[#04f5ff] focus:ring-1 focus:ring-[#04f5ff]" :class="darkMode ? 'bg-white/5 border-white/10 text-white' : 'bg-gray-50 border-gray-300 text-gray-900'">
                                 <svg class="w-5 h-5 absolute left-3 top-3.5" :class="darkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                             </div>
                             <div class="flex gap-2 overflow-x-auto pb-2 no-scrollbar">
                                 <select x-model="filters.patrol" @change="filterScouts" class="border rounded-lg px-3 py-2 text-sm whitespace-nowrap outline-none focus:border-[#04f5ff]" :class="darkMode ? 'bg-white/5 border-white/10 text-white' : 'bg-white border-gray-300 text-gray-900'">
                                     <option value="">كل الطلائع</option>
                                     @foreach($patrols as $patrol)<option value="{{ $patrol->patrol_id }}">{{ $patrol->patrol_name }}</option>@endforeach
                                 </select>
                                 <select x-model="filters.role" @change="filterScouts" class="border rounded-lg px-3 py-2 text-sm whitespace-nowrap outline-none focus:border-[#04f5ff]" :class="darkMode ? 'bg-white/5 border-white/10 text-white' : 'bg-white border-gray-300 text-gray-900'">
                                     <option value="">كل الأدوار</option><option value="scout">كشاف</option><option value="leader">قائد</option><option value="senior">رائد</option>
                                 </select>
                                 <select x-model="filters.sort" @change="sortScouts" class="border rounded-lg px-3 py-2 text-sm whitespace-nowrap outline-none focus:border-[#04f5ff]" :class="darkMode ? 'bg-white/5 border-white/10 text-white' : 'bg-white border-gray-300 text-gray-900'">
                                     <option value="points">الأعلى نقاطاً</option><option value="price_desc">الأغلى سعراً</option><option value="price_asc">الأرخص سعراً</option>
                                 </select>
                             </div>
                         </div>
                         <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar" :class="darkMode ? 'bg-[#12002b]/50' : 'bg-gray-50'">
                             <template x-for="scout in filteredScouts" :key="scout.scout_id">
                                 <div @click="scout.can_pick && selectScoutIn(scout.scout_id)" 
                                      class="border rounded-xl p-3 transition flex items-center gap-3 group relative overflow-hidden" 
                                      :class="[
                                         darkMode ? 'bg-white/5 border-white/5' : 'bg-white border-gray-200 shadow-sm',
                                         !scout.can_pick ? (darkMode ? 'opacity-60 border-red-500/30 bg-red-900/10 cursor-not-allowed' : 'opacity-60 border-red-200 bg-red-50 cursor-not-allowed') : (darkMode ? 'hover:bg-white/10 cursor-pointer' : 'hover:bg-gray-50 hover:border-blue-300 cursor-pointer')
                                      ]">
                                      <div class="absolute inset-0 bg-gradient-to-r transition-all duration-300" :class="darkMode ? 'from-[#00ff85]/0 to-[#00ff85]/0 group-hover:from-[#00ff85]/10 group-hover:to-transparent' : 'from-blue-500/0 to-blue-500/0 group-hover:from-blue-50/50 group-hover:to-transparent'"></div>
                                      <img :src="scout.photo_url || defaultAvatar" class="w-12 h-12 rounded-full border object-cover z-10" :class="[darkMode ? 'border-white/20' : 'border-gray-200', !scout.can_pick ? 'grayscale' : '']">
                                      <div class="flex-1 min-w-0 z-10">
                                          <div class="flex justify-between items-start">
                                              <div><div class="font-bold text-sm" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="scout.full_name"></div><div class="text-xs" :class="darkMode ? 'text-gray-400' : 'text-gray-500'" x-text="scout.patrol?.patrol_name"></div></div>
                                              <div class="font-bold text-base" :class="darkMode ? 'text-[#04f5ff]' : 'text-blue-600'" x-text="scout.current_price"></div>
                                          </div>
                                          <div class="flex items-center gap-4 mt-1.5"><div class="text-xs font-bold" :class="darkMode ? 'text-[#00ff85]' : 'text-green-600'"><span x-text="scout.total_points"></span> نقطة</div><div class="text-[10px] px-1.5 py-0.5 rounded" :class="darkMode ? 'text-gray-500 bg-black/30' : 'text-gray-600 bg-gray-100'" x-text="scout.role === 'leader' ? 'CAP' : (scout.role === 'senior' ? 'SNR' : 'SCT')"></div></div>
                                          
                                           <!-- Limits Display -->
                                            <div class="flex gap-2 mt-1.5 text-[9px] font-mono leading-none">
                                                <template x-if="scout.role === 'leader' || scout.role === 'senior'">
                                                        <div class="px-1.5 py-1 rounded border flex items-center gap-1" 
                                                            :class="[darkMode ? 'bg-black/30 border-white/10' : 'bg-gray-100 border-gray-200', scout.ownership_count >= 20 ? 'text-red-500 border-red-500/30' : (darkMode ? 'text-gray-400' : 'text-gray-500')]">
                                                            <span>Total:</span>
                                                            <span class="font-bold" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="scout.ownership_count"></span><span class="opacity-50">/20</span>
                                                        </div>
                                                </template>
                                                <template x-if="scout.role === 'scout'">
                                                        <div class="flex gap-1.5">
                                                            <!-- Local -->
                                                            <div class="px-1.5 py-1 rounded border flex items-center gap-1"
                                                                :class="[
                                                                    scout.is_local_match ? (darkMode ? 'bg-blue-500/10 border-blue-500/30' : 'bg-blue-50 border-blue-200') : (darkMode ? 'bg-black/30 border-white/10' : 'bg-gray-100 border-gray-200'),
                                                                    scout.local_ownership_count >= 7 ? 'text-red-500 border-red-500/50' : (darkMode ? 'text-gray-400' : 'text-gray-500')
                                                                ]">
                                                                <span :class="scout.is_local_match ? 'text-blue-500 font-bold' : ''">L:</span>
                                                                <span class="font-bold" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="scout.local_ownership_count"></span><span class="opacity-50">/7</span>
                                                            </div>
                                                            <!-- External -->
                                                            <div class="px-1.5 py-1 rounded border flex items-center gap-1"
                                                                :class="[
                                                                    !scout.is_local_match ? (darkMode ? 'bg-purple-500/10 border-purple-500/30' : 'bg-purple-50 border-purple-200') : (darkMode ? 'bg-black/30 border-white/10' : 'bg-gray-100 border-gray-200'),
                                                                    scout.external_ownership_count >= 5 ? 'text-red-500 border-red-500/50' : (darkMode ? 'text-gray-400' : 'text-gray-500')
                                                                ]">
                                                                <span :class="!scout.is_local_match ? 'text-purple-500 font-bold' : ''">E:</span>
                                                                <span class="font-bold" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="scout.external_ownership_count"></span><span class="opacity-50">/5</span>
                                                            </div>
                                                        </div>
                                                </template>
                                            </div>

                                      </div>
                                      <div class="flex flex-col items-end gap-1 z-10">
                                          <!-- Hover Action Icon (Only if pickable) -->
                                          <div x-show="scout.can_pick" class="opacity-0 group-hover:opacity-100 transition-opacity transform group-hover:translate-x-1" :class="darkMode ? 'text-[#00ff85]' : 'text-green-600'">
                                              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                          </div>
                                          
                                          <!-- Locked Badge (Always visible if not pickable) -->
                                          <div x-show="!scout.can_pick">
                                              <span class="text-[10px] font-bold text-red-500 uppercase border border-red-500/30 bg-red-500/10 px-2 py-1 rounded-lg">مغلق</span>
                                          </div>
                                      </div>
                                 </div>
                             </template>
                             <div x-show="filteredScouts.length === 0" class="text-center py-10" :class="darkMode ? 'text-gray-500' : 'text-gray-400'">لا يوجد لاعبين مطابقين للبحث</div>
                         </div>
                    </div>
                    <!-- Desktop Placeholder -->
                    <div class="hidden lg:flex border rounded-2xl p-10 items-center justify-center text-center h-96 border-dashed" :class="darkMode ? 'bg-[#1a0b2e]/30 border-white/5' : 'bg-gray-50 border-gray-300'" x-show="!scoutOutSelected">
                         <div><div class="text-5xl mb-4 opacity-30">👈</div><h3 class="text-xl font-bold" :class="darkMode ? 'text-white/50' : 'text-gray-400'">اختر لاعب من تشكيلتك لاستبداله</h3></div>
                    </div>
                </div>

            @endif
        @endif
    </div>

    <!-- Mobile Transfers Modal (Inside Context) -->
    <div x-show="showMobileDraft" x-cloak 
         class="fixed inset-0 flex items-end sm:items-center justify-center lg:hidden"
         role="dialog" aria-modal="true"
         style="position: fixed !important; z-index: 99999 !important; isolation: isolate;">
         
         <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="showMobileDraft = false" 
              x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
              x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

         <div class="relative w-full max-w-lg rounded-t-3xl sm:rounded-2xl shadow-[0_-10px_40px_rgba(0,0,0,0.5)] overflow-hidden transform transition-all max-h-[85vh] flex flex-col mb-0 sm:mb-0"
              :class="darkMode ? 'bg-[#1a0b2e] border-t border-white/20' : 'bg-white'"
              x-transition:enter="transition ease-out duration-300" 
              x-transition:enter-start="translate-y-full sm:scale-95 sm:opacity-0" 
              x-transition:enter-end="translate-y-0 sm:scale-100 sm:opacity-100"
              x-transition:leave="transition ease-in duration-200" 
              x-transition:leave-start="translate-y-0 sm:scale-100 sm:opacity-100" 
              x-transition:leave-end="translate-y-full sm:scale-95 sm:opacity-0">
             
             <div class="p-5 border-b flex justify-between items-center bg-white/5" :class="darkMode ? 'border-white/10' : 'border-gray-100'">
                 <h3 class="text-xl font-bold font-['Changa']" :class="darkMode ? 'text-white' : 'text-gray-900'">مراجعة الانتقالات</h3>
                 <button @click="showMobileDraft = false" class="p-2 rounded-full hover:bg-white/10 transition-colors">✕</button>
             </div>

             <div class="p-4 overflow-y-auto space-y-3 custom-scrollbar flex-1 bg-black/20">
                 <template x-for="(transfer, index) in transfers" :key="index">
                    <div class="flex items-center gap-3 rounded-xl p-3 border relative shadow-md"
                         :class="darkMode ? 'bg-[#1a0b2e] border-white/10' : 'bg-white border-gray-200'">
                        <div class="flex-1 min-w-0 opacity-70">
                             <div class="text-[10px] text-red-400 font-bold uppercase tracking-wider mb-1">Out</div>
                             <div class="font-bold truncate text-sm" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="getScout(transfer.out)?.full_name"></div>
                        </div>
                        <div class="text-gray-500 font-bold">➜</div>
                        <div class="flex-1 min-w-0">
                             <div class="text-[10px] text-[#00ff85] font-bold uppercase tracking-wider mb-1">In</div>
                             <div class="font-bold truncate text-sm" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="getScout(transfer.in)?.full_name"></div>
                        </div>
                        <button @click="removeTransfer(index); if(transfers.length === 0) showMobileDraft = false;" class="text-red-500 p-2 hover:bg-red-500/10 rounded-lg transition-colors">🗑️</button>
                    </div>
                 </template>
             </div>
             
             <div class="p-5 border-t bg-white/5" :class="darkMode ? 'border-white/10' : 'border-gray-100'">
                 <div class="flex justify-between items-center mb-4 text-sm font-bold">
                     <span :class="darkMode ? 'text-gray-400' : 'text-gray-600'">التكلفة النهائية</span>
                     <span class="text-xl" :class="transferCost > 0 ? 'text-red-500' : (darkMode ? 'text-white' : 'text-gray-900')" x-text="transferCost"></span>
                 </div>
                 <button @click="confirmTransfers" :disabled="!canConfirm || loading"
                         class="w-full font-black py-4 rounded-xl shadow-[0_0_20px_rgba(0,255,133,0.2)] transition-all transform active:scale-95 text-lg"
                         :class="canConfirm && !loading ? (darkMode ? 'bg-[#00ff85] text-[#1a0b2e] hover:bg-[#00e676]' : 'bg-green-500 text-white hover:bg-green-600') : 'bg-gray-600 text-gray-400 cursor-not-allowed'">
                     <span x-show="!loading">تأكيد نهائي</span>
                     <span x-show="loading">جاري التنفيذ...</span>
                 </button>
             </div>
         </div>
    </div>
</div>

@push('scripts')
<script>
function transferManager() {
    return {
        currentTeam: @json($currentTeamScouts ?? []),
        availableScouts: @json($availableScouts ?? []),
        defaultAvatar: '{{ asset('images/default-avatar.png') }}',
        userScoutId: '{{ $user->scout_id }}',
        freeTransfers: {{ $user->free_transfers }},
        bankBalance: {{ $user->bank_balance }},
        freeHitActive: {{ $freeHitActive ? 'true' : 'false' }},
        transfers: [],
        scoutOutSelected: null,
        showMobileDraft: false,
        filters: { patrol: '', role: '', search: '', sort: 'points' },
        filteredScouts: [],
        loading: false,

        mobile: window.innerWidth < 1024,

        init() {
            this.filterScouts();
            window.addEventListener('resize', () => {
                this.mobile = window.innerWidth < 1024;
            });
            // Expose instance globally for modal access
            window.transferManagerInstance = this;
        },

        get transferCost() {
            if (this.freeHitActive) return 0;
            const count = this.transfers.length;
            return count > this.freeTransfers ? (count - this.freeTransfers) * 4 : 0;
        },

        get moneyIn() {
            return this.transfers.reduce((sum, t) => {
                const scout = this.currentTeam.find(s => s.scout_id === t.out);
                return sum + (scout ? parseFloat(scout.current_price) : 0);
            }, 0);
        },

        get moneyOut() {
            return this.transfers.reduce((sum, t) => {
                const scout = this.availableScouts.find(s => s.scout_id === t.in);
                return sum + (scout ? parseFloat(scout.current_price) : 0);
            }, 0);
        },

        get newBalance() {
            return this.bankBalance + this.moneyIn - this.moneyOut;
        },

        get canConfirm() {
            return this.transfers.length > 0 && this.newBalance >= 0;
        },

        getScout(id) {
            return this.currentTeam.find(s => s.scout_id === id) || 
                   this.availableScouts.find(s => s.scout_id === id);
        },

        isBeingTransferred(id) {
            return this.transfers.some(t => t.out === id);
        },

        selectScoutOut(id) {
            if (this.isBeingTransferred(id)) {
                this.transfers = this.transfers.filter(t => t.out !== id);
                this.scoutOutSelected = null;
            } else {
                this.scoutOutSelected = id;
                this.filterScouts();
            }
        },

        selectScoutIn(id) {
            if (!this.scoutOutSelected) return;

            this.transfers.push({
                out: this.scoutOutSelected,
                in: id
            });

            this.scoutOutSelected = null;
        },

        cancelTransfer() {
            this.scoutOutSelected = null;
        },

        removeTransfer(index) {
            this.transfers.splice(index, 1);
        },

        filterScouts() {
            this.filteredScouts = this.availableScouts.filter(scout => {
                if (scout.scout_id === this.userScoutId) return false;
                // if (!scout.can_pick) return false; // Allowed to show locked players now
                if (this.transfers.some(t => t.in === scout.scout_id)) return false;
                if (this.currentTeam.some(s => s.scout_id === scout.scout_id)) return false;

                if (this.filters.patrol && scout.patrol_id != this.filters.patrol) return false;
                if (this.filters.role && scout.role !== this.filters.role) return false;
                if (this.filters.search) {
                    const search = this.filters.search.toLowerCase();
                    const name = (scout.first_name + ' ' + (scout.last_name || '')).toLowerCase();
                    if (!name.includes(search)) return false;
                }
                return true;
            });
            this.sortScouts();
        },

        sortScouts() {
             this.filteredScouts.sort((a, b) => {
                if (this.filters.sort === 'points') return b.total_points - a.total_points;
                if (this.filters.sort === 'price_desc') return b.current_price - a.current_price;
                if (this.filters.sort === 'price_asc') return a.current_price - b.current_price;
                return 0;
            });
        },

        async confirmTransfers() {
            if (!this.canConfirm || this.loading) return;
            this.loading = true;
            try {
                const response = await fetch('{{ route("transfers.make") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        transfers: this.transfers.map(t => ({
                            scout_out: t.out,
                            scout_in: t.in
                        }))
                    })
                });
                const data = await response.json();
                if (data.success) {
                   const splash = document.createElement('div');
                   splash.className = 'fixed inset-0 z-[100] bg-black/80 flex items-center justify-center backdrop-blur-md';
                   splash.innerHTML = `<div class="bg-[#1a0b2e] border border-[#00ff85] p-10 rounded-2xl text-center transform scale-100 animate-bounce"><div class="text-6xl mb-4">✅</div><h2 class="text-3xl font-bold text-white mb-2">تمت التبديلات بنجاح!</h2></div>`;
                   document.body.appendChild(splash);
                   setTimeout(() => window.location.reload(), 2000);
                } else {
                    alert(data.error || 'حدث خطأ');
                }
            } catch (error) {
                alert('حدث خطأ غير متوقع');
            } finally {
                this.loading = false;
            }
        }
    }
}

function countdown(deadline) {
    return {
        timeLeft: '',
        init() {
            this.updateTime();
            setInterval(() => this.updateTime(), 1000);
        },
        updateTime() {
            const now = new Date().getTime();
            const target = new Date(deadline).getTime();
            const distance = target - now;
            if (distance < 0) {
                this.timeLeft = '00:00:00:00';
                return;
            }
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            this.timeLeft = `${days}d ${hours}h ${minutes}m ${seconds}s`;
        }
    }
}
</script>
@endpush
@endsection
