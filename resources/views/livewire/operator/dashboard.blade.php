<div wire:poll.3s
     x-data="{
         handleKey(e) {
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
     class="space-y-5">

    {{-- Top Bar: Counter & Status --}}
    <div class="gov-card p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        {{-- Counter Display (No Switching for Operators) --}}
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-gray-600">Loket:</span>
            <div class="flex items-center gap-2">
                @if($currentCounter)
                    <div class="px-4 py-2 rounded-lg text-base font-bold border-2 border-blue-600 bg-blue-50 text-blue-900 flex items-center gap-2">
                        {{ $currentCounter->name }}
                        @if($currentCounter->service)
                            <span class="text-xs font-normal text-blue-600">• {{ $currentCounter->service->name }}</span>
                        @endif
                        @if($currentCounter->status === 'active')
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        @elseif($currentCounter->status === 'break')
                            <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                        @endif
                    </div>
                @else
                    <div class="px-4 py-2 rounded-lg text-sm bg-red-50 text-red-700 border border-red-200">
                        Belum ditugaskan ke loket
                    </div>
                @endif
            </div>
        </div>

        {{-- Status Toggle --}}
        <div class="flex items-center gap-2">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status:</span>
            <button type="button" wire:click="updateStatus('active')"
                    class="px-3 py-1.5 rounded-md text-xs font-semibold transition border
                           {{ $counterStatus === 'active' ? 'bg-green-600 text-white border-green-600' : 'text-green-700 bg-green-50 border-green-200 hover:bg-green-100' }}">
                Aktif
            </button>
            <button type="button" wire:click="updateStatus('break')"
                    class="px-3 py-1.5 rounded-md text-xs font-semibold transition border
                           {{ $counterStatus === 'break' ? 'bg-amber-500 text-white border-amber-500' : 'text-amber-700 bg-amber-50 border-amber-200 hover:bg-amber-100' }}">
                Istirahat
            </button>
            <button type="button" wire:click="updateStatus('closed')"
                    class="px-3 py-1.5 rounded-md text-xs font-semibold transition border
                           {{ $counterStatus === 'closed' ? 'bg-red-600 text-white border-red-600' : 'text-red-700 bg-red-50 border-red-200 hover:bg-red-100' }}">
                Tutup
            </button>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Left 2 Cols --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Active Calling Card --}}
            <div class="gov-card p-8 text-center relative overflow-hidden"
                 style="border-top: 3px solid var(--primary, #1a56a8);">
                @if($currentCounter && $currentCounter->currentTicket)
                    @php $ticket = $currentCounter->currentTicket; @endphp
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 mb-3">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-ping" style="animation-duration: 1.5s;"></span>
                        Sedang Dilayani di {{ $currentCounter->name }}
                    </div>
                    <div class="text-sm text-gray-500 mb-2">{{ $ticket->service ? $ticket->service->name : 'Layanan' }}</div>

                    <div class="text-7xl sm:text-8xl font-black font-mono tracking-widest text-gray-900 my-4 animate-call-pulse inline-block px-6 py-2 rounded-xl"
                         style="color: var(--primary, #1a56a8);">
                        {{ $ticket->ticket_number }}
                    </div>

                    <div class="text-xs text-gray-400 mt-2">
                        Dipanggil sejak: <span class="font-mono font-semibold text-gray-600">{{ $ticket->called_at ? $ticket->called_at->format('H:i:s') : '-' }}</span>
                    </div>
                @else
                    <div class="py-8 text-gray-400">
                        <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-base font-semibold text-gray-500">Belum Ada Nomor yang Dipanggil</p>
                        <p class="text-xs text-gray-400 mt-1">Tekan <strong>PANGGIL BERIKUTNYA</strong> atau tekan <kbd class="font-mono bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200 text-gray-700">Space</kbd></p>
                    </div>
                @endif
            </div>

            {{-- Action Buttons --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                {{-- NEXT --}}
                <button type="button" wire:click="next"
                        class="col-span-2 py-4 px-6 rounded-lg font-bold text-base text-white transition flex items-center justify-center gap-3 hover:opacity-90 active:scale-98"
                        style="background-color: var(--primary, #1a56a8);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                    </svg>
                    <div class="text-left">
                        <div class="leading-none">PANGGIL BERIKUTNYA</div>
                        <div class="text-xs font-normal text-blue-200 mt-0.5">[Space / Enter]</div>
                    </div>
                </button>

                {{-- RECALL --}}
                <button type="button" wire:click="recall"
                        @if(!$currentCounter || !$currentCounter->current_ticket_id) disabled @endif
                        class="py-3.5 px-4 rounded-lg font-semibold text-sm text-amber-700 bg-amber-50 border border-amber-200 hover:bg-amber-100 transition disabled:opacity-40 disabled:cursor-not-allowed flex flex-col items-center justify-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Panggil Ulang</span>
                    <span class="text-[10px] font-mono text-amber-500">[R]</span>
                </button>

                {{-- FINISH --}}
                <button type="button" wire:click="finish"
                        @if(!$currentCounter || !$currentCounter->current_ticket_id) disabled @endif
                        class="py-3.5 px-4 rounded-lg font-semibold text-sm text-green-700 bg-green-50 border border-green-200 hover:bg-green-100 transition disabled:opacity-40 disabled:cursor-not-allowed flex flex-col items-center justify-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Selesai</span>
                    <span class="text-[10px] font-mono text-green-500">[F]</span>
                </button>
            </div>

            {{-- Secondary Actions --}}
            <div class="grid grid-cols-2 gap-3">
                <button type="button" wire:click="skip"
                        @if(!$currentCounter || !$currentCounter->current_ticket_id) disabled @endif
                        class="py-2.5 px-4 rounded-lg text-sm font-medium text-red-600 bg-red-50 border border-red-100 hover:bg-red-100 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                    </svg>
                    Lewati Antrean [S]
                </button>
                <button type="button" wire:click="openTransferModal"
                        @if(!$currentCounter || !$currentCounter->current_ticket_id) disabled @endif
                        class="py-2.5 px-4 rounded-lg text-sm font-medium text-purple-600 bg-purple-50 border border-purple-100 hover:bg-purple-100 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Transfer Layanan
                </button>
            </div>
        </div>

        {{-- Right Col: Stats & Queue --}}
        <div class="space-y-4">

            {{-- Daily Stats --}}
            <div class="grid grid-cols-1 gap-3">
                {{-- Current Ticket --}}
                <div class="gov-card p-4 border-l-4 border-blue-600">
                    <div class="text-xs text-gray-500 font-medium mb-1">Sedang Dilayani</div>
                    @if($currentCounter && $currentCounter->currentTicket)
                        <div class="text-3xl font-black text-blue-600 font-mono">{{ $currentCounter->currentTicket->ticket_number }}</div>
                        <div class="text-xs text-gray-400 mt-1">Sejak {{ $currentCounter->currentTicket->called_at ? $currentCounter->currentTicket->called_at->format('H:i') : '-' }}</div>
                    @else
                        <div class="text-2xl font-bold text-gray-400">—</div>
                    @endif
                </div>

                {{-- Next in Queue --}}
                <div class="gov-card p-4 border-l-4 border-amber-500">
                    <div class="text-xs text-gray-500 font-medium mb-1">Antrian Berikutnya</div>
                    @if($waitingTickets->isNotEmpty())
                        <div class="text-3xl font-black text-amber-600 font-mono">{{ $waitingTickets->first()->ticket_number }}</div>
                        <div class="text-xs text-gray-400 mt-1">{{ $waitingTickets->count() }} menunggu</div>
                    @else
                        <div class="text-2xl font-bold text-gray-400">—</div>
                        <div class="text-xs text-gray-400 mt-1">Tidak ada</div>
                    @endif
                </div>

                {{-- Completed Today --}}
                <div class="gov-card p-4 border-l-4 border-green-500">
                    <div class="text-xs text-gray-500 font-medium mb-1">Selesai Hari Ini</div>
                    <div class="text-3xl font-black text-green-600 font-mono">{{ $servedTodayCount }}</div>
                    <div class="text-xs text-gray-400 mt-1">tiket</div>
                </div>

                {{-- Skipped --}}
                <div class="gov-card p-4 border-l-4 border-red-500">
                    <div class="text-xs text-gray-500 font-medium mb-1">Dilewati</div>
                    <div class="text-3xl font-black text-red-500 font-mono">{{ $skippedTodayCount }}</div>
                    <div class="text-xs text-gray-400 mt-1">tiket</div>
                </div>
            </div>

            {{-- Waiting Queue --}}
            <div class="gov-card flex flex-col" style="max-height: 440px;">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-semibold text-gray-800">Antrean Menunggu</h3>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                            {{ $waitingTickets->count() }}
                        </span>
                    </div>
                    <span class="text-xs text-gray-400">{{ $currentCounter && $currentCounter->service ? $currentCounter->service->name : 'Semua' }}</span>
                </div>

                <div class="flex-1 overflow-y-auto divide-y divide-gray-50">
                    @forelse($waitingTickets as $index => $wt)
                        <div class="px-5 py-3 flex items-center justify-between {{ $index === 0 ? 'bg-blue-50' : '' }}">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 font-mono text-xs flex items-center justify-center font-bold">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <div class="font-bold text-gray-900 font-mono">{{ $wt->ticket_number }}</div>
                                    <div class="text-xs text-gray-400">{{ $wt->created_at->format('H:i:s') }}</div>
                                </div>
                            </div>
                            @if($index === 0)
                                <span class="text-xs font-semibold text-blue-600 bg-blue-100 px-2 py-0.5 rounded-md">Berikutnya</span>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12 text-xs text-gray-400">
                            Tidak ada antrean yang menunggu.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- Transfer Modal --}}
    @if($showTransferModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 animate-fade-in"
             @keydown.escape.window="$wire.closeTransferModal()">
            <div class="gov-panel max-w-md w-full p-6">
                <h3 class="text-base font-bold text-gray-900 mb-1">Transfer Nomor Antrean</h3>
                <p class="text-sm text-gray-500 mb-4">Pindahkan antrean ini ke layanan lainnya:</p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih Layanan Tujuan</label>
                        <select wire:model="transferServiceId"
                                class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">-- Pilih Layanan --</option>
                            @foreach($services as $srv)
                                @if(!$currentCounter || $srv->id !== $currentCounter->service_id)
                                    <option value="{{ $srv->id }}">{{ $srv->code }} - {{ $srv->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" wire:click="closeTransferModal"
                                class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button type="button" wire:click="executeTransfer"
                                class="px-5 py-2 rounded-lg text-sm font-semibold text-white transition hover:opacity-90"
                                style="background-color: #7c3aed;">
                            Konfirmasi Transfer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
