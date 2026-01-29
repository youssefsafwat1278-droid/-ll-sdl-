<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="ar" dir="rtl" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - Scout Tanzania</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Changa:wght@200;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="font-arabic min-h-screen bg-[#0f0518] overflow-hidden selection:bg-[#04f5ff] selection:text-[#1a0b2e]">
    
    <!-- Fantasy Background -->
    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-br from-[#1a0b2e] via-[#0f0518] to-[#2d1b4e]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-purple-900/30 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-900/20 rounded-full blur-[100px] animate-pulse" style="animation-duration: 4s;"></div>
        
        <!-- Animated Particles -->
        <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 30px 30px;"></div>
    </div>

    <div class="relative z-10 min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            
            <!-- Logo/Header -->
            <div class="text-center mb-8 relative group">
                <div class="absolute -inset-10 bg-gradient-to-r from-purple-600/20 to-blue-600/20 rounded-full blur-3xl group-hover:blur-2xl transition-all duration-500"></div>
                <h1 class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-[#04f5ff] to-purple-500 font-['Changa'] drop-shadow-[0_0_15px_rgba(4,245,255,0.3)]">
                    Scout Fantasy
                </h1>
                <p class="text-gray-400 mt-2 font-bold tracking-widest text-sm uppercase">دوري الكشافة الافتراضي</p>
            </div>

            <!-- Login Card -->
            <div class="bg-[#1a0b2e]/60 backdrop-blur-2xl rounded-3xl shadow-[0_0_40px_rgba(0,0,0,0.5)] border border-white/10 p-8 relative overflow-hidden group">
                <!-- Neon Border Effect -->
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-[#04f5ff] to-transparent opacity-50 group-hover:opacity-100 transition-opacity"></div>
                
                <h2 class="text-2xl font-bold text-white mb-6 text-center">تسجيل الدخول</h2>

                @if(session('success'))
                    <div class="bg-[#00ff85]/10 border border-[#00ff85]/20 text-[#00ff85] px-4 py-3 rounded-xl mb-6 text-sm font-bold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl mb-6 text-xs font-bold space-y-1">
                        @foreach($errors->all() as $error)
                            <p class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $error }}
                            </p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="space-y-2">
                        <label for="scout_id" class="block text-gray-400 text-sm font-bold">رقم الكشاف</label>
                        <div class="relative">
                            <input type="text"
                                   id="scout_id"
                                   name="scout_id"
                                   value="{{ old('scout_id') }}"
                                   placeholder="SC001"
                                   required
                                   class="w-full bg-[#1a0b2e]/50 border border-white/10 rounded-xl px-4 py-3.5 text-white placeholder-gray-600 focus:border-[#04f5ff] focus:ring-1 focus:ring-[#04f5ff] focus:outline-none transition-all font-mono">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="block text-gray-400 text-sm font-bold">كلمة المرور</label>
                        <div class="relative">
                            <input type="password"
                                   id="password"
                                   name="password"
                                   required
                                   class="w-full bg-[#1a0b2e]/50 border border-white/10 rounded-xl px-4 py-3.5 text-white placeholder-gray-600 focus:border-[#04f5ff] focus:ring-1 focus:ring-[#04f5ff] focus:outline-none transition-all">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-600 bg-[#1a0b2e] text-[#04f5ff] focus:ring-[#04f5ff] focus:ring-offset-0">
                            <span class="mr-2 text-sm text-gray-400 group-hover:text-white transition-colors">تذكرني</span>
                        </label>
                    </div>

                    <button type="submit"
                            class="w-full bg-gradient-to-r from-[#04f5ff] to-blue-600 hover:from-[#04f5ff] hover:to-blue-500 text-[#1a0b2e] font-black py-4 rounded-xl transition-all duration-300 transform hover:scale-[1.02] shadow-[0_0_20px_rgba(4,245,255,0.3)] hover:shadow-[0_0_30px_rgba(4,245,255,0.5)]">
                        دخول النظام
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-white/5 text-center">
                    <p class="text-gray-400 text-sm">ليس لديك فريق؟
                        <a href="{{ route('register') }}" class="text-[#04f5ff] hover:text-white font-bold transition-colors mr-1">انضم للدوري الآن</a>
                    </p>
                </div>
            </div>
            
            <div class="text-center mt-6 text-gray-500 text-xs font-mono">
                &copy; {{ date('Y') }} Scout Tanzania Fantasy League
            </div>

        </div>
    </div>
</body>
</html>
