<div wire:poll.3s
     x-data="{
         handleKey(e) {
             // Ignore if inside input/select
             if (['INPUT', 'SELECT', 'TEXTAREA'].includes(e.target.tagName)) return;
             if (e.code === 'Space' || e.key === 'Enter') {
                 e.preventDefault();
                 $wire.next();
             } else if (e.key === 'r' || e.key === 'R') {
                 e.preventDefault();
                 $wire.recall();
             } else if (e.key === 's' || e.key === 'S') {
                 e.preventDefault();
                 $wire.skip();
             } else if (e.key === 'f' || e.key === 'F') {
                 e.preventDefault();
                 $wire.finish();
             }
         }
     }"
     @keydown.window="handleKey($event)"
     class="space-y-6">

    <!-- Top Bar: Counter Selector & Counter Status Toggle -->
    <div class="glass-panel p-6 rounded-2xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Counter Selection -->
        <div class="flex items-center space-x-4">
            <label class="text-sm font-semibold text-slate-400">Pilih Loket:</label>
            <div class="flex flex-wrap gap-2">
                @foreach($counters as $c)
                    <button type="button"
                            wire:click="setCounter({{ $c->id }})"
                            class="px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2 border {{ $selectedCounterId === $c->id ? 'bg-blue-600 text-white border-blue-400 shadow-lg shadow-blue-500/25' : 'bg-slate-900/60 text-slate-300 border-slate-800 hover:bg-slate-800' }}">
                        <span>{{ $c->name }}</span>
                        @if($c->status === 'active')
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        @elseif($c->status === 'break')
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Status Toggle Buttons (REQ-F-06) -->
        <div class="flex items-center space-x-2">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider mr-2">Status Loket:</span>
            <button type="button"
                    wire:click="updateStatus('active')"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition border {{ $counterStatus === 'active' ? 'bg-emerald-500 text-white border-emerald-400 shadow-lg shadow-emerald-500/30' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white' }}">
                AKTIF
            </button>
            <button type="button"
                    wire:click="updateStatus('break')"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition border {{ $counterStatus === 'break' ? 'bg-amber-500 text-white border-amber-400 shadow-lg shadow-amber-500/30' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white' }}">
                ISTIRAHAT
            </button>
            <button type="button"
                    wire:click="updateStatus('closed')"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition border {{ $counterStatus === 'closed' ? 'bg-rose-600 text-white border-rose-500 shadow-lg shadow-rose-500/30' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white' }}">
                TUTUP
            </button>
        </div>
    </div>

    <!-- Main Operator Workspace Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Active Calling Card & Big Control Buttons -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Calling Ticket Card -->
            <div class="glass-panel p-8 rounded-3xl border border-slate-800 relative overflow-hidden text-center flex flex-col items-center justify-center min-h-[280px]">
                @if($currentCounter && $currentCounter->currentTicket)
                    @php $ticket = $currentCounter->currentTicket; @endphp
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30 mb-2">
                        <span class="w-2 h-2 rounded-full bg-blue-400 animate-ping"></span>
                        Sedang Dilayani di {{ $currentCounter->name }}
                    </div>
                    <div class="text-sm font-semibold text-slate-400">{{ $ticket->service ? $ticket->service->name : 'Layanan' }}</div>
                    
                    <div class="my-4 text-7xl sm:text-8xl font-black text-white font-mono tracking-widest animate-call-glow px-8 py-2 rounded-3xl bg-slate-900/60 border border-blue-500/30">
                        {{ $ticket->ticket_number }}
                    </div>

                    <div class="text-xs text-slate-400 font-medium">
                        Dipanggil sejak: <span class="text-cyan-400 font-mono font-bold">{{ $ticket->called_at ? $ticket->called_at->format('H:i:s') : '-' }}</span>
                    </div>
                @else
                    <div class="text-slate-500 flex flex-col items-center py-6">
                        <div class="w-16 h-16 rounded-2xl bg-slate-900 flex items-center justify-center text-slate-600 mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="text-lg font-bold text-slate-300">Belum Ada Nomor yang Dipanggil</div>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm">Tekan tombol <strong>PANGGIL BERIKUTNYA</strong> atau gunakan tombol <strong>[SPACE]</strong> pada keyboard.</p>
                    </div>
                @endif
            </div>

            <!-- Operator Action Buttons Grid (REQ-F-04) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <!-- NEXT Button -->
                <button type="button"
                        wire:click="next"
                        class="col-span-2 py-5 px-6 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black text-lg shadow-xl shadow-blue-600/30 hover:brightness-110 active:scale-98 transition flex items-center justify-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                    </svg>
                    <div class="text-left">
                        <div class="leading-none">PANGGIL BERIKUTNYA</div>
                        <div class="text-[10px] font-normal text-blue-200 mt-1">[Space / Enter]</div>
                    </div>
                </button>

                <!-- RECALL Button -->
                <button type="button"
                        wire:click="recall"
                        @if(!$currentCounter || !$currentCounter->current_ticket_id) disabled @endif
                        class="py-4 px-4 rounded-2xl bg-amber-500/20 text-amber-300 border border-amber-500/40 hover:bg-amber-500/30 active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed font-bold text-sm transition flex flex-col items-center justify-center">
                    <svg class="w-5 h-5 mb-1 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Panggil Ulang</span>
                    <span class="text-[10px] text-amber-400/70 font-mono mt-0.5">[R]</span>
                </button>

                <!-- FINISH Button -->
                <button type="button"
                        wire:click="finish"
                        @if(!$currentCounter || !$currentCounter->current_ticket_id) disabled @endif
                        class="py-4 px-4 rounded-2xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-500/30 active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed font-bold text-sm transition flex flex-col items-center justify-center">
                    <svg class="w-5 h-5 mb-1 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Selesai</span>
                    <span class="text-[10px] text-emerald-400/70 font-mono mt-0.5">[F]</span>
                </button>
            </div>

            <!-- Secondary Action Row: Skip & Transfer -->
            <div class="grid grid-cols-2 gap-4">
                <button type="button"
                        wire:click="skip"
                        @if(!$currentCounter || !$currentCounter->current_ticket_id) disabled @endif
                        class="py-3 px-4 rounded-xl bg-slate-900 text-rose-400 border border-rose-500/30 hover:bg-rose-500/10 active:scale-98 disabled:opacity-40 disabled:cursor-not-allowed font-semibold text-xs transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                    </svg>
                    <span>Lewati Antrean [S]</span>
                </button>

                <button type="button"
                        wire:click="openTransferModal"
                        @if(!$currentCounter || !$currentCounter->current_ticket_id) disabled @endif
                        class="py-3 px-4 rounded-xl bg-slate-900 text-purple-400 border border-purple-500/30 hover:bg-purple-500/10 active:scale-98 disabled:opacity-40 disabled:cursor-not-allowed font-semibold text-xs transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <span>Transfer ke Layanan Lain</span>
                </button>
            </div>
        </div>

        <!-- Right Col: Waiting Queue & Today Stats -->
        <div class="space-y-6">
            
            <!-- Daily Stats Pill Widget -->
            <div class="grid grid-cols-2 gap-3">
                <div class="glass-panel p-4 rounded-2xl border border-slate-800">
                    <div class="text-xs text-slate-400 font-medium">Selesai Hari Ini</div>
                    <div class="text-2xl font-black text-emerald-400 font-mono mt-1">{{ $servedTodayCount }}</div>
                </div>
                <div class="glass-panel p-4 rounded-2xl border border-slate-800">
                    <div class="text-xs text-slate-400 font-medium">Dilewati</div>
                    <div class="text-2xl font-black text-rose-400 font-mono mt-1">{{ $skippedTodayCount }}</div>
                </div>
            </div>

            <!-- Waiting Queue List -->
            <div class="glass-panel rounded-3xl border border-slate-800 p-6 flex flex-col h-[400px]">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <h3 class="font-bold text-white text-sm">Antrean Menunggu</h3>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-500/20 text-blue-300">
                            {{ $waitingTickets->count() }}
                        </span>
                    </div>
                    <span class="text-xs text-slate-500">{{ $currentCounter && $currentCounter->service ? $currentCounter->service->name : 'Semua' }}</span>
                </div>

                <div class="flex-1 overflow-y-auto divide-y divide-slate-800/60 mt-2 space-y-1">
                    @forelse($waitingTickets as $index => $wt)
                        <div class="py-3 flex items-center justify-between {{ $index === 0 ? 'bg-blue-900/20 px-3 rounded-xl border border-blue-500/30' : '' }}">
                            <div class="flex items-center space-x-3">
                                <span class="w-6 h-6 rounded-full bg-slate-800 text-slate-400 font-mono text-xs flex items-center justify-center font-bold">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <div class="font-bold text-white font-mono text-base">{{ $wt->ticket_number }}</div>
                                    <div class="text-[10px] text-slate-400">Diambil: {{ $wt->created_at->format('H:i:s') }}</div>
                                </div>
                            </div>
                            @if($index === 0)
                                <span class="text-[10px] font-bold text-cyan-400 bg-cyan-500/10 px-2 py-0.5 rounded-md border border-cyan-500/30">
                                    Berikutnya
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12 text-slate-500 text-xs">
                            Tidak ada antrean yang sedang menunggu.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <!-- Transfer Modal -->
    @if($showTransferModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in"
             @keydown.escape.window="$wire.closeTransferModal()">
            <div class="glass-panel max-w-md w-full rounded-2xl p-6 border border-slate-700 shadow-2xl">
                <h3 class="text-lg font-bold text-white mb-2">Transfer Nomor Antrean</h3>
                <p class="text-xs text-slate-400 mb-4">Pindahkan antrean ini ke antrean layanan lainnya:</p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Pilih Layanan Tujuan</label>
                        <select wire:model="transferServiceId" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500">
                            <option value="">-- Pilih Layanan --</option>
                            @foreach($services as $srv)
                                @if(!$currentCounter || $srv->id !== $currentCounter->service_id)
                                    <option value="{{ $srv->id }}">{{ $srv->code }} - {{ $srv->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-800">
                        <button type="button" wire:click="closeTransferModal" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:bg-slate-800 transition">
                            Batal
                        </button>
                        <button type="button" wire:click="executeTransfer" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold shadow-lg transition">
                            Konfirmasi Transfer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
