@extends('layouts.admin')

@section('title', 'لوحة التحكم - Admin')

@section('content')
<div class="space-y-6 animate-fade-in">
    
    <div class="flex items-center justify-between">
        <h2 class="text-3xl font-black font-['Changa']"
            :class="darkMode ? 'text-white' : 'text-gray-900'">لوحة التحكم 📊</h2>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Users -->
        <div class="backdrop-blur-md border rounded-2xl p-6 shadow-xl relative overflow-hidden group transition-all"
             :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
            <div class="absolute right-0 top-0 w-24 h-24 bg-purple-600/10 rounded-full blur-xl -mr-10 -mt-10 group-hover:bg-purple-600/20 transition-all"></div>
            <div class="flex justify-between items-start relative z-10">
                <div>
                     <p class="text-sm font-bold uppercase tracking-wider"
                        :class="darkMode ? 'text-gray-400' : 'text-gray-500'">المستخدمين</p>
                     <h3 class="text-3xl font-black mt-1"
                         :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $totalUsers }}</h3>
                </div>
                <div class="bg-purple-500/20 p-3 rounded-xl text-purple-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
            <div class="mt-4 text-xs font-medium"
                 :class="darkMode ? 'text-purple-300' : 'text-purple-600'">نشطين في النظام</div>
        </div>

        <!-- Scouts -->
        <div class="backdrop-blur-md border rounded-2xl p-6 shadow-xl relative overflow-hidden group transition-all"
             :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
            <div class="absolute right-0 top-0 w-24 h-24 bg-green-600/10 rounded-full blur-xl -mr-10 -mt-10 group-hover:bg-green-600/20 transition-all"></div>
            <div class="flex justify-between items-start relative z-10">
                <div>
                     <p class="text-sm font-bold uppercase tracking-wider"
                        :class="darkMode ? 'text-gray-400' : 'text-gray-500'">الكشافة</p>
                     <h3 class="text-3xl font-black mt-1"
                         :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $totalScouts }}</h3>
                </div>
                <div class="bg-green-500/20 p-3 rounded-xl text-green-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
             <div class="mt-4 text-xs font-medium"
                  :class="darkMode ? 'text-green-300' : 'text-green-600'">لاعب متاح</div>
        </div>

        <!-- Teams -->
        <div class="backdrop-blur-md border rounded-2xl p-6 shadow-xl relative overflow-hidden group transition-all"
             :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
            <div class="absolute right-0 top-0 w-24 h-24 bg-[#04f5ff]/10 rounded-full blur-xl -mr-10 -mt-10 group-hover:bg-[#04f5ff]/20 transition-all"></div>
            <div class="flex justify-between items-start relative z-10">
                <div>
                     <p class="text-sm font-bold uppercase tracking-wider"
                        :class="darkMode ? 'text-gray-400' : 'text-gray-500'">فرق مكتملة</p>
                     <h3 class="text-3xl font-black mt-1"
                         :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $usersWithTeams }}</h3>
                </div>
                <div class="bg-[#04f5ff]/20 p-3 rounded-xl text-[#04f5ff]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
            </div>
             <div class="mt-4 text-xs font-medium"
                  :class="darkMode ? 'text-cyan-200' : 'text-cyan-600'">جاهزون للمنافسة</div>
        </div>

        <!-- Points Entry -->
        <div class="backdrop-blur-md border rounded-2xl p-6 shadow-xl relative overflow-hidden group transition-all"
             :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
            <div class="absolute right-0 top-0 w-24 h-24 bg-yellow-500/10 rounded-full blur-xl -mr-10 -mt-10 group-hover:bg-yellow-500/20 transition-all"></div>
            <div class="flex justify-between items-start relative z-10">
                <div>
                     <p class="text-sm font-bold uppercase tracking-wider"
                        :class="darkMode ? 'text-gray-400' : 'text-gray-500'">إدخال النقاط</p>
                     <h3 class="text-3xl font-black mt-1"
                         :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $performancesEntered }}/140</h3>
                </div>
                <div class="bg-yellow-500/20 p-3 rounded-xl text-yellow-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                </div>
            </div>
            <div class="w-full rounded-full h-1.5 mt-4 overflow-hidden"
                 :class="darkMode ? 'bg-white/10' : 'bg-gray-100'">
                <div class="bg-yellow-500 h-full" style="width: {{ ($performancesEntered / 140) * 100 }}%"></div>
            </div>
        </div>
    </div>

    <!-- Current Gameweek -->
    @if($currentGameweek)
        <div class="bg-gradient-to-r from-blue-900 to-indigo-900 rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden border border-white/10">
            <div class="absolute inset-0 bg-white/5 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/10 to-transparent pointer-events-none"></div>
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative z-10">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="bg-[#00ff85] text-[#1a0b2e] px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest animate-pulse">Live Now</span>
                        <h3 class="text-3xl font-black text-white">{{ $currentGameweek->name }}</h3>
                    </div>
                    
                    <div class="flex gap-6 mt-4 text-gray-300">
                        <div>
                            <span class="text-xs uppercase text-gray-500 font-bold block">التاريخ</span>
                            <span class="font-bold text-white">{{ $currentGameweek->date->format('Y-m-d') }}</span>
                        </div>
                        <div>
                            <span class="text-xs uppercase text-gray-500 font-bold block">الموعد النهائي</span>
                            <span class="font-bold text-[#04f5ff]">{{ $currentGameweek->deadline->format('Y-m-d H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-xs uppercase text-gray-500 font-bold block">المكان</span>
                            <span class="font-bold text-white">{{ $currentGameweek->location }}</span>
                        </div>
                    </div>
                </div>

                <div class="w-full md:w-auto">
                    @if(!$currentGameweek->is_finished)
                        <form action="{{ route('admin.gameweeks.finalize', $currentGameweek->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إنهاء هذا الأسبوع؟ لن يمكن التراجع!')">
                            @csrf
                            <button type="submit"
                                    class="w-full md:w-auto bg-[#00ff85] hover:bg-[#00e676] text-[#1a0b2e] px-8 py-4 rounded-xl font-black text-lg transition shadow-[0_0_20px_rgba(0,255,133,0.3)] hover:shadow-[0_0_30px_rgba(0,255,133,0.5)]">
                                🔚 إنهاء الأسبوع
                            </button>
                        </form>
                    @else
                        <span class="bg-gray-600 text-white px-6 py-3 rounded-xl font-bold">الأسبوع منتهي</span>
                    @endif
                </div>
            </div>

            @if($performancesEntered < 140)
                <div class="mt-6 bg-red-500/20 border border-red-500/30 rounded-xl p-4 flex items-center gap-3 text-red-200">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <p class="font-bold text-sm">تنبيه: لا يزال هناك كشافة لم يتم إدخال نقاطهم ({{ 140 - $performancesEntered }} متبقي). لا تقم بإنهاء الأسبوع قبل اكتمال الإدخال.</p>
                </div>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Transfers -->
        <div class="lg:col-span-2 backdrop-blur-md border rounded-2xl p-6 shadow-xl"
             :class="darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200'">
            <h3 class="text-xl font-bold mb-4 flex items-center gap-2"
                :class="darkMode ? 'text-white' : 'text-gray-900'">
                <span>🔄</span> آخر التبديلات
            </h3>
            <div class="space-y-3">
                @foreach($recentTransfers as $transfer)
                    <div class="rounded-xl p-3 flex items-center justify-between border transition"
                         :class="darkMode ? 'bg-white/5 hover:bg-white/10 border-white/5' : 'bg-gray-50 hover:bg-gray-100 border-gray-200'">
                        <div class="flex items-center gap-4">
                             <img src="{{ $transfer->user->photo_url ?? asset('images/default-avatar.png') }}" 
                                  class="w-10 h-10 rounded-full border"
                                  :class="darkMode ? 'border-white/20' : 'border-gray-200'">
                             <div>
                                 <div class="font-bold text-sm"
                                      :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $transfer->user->team_name }}</div>
                                 <div class="text-xs text-gray-400">قام بتبديل</div>
                             </div>
                        </div>
                        
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border"
                             :class="darkMode ? 'bg-black/30 border-white/5' : 'bg-white border-gray-200'">
                            <span class="text-red-400 font-bold text-sm">{{ $transfer->scoutOut->full_name }}</span>
                            <span class="text-gray-500">➜</span>
                            <span class="text-[#00ff85] font-bold text-sm">{{ $transfer->scoutIn->full_name }}</span>
                        </div>
                        
                        <div class="text-xs text-gray-500">{{ $transfer->created_at->diffForHumans() }}</div>
                    </div>
                @endforeach

                @if($recentTransfers->isEmpty())
                    <div class="text-center py-10 text-gray-500">لا توجد تبديلات حديثة</div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="space-y-4">
             <a href="{{ route('admin.points.index') }}" class="block bg-gradient-to-r from-yellow-600 to-orange-600 rounded-2xl p-6 text-white text-center hover:scale-[1.02] transition-transform shadow-lg group">
                 <div class="bg-white/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:rotate-12 transition-transform">
                     <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                 </div>
                 <h4 class="font-black text-xl">إدخال النقاط</h4>
                 <p class="text-yellow-100 text-sm mt-1">تحديث نتائج الكشافة</p>
             </a>

             <a href="{{ route('admin.news.index') }}" class="block bg-gradient-to-r from-teal-600 to-emerald-600 rounded-2xl p-6 text-white text-center hover:scale-[1.02] transition-transform shadow-lg group">
                 <div class="bg-white/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:rotate-12 transition-transform">
                     <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                 </div>
                 <h4 class="font-black text-xl">إدارة الأخبار</h4>
                 <p class="text-teal-100 text-sm mt-1">نشر تحديثات جديدة</p>
             </a>
        </div>
    </div>

</div>
@endsection
