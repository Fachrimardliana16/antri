<div class="h-screen flex flex-col bg-gray-50 overflow-hidden">
    {{-- Header --}}
    <header class="bg-white border-b border-gray-200 shadow-sm flex-shrink-0">
        <div class="px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-slate-900 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-lg font-bold text-gray-900">Panel Operator</div>
                </div>
            </div>
            <div class="flex items-center gap-4">
                @auth
                    <div class="flex items-center gap-3 px-4 py-2 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="w-8 h-8 rounded-full bg-slate-900 flex items-center justify-center text-white text-sm font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Keluar</button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    @if(!$myCounter)
        <div class="flex-1 flex items-center justify-center">
            <div class="text-center max-w-md bg-white rounded-2xl p-12 shadow-lg">
                <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Loket Belum Ditugaskan</h3>
                <p class="text-gray-600">Hubungi administrator untuk mendapatkan penugasan loket.</p>
            </div>
        </div>
    @else
        <div class="flex-1 grid grid-cols-12 gap-4 p-4 overflow-hidden">
            {{-- Left 70% --}}
            <div class="col-span-8 flex flex-col gap-4">
                {{-- Counter Info + Status --}}
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-xl bg-slate-900 flex items-center justify-center shadow-lg">
                            <span class="text-3xl font-black text-white">{{ $myCounter->number }}</span>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $myCounter->name }}</h2>
                            <p class="text-sm text-gray-500">{{ $myCounter->service ? $myCounter->service->name : 'Layanan Umum' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($counterStatus === 'closed')
                            <button wire:click="openCounter" class="px-6 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-lg">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                    <span>Buka Loket</span>
                                </div>
                            </button>
                        @elseif($counterStatus === 'active')
                            <div class="flex items-center gap-2 px-4 py-2 bg-emerald-50 border-2 border-emerald-200 rounded-xl">
                                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                <span class="font-bold text-emerald-700">Aktif</span>
                            </div>
                            <button wire:click="takeBreak" class="px-4 py-2 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-600">Istirahat</button>
                            <button wire:click="closeCounter" class="px-4 py-2 bg-gray-600 text-white font-bold rounded-xl hover:bg-gray-700">Tutup</button>
                        @else
                            <div class="flex items-center gap-2 px-4 py-2 bg-amber-50 border-2 border-amber-200 rounded-xl">
                                <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                                <span class="font-bold text-amber-700">Istirahat</span>
                            </div>
                            <button wire:click="openCounter" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700">Aktifkan</button>
                        @endif
                    </div>
                </div>

                {{-- Current Ticket --}}
                <div class="flex-1 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col overflow-hidden">
                    @if($myCounter->currentTicket)
                        <div class="flex-1 flex flex-col items-center justify-center p-8">
                            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-3">Sedang Dilayani</div>
                            <div class="text-[140px] leading-none font-black text-slate-900 mb-6 font-mono">{{ $myCounter->currentTicket->ticket_number }}</div>
                            <div class="text-lg text-gray-500 mb-8">{{ $myCounter->currentTicket->service ? $myCounter->currentTicket->service->name : '' }}</div>
                            <div class="grid grid-cols-3 gap-4 w-full max-w-3xl">
                                <button wire:click="recallCurrent" class="py-6 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all shadow-lg">
                                    <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.933 12.8a1 1 0 000-1.6L6.6 7.2A1 1 0 005 8v8a1 1 0 001.6.8l5.333-4zM19.933 12.8a1 1 0 000-1.6l-5.333-4A1 1 0 0013 8v8a1 1 0 001.6.8l5.333-4z"/></svg>
                                    <span class="text-xl font-bold">Panggil Ulang</span>
                                </button>
                                <button wire:click="completeCurrent" class="py-6 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-all shadow-lg">
                                    <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-xl font-bold">Selesai</span>
                                </button>
                                <button wire:click="rejectCurrent" class="py-6 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all shadow-lg">
                                    <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-xl font-bold">Tolak</span>
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="flex-1 flex flex-col items-center justify-center p-8">
                            <svg class="w-24 h-24 text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            <div class="text-2xl font-bold text-gray-400 mb-3">Belum Ada Antrian</div>
                            <p class="text-gray-500 mb-8">Silakan panggil antrian berikutnya</p>
                            @if($counterStatus === 'active')
                                <button wire:click="callNext" class="px-12 py-6 bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-all shadow-2xl">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span class="text-2xl font-black">PANGGIL BERIKUTNYA</span>
                                    </div>
                                </button>
                            @else
                                <div class="text-gray-400">Buka loket terlebih dahulu</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right 30% --}}
            <div class="col-span-4 flex flex-col gap-4">
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-6 text-white shadow-lg">
                    <h3 class="text-sm font-bold uppercase tracking-wider opacity-75 mb-3">Selanjutnya</h3>
                    @if($nextTicket)
                        <div class="text-center py-4">
                            <div class="text-6xl font-black mb-2 font-mono">{{ $nextTicket->ticket_number }}</div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="text-4xl font-black mb-2 opacity-30">—</div>
                        </div>
                    @endif
                    <div class="pt-4 border-t border-white/20 flex items-center justify-between">
                        <span class="text-sm font-semibold opacity-75">Menunggu</span>
                        <span class="text-2xl font-black">{{ $waitingCount }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 flex-1">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Hari Ini</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Dilayani</span>
                            <span class="text-2xl font-black text-emerald-600">{{ $todayServed }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Ditolak</span>
                            <span class="text-2xl font-black text-red-600">{{ $todayRejected }}</span>
                        </div>
                        <div class="pt-4 border-t border-gray-200 flex items-center justify-between">
                            <span class="text-sm font-bold text-gray-700">Total</span>
                            <span class="text-3xl font-black text-slate-900">{{ $todayServed + $todayRejected }}</span>
                        </div>
                    </div>
                </div>

                @if($myCounter->currentTicket && $counterStatus === 'active')
                    <button wire:click="callNext" class="w-full py-4 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition-all shadow-lg">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-lg">Panggil Berikutnya</span>
                        </div>
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>