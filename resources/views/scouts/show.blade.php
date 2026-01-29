<!-- resources/views/scouts/show.blade.php -->
@extends('layouts.app')

@section('title', $scout->full_name . ' - Scout Tanzania')

@section('content')
<div class="space-y-8 animate-fade-in pb-20 sm:pb-0">
    
    <!-- Scout Header -->
    <div class="relative bg-gradient-to-r from-purple-900 to-[#1a0b2e] rounded-3xl p-8 overflow-hidden border border-white/10 shadow-2xl group">
        <div class="absolute inset-0 bg-[#00ff85]/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-purple-600/20 rounded-full blur-[100px]"></div>
        
        <div class="flex flex-col md:flex-row items-center gap-8 relative z-10">
            <div class="relative">
                <img src="{{ $scout->photo_url ?? asset('images/default-avatar.png') }}" 
                     alt="{{ $scout->full_name }}" 
                     class="w-40 h-40 md:w-48 md:h-48 rounded-full border-4 border-white/20 shadow-[0_0_30px_rgba(0,0,0,0.5)] object-cover bg-[#12002b]">
                @if($scout->role === 'leader')
                    <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 bg-purple-600 text-white px-4 py-1 rounded-full text-sm font-black border-2 border-[#1a0b2e] shadow-lg whitespace-nowrap">قائد</div>
                @elseif($scout->role === 'senior')
                    <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 bg-[#04f5ff] text-[#1a0b2e] px-4 py-1 rounded-full text-sm font-black border-2 border-[#1a0b2e] shadow-lg whitespace-nowrap">رائد</div>
                @else
                    <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 bg-gray-600 text-white px-4 py-1 rounded-full text-sm font-black border-2 border-[#1a0b2e] shadow-lg whitespace-nowrap">كشاف</div>
                @endif
            </div>

            <div class="flex-1 text-center md:text-right">
                <div class="inline-block bg-white/10 text-gray-300 px-3 py-1 rounded-lg text-xs font-bold mb-3 border border-white/5">
                    {{ $scout->patrol->patrol_name ?? 'غير محدد' }}
                </div>
                <h2 class="text-4xl md:text-5xl font-black text-white mb-2 font-['Changa']">{{ $scout->full_name }}</h2>
                
                <div class="flex flex-wrap justify-center md:justify-start gap-3 mt-4">
                    @if(!$scout->is_available)
                        <span class="bg-red-500/20 text-red-400 border border-red-500/30 px-4 py-1.5 rounded-full text-sm font-bold flex items-center gap-2">
                            <span>🚫 غير متاح</span>
                            <span class="text-xs bg-black/20 px-2 py-0.5 rounded">
                                @if($scout->isLeaderOrSenior())
                                    {{ $scout->ownership_count }}/20
                                @else
                                    ممتلئ
                                @endif
                            </span>
                        </span>
                    @else
                         <span class="bg-[#00ff85]/20 text-[#00ff85] border border-[#00ff85]/30 px-4 py-1.5 rounded-full text-sm font-bold">
                             ✓ متاح للاختيار
                         </span>
                    @endif
                </div>
            </div>

            <!-- Price Badge -->
            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 text-center min-w-[120px]">
                <div class="text-gray-400 text-xs font-bold uppercase mb-1">السعر الحالي</div>
                <div class="text-4xl font-black text-[#04f5ff]">{{ $scout->current_price }}</div>
                @if($scout->price_change != 0)
                    <div class="{{ $scout->price_change > 0 ? 'text-[#00ff85]' : 'text-red-500' }} text-sm font-bold mt-1 bg-black/20 rounded px-2 py-0.5">
                        {{ $scout->price_change > 0 ? '↑' : '↓' }} {{ abs($scout->price_change) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl p-6 text-center shadow-lg group hover:bg-[#1a0b2e]/80 transition">
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">إجمالي النقاط</div>
            <div class="text-4xl font-black text-[#00ff85] group-hover:scale-110 transition-transform">{{ $scout->total_points }}</div>
        </div>

        <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl p-6 text-center shadow-lg group hover:bg-[#1a0b2e]/80 transition">
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">نقاط الجولة</div>
            <div class="text-4xl font-black text-white group-hover:scale-110 transition-transform">{{ $scout->gameweek_points }}</div>
        </div>

        <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl p-6 text-center shadow-lg group hover:bg-[#1a0b2e]/80 transition">
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">الشكل (Form)</div>
            <div class="text-4xl font-black text-purple-400 group-hover:scale-110 transition-transform">{{ $scout->form }}</div>
        </div>

        <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl p-6 text-center shadow-lg group hover:bg-[#1a0b2e]/80 transition">
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">نسبة الملكية</div>
            <div class="text-4xl font-black text-white group-hover:scale-110 transition-transform">{{ round($scout->ownership_percentage, 1) }}<span class="text-lg text-gray-500">%</span></div>
             @if(!$scout->isLeaderOrSenior())
                <div class="text-[10px] text-gray-500 mt-1">L: {{ $scout->local_ownership_count }} | E: {{ $scout->external_ownership_count }}</div>
             @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Performance Chart -->
        <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-xl">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <span>📈</span> الأداء في آخر 10 أسابيع
            </h3>
            <div class="relative h-64 w-full">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>

        <!-- History Table -->
        <div class="bg-[#1a0b2e]/60 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-xl overflow-hidden">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <span>📜</span> سجل النقاط
            </h3>
            <div class="overflow-y-auto max-h-64 custom-scrollbar">
                <table class="w-full text-right">
                    <thead class="bg-white/5 text-gray-400 text-xs uppercase sticky top-0 backdrop-blur-md">
                        <tr>
                            <th class="px-4 py-3">الأسبوع</th>
                            <th class="px-4 py-3">نقاط</th>
                            <th class="px-4 py-3">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($performances as $perf)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-3 text-white font-medium">{{ $perf->gameweek->name }}</td>
                                <td class="px-4 py-3 font-black text-[#00ff85]">{{ $perf->total_points }}</td>
                                <td class="px-4 py-3 text-sm text-gray-400">{{ $perf->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart Config Defaults
Chart.defaults.color = 'rgba(255, 255, 255, 0.7)';
Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';
Chart.defaults.font.family = 'Cairo';

// Performance Chart
const perfCtx = document.getElementById('performanceChart').getContext('2d');
new Chart(perfCtx, {
    type: 'line',
    data: {
        labels: @json($performances->pluck('gameweek.name')->reverse()),
        datasets: [{
            label: 'النقاط',
            data: @json($performances->pluck('total_points')->reverse()),
            borderColor: '#04f5ff',
            backgroundColor: (context) => {
                const ctx = context.chart.ctx;
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(4, 245, 255, 0.5)');
                gradient.addColorStop(1, 'rgba(4, 245, 255, 0)');
                return gradient;
            },
            pointBackgroundColor: '#1a0b2e',
            pointBorderColor: '#04f5ff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(26, 11, 46, 0.9)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: 'rgba(255,255,255,0.1)',
                borderWidth: 1,
                padding: 10,
                cornerRadius: 8,
                displayColors: false
            }
        },
        scales: {
            y: { 
                beginAtZero: true,
                grid: { color: 'rgba(255, 255, 255, 0.05)' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});
</script>
@endpush
@endsection
