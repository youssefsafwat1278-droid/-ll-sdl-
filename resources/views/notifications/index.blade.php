@extends('layouts.app')

@section('title', 'الإشعارات - Scout Tanzania')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold flex items-center gap-3" :class="darkMode ? 'text-white' : 'text-gray-900'">
                <span>🔔</span>
                الإشعارات
            </h1>
        </div>

        <div class="space-y-4">
            @forelse($notifications as $notification)
                <div class="backdrop-blur-md border rounded-2xl p-6 transition-all duration-300 hover:shadow-lg relative overflow-hidden group"
                     :class="[
                        darkMode ? 'bg-[#1a0b2e]/60 border-white/10' : 'bg-white border-gray-200',
                        !{{ $notification->is_read }} ? (darkMode ? 'bg-white/10' : 'bg-blue-50') : ''
                     ]">
                    
                    @if(!$notification->is_read)
                        <div class="absolute top-0 right-0 w-2 h-full bg-[#00ff85]"></div>
                    @endif

                    <div class="flex flex-col md:flex-row gap-4 justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold mb-2 group-hover:text-[#04f5ff] transition-colors"
                                :class="darkMode ? 'text-white' : 'text-gray-900'">
                                {{ $notification->title }}
                            </h3>
                            <p class="mb-4 leading-relaxed"
                               :class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                                {{ $notification->message }}
                            </p>
                            <div class="flex items-center gap-4 text-sm" :class="darkMode ? 'text-gray-500' : 'text-gray-400'">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        @if(!$notification->is_read)
                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="px-4 py-2 rounded-xl text-sm font-bold transition-colors flex items-center gap-2"
                                        :class="darkMode ? 'bg-white/10 hover:bg-white/20 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-900'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    تحديد كمقروء
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-16 rounded-3xl border border-dashed"
                     :class="darkMode ? 'border-white/10' : 'border-gray-200'">
                    <div class="w-20 h-20 mx-auto bg-gray-500/10 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2" :class="darkMode ? 'text-white' : 'text-gray-900'">لا توجد إشعارات</h3>
                    <p :class="darkMode ? 'text-gray-500' : 'text-gray-400'">ليس لديك أي إشعارات في الوقت الحالي</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
