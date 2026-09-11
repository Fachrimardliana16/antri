<div class="space-y-5">
    {{-- Header & Period Filter --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold" style="color: var(--text-primary);">Analitik Pelayanan & Laporan SLA</h1>
            <p class="text-sm mt-0.5" style="color: var(--text-muted);">Metrik performa antrean, waktu tunggu (AWT), waktu layanan (AST), dan produktivitas loket.</p>
        </div>

        <div class="flex items-center border rounded-lg overflow-hidden" style="border-color: var(--border-default); background-color: var(--bg-surface);">
            <button type="button" wire:click="setPeriod('today')"
                    class="px-3.5 py-2 text-xs font-medium transition {{ $period === 'today' ? 'text-white' : '' }}"
                    style="{{ $period === 'today' ? 'background-color: var(--primary); color: white;' : 'color: var(--text-secondary);' }}"
                    onmouseover="if (!'{{ $period === 'today' }}') this.style.backgroundColor='var(--bg-hover)'"
                    onmouseout="if (!'{{ $period === 'today' }}') this.style.backgroundColor=''">
                Hari Ini
            </button>
            <button type="button" wire:click="setPeriod('week')"
                    class="px-3.5 py-2 text-xs font-medium transition border-l {{ $period === 'week' ? 'text-white' : '' }}"
                    style="{{ $period === 'week' ? 'background-color: var(--primary); color: white;' : 'color: var(--text-secondary);' }} border-color: var(--border-default);"
                    onmouseover="if (!'{{ $period === 'week' }}') this.style.backgroundColor='var(--bg-hover)'"
                    onmouseout="if (!'{{ $period === 'week' }}') this.style.backgroundColor=''">
                7 Hari
            </button>
            <button type="button" wire:click="setPeriod('month')"
                    class="px-3.5 py-2 text-xs font-medium transition border-l {{ $period === 'month' ? 'text-white' : '' }}"
                    style="{{ $period === 'month' ? 'background-color: var(--primary); color: white;' : 'color: var(--text-secondary);' }} border-color: var(--border-default);"
                    onmouseover="if (!'{{ $period === 'month' }}') this.style.backgroundColor='var(--bg-hover)'"
                    onmouseout="if (!'{{ $period === 'month' }}') this.style.backgroundColor=''">
                30 Hari
            </button>
            <button type="button" wire:click="setPeriod('all')"
                    class="px-3.5 py-2 text-xs font-medium transition border-l {{ $period === 'all' ? 'text-white' : '' }}"
                    style="{{ $period === 'all' ? 'background-color: var(--primary); color: white;' : 'color: var(--text-secondary);' }} border-color: var(--border-default);"
                    onmouseover="if (!'{{ $period === 'all' }}') this.style.backgroundColor='var(--bg-hover)'"
                    onmouseout="if (!'{{ $period === 'all' }}') this.style.backgroundColor=''">
                Semua
            </button>
        </div>
    </div>

    {{-- 4 KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Tickets --}}
        <div class="gov-card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wide" style="color: var(--text-muted);">Total Tiket</span>
                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold font-mono" style="color: var(--text-primary);">{{ $totalTickets }}</div>
            <div class="text-xs mt-1" style="color: var(--text-muted);">{{ $completedTickets }} selesai dilayani</div>
        </div>

        {{-- AWT --}}
        <div class="gov-card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wide" style="color: var(--text-muted);">Rata-rata Tunggu (AWT)</span>
                <div class="w-9 h-9 rounded-lg bg-cyan-50 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold font-mono" style="color: var(--text-primary);">{{ $avgWaitFormatted }}</div>
            <div class="text-xs mt-1" style="color: var(--text-muted);">Sejak tiket diambil s/d dipanggil</div>
        </div>

        {{-- AST --}}
        <div class="gov-card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wide" style="color: var(--text-muted);">Rata-rata Layanan (AST)</span>
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold font-mono" style="color: var(--text-primary);">{{ $avgServiceFormatted }}</div>
            <div class="text-xs mt-1" style="color: var(--text-muted);">Durasi proses di loket</div>
        </div>

        {{-- Completion Rate --}}
        <div class="gov-card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wide" style="color: var(--text-muted);">Tingkat Penyelesaian</span>
                <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            @php $rate = $totalTickets > 0 ? round(($completedTickets / $totalTickets) * 100, 1) : 100; @endphp
            <div class="text-2xl font-bold font-mono" style="color: var(--text-primary);">{{ $rate }}%</div>
            <div class="text-xs mt-1" style="color: var(--text-muted);">{{ $skippedTickets }} antrean dilewati</div>
        </div>
    </div>

    {{-- Service & Operator Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Service Volume --}}
        <div class="gov-card p-5">
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-primary);">Distribusi Per Layanan</h3>
            <div class="divide-y" style="border-color: var(--border-light);">
                @foreach($services as $srv)
                    <div class="py-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-white text-xs"
                                 style="background-color: {{ $srv->color }};">
                                {{ $srv->code }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold" style="color: var(--text-primary);">{{ $srv->name }}</div>
                                <div class="text-xs" style="color: var(--text-muted);">Target SLA: {{ $srv->estimated_time_minutes }} Menit</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold font-mono" style="color: var(--text-primary);">{{ $srv->total_tickets_count }} Tiket</div>
                            <div class="text-xs text-green-600 font-medium">{{ $srv->completed_tickets_count }} Selesai</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Operator Performance --}}
        <div class="gov-card p-5">
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-primary);">Performa Petugas Operator</h3>
            <div class="divide-y" style="border-color: var(--border-light);">
                @forelse($operators as $op)
                    <div class="py-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs"
                                 style="background-color: var(--bg-hover); color: var(--text-secondary);">
                                {{ substr($op->name, 0, 2) }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold" style="color: var(--text-primary);">{{ $op->name }}</div>
                                <div class="text-xs" style="color: var(--text-muted);">{{ $op->email }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-5 text-right">
                            <div>
                                <div class="text-xs" style="color: var(--text-muted);">Dilayani</div>
                                <div class="text-sm font-bold text-green-600 font-mono">{{ $op->served_count }}</div>
                            </div>
                            <div>
                                <div class="text-xs" style="color: var(--text-muted);">Dilewati</div>
                                <div class="text-sm font-bold text-red-500 font-mono">{{ $op->skipped_count }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-xs" style="color: var(--text-muted);">Belum ada data operator.</div>
                @endforelse
            </div>
        </div>

    </div>
</div>
