<div class="space-y-6">
    <!-- Header & Period Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Analitik Pelayanan & Laporan SLA</h1>
            <p class="text-sm text-slate-400 mt-1">Metrik performa antrean, waktu tunggu (AWT), waktu layanan (AST), dan produktivitas loket.</p>
        </div>

        <div class="flex items-center space-x-1 bg-slate-900 p-1.5 rounded-xl border border-slate-800">
            <button type="button" wire:click="setPeriod('today')" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ $period === 'today' ? 'bg-blue-600 text-white shadow' : 'text-slate-400 hover:text-white' }}">
                Hari Ini
            </button>
            <button type="button" wire:click="setPeriod('week')" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ $period === 'week' ? 'bg-blue-600 text-white shadow' : 'text-slate-400 hover:text-white' }}">
                7 Hari Terakhir
            </button>
            <button type="button" wire:click="setPeriod('month')" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ $period === 'month' ? 'bg-blue-600 text-white shadow' : 'text-slate-400 hover:text-white' }}">
                30 Hari Terakhir
            </button>
            <button type="button" wire:click="setPeriod('all')" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ $period === 'all' ? 'bg-blue-600 text-white shadow' : 'text-slate-400 hover:text-white' }}">
                Semua Waktu
            </button>
        </div>
    </div>

    <!-- 4 Main KPI Cards (REQ-F-11) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total Tickets -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Tiket</span>
                <span class="p-2 rounded-xl bg-blue-500/20 text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-black text-white font-mono">{{ $totalTickets }}</div>
                <div class="text-xs text-slate-400 mt-1">{{ $completedTickets }} selesai dilayani</div>
            </div>
        </div>

        <!-- Average Wait Time (AWT) -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Rata-rata Waktu Tunggu (AWT)</span>
                <span class="p-2 rounded-xl bg-cyan-500/20 text-cyan-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-cyan-400 font-mono">{{ $avgWaitFormatted }}</div>
                <div class="text-xs text-slate-400 mt-1">Sejak tiket diambil s/d dipanggil</div>
            </div>
        </div>

        <!-- Average Service Time (AST) -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Rata-rata Waktu Layanan (AST)</span>
                <span class="p-2 rounded-xl bg-emerald-500/20 text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-emerald-400 font-mono">{{ $avgServiceFormatted }}</div>
                <div class="text-xs text-slate-400 mt-1">Durasi proses di loket</div>
            </div>
        </div>

        <!-- Completion Rate / SLA -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tingkat Penyelesaian</span>
                <span class="p-2 rounded-xl bg-purple-500/20 text-purple-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                @php $rate = $totalTickets > 0 ? round(($completedTickets / $totalTickets) * 100, 1) : 100; @endphp
                <div class="text-3xl font-black text-purple-400 font-mono">{{ $rate }}%</div>
                <div class="text-xs text-slate-400 mt-1">{{ $skippedTickets }} antrean dilewati</div>
            </div>
        </div>
    </div>

    <!-- Service Breakdown & Operator Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Service Volume Table -->
        <div class="glass-panel rounded-3xl p-6 border border-slate-800 space-y-4">
            <h3 class="text-base font-bold text-white">Distribusi Per Layanan</h3>
            <div class="divide-y divide-slate-800">
                @foreach($services as $srv)
                    <div class="py-3.5 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-white text-xs" style="background: {{ $srv->color }};">
                                {{ $srv->code }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-white">{{ $srv->name }}</div>
                                <div class="text-xs text-slate-400">Target SLA: {{ $srv->estimated_time_minutes }} Menit</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-base font-bold text-white font-mono">{{ $srv->total_tickets_count }} Tiket</div>
                            <div class="text-xs text-emerald-400 font-medium">{{ $srv->completed_tickets_count }} Selesai</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Operator Productivity Table -->
        <div class="glass-panel rounded-3xl p-6 border border-slate-800 space-y-4">
            <h3 class="text-base font-bold text-white">Performa Petugas Operator</h3>
            <div class="divide-y divide-slate-800">
                @forelse($operators as $op)
                    <div class="py-3.5 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-slate-800 text-slate-300 flex items-center justify-center font-bold text-xs">
                                {{ substr($op->name, 0, 2) }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-white">{{ $op->name }}</div>
                                <div class="text-xs text-slate-400">{{ $op->email }}</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-6 text-right">
                            <div>
                                <div class="text-xs text-slate-400">Dilayani</div>
                                <div class="text-sm font-bold text-emerald-400 font-mono">{{ $op->served_count }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400">Dilewati</div>
                                <div class="text-sm font-bold text-rose-400 font-mono">{{ $op->skipped_count }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-xs text-slate-500">
                        Belum ada data operator.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
