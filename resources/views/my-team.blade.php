<!-- resources/views/my-team.blade.php -->
@extends('layouts.app')

@section('title', 'فريقي - Scout Tanzania')

@section('content')
@php
    $user = $user ?? auth()->user();
    $currentGameweek = $currentGameweek ?? \App\Models\Gameweek::where('is_current', true)->first();
    $freeHitActive = $currentGameweek && $user ? \App\Models\ChipUsage::where('user_id', $user->id)
        ->where('gameweek_id', $currentGameweek->id)
        ->where('chip_type', 'free_hit')
        ->exists() : false;
        
    // Chip status defaults
    $hasUsedTripleCapBefore = $hasUsedTripleCapBefore ?? false;
    $tripleCapUsed = $tripleCapUsed ?? false;
    $hasUsedFreeHitBefore = $hasUsedFreeHitBefore ?? false;
@endphp

<div class="space-y-8 animate-fade-in pb-20 sm:pb-0">
    <!-- Messages -->
    @if(session('success'))
        <div class="bg-[#00ff85]/20 border border-[#00ff85]/50 text-[#00ff85] px-6 py-4 rounded-xl backdrop-blur-md shadow-[0_0_20px_rgba(0,255,133,0.1)] flex items-center gap-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500/20 border border-red-500/50 text-red-400 px-6 py-4 rounded-xl backdrop-blur-md shadow-[0_0_20px_rgba(239,68,68,0.1)] flex items-center gap-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif

    @if($freeHitActive)
        <div class="bg-gradient-to-r from-[#00ff85] to-[#04f5ff] rounded-2xl shadow-[0_0_30px_rgba(4,245,255,0.3)] p-[1px]">
            <div class="bg-[#1a0b2e]/90 rounded-2xl p-6 h-full backdrop-blur-xl">
               <div class="flex items-center gap-4">
                    <div class="text-4xl animate-spin-slow">🔄</div>
                    <div>
                        <div class="text-2xl font-black text-white italic uppercase tracking-wider">Free Hit Active!</div>
                        <div class="text-[#04f5ff] font-medium mt-1">
                            فريقك مؤقت لهذه الجولة فقط. سيتم استعادة الفريق السابق تلقائياً.
                        </div>
                    </div>
               </div>
            </div>
        </div>
    @endif

    <!-- Header & Stats -->
    <div class="flex flex-col lg:flex-row gap-6 items-start lg:items-center justify-between">
        <div>
            <h2 class="text-4xl font-black font-['Changa'] drop-shadow-lg tracking-wide"
                :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $user->team_name }}</h2>
            <div class="font-bold text-lg mt-1"
                 :class="darkMode ? 'text-[#04f5ff]' : 'text-blue-600'">Gameweek {{ $currentGameweek->number ?? '-' }}</div>
        </div>
        
        <a href="{{ route('transfers') }}" class="group relative inline-flex items-center justify-center px-8 py-3 font-bold text-white transition-all duration-200 bg-[#e90052] font-['Changa'] rounded-lg hover:bg-[#ff005a] hover:shadow-[0_0_20px_#e90052] focus:outline-none ring-offset-2 focus:ring-2">
            <span class="mr-2">التبديلات</span>
            <span class="bg-black/20 px-2 py-0.5 rounded text-sm group-hover:bg-black/30 transition-colors">{{ $user->free_transfers }} مجانية</span>
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 md:gap-4 mt-4">
        <div class="backdrop-blur-md border rounded-xl p-2 md:p-6 text-center shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 flex flex-row md:flex-col justify-between md:justify-center items-center px-4 md:px-0 min-h-[50px] md:min-h-[60px]"
             :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10 hover:shadow-[0_0_20px_rgba(255,255,255,0.05)]' : 'bg-white border-gray-200'">
            <div class="text-xs md:text-sm font-bold uppercase tracking-widest md:mb-0.5"
                 :class="darkMode ? 'text-gray-400' : 'text-gray-500'">الإجمالي</div>
            <div class="text-xl md:text-5xl font-black"
                 :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $displayTotalPoints ?? 0 }}</div>
        </div>
        <div class="backdrop-blur-md border rounded-xl p-2 md:p-6 text-center shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 flex flex-row md:flex-col justify-between md:justify-center items-center px-4 md:px-0 min-h-[50px] md:min-h-[60px]"
             :class="darkMode ? 'bg-[#1a0b2e]/60 border-[#00ff85]/20 hover:shadow-[0_0_20px_rgba(0,255,133,0.1)]' : 'bg-white border-green-200'">
            <div class="text-xs md:text-sm font-bold uppercase tracking-widest md:mb-0.5"
                 :class="darkMode ? 'text-[#00ff85]' : 'text-green-600'">الجولة</div>
            <div class="text-xl md:text-5xl font-black drop-shadow-lg"
                 :class="darkMode ? 'text-[#00ff85]' : 'text-green-600'">{{ $teamGameweekPoints ?? 0 }}</div>
        </div>
        <div class="backdrop-blur-md border rounded-xl p-2 md:p-6 text-center shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 flex flex-row md:flex-col justify-between md:justify-center items-center px-4 md:px-0 min-h-[50px] md:min-h-[60px]"
             :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10 hover:shadow-[0_0_20px_rgba(255,255,255,0.05)]' : 'bg-white border-gray-200'">
            <div class="text-xs md:text-sm font-bold uppercase tracking-widest md:mb-0.5"
                 :class="darkMode ? 'text-gray-400' : 'text-gray-500'">الرصيد</div>
            <div class="text-xl md:text-4xl font-black"
                 :class="darkMode ? 'text-[#04f5ff]' : 'text-blue-600'">{{ $user->bank_balance }}</div>
        </div>
        <div class="backdrop-blur-md border rounded-xl p-2 md:p-6 text-center shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 flex flex-row md:flex-col justify-between md:justify-center items-center px-4 md:px-0 min-h-[50px] md:min-h-[60px]"
             :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10 hover:shadow-[0_0_20px_rgba(255,255,255,0.05)]' : 'bg-white border-gray-200'">
            <div class="text-xs md:text-sm font-bold uppercase tracking-widest md:mb-0.5"
                 :class="darkMode ? 'text-gray-400' : 'text-gray-500'">المصروف</div>
            <div class="text-xl md:text-4xl font-black text-pink-500">{{ 100 - $user->bank_balance }}</div>
        </div>
    </div>

    @if(isset($team) && $team->count() > 0)
        <!-- Pitch View -->
        <div class="relative w-full aspect-[4/5] sm:aspect-[16/9] md:aspect-[16/10] bg-green-800 rounded-xl overflow-hidden shadow-2xl border-2 border-white/10 mx-auto max-w-5xl mt-6">
            <!-- Pitch Pattern -->
             <div class="absolute inset-0 opacity-40" 
                 style="background: repeating-linear-gradient(0deg, transparent, transparent 10%, rgba(0,0,0,0.1) 10%, rgba(0,0,0,0.1) 20%);">
            </div>
            <!-- Pitch Lines -->
            <div class="absolute inset-2 sm:inset-4 border-2 border-white/30 rounded-lg pointer-events-none"></div>
            <div class="absolute left-1/2 top-0 bottom-0 w-px bg-white/30 hidden sm:block"></div>
            <div class="absolute top-1/2 left-0 right-0 h-px bg-white/30 sm:hidden"></div>
            
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-16 h-16 sm:w-24 sm:h-24 border-2 border-white/30 rounded-full"></div>

            <!-- Players Container -->
            <div class="absolute inset-0 flex flex-col justify-around py-2 sm:py-6">
                 @php
                    $positions = [
                        [0, 1, 2], // Attackers
                        [3, 4, 5, 6], // Midfielders
                        [7, 8, 9, 10] // Defenders
                    ];
                @endphp

                @foreach($positions as $line)
                    <div class="grid w-full px-0.5" style="grid-template-columns: repeat({{ count($line) }}, minmax(0, 1fr));">
                        @foreach($line as $pos)
                            @php $player = $team[$pos] ?? null; @endphp
                            <div class="flex flex-col items-center justify-center p-0.5">
                                @if($player)
                                    <div class="relative group cursor-pointer flex flex-col items-center w-full">
                                        <!-- Image Container -->
                                        <div class="relative mb-0.5 transition-transform transform group-hover:scale-110 duration-200">
                                            <div class="w-8 h-8 sm:w-16 sm:h-16 rounded-full border-[1.5px] sm:border-2 {{ $player->scout->role === 'leader' ? 'border-purple-500 shadow-[0_0_10px_#a855f7]' : ($player->scout->role === 'senior' ? 'border-[#04f5ff] shadow-[0_0_10px_#04f5ff]' : 'border-white shadow-sm') }} overflow-hidden bg-gray-900">
                                                <img src="{{ $player->scout->photo_url ?? asset('images/default-avatar.png') }}" alt="{{ $player->scout->full_name }}" class="w-full h-full object-cover">
                                            </div>
                                            
                                            <!-- Captain Badge -->
                                            @if($player->is_captain)
                                                <div class="absolute -top-1 -right-1 bg-black text-white w-3 h-3 sm:w-6 sm:h-6 rounded-full flex items-center justify-center font-black text-[7px] sm:text-xs border border-white z-10">C</div>
                                            @elseif($player->is_vice_captain)
                                                <div class="absolute -top-1 -right-1 bg-gray-600 text-white w-3 h-3 sm:w-6 sm:h-6 rounded-full flex items-center justify-center font-black text-[7px] sm:text-xs border border-white z-10">V</div>
                                            @endif

                                            <!-- Rank Badge -->
                                             @if($player->scout->role === 'leader')
                                                 <div class="absolute bottom-0 right-1/2 translate-x-1/2 translate-y-1/2 bg-purple-600 text-[5px] sm:text-[10px] px-1 sm:px-1.5 rounded text-white font-bold tracking-tighter border border-purple-400 whitespace-nowrap z-20 scale-[0.8] sm:scale-100">قائد</div>
                                             @endif
                                        </div>

                                        <!-- Name & Info Box -->
                                        <div class="backdrop-blur-sm border rounded-[3px] sm:rounded-md px-1 py-0.5 text-center w-[98%] sm:w-auto max-w-[60px] sm:max-w-[100px] overflow-hidden"
                                             :class="darkMode ? 'bg-[#1a0b2e]/90 border-white/20' : 'bg-white/90 border-white/50'">
                                            <div class="text-[6px] sm:text-xs truncate font-bold leading-none mb-0.5"
                                                 :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $player->scout->first_name }}</div>
                                            <div class="text-[5px] sm:text-[10px] truncate leading-none opacity-80"
                                                 :class="darkMode ? 'text-gray-300' : 'text-gray-600'">{{ $player->scout->patrol->patrol_name }}</div>
                                        </div>

                                        <!-- Points -->
                                        <div class="mt-0.5 bg-white/90 text-gray-900 font-black text-[7px] sm:text-sm px-1.5 sm:px-2 rounded-sm shadow-sm leading-tight">{{ $player->scout->gameweek_points ?? 0 }}</div>
                                        
                                        <!-- Tooltip -->
                                        <div class="hidden md:block absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 bg-[#1a0b2e] text-white text-xs rounded-lg p-3 opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-50 border border-white/20 shadow-xl">
                                            <div class="font-bold text-[#00ff85] text-sm">{{ $player->scout->full_name }}</div>
                                            <div class="text-gray-300">السعر: {{ $player->scout->current_price }}</div>
                                            <div class="text-xs text-gray-400 mt-1 uppercase">{{ $player->scout->role }}</div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Empty Slot Placeholder -->
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-white/10 border-2 border-white/20 border-dashed flex items-center justify-center">
                                        <span class="text-white/30 text-[10px]">+</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Table View -->
        <div class="backdrop-blur-xl border rounded-xl overflow-hidden shadow-xl mt-4 sm:mt-8"
             :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
            <div class="px-4 py-3 border-b flex justify-between items-center"
                 :class="darkMode ? 'border-white/5' : 'border-gray-200'">
                 <h3 class="text-lg font-bold" :class="darkMode ? 'text-white' : 'text-gray-900'">تفاصيل الفريق</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right whitespace-nowrap text-xs">
                    <thead class="uppercase font-bold"
                           :class="darkMode ? 'bg-white/5 text-gray-400' : 'bg-gray-50 text-gray-500'">
                        <tr>
                            <th class="px-4 py-2">اللاعب</th>
                            <!-- Patrol Column Removed as requested -->
                            <th class="px-4 py-2">الدور</th>
                            <th class="px-4 py-2">السعر</th>
                            <th class="px-4 py-2">النقاط</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" :class="darkMode ? 'divide-white/5' : 'divide-gray-100'">
                        @foreach($team as $member)
                            <tr class="transition-colors" :class="darkMode ? 'hover:bg-white/5' : 'hover:bg-gray-50'">
                                <td class="px-4 py-2 font-medium flex items-center gap-2"
                                    :class="darkMode ? 'text-white' : 'text-gray-900'">
                                    <div class="w-6 h-6 rounded-full bg-gray-800 overflow-hidden hidden sm:block">
                                        <img src="{{ $member->scout->photo_url ?? asset('images/default-avatar.png') }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <div>{{ $member->scout->full_name }}</div>
                                        <div class="text-[10px] opacity-60" :class="darkMode ? 'text-gray-300' : 'text-gray-500'">{{ $member->scout->patrol->patrol_name }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-2 text-[10px] sm:text-xs">
                                    @if($member->scout->role === 'leader')
                                        <span class="text-purple-400 bg-purple-400/10 px-1.5 py-0.5 rounded border border-purple-400/20">قائد</span>
                                    @elseif($member->scout->role === 'senior')
                                        <span class="text-[#04f5ff] bg-[#04f5ff]/10 px-1.5 py-0.5 rounded border border-[#04f5ff]/20">رائد</span>
                                    @else
                                        <span class="text-gray-400">كشاف</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 font-bold" :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $member->scout->current_price }}</td>
                                <td class="px-4 py-2 font-black text-sm" :class="darkMode ? 'text-[#00ff85]' : 'text-green-600'">{{ $member->scout->gameweek_points }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Actions Grid (Captain/Chips) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 sm:mt-8">
            <!-- Captaincy -->
            @if(isset($currentGameweek) && !$currentGameweek->isDeadlinePassed())
            <div class="backdrop-blur-xl border rounded-xl p-4 sm:p-6"
                 :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200 shadow-md'">
                 <h3 class="text-base font-bold mb-4 border-b pb-2"
                     :class="darkMode ? 'text-white border-white/10' : 'text-gray-900 border-gray-200'">خيارات القيادة</h3>
                 <form action="{{ route('team.captain') }}" method="POST" class="space-y-3">
                     @csrf
                     @method('PUT')
                     <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                         <div class="space-y-1">
                             <label class="text-xs" :class="darkMode ? 'text-gray-400' : 'text-gray-600'">القائد (x2)</label>
                             <select name="captain_id" 
                                     class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-1 outline-none appearance-none"
                                     :class="darkMode ? 'bg-[#12002b] border-white/20 text-white focus:border-[#00ff85] focus:ring-[#00ff85]' : 'bg-white border-gray-300 text-gray-900 focus:border-blue-500 focus:ring-blue-500'">
                                @foreach($team as $member)
                                    <option value="{{ $member->scout_id }}" {{ $member->is_captain ? 'selected' : '' }}>
                                        {{ $member->scout->full_name }}
                                    </option>
                                @endforeach
                             </select>
                         </div>
                         <div class="space-y-1">
                             <label class="text-xs" :class="darkMode ? 'text-gray-400' : 'text-gray-600'">نائب القائد</label>
                             <select name="vice_captain_id" 
                                     class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-1 outline-none appearance-none"
                                     :class="darkMode ? 'bg-[#12002b] border-white/20 text-white focus:border-[#00ff85] focus:ring-[#00ff85]' : 'bg-white border-gray-300 text-gray-900 focus:border-blue-500 focus:ring-blue-500'">
                                @foreach($team as $member)
                                    <option value="{{ $member->scout_id }}" {{ $member->is_vice_captain ? 'selected' : '' }}>
                                        {{ $member->scout->full_name }}
                                    </option>
                                @endforeach
                             </select>
                         </div>
                     </div>
                     <button type="submit" 
                             class="w-full font-black py-2.5 rounded-lg transition-all shadow-lg text-sm"
                             :class="darkMode ? 'bg-[#00ff85] hover:bg-[#00e676] text-[#1a0b2e]' : 'bg-green-500 hover:bg-green-600 text-white'">
                         حفظ
                     </button>
                 </form>
            </div>
            @endif

            <!-- Chips -->
            <div class="space-y-3">
                 <!-- Triple Captain -->
                 <div class="backdrop-blur-xl border rounded-xl p-4 relative overflow-hidden group transition-all"
                      :class="hasUsedTripleCapBefore || tripleCapUsed 
                          ? (darkMode ? 'bg-gray-800/50 border-gray-700 opacity-60' : 'bg-gray-100 border-gray-300 opacity-60') 
                          : (darkMode ? 'bg-[#1a0b2e]/60 border-yellow-500/30' : 'bg-yellow-50 border-yellow-200')">
                    
                    @if(!$hasUsedTripleCapBefore && !$tripleCapUsed)
                    <div class="absolute inset-0 transition-colors"
                         :class="darkMode ? 'bg-yellow-500/5 group-hover:bg-yellow-500/10' : 'bg-yellow-100/50 group-hover:bg-yellow-100'"></div>
                    @endif
                    
                    <div class="relative flex justify-between items-center">
                        <div>
                            <h4 class="font-black text-sm" :class="darkMode ? 'text-yellow-400' : 'text-yellow-700'">Triple Captain</h4>
                            <p class="text-xs" :class="darkMode ? 'text-gray-400' : 'text-gray-600'">نقاط القائد ×3</p>
                        </div>
                        
                        @if($tripleCapUsed)
                             <span class="px-3 py-1 rounded-lg text-xs font-bold bg-yellow-500/20 text-yellow-500 border border-yellow-500/30">نشط الآن</span>
                        @elseif($hasUsedTripleCapBefore)
                             <span class="px-3 py-1 rounded-lg text-xs font-bold bg-gray-500/20 text-gray-500 border border-gray-500/30">تم استخدامه</span>
                        @else
                            <form action="{{ route('team.triple-captain') }}" method="POST" onsubmit="return confirm('تفعيل Triple Captain؟')">
                                @csrf
                                <button class="font-bold px-3 py-1.5 rounded-lg transition-all shadow-lg text-xs"
                                        :class="darkMode ? 'bg-yellow-500 hover:bg-yellow-400 text-black' : 'bg-yellow-500 hover:bg-yellow-600 text-white'">تفعيل</button>
                            </form>
                        @endif
                    </div>
                </div>

                 <!-- Free Hit -->
                 <div class="backdrop-blur-xl border rounded-xl p-4 relative overflow-hidden group transition-all"
                      :class="hasUsedFreeHitBefore || freeHitActive 
                          ? (darkMode ? 'bg-gray-800/50 border-gray-700 opacity-60' : 'bg-gray-100 border-gray-300 opacity-60') 
                          : (darkMode ? 'bg-[#1a0b2e]/60 border-[#04f5ff]/30' : 'bg-cyan-50 border-cyan-200')">

                    @if(!$hasUsedFreeHitBefore && !$freeHitActive)
                    <div class="absolute inset-0 transition-colors"
                         :class="darkMode ? 'bg-[#04f5ff]/5 group-hover:bg-[#04f5ff]/10' : 'bg-cyan-100/50 group-hover:bg-cyan-100'"></div>
                    @endif

                    <div class="relative flex justify-between items-center">
                        <div>
                            <h4 class="font-black text-sm" :class="darkMode ? 'text-[#04f5ff]' : 'text-cyan-700'">Free Hit</h4>
                            <p class="text-xs" :class="darkMode ? 'text-gray-400' : 'text-gray-600'">تبديلات لا نهائية</p>
                        </div>

                        @if($freeHitActive)
                             <span class="px-3 py-1 rounded-lg text-xs font-bold bg-[#04f5ff]/20 text-[#04f5ff] border border-[#04f5ff]/30">نشط الآن</span>
                        @elseif($hasUsedFreeHitBefore)
                             <span class="px-3 py-1 rounded-lg text-xs font-bold bg-gray-500/20 text-gray-500 border border-gray-500/30">تم استخدامه</span>
                        @else
                            <form action="{{ route('team.free-hit') }}" method="POST" onsubmit="return confirm('تفعيل Free Hit؟')">
                                @csrf
                                <button class="font-bold px-3 py-1.5 rounded-lg transition-all shadow-lg text-xs"
                                        :class="darkMode ? 'bg-[#04f5ff] hover:bg-[#00d0da] text-black' : 'bg-cyan-500 hover:bg-cyan-600 text-white'">تفعيل</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Empty State -->
        <div class="text-center py-20 bg-[#1a0b2e]/50 backdrop-blur-md rounded-3xl border border-white/10">
            <div class="inline-block p-6 rounded-full bg-white/5 mb-6">
                <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            </div>
            <h3 class="text-3xl font-bold text-white mb-2">قم ببناء فريقك</h3>
            <p class="text-gray-400 mb-8 max-w-md mx-auto">اختر 11 كشافاً لتبدأ المنافسة. لديك ميزانية 100.</p>
            <a href="{{ route('team.builder') }}" class="bg-[#00ff85] text-[#1a0b2e] px-10 py-4 rounded-xl font-black text-lg hover:shadow-[0_0_30px_#00ff85] transition-all">
                ابدأ الآن 🚀
            </a>
        </div>
    @endif
</div>

@if(isset($currentGameweek) && !$currentGameweek->isDeadlinePassed())
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const captainSelect = document.querySelector('select[name="captain_id"]');
        const viceCaptainSelect = document.querySelector('select[name="vice_captain_id"]');

        if (captainSelect && viceCaptainSelect) {
            function updateSelections() {
                const captainValue = captainSelect.value;
                const viceValue = viceCaptainSelect.value;

                Array.from(viceCaptainSelect.options).forEach(option => {
                    option.disabled = (option.value === captainValue);
                });

                Array.from(captainSelect.options).forEach(option => {
                    option.disabled = (option.value === viceValue);
                });

                if (captainValue === viceValue) viceCaptainSelect.value = '';
            }
            captainSelect.addEventListener('change', updateSelections);
            viceCaptainSelect.addEventListener('change', updateSelections);
            updateSelections();
        }
    });
    </script>
    @endpush
@endif
@endsection
