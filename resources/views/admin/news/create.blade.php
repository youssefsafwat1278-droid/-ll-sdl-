@extends('layouts.admin')

@section('title', 'إضافة خبر - Admin')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in">
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl p-8">
        <h2 class="text-3xl font-black text-white mb-8 flex items-center gap-3">
            <span class="bg-[#04f5ff]/20 text-[#04f5ff] p-2 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </span>
            إضافة خبر جديد
        </h2>

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-200 px-4 py-3 rounded-xl mb-6 backdrop-blur-sm">
                <ul class="list-disc list-inside space-y-1 text-sm font-bold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="group">
                <label class="block text-sm font-bold text-gray-400 mb-2 uppercase group-focus-within:text-[#04f5ff] transition">عنوان الخبر</label>
                <input type="text"
                       name="title"
                       value="{{ old('title') }}"
                       required
                       class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#04f5ff] focus:ring-1 focus:ring-[#04f5ff] outline-none transition placeholder-gray-600"
                       placeholder="مثال: انطلاق الجولة الخامسة...">
                @error('title')
                    <p class="text-red-400 text-sm mt-1 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <div class="group">
                <label class="block text-sm font-bold text-gray-400 mb-2 uppercase group-focus-within:text-[#04f5ff] transition">المحتوى</label>
                <textarea name="content"
                          rows="10"
                          required
                          class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-[#04f5ff] focus:ring-1 focus:ring-[#04f5ff] outline-none transition placeholder-gray-600"
                          placeholder="اكتب تفاصيل الخبر هنا...">{{ old('content') }}</textarea>
                @error('content')
                    <p class="text-red-400 text-sm mt-1 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <div class="group">
                <label class="block text-sm font-bold text-gray-400 mb-2 uppercase group-focus-within:text-[#04f5ff] transition">صورة الخبر (اختياري)</label>
                <div class="relative">
                    <input type="file"
                           name="image"
                           accept="image/*"
                           class="w-full text-sm text-gray-400 bg-[#12002b] border border-white/10 rounded-xl cursor-pointer file:mr-4 file:py-3 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-bold file:bg-white/10 file:text-white hover:file:bg-white/20 transition">
                </div>
                <p class="text-[10px] text-gray-500 mt-2 font-mono">JPG, PNG, GIF - Max: 2MB</p>
                @error('image')
                    <p class="text-red-400 text-sm mt-1 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 bg-white/5 p-4 rounded-xl border border-white/5">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox"
                       name="is_featured"
                       id="is_featured"
                       value="1"
                       {{ old('is_featured') ? 'checked' : '' }}
                       class="w-5 h-5 text-[#04f5ff] border-white/20 rounded bg-[#12002b] focus:ring-[#04f5ff] focus:ring-offset-0">
                <label for="is_featured" class="font-bold text-white cursor-pointer select-none">
                    تمييز الخبر
                    <span class="block text-xs text-gray-400 font-normal mt-0.5">سيظهر هذا الخبر بشكل بارز في الصفحة الرئيسية</span>
                </label>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-black py-4 rounded-xl shadow-lg transform hover:-translate-y-1 transition text-lg">
                    💾 حفظ ونشر
                </button>
                <a href="{{ route('admin.news.index') }}" class="px-8 bg-white/5 hover:bg-white/10 text-gray-300 font-bold py-4 rounded-xl text-center transition border border-white/10 hover:text-white">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
