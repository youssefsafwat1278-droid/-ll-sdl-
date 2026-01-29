<!-- resources/views/home.blade.php -->
@extends('layouts.app')

@section('title', 'الرئيسية - Scout Tanzania')

@section('content')
<div class="space-y-8 animate-fade-in pb-20 sm:pb-0">
    <!-- Hero Section -->
    <div class="relative overflow-hidden rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] group border border-white/10">
        <div class="absolute inset-0 bg-gradient-to-r from-[#1a0b2e] to-transparent z-10"></div>
        <img src="{{ asset('images/hero-banner.jpeg') }}"
             alt="Scout Tanzania"
             class="w-full h-56 md:h-80 object-cover transform group-hover:scale-105 transition-transform duration-700">
        
        <div class="absolute inset-0 p-8 flex flex-col justify-center z-20">
            <div class="inline-flex items-center gap-2 mb-2">
                <span class="w-10 h-1 bg-[#00ff85]"></span>
                <span class="text-[#00ff85] font-bold tracking-widest uppercase text-sm">Official Fantasy Game</span>
            </div>
            <h2 class="text-4xl md:text-6xl font-black text-white mb-4 leading-tight font-['Changa'] drop-shadow-lg">
                سيستم العشيرة <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#00ff85] to-[#04f5ff]">الجديد كلياً</span>
            </h2>
             @if(!$hasTeam)
                <a href="{{ route('team.builder') }}" class="w-fit bg-white text-[#1a0b2e] px-8 py-3 rounded-full font-black hover:bg-[#00ff85] transition-colors shadow-lg flex items-center gap-2">
                    ابدأ اللعب الآن 
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
             @endif
        </div>
    </div>

    <!-- User Dashboard Card -->
    <div class="rounded-3xl p-1 relative overflow-hidden shadow-2xl"
         :class="darkMode ? 'bg-gradient-to-r from-[#31004a] to-[#12002b]' : 'bg-gradient-to-r from-purple-600 to-indigo-600'">
        <div class="absolute top-0 right-0 w-64 h-64 bg-[#e90052] rounded-full mix-blend-screen filter blur-[100px] opacity-20"></div>
        <div class="backdrop-blur-xl rounded-[22px] p-6 md:p-8 flex flex-col md:flex-row justify-between items-center gap-6 relative z-10"
             :class="darkMode ? 'bg-[#1a0b2e]/50' : 'bg-white/20'">
            <div class="flex items-center gap-6 w-full md:w-auto">
                <div class="w-20 h-20 rounded-full border-4 border-[#00ff85] overflow-hidden shadow-[0_0_20px_rgba(0,255,133,0.3)]">
                     <img src="{{ auth()->user()->photo_url ?? asset('images/default-avatar.png') }}" class="w-full h-full object-cover">
                </div>
                <div>
                     <h2 class="text-3xl font-bold text-white mb-1">مرحباً {{ $user->first_name }} 👋</h2>
                     <p class="text-lg font-medium" :class="darkMode ? 'text-gray-300' : 'text-purple-100'">{{ $user->team_name }}</p>
                </div>
            </div>

            <div class="flex gap-4 w-full md:w-auto">
                 <div class="rounded-2xl p-4 flex-1 md:w-40 text-center border"
                      :class="darkMode ? 'bg-black/30 border-white/5' : 'bg-black/10 border-white/10'">
                     <div class="text-xs font-bold uppercase mb-1" :class="darkMode ? 'text-gray-400' : 'text-purple-100'">إجمالي النقاط</div>
                     <div class="text-4xl font-black text-white">{{ $displayTotalPoints }}</div>
                 </div>
                 @if($userRanking)
                 <div class="rounded-2xl p-4 flex-1 md:w-40 text-center border"
                      :class="darkMode ? 'bg-black/30 border-white/5' : 'bg-black/10 border-white/10'">
                     <div class="text-xs font-bold uppercase mb-1" :class="darkMode ? 'text-gray-400' : 'text-purple-100'">الترتيب العام</div>
                     <div class="text-4xl font-black text-[#04f5ff]">#{{ $userRanking->overall_rank }}</div>
                 </div>
                 @endif
            </div>
        </div>
    </div>

    @if($currentGameweek)
        <!-- Gameweek Info -->
        <div class="backdrop-blur-md border rounded-3xl p-6 md:p-8 shadow-xl"
             :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold flex items-center gap-2"
                    :class="darkMode ? 'text-white' : 'text-gray-900'">
                    <span class="text-[#00ff85]">●</span>
                    {{ $currentGameweek->name }}
                </h3>
                <div class="bg-[#e90052] text-white px-4 py-1.5 rounded-full text-sm font-bold shadow-[0_0_15px_#e90052] animate-pulse">
                    DeadLine
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                 <!-- Countdown -->
                 <div class="md:col-span-1 rounded-2xl p-6 border flex flex-col justify-center items-center text-center"
                      :class="darkMode ? 'bg-gradient-to-br from-[#12002b] to-black border-white/10' : 'bg-gradient-to-br from-gray-900 to-gray-800 border-gray-700'">
                     <div class="text-gray-400 text-sm mb-2">الوقت المتبقي</div>
                     <div class="text-3xl font-mono font-bold text-[#04f5ff] tracking-widest" x-data="countdown('{{ $currentGameweek->deadline }}')" x-text="timeLeft"></div>
                 </div>

                 <!-- Details -->
                 <div class="md:col-span-2 grid grid-cols-2 gap-4">
                      <div class="rounded-2xl p-5 border"
                           :class="darkMode ? 'bg-white/5 border-white/5' : 'bg-gray-50 border-gray-200'">
                          <div class="text-xs mb-1" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">التاريخ</div>
                          <div class="text-xl font-bold" :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $currentGameweek->date->format('Y-m-d') }}</div>
                      </div>
                      <div class="rounded-2xl p-5 border"
                           :class="darkMode ? 'bg-white/5 border-white/5' : 'bg-gray-50 border-gray-200'">
                          <div class="text-xs mb-1" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">المكان</div>
                          <div class="text-xl font-bold" :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $currentGameweek->location }}</div>
                      </div>
                 </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Scouts -->
        <!-- Top Scouts -->
        <div class="backdrop-blur-md border rounded-3xl p-6 shadow-xl h-[400px] overflow-hidden flex flex-col"
             :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-2"
                :class="darkMode ? 'text-white' : 'text-gray-900'">
                <span>🔥</span> أفضل الكشافين
            </h3>
            <div class="flex-1 overflow-y-auto custom-scrollbar space-y-3 pr-2">
                @foreach($topScouts as $scout)
                    <a href="{{ route('scouts.show', $scout->scout_id) }}" 
                       class="flex items-center gap-4 p-3 rounded-xl border transition group"
                       :class="darkMode ? 'bg-white/5 hover:bg-white/10 border-white/5 hover:border-white/20' : 'bg-gray-50 hover:bg-gray-100 border-gray-200 hover:border-gray-300'">
                        <img src="{{ $scout->photo_url ?? asset('images/default-avatar.png') }}" 
                             class="w-12 h-12 rounded-full border"
                             :class="darkMode ? 'border-white/20' : 'border-gray-200'">
                        <div class="flex-1 min-w-0">
                            <div class="font-bold truncate group-hover:text-[#04f5ff] transition-colors"
                                 :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $scout->full_name }}</div>
                            <div class="text-xs text-gray-400">{{ $scout->patrol->patrol_name ?? '-' }}</div>
                        </div>
                        <div class="text-right">
                             <div class="text-xl font-black text-[#00ff85]">{{ $scout->total_points }}</div>
                             <div class="text-[10px] text-gray-500 font-bold uppercase">Points</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-rows-3 gap-4 h-[400px]">
             <a href="{{ route('transfers') }}" class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-3xl p-6 flex items-center justify-between group hover:shadow-[0_0_30px_rgba(37,99,235,0.4)] transition-all">
                <div>
                    <h4 class="text-2xl font-bold text-white">الانتقالات</h4>
                    <p class="text-blue-100 mt-1">{{ $user->free_transfers }} مجانية متاحة</p>
                </div>
                <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                </div>
             </a>

             <a href="{{ route('rankings') }}" class="bg-gradient-to-r from-purple-600 to-purple-800 rounded-3xl p-6 flex items-center justify-between group hover:shadow-[0_0_30px_rgba(147,51,234,0.4)] transition-all">
                <div>
                    <h4 class="text-2xl font-bold text-white">الترتيب العام</h4>
                    <p class="text-purple-100 mt-1">نافس العشيرة</p>
                </div>
                <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
             </a>

             <div class="backdrop-blur-md border rounded-3xl p-6 flex items-center justify-between"
                  :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200 shadow-sm'">
                  <div>
                      <h4 class="text-lg font-bold" :class="darkMode ? 'text-white' : 'text-gray-900'">إشعارات جديدة</h4>
                      <p class="text-sm mt-1" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">{{ $notifications->count() }} إشعار</p>
                  </div>
                  <div class="flex -space-x-2 space-x-reverse overflow-hidden">
                      @foreach($notifications->take(3) as $note)
                          <div class="inline-block h-10 w-10 rounded-full bg-gray-800 ring-2 ring-[#1a0b2e] flex items-center justify-center text-xs text-white font-bold">
                              !
                          </div>
                      @endforeach
                  </div>
             </div>
        </div>
    </div>

    <!-- News and Notifications -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Latest News -->
         <div class="backdrop-blur-md border rounded-3xl p-6 shadow-xl h-[400px] overflow-hidden flex flex-col"
             :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold flex items-center gap-2"
                    :class="darkMode ? 'text-white' : 'text-gray-900'">
                    <span>📰</span> آخر الأخبار
                </h3>
            </div>
            
            <div class="flex-1 overflow-y-auto custom-scrollbar space-y-4 pr-2">
                @forelse($featuredNews as $news)
                    <div class="rounded-2xl p-4 border transition group"
                         :class="darkMode ? 'bg-white/5 hover:bg-white/10 border-white/5 hover:border-white/20' : 'bg-gray-50 hover:bg-gray-100 border-gray-200 hover:border-gray-300'">
                        @if($news->image_url)
                            <img src="{{ $news->image_url }}" class="w-full h-32 object-cover rounded-xl mb-3">
                        @endif
                        <h4 class="font-bold text-lg mb-2 group-hover:text-[#04f5ff] transition-colors"
                            :class="darkMode ? 'text-white' : 'text-gray-900'">
                            {{ $news->title }}
                        </h4>
                        <p class="text-sm line-clamp-2 mb-3" :class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                            {{ $news->content }}
                        </p>
                        <div class="text-xs text-gray-500">
                            {{ $news->created_at->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-500">
                        لا توجد أخبار حالياً
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Notifications -->
        <div class="backdrop-blur-md border rounded-3xl p-6 shadow-xl h-[400px] overflow-hidden flex flex-col"
             :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold flex items-center gap-2"
                    :class="darkMode ? 'text-white' : 'text-gray-900'">
                    <span>🔔</span> الإشعارات
                </h3>
                @if($notifications->count() > 0 && Route::has('notifications.index'))
                    <a href="{{ route('notifications.index') }}" class="text-xs font-bold text-[#00ff85] hover:underline">عرض الكل</a>
                @endif
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar space-y-3 pr-2">
                @forelse($notifications as $notification)
                    <div class="p-4 rounded-xl border relative overflow-hidden transition {{ !$notification->is_read ? 'border-l-4 border-l-[#00ff85]' : '' }}"
                         :class="darkMode ? 'bg-white/5 border-white/5 hover:bg-white/10' : 'bg-gray-50 border-gray-200 hover:bg-gray-100'">
                        <div class="flex justify-between items-start gap-3">
                            <div>
                                <h5 class="font-bold text-sm mb-1" :class="darkMode ? 'text-white' : 'text-gray-900'">
                                    {{ $notification->title }}
                                </h5>
                                <p class="text-xs mb-2" :class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                                    {{ $notification->message }}
                                </p>
                                <span class="text-[10px] text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                            @if(!$notification->is_read)
                                <span class="w-2 h-2 rounded-full bg-[#00ff85]"></span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-500">
                        لا توجد إشعارات جديدة
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
                this.timeLeft = '00:00:00';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Pad hours, minutes, seconds
            const h = hours < 10 ? '0'+hours : hours;
            const m = minutes < 10 ? '0'+minutes : minutes;
            const s = seconds < 10 ? '0'+seconds : seconds;

            this.timeLeft = `${days}d ${h}:${m}:${s}`;
        }
    }
}
</script>
@endpush
@endsection
