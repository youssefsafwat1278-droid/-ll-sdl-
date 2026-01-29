@extends('layouts.admin')

@section('title', 'تعديل خبر - Admin')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in">
    <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl p-8">
        <h2 class="text-3xl font-black text-white mb-8 flex items-center gap-3">
            <span class="bg-blue-500/20 text-blue-400 p-2 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            </span>
            تعديل الخبر
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

        <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="group">
                <label class="block text-sm font-bold text-gray-400 mb-2 uppercase group-focus-within:text-blue-400 transition">العنوان</label>
                <input type="text"
                       name="title"
                       value="{{ old('title', $news->title) }}"
                       required
                       class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-400 focus:ring-1 focus:ring-blue-400 outline-none transition placeholder-gray-600">
                @error('title')
                    <p class="text-red-400 text-sm mt-1 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <div class="group">
                <label class="block text-sm font-bold text-gray-400 mb-2 uppercase group-focus-within:text-blue-400 transition">المحتوى</label>
                <textarea name="content"
                          rows="10"
                          required
                          class="w-full bg-[#12002b] border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-400 focus:ring-1 focus:ring-blue-400 outline-none transition placeholder-gray-600">{{ old('content', $news->content) }}</textarea>
                @error('content')
                    <p class="text-red-400 text-sm mt-1 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <div class="group">
                <label class="block text-sm font-bold text-gray-400 mb-2 uppercase group-focus-within:text-blue-400 transition">صورة الخبر</label>

                @if($news->image_url)
                    <div class="mb-4 relative w-48 group/img">
                        <img src="{{ $news->image_url }}" alt="Current image" class="w-48 h-32 object-cover rounded-xl border-2 border-white/10 group-hover/img:border-blue-400 transition">
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover/img:opacity-100 transition flex items-center justify-center rounded-xl text-xs font-bold text-white pointer-events-none">
                            الصورة الحالية
                        </div>
                    </div>
                @endif

                <div class="relative">
                    <input type="file"
                           name="image"
                           accept="image/*"
                           class="w-full text-sm text-gray-400 bg-[#12002b] border border-white/10 rounded-xl cursor-pointer file:mr-4 file:py-3 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-bold file:bg-white/10 file:text-white hover:file:bg-white/20 transition">
                </div>
                <p class="text-[10px] text-gray-500 mt-2 font-mono">اترك فارغاً للإبقاء على الصورة الحالية</p>
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
                       {{ old('is_featured', $news->is_featured) ? 'checked' : '' }}
                       class="w-5 h-5 text-blue-400 border-white/20 rounded bg-[#12002b] focus:ring-blue-400 focus:ring-offset-0">
                <label for="is_featured" class="font-bold text-white cursor-pointer select-none">
                    تمييز الخبر
                    <span class="block text-xs text-gray-400 font-normal mt-0.5">سيظهر هذا الخبر بشكل بارز في الصفحة الرئيسية</span>
                </label>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl shadow-lg transform hover:-translate-y-1 transition text-lg">
                    💾 حفظ التعديلات
                </button>
                <a href="{{ route('admin.news.index') }}" class="px-8 bg-white/5 hover:bg-white/10 text-gray-300 font-bold py-4 rounded-xl text-center transition border border-white/10 hover:text-white">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
