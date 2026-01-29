@extends('layouts.admin')

@section('title', 'إدارة الجولات - Admin')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
        <h2 class="text-3xl font-black text-white font-['Changa']">إدارة الجولات ⚔️</h2>
        @if($currentGameweek)
            <span class="bg-[#00ff85] text-[#1a0b2e] px-4 py-2 rounded-xl text-sm font-black uppercase tracking-widest shadow-[0_0_20px_#00ff8540] animate-pulse">
                الجولة الحالية: {{ $currentGameweek->gameweek_number }}
            </span>
        @endif
    </div>

    <!-- Add New Gameweek Form -->
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-2xl relative overflow-hidden group hover:border-[#04f5ff]/30 transition">
        <div class="absolute inset-0 bg-blue-500/5 group-hover:bg-blue-500/10 transition"></div>
        <h3 class="text-xl font-bold text-white mb-6 relative z-10 flex items-center gap-2">
            <svg class="w-6 h-6 text-[#04f5ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            إضافة جولة جديدة
        </h3>
        
        <form action="{{ route('admin.gameweeks.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4 relative z-10">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">رقم الجولة</label>
                <input type="number" name="gameweek_number" min="1" required class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#04f5ff] focus:ring-1 focus:ring-[#04f5ff] outline-none transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">اسم الجولة</label>
                <input type="text" name="name" class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#04f5ff] focus:ring-1 focus:ring-[#04f5ff] outline-none transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">تاريخ الجولة</label>
                <input type="date" name="date" required class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#04f5ff] focus:ring-1 focus:ring-[#04f5ff] outline-none transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">مكان الجولة</label>
                <input type="text" name="location" class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#04f5ff] focus:ring-1 focus:ring-[#04f5ff] outline-none transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">موعد الديدلاين</label>
                <input type="datetime-local" name="deadline" required class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#04f5ff] focus:ring-1 focus:ring-[#04f5ff] outline-none transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">صورة الجولة</label>
                <input type="file" name="photo" accept="image/*" class="w-full text-sm text-gray-400 border border-white/10 rounded-xl cursor-pointer bg-[#12002b] file:mr-4 file:py-3 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-semibold file:bg-white/10 file:text-white hover:file:bg-white/20">
                <p class="text-[10px] text-gray-500 mt-1">JPG, PNG, GIF - Max 2MB</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">الوصف</label>
                <textarea name="description" rows="3" class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#04f5ff] focus:ring-1 focus:ring-[#04f5ff] outline-none transition"></textarea>
            </div>
            <div class="md:col-span-2 flex items-center gap-3 bg-[#12002b] p-3 rounded-xl border border-white/5">
                <input type="hidden" name="is_current" value="0">
                <input type="checkbox" id="is_current" name="is_current" value="1" class="w-5 h-5 text-[#04f5ff] border-white/10 rounded bg-white/5 focus:ring-[#04f5ff]">
                <label for="is_current" class="font-bold text-white cursor-pointer select-none">تعيين الجولة كجولة حالية <span class="text-xs text-gray-500 font-normal">(سيؤدي هذا إلى إلغاء أي جولة حالية أخرى)</span></label>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-black py-3 rounded-xl shadow-lg shadow-green-900/20 transform hover:-translate-y-0.5 transition-all">
                    ➕ إضافة الجولة
                </button>
            </div>
        </form>
    </div>

    <!-- Gameweeks List -->
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
            <thead class="bg-black/20 text-gray-400 text-xs font-bold uppercase border-b border-white/10">
                <tr>
                    <th class="px-6 py-4 text-right">رقم</th>
                    <th class="px-6 py-4 text-right">الاسم</th>
                    <th class="px-6 py-4 text-right">التاريخ</th>
                    <th class="px-6 py-4 text-right">الديدلاين</th>
                    <th class="px-6 py-4 text-right">الحالة</th>
                    <th class="px-6 py-4 text-right w-1/3">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($gameweeks as $gameweek)
                    <tr class="group hover:bg-white/5 transition">
                        <td class="px-6 py-4 font-black text-white text-lg">GW{{ $gameweek->gameweek_number }}</td>
                        <td class="px-6 py-4 font-bold text-white">{{ $gameweek->name }}</td>
                        <td class="px-6 py-4 text-gray-300">{{ $gameweek->date->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 font-mono text-[#04f5ff]">{{ $gameweek->deadline->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4">
                            @if($gameweek->is_current)
                                <span class="bg-[#00ff85]/20 text-[#00ff85] border border-[#00ff85]/30 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider animate-pulse">Live</span>
                            @else
                                <span class="bg-white/5 text-gray-400 border border-white/10 px-3 py-1 rounded-full text-xs font-bold">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <details class="group/details">
                                <summary class="cursor-pointer bg-white/5 hover:bg-white/10 border border-white/10 px-4 py-2 rounded-lg text-center font-bold text-white transition select-none flex items-center justify-center gap-2">
                                    <span>⚙️ إدارة</span>
                                    <svg class="w-4 h-4 transition-transform group-open/details:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </summary>
                                
                                <div class="mt-4 bg-black/20 rounded-xl p-4 border border-white/5">
                                    <form action="{{ route('admin.gameweeks.update', $gameweek->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 mb-4">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <input type="number" name="gameweek_number" min="1" value="{{ $gameweek->gameweek_number }}" required class="bg-[#12002b] border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:border-[#04f5ff] outline-none">
                                            <input type="text" name="name" value="{{ $gameweek->name }}" class="bg-[#12002b] border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:border-[#04f5ff] outline-none">
                                            <input type="date" name="date" value="{{ $gameweek->date->format('Y-m-d') }}" required class="bg-[#12002b] border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:border-[#04f5ff] outline-none">
                                            <input type="text" name="location" value="{{ $gameweek->location }}" class="bg-[#12002b] border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:border-[#04f5ff] outline-none">
                                            <input type="datetime-local" name="deadline" value="{{ $gameweek->deadline->format('Y-m-d\TH:i') }}" required class="bg-[#12002b] border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:border-[#04f5ff] outline-none">
                                            <div class="flex items-center gap-2">
                                                @if($gameweek->photo_url)
                                                    <img src="{{ asset(ltrim($gameweek->photo_url, '/')) }}" class="w-10 h-10 object-cover rounded border border-white/20">
                                                @endif
                                                <input type="file" name="photo" class="text-xs text-gray-400 file:bg-white/10 file:text-white file:border-0 file:rounded-lg">
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 bg-[#12002b] p-2 rounded-lg border border-white/5">
                                            <input type="checkbox" id="edit_is_current_{{ $gameweek->id }}" name="is_current" value="1" class="w-4 h-4 text-[#04f5ff] bg-white/5 border-white/10 rounded" {{ $gameweek->is_current ? 'checked' : '' }}>
                                            <label for="edit_is_current_{{ $gameweek->id }}" class="text-sm text-gray-300">تعيين كجولة حالية</label>
                                        </div>
                                        <button type="submit" class="w-full bg-blue-600/80 hover:bg-blue-600 text-white text-sm font-bold py-2 rounded-lg transition hover:shadow-lg">
                                            💾 حفظ التعديلات
                                        </button>
                                    </form>
                                    
                                    <div class="flex flex-wrap gap-2 justify-end border-t border-white/5 pt-3">
                                         <form action="{{ route('admin.gameweeks.refresh-points', $gameweek->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-indigo-500/20 hover:bg-indigo-500/40 text-indigo-300 border border-indigo-500/30 px-3 py-1.5 rounded-lg text-xs font-bold transition disabled:opacity-50" {{ $gameweek->is_current ? '' : 'disabled' }}>
                                                🔄 تحديث النقاط
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('admin.gameweeks.finalize', $gameweek->id) }}" method="POST" id="finalize-form-{{ $gameweek->id }}" onsubmit="return handleFinalizeSubmit(this, event)">
                                            @csrf
                                            <button type="submit" class="bg-yellow-500/20 hover:bg-yellow-500/40 text-yellow-300 border border-yellow-500/30 px-3 py-1.5 rounded-lg text-xs font-bold transition disabled:opacity-50" {{ $gameweek->is_current ? '' : 'disabled' }}>
                                                🏁 إنهاء الجولة
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.gameweeks.destroy', $gameweek->id) }}" method="POST" onsubmit="return confirm('هل تريد حذف الجولة؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500/20 hover:bg-red-500/40 text-red-300 border border-red-500/30 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                                🗑️ حذف
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </details>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            </table>
        </div>

        @if($gameweeks->isEmpty())
            <div class="text-center py-12 text-gray-500">لا توجد جولات حاليا</div>
        @endif
    </div>
</div>

<script>
function handleFinalizeSubmit(form, event) {
    event.preventDefault();

    if (!confirm('هل تريد إنهاء الجولة؟\n\nتحذير: هذه العملية قد تستغرق عدة دقائق. سيتم:\n- حساب نقاط المستخدمين\n- تحديث الترتيبات\n- تغيير أسعار الكشافة\n- نسخ الفرق للجولة القادمة\n- إرسال الإشعارات\n\nيرجى الانتظار حتى انتهاء العملية ولا تغلق الصفحة.')) {
        return false;
    }

    // تعطيل الزر وإظهار رسالة تحميل
    const button = form.querySelector('button[type="submit"]');
    const originalText = button.innerHTML;
    button.disabled = true;
    button.classList.add('opacity-50', 'cursor-not-allowed');
    button.innerHTML = '⏳ جاري الإنهاء... قد يستغرق 2-5 دقائق';

    // إنشاء overlay للتحميل
    const overlay = document.createElement('div');
    overlay.id = 'finalize-overlay';
    overlay.className = 'fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50';
    overlay.innerHTML = `
        <div class="bg-[#1a0b2e]/90 border-2 border-[#04f5ff]/50 rounded-2xl p-8 max-w-md text-center shadow-2xl">
            <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-[#04f5ff] mx-auto mb-4"></div>
            <h3 class="text-2xl font-black text-white mb-2">جاري إنهاء الجولة...</h3>
            <p class="text-gray-400 text-sm mb-4">قد تستغرق هذه العملية عدة دقائق</p>
            <div class="space-y-2 text-xs text-gray-500 text-right">
                <p>✓ حساب نقاط المستخدمين</p>
                <p>✓ تحديث ترتيب الطلائع</p>
                <p>✓ تحديث أسعار الكشافة</p>
                <p>✓ نسخ الفرق للجولة القادمة</p>
                <p>✓ إرسال الإشعارات</p>
            </div>
            <p class="text-yellow-400 font-bold mt-4 animate-pulse">⚠️ لا تغلق الصفحة</p>
        </div>
    `;
    document.body.appendChild(overlay);

    // إرسال الفورم
    form.submit();

    return false;
}
</script>

@endsection
