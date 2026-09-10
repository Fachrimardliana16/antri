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
     class="h-screen flex flex-col bg-slate-900 overflow-hidden">

    {{-- Header --}}
    <div class="bg-slate-800 border-b border-slate-700 px-8 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                @php
                    $logoUrl = \App\Models\AppSetting::getValue('logo_url', '');
                    $appName = \App\Models\AppSetting::getValue('app_name', 'Sistem Antrian Terpadu');
                @endphp

                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo" class="w-full h-full object-contain">
                    @else
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    @endif
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white">{{ $appName }}</h1>
                    <p class="text-blue-400 text-sm">Dashboard Operator</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-right">
                    <div class="text-sm text-slate-400">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-slate-500">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 text-sm rounded-lg transition border border-slate-600">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="mx-8 mt-4 px-4 py-3 bg-green-500/20 border border-green-500 rounded-lg text-green-300 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mx-8 mt-4 px-4 py-3 bg-red-500/20 border border-red-500 rounded-lg text-red-300 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Main Content --}}
    <div class="flex-1 overflow-auto p-8">
        <div class="max-w-7xl mx-auto space-y-6">

            {{-- Counter Info Bar --}}
            <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-slate-400">Loket Anda:</span>
                    @if($currentCounter)
                        <div class="px-4 py-2 rounded-lg bg-blue-600 text-white font-bold flex items-center gap-3">
                            <span class="text-lg">{{ $currentCounter->name }}</span>
                            @if($currentCounter->service)
                                <span class="text-sm font-normal opacity-80">• {{ $currentCounter->service->name }}</span>
                            @endif
                        </div>
                    @else
                        <div class="px-4 py-2 rounded-lg bg-red-500/20 text-red-400 border border-red-500">
                            Belum ditugaskan ke loket
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium text-slate-500 uppercase">Status:</span>
                    <button type="button" wire:click="updateStatus('active')"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $counterStatus === 'active' ? 'bg-green-600 text-white' : 'bg-slate-700 text-green-400 hover:bg-slate-600' }}">
                        Aktif
                    </button>
                    <button type="button" wire:click="updateStatus('break')"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $counterStatus === 'break' ? 'bg-amber-500 text-white' : 'bg-slate-700 text-amber-400 hover:bg-slate-600' }}">
                        Istirahat
                    </button>
                    <button type="button" wire:click="updateStatus('closed')"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $counterStatus === 'closed' ? 'bg-red-600 text-white' : 'bg-slate-700 text-red-400 hover:bg-slate-600' }}">
                        Tutup
                    </button>
                </div>
            </div>

            {{-- Grid Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left: Current Ticket + Actions --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Current Ticket Card --}}
                    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-8 text-center">
                        @if($currentCounter && $currentCounter->currentTicket)
                            @php $ticket = $currentCounter->currentTicket; @endphp
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-400 text-xs font-bold mb-3">
                                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                                Sedang Dilayani
                            </div>
                            <div class="text-sm text-slate-400 mb-2">{{ $ticket->service ? $ticket->service->name : 'Layanan' }}</div>
                            <div class="text-9xl font-black font-mono text-blue-400 my-4">
                                {{ $ticket->ticket_number }}
                            </div>
                            <div class="text-xs text-slate-500">
                                Dipanggil: <span class="font-mono text-slate-400">{{ $ticket->called_at ? $ticket->called_at->format('H:i:s') : '-' }}</span>
                            </div>
                        @else
                            <div class="py-12 text-slate-500">
                                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-lg font-semibold">Belum Ada Nomor yang Dipanggil</p>
                                <p class="text-sm text-slate-600 mt-2">Tekan <kbd class="px-2 py-1 bg-slate-700 rounded border border-slate-600 text-slate-400 font-mono">Space</kbd> untuk panggil berikutnya</p>
                            </div>
                        @endif
                    </div>

                    {{-- Action Buttons Container - Same width as card above --}}
                    <div class="space-y-3">
                        {{-- Main Actions Row: Next | Recall | Finish - 1 baris horizontal --}}
                        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-3">
                            <div class="grid grid-cols-10 gap-2">
                                {{-- NEXT - 30% (3 cols) --}}
                                <button type="button" wire:click="next"
                                        class="col-span-3 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm transition flex flex-col items-center justify-center gap-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                    </svg>
                                    <span class="text-xs">PANGGIL BERIKUTNYA</span>
                                    <span class="text-xs opacity-70">[Space]</span>
                                </button>

                                {{-- RECALL - 50% (5 cols) --}}
                                <button type="button" wire:click="recall"
                                        @if(!$currentCounter || !$currentCounter->current_ticket_id) disabled @endif
                                        class="col-span-5 py-4 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold text-sm transition disabled:opacity-40 disabled:cursor-not-allowed flex flex-col items-center justify-center gap-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <span class="text-xs">PANGGIL ULANG</span>
                                    <span class="text-xs opacity-70">[R]</span>
                                </button>

                                {{-- FINISH - 20% (2 cols) --}}
                                <button type="button" wire:click="finish"
                                        @if(!$currentCounter || !$currentCounter->current_ticket_id) disabled @endif
                                        class="col-span-2 py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-sm transition disabled:opacity-40 disabled:cursor-not-allowed flex flex-col items-center justify-center gap-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-xs">SELESAI</span>
                                    <span class="text-xs opacity-70">[F]</span>
                                </button>
                            </div>
                        </div>

                        {{-- Secondary Actions Row: Skip | Transfer --}}
                        <div class="grid grid-cols-2 gap-3">
                            {{-- SKIP --}}
                            <button type="button" wire:click="skip"
                                    @if(!$currentCounter || !$currentCounter->current_ticket_id) disabled @endif
                                    class="py-3 bg-red-600/80 hover:bg-red-600 text-white rounded-xl font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                </svg>
                                Lewati [S]
                            </button>

                            {{-- TRANSFER --}}
                            <button type="button" wire:click="openTransferModal"
                                    @if(!$currentCounter || !$currentCounter->current_ticket_id) disabled @endif
                                    class="py-3 bg-purple-600/80 hover:bg-purple-600 text-white rounded-xl font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                Transfer
                            </button>
                        </div>
                    </div>

                </div>

                {{-- Right: Stats + Queue --}}
                <div class="space-y-4">

                    {{-- Stats Cards --}}
                    <div class="space-y-3">
                        {{-- Current Ticket --}}
                        <div class="bg-slate-800 border-l-4 border-blue-500 rounded-lg p-4">
                            <div class="text-xs text-slate-400 font-semibold mb-1">Sedang Dilayani</div>
                            @if($currentCounter && $currentCounter->currentTicket)
                                <div class="text-3xl font-black text-blue-400 font-mono">{{ $currentCounter->currentTicket->ticket_number }}</div>
                                <div class="text-xs text-slate-500 mt-1">Sejak {{ $currentCounter->currentTicket->called_at ? $currentCounter->currentTicket->called_at->format('H:i') : '-' }}</div>
                            @else
                                <div class="text-2xl font-bold text-slate-600">—</div>
                            @endif
                        </div>

                        {{-- Next in Queue --}}
                        <div class="bg-slate-800 border-l-4 border-amber-500 rounded-lg p-4">
                            <div class="text-xs text-slate-400 font-semibold mb-1">Antrian Berikutnya</div>
                            @if($waitingTickets->isNotEmpty())
                                <div class="text-3xl font-black text-amber-400 font-mono">{{ $waitingTickets->first()->ticket_number }}</div>
                                <div class="text-xs text-slate-500 mt-1">{{ $waitingTickets->count() }} menunggu</div>
                            @else
                                <div class="text-2xl font-bold text-slate-600">—</div>
                                <div class="text-xs text-slate-500 mt-1">Tidak ada</div>
                            @endif
                        </div>
                    </div>

                    {{-- Waiting Queue List --}}
                    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-700 flex items-center justify-between bg-slate-750">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-white">Antrean Menunggu</h3>
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-500/20 text-blue-400">
                                    {{ $waitingTickets->count() }}
                                </span>
                            </div>
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            @forelse($waitingTickets->take(10) as $index => $wt)
                                <div class="px-4 py-3 flex items-center justify-between border-b border-slate-700/50 {{ $index === 0 ? 'bg-blue-500/10' : '' }}">
                                    <div class="flex items-center gap-3">
                                        <span class="w-7 h-7 rounded-full bg-slate-700 text-slate-400 font-mono text-xs flex items-center justify-center font-bold">
                                            {{ $index + 1 }}
                                        </span>
                                        <div>
                                            <div class="font-bold text-white font-mono">{{ $wt->ticket_number }}</div>
                                            <div class="text-xs text-slate-500">{{ $wt->created_at->format('H:i') }}</div>
                                        </div>
                                    </div>
                                    @if($index === 0)
                                        <span class="text-xs font-bold text-blue-400 uppercase">Next</span>
                                    @endif
                                </div>
                            @empty
                                <div class="px-4 py-8 text-center text-slate-500">
                                    <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-sm font-semibold">Tidak ada antrian</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    {{-- Transfer Modal --}}
    @if($showTransferModal)
        <div class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-8" wire:click="closeTransferModal">
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 max-w-md w-full" @click.stop>
                <h3 class="text-xl font-bold text-white mb-4">Transfer Antrean</h3>
                <p class="text-sm text-slate-400 mb-4">Pilih layanan tujuan untuk mentransfer antrean saat ini</p>

                <div class="space-y-2 mb-6">
                    @foreach($services as $svc)
                        <button type="button"
                                wire:click="$set('transferServiceId', {{ $svc->id }})"
                                class="w-full px-4 py-3 rounded-lg text-left transition border {{ $transferServiceId === $svc->id ? 'bg-blue-600 border-blue-500 text-white' : 'bg-slate-700 border-slate-600 text-slate-300 hover:bg-slate-600' }}">
                            <div class="font-bold">{{ $svc->name }}</div>
                            <div class="text-xs opacity-80">{{ $svc->description }}</div>
                        </button>
                    @endforeach
                </div>

                <div class="flex gap-3">
                    <button wire:click="closeTransferModal"
                            class="flex-1 py-3 bg-slate-700 hover:bg-slate-600 text-slate-300 rounded-lg font-semibold transition">
                        Batal
                    </button>
                    <button wire:click="executeTransfer"
                            class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                        Transfer
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
