@extends('layouts.app')

@section('title', 'الإعدادات - Scout Tanzania')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 animate-fade-in">
    <div class="flex items-center justify-between">
        <h2 class="text-3xl font-black text-white font-['Changa']">إعدادات الحساب ⚙️</h2>
    </div>

    <!-- Account Summary -->
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-2xl">
        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#04f5ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884-.5 2-2 2h4c-1.5 0-2-1.116-2-2z"></path></svg>
            محفظة الفريق
        </h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white/5 rounded-xl p-4 text-center border border-white/5">
                <div class="text-xs text-gray-400 font-bold uppercase mb-1">إجمالي النقاط</div>
                <div class="text-2xl font-black text-[#04f5ff]">{{ $displayTotalPoints }}</div>
            </div>
            <div class="bg-white/5 rounded-xl p-4 text-center border border-white/5">
                <div class="text-xs text-gray-400 font-bold uppercase mb-1">الرصيد المتاح</div>
                <div class="text-2xl font-black text-[#00ff85]">{{ $user->bank_balance }}</div>
            </div>
            <div class="bg-white/5 rounded-xl p-4 text-center border border-white/5">
                <div class="text-xs text-gray-400 font-bold uppercase mb-1">تبديلات مجانية</div>
                <div class="text-2xl font-black text-yellow-400">{{ $user->free_transfers }}</div>
            </div>
            <div class="bg-white/5 rounded-xl p-4 text-center border border-white/5">
                <div class="text-xs text-gray-400 font-bold uppercase mb-1">الخواص المستخدمة</div>
                <div class="text-2xl font-black text-purple-400 opacity-60">
                    {{ $user->triple_captain_used + ($user->bench_boost_used ? 1 : 0) + ($user->free_hit_used ? 1 : 0) }}/3
                </div>
            </div>
        </div>
    </div>

    <!-- Main Settings Form -->
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl p-8 shadow-2xl relative overflow-hidden">
        
        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-200 px-4 py-3 rounded-xl mb-6 backdrop-blur-sm font-bold">
                <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Profile Info -->
            <div class="space-y-6">
                <h3 class="text-lg font-bold text-white border-b border-white/10 pb-2">المعلومات الشخصية</h3>
                
                <div class="flex items-center gap-6">
                    <div class="relative group cursor-pointer">
                        <img src="{{ $user->photo_url ?? asset('images/default-avatar.png') }}" alt="User Photo" class="w-20 h-20 rounded-full object-cover border-2 border-white/10 group-hover:border-[#04f5ff] transition">
                        <div class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <input type="file" name="photo" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*">
                    </div>
                    <div class="flex-1">
                        <div class="text-gray-400 text-xs mb-1">اضغط على الصورة للتغيير</div>
                        <div class="text-white font-bold">{{ $user->first_name }} {{ $user->last_name }}</div>
                        <div class="text-[#04f5ff] text-sm font-mono">ID: {{ $user->scout_id }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="group">
                        <label class="block text-xs font-bold text-gray-400 mb-2 uppercase group-focus-within:text-[#04f5ff] transition">اسم الفريق</label>
                        <input type="text" name="team_name" value="{{ old('team_name', $user->team_name) }}" class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#04f5ff] font-bold outline-none transition placeholder-gray-600">
                    </div>
                    
                    <div class="group">
                        <label class="block text-xs font-bold text-gray-400 mb-2 uppercase group-focus-within:text-[#04f5ff] transition">البريد الإلكتروني</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#04f5ff] outline-none transition placeholder-gray-600 font-mono">
                    </div>
                </div>
            </div>

            <!-- Password -->
            <div class="space-y-6">
                <h3 class="text-lg font-bold text-white border-b border-white/10 pb-2">الأمان وكلمة المرور</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="group">
                        <label class="block text-xs font-bold text-gray-400 mb-2 uppercase group-focus-within:text-[#04f5ff] transition">كلمة المرور الجديدة</label>
                        <input type="password" name="password" class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#04f5ff] outline-none transition placeholder-gray-600" placeholder="••••••••">
                    </div>
                    <div class="group">
                        <label class="block text-xs font-bold text-gray-400 mb-2 uppercase group-focus-within:text-[#04f5ff] transition">تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#04f5ff] outline-none transition placeholder-gray-600" placeholder="••••••••">
                    </div>
                </div>
            </div>

            <!-- Preferences -->
            <div class="space-y-6">
                 <h3 class="text-lg font-bold text-white border-b border-white/10 pb-2">التفضيلات</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                     <div class="flex items-center justify-between bg-white/5 p-4 rounded-xl border border-white/5">
                        <span class="text-gray-300 font-bold">تفعيل الإشعارات</span>
                         <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="notifications_enabled" value="0">
                            <input type="checkbox" name="notifications_enabled" value="1" class="sr-only peer" {{ $user->notifications_enabled ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#04f5ff] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#04f5ff]"></div>
                        </label>
                     </div>

                     <div class="flex items-center justify-between bg-white/5 p-4 rounded-xl border border-white/5">
                        <span class="text-gray-300 font-bold">الملف الشخصي عام</span>
                         <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="profile_public" value="0">
                            <input type="checkbox" name="profile_public" value="1" class="sr-only peer" {{ $user->profile_public ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#04f5ff] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#04f5ff]"></div>
                        </label>
                     </div>
                 </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-4 pt-6 border-t border-white/10">
                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-black py-4 rounded-xl shadow-lg transform hover:-translate-y-1 transition text-lg flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
