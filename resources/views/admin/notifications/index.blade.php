@extends('layouts.admin')

@section('title', 'إدارة الإشعارات - Admin')

@section('content')
<div class="space-y-8 animate-fade-in">
    <div class="flex items-center justify-between">
        <h2 class="text-3xl font-black text-white font-['Changa']">إدارة الإشعارات 🔔</h2>
    </div>

    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/30 text-red-200 px-6 py-4 rounded-xl flex items-center gap-3 font-bold backdrop-blur-sm">
             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/30 text-green-200 px-6 py-4 rounded-xl flex items-center gap-3 font-bold backdrop-blur-sm">
             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/30 text-red-200 px-6 py-4 rounded-xl font-bold backdrop-blur-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Send Notification Form -->
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl p-8 relative overflow-hidden">
        <div class="absolute inset-0 bg-blue-600/5 pointer-events-none"></div>
        <h3 class="text-xl font-bold text-white mb-6 relative z-10 flex items-center gap-2">
            <span class="bg-blue-500/20 text-blue-400 p-2 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg></span>
            إرسال إشعار جديد
        </h3>
        
        <form id="notification-form" action="{{ route('admin.notifications.store') }}" method="POST" class="space-y-6 relative z-10">
            @csrf
            <input type="hidden" name="delivery" id="delivery-input" value="">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="group">
                    <label class="block text-sm font-bold text-gray-400 mb-2 uppercase group-focus-within:text-blue-400 transition">النوع</label>
                    <select name="type" class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-400 focus:ring-1 focus:ring-blue-400 outline-none transition appearance-none">
                        <option value="news">📰 خبر</option>
                        <option value="deadline">⏰ ديدلاين</option>
                        <option value="ranking">🏆 ترتيب</option>
                        <option value="price_alert">💰 تنبيه سعر</option>
                        <option value="other">📢 أخرى</option>
                    </select>
                </div>
                <div class="group">
                    <label class="block text-sm font-bold text-gray-400 mb-2 uppercase group-focus-within:text-blue-400 transition">المستهدف</label>
                    <select name="target" id="target" class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-400 focus:ring-1 focus:ring-blue-400 outline-none transition appearance-none">
                        <option value="all">🌍 كل المستخدمين</option>
                        <option value="users">👤 مستخدمين محددين</option>
                    </select>
                </div>
            </div>

            <div id="user-picker" class="hidden">
                <label class="block text-sm font-bold text-gray-400 mb-2 uppercase">اختيار المستخدمين</label>
                <select name="user_ids[]" multiple size="5" class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-400 focus:ring-1 focus:ring-blue-400 outline-none transition custom-scrollbar text-sm">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" class="py-1 px-2 hover:bg-white/10 rounded">
                            {{ trim($user->first_name . ' ' . $user->last_name) }} ({{ $user->scout_id }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-gray-500 mt-1 font-mono">اضغط Ctrl للاختيار المتعدد</p>
            </div>

            <div class="group">
                <label class="block text-sm font-bold text-gray-400 mb-2 uppercase group-focus-within:text-blue-400 transition">العنوان</label>
                <input type="text" name="title" value="{{ old('title') }}" class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-400 focus:ring-1 focus:ring-blue-400 outline-none transition placeholder-gray-600" required placeholder="عنوان الإشعار">
            </div>

            <div class="group">
                <label class="block text-sm font-bold text-gray-400 mb-2 uppercase group-focus-within:text-blue-400 transition">الرسالة</label>
                <textarea name="message" rows="4" class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-400 focus:ring-1 focus:ring-blue-400 outline-none transition placeholder-gray-600" required placeholder="محتوى الرسالة..."></textarea>
            </div>

            <div class="flex flex-wrap gap-4 pt-4 border-t border-white/5">
                <button type="button" onclick="submitForm('in_app')" class="flex-1 bg-gradient-to-r from-purple-600 to-indigo-700 hover:from-purple-700 hover:to-indigo-800 text-white font-bold py-4 rounded-xl shadow-lg transform hover:-translate-y-1 transition text-sm sm:text-base flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    إرسال داخل التطبيق
                </button>
                <button type="button" onclick="submitForm('email')" class="flex-1 bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white border border-white/10 hover:border-white/20 font-bold py-4 rounded-xl shadow-lg transform hover:-translate-y-1 transition text-sm sm:text-base flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    إرسال بريد إلكتروني
                </button>
            </div>
        </form>
    </div>

    <!-- Notifications History -->
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-white/10">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                أحدث الإشعارات
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-black/20 text-gray-400 text-xs font-bold uppercase border-b border-white/10">
                    <tr>
                        <th class="px-6 py-4 text-right">المستخدم</th>
                        <th class="px-6 py-4 text-right">النوع</th>
                        <th class="px-6 py-4 text-right">العنوان</th>
                        <th class="px-6 py-4 text-right">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($notifications as $note)
                        <tr class="group hover:bg-white/5 transition">
                            <td class="px-6 py-4 text-gray-300 font-bold">
                                {{ $note->user ? trim($note->user->first_name . ' ' . $note->user->last_name) : 'الكل' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-white/5 border border-white/10 px-2 py-1 rounded text-xs font-mono text-gray-400">{{ $note->type }}</span>
                            </td>
                            <td class="px-6 py-4 text-white">{{ $note->title }}</td>
                            <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $note->created_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                لا توجد إشعارات سابقة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const target = document.getElementById('target');
        const picker = document.getElementById('user-picker');

        function togglePicker() {
            picker.classList.toggle('hidden', target.value !== 'users');
        }

        target.addEventListener('change', togglePicker);
        togglePicker();
    });

    function submitForm(deliveryType) {
        const deliveryInput = document.getElementById('delivery-input');
        const form = document.getElementById('notification-form');

        deliveryInput.value = deliveryType;
        form.submit();
    }
</script>
@endpush
@endsection
