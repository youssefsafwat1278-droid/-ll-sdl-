@extends('layouts.admin')

@section('title', 'إدارة الأخبار - Admin')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <h2 class="text-3xl font-black text-white font-['Changa']">إدارة الأخبار 📰</h2>
        <a href="{{ route('admin.news.create') }}" class="w-full sm:w-auto bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg transform hover:-translate-y-0.5 transition flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            إضافة خبر جديد
        </a>
    </div>

    <!-- News List -->
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
            <thead class="bg-black/20 text-gray-400 text-xs font-bold uppercase border-b border-white/10">
                <tr>
                    <th class="px-6 py-4 text-right">العنوان</th>
                    <th class="px-6 py-4 text-right">الكاتب</th>
                    <th class="px-6 py-4 text-right">مميز</th>
                    <th class="px-6 py-4 text-right">التاريخ</th>
                    <th class="px-6 py-4 text-right w-1/4">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($news as $article)
                    <tr class="group hover:bg-white/5 transition border-l-4 border-transparent hover:border-[#04f5ff]">
                        <td class="px-6 py-4 font-bold text-white">{{ $article->title }}</td>
                        <td class="px-6 py-4 text-gray-300 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-xs font-bold text-white">
                                {{ substr($article->author->first_name ?? '?', 0, 1) }}
                            </div>
                            {{ $article->author->first_name ?? 'غير معروف' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($article->is_featured)
                                <span class="bg-yellow-500/20 text-yellow-300 border border-yellow-500/30 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1 w-fit">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    نعم
                                </span>
                            @else
                                <span class="text-gray-500 text-xs font-bold px-3">لا</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-400 font-mono text-xs">{{ $article->created_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.news.edit', $article->id) }}" class="bg-blue-600/20 hover:bg-blue-600/40 text-blue-300 border border-blue-500/30 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                    تعديل
                                </a>
                                <form action="{{ route('admin.news.toggle-featured', $article->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-yellow-600/20 hover:bg-yellow-600/40 text-yellow-300 border border-yellow-500/30 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                        {{ $article->is_featured ? 'إلغاء التمييز' : 'تمييز' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.news.destroy', $article->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الخبر؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600/20 hover:bg-red-600/40 text-red-300 border border-red-500/30 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            </table>
        </div>

        @if($news->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="bg-white/5 rounded-full p-4 mb-4">
                    <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-white">لا توجد أخبار</h3>
                <p class="text-gray-400 mt-2">ابدأ بإضافة أول خبر للنظام</p>
            </div>
        @endif
    </div>

    <div class="mt-6">
        {{ $news->links() }}
    </div>
</div>
@endsection
