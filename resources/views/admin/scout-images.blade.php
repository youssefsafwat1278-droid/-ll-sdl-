@extends('layouts.admin')

@section('title', 'إدارة الصور - Admin')

@section('content')
<div class="space-y-8 animate-fade-in" x-data="{ activeTab: 'scouts' }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-3xl font-black text-white font-['Changa']">إدارة الصور 🖼️</h2>
            <p class="text-gray-400 text-sm mt-1">رفع وإدارة صور الكشافة وشعارات الطلائع</p>
        </div>
        
        <!-- Tab Switcher -->
        <div class="bg-[#1a0b2e] border border-white/10 p-1 rounded-xl flex gap-1">
            <button @click="activeTab = 'scouts'" 
                    :class="activeTab === 'scouts' ? 'bg-[#04f5ff] text-[#1a0b2e] shadow-lg shadow-[#04f5ff]/20' : 'text-gray-400 hover:text-white hover:bg-white/5'"
                    class="px-6 py-2 rounded-lg text-sm font-bold transition-all duration-300">
                صور الكشافة
            </button>
            <button @click="activeTab = 'patrols'" 
                    :class="activeTab === 'patrols' ? 'bg-[#04f5ff] text-[#1a0b2e] shadow-lg shadow-[#04f5ff]/20' : 'text-gray-400 hover:text-white hover:bg-white/5'"
                    class="px-6 py-2 rounded-lg text-sm font-bold transition-all duration-300">
                شعارات الطلائع
            </button>
        </div>
    </div>

    <!-- Info Banner -->
    <div class="bg-blue-900/20 border border-blue-500/30 text-blue-200 px-6 py-4 rounded-xl flex items-center gap-3 backdrop-blur-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        <span class="text-sm font-bold">الصور المقبولة: JPG, PNG, GIF - الحد الأقصى: 2 ميجابايت للكشافة، 1 ميجابايت للطلائع</span>
    </div>

    <!-- Scouts Grid -->
    <div x-show="activeTab === 'scouts'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($scouts as $scout)
                <div class="bg-[#1a0b2e]/60 backdrop-blur-md border border-white/10 rounded-2xl p-5 hover:border-white/20 transition group relative overflow-hidden">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="relative">
                            <img src="{{ $scout->photo_url ?? asset('images/default-avatar.png') }}" alt="{{ $scout->full_name }}" class="w-16 h-16 rounded-full object-cover border-2 border-white/10 group-hover:border-[#04f5ff] transition">
                            @if(!$scout->photo_url)
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-red-500 rounded-full border-2 border-[#1a0b2e]" title="No Image"></div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-white truncate">{{ $scout->full_name }}</div>
                            <div class="text-xs text-gray-500 font-mono">{{ $scout->scout_id }}</div>
                            @if($scout->patrol)
                                <div class="text-xs text-[#04f5ff] mt-0.5">{{ $scout->patrol->patrol_name }}</div>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('admin.scout-images.upload', $scout->scout_id) }}" method="POST" enctype="multipart/form-data" class="space-y-3 relative z-10">
                        @csrf
                        <div class="relative group/upload">
                            <div class="flex items-center justify-center w-full">
                                <label for="file-scout-{{ $scout->scout_id }}" class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-white/10 rounded-xl cursor-pointer bg-white/5 hover:bg-white/10 hover:border-[#04f5ff]/50 transition">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-6 h-6 mb-2 text-gray-400 group-hover/upload:text-[#04f5ff] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        <p class="text-[10px] text-gray-400 group-hover/upload:text-white transition">اضغط لاختيار صورة</p>
                                    </div>
                                    <input id="file-scout-{{ $scout->scout_id }}" name="photo" type="file" class="hidden" onchange="this.form.submit()" accept="image/*" />
                                </label>
                            </div>
                        </div>
                        
                         @if($scout->photo_url)
                            <button type="button" onclick="deleteScoutPhoto({{ $scout->scout_id }})" class="w-full py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-bold rounded-lg transition border border-red-500/20">
                                حذف الصورة الحالية
                            </button>
                        @endif
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Patrols Grid -->
    <div x-show="activeTab === 'patrols'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($patrols as $patrol)
                <div class="bg-[#1a0b2e]/60 backdrop-blur-md border border-white/10 rounded-2xl p-5 hover:border-white/20 transition relative overflow-hidden" style="border-top: 4px solid {{ $patrol->patrol_color }}">
                    <div class="flex items-center gap-4 mb-4">
                        <img src="{{ $patrol->patrol_logo_url ?? asset('images/default-patrol.png') }}" alt="{{ $patrol->patrol_name }}" class="w-16 h-16 object-contain">
                        <div>
                            <div class="font-bold text-white tracking-wider">{{ $patrol->patrol_name }}</div>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="w-3 h-3 rounded-full" style="background-color: {{ $patrol->patrol_color }}"></div>
                                <span class="text-xs text-gray-500 font-mono">{{ $patrol->patrol_color }}</span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.patrol-images.upload', $patrol->patrol_id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div class="relative group/upload">
                            <label for="file-patrol-{{ $patrol->patrol_id }}" class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-white/10 rounded-xl cursor-pointer bg-white/5 hover:bg-white/10 hover:border-{{ $patrol->patrol_color }}/50 transition" style="border-color: transparent;">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-6 h-6 mb-2 text-gray-400 group-hover/upload:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    <p class="text-[10px] text-gray-400">رفع شعار جديد</p>
                                </div>
                                <input id="file-patrol-{{ $patrol->patrol_id }}" name="logo" type="file" class="hidden" onchange="this.form.submit()" accept="image/*" />
                            </label>
                        </div>

                         @if($patrol->patrol_logo_url)
                            <button type="button" onclick="deletePatrolLogo({{ $patrol->patrol_id }})" class="w-full py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-bold rounded-lg transition border border-red-500/20">
                                حذف الشعار
                            </button>
                        @endif
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteScoutPhoto(scoutId) {
    if (!confirm('هل أنت متأكد من حذف صورة الكشاف؟')) return;
    fetch(`/admin/scout-images/${scoutId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    }).then(res => res.json()).then(() => window.location.reload()).catch(() => alert('Error deleting photo'));
}

function deletePatrolLogo(patrolId) {
    if (!confirm('هل أنت متأكد من حذف شعار الطليعة؟')) return;
    fetch(`/admin/patrol-images/${patrolId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    }).then(res => res.json()).then(() => window.location.reload()).catch(() => alert('Error deleting logo'));
}
</script>
@endpush
@endsection
