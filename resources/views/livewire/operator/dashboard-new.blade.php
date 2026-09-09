<div class="h-full flex flex-col p-6 gap-6">
    {{-- Counter Info Card --}}
    @if(!$myCounter)
        <div class="flex-1 flex items-center justify-center">
            <div class="text-center max-w-md">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Loket Belum Ditugaskan</h3>
                <p class="text-sm text-gray-600">Anda belum ditugaskan ke loket manapun. Silakan hubungi administrator untuk mendapatkan penugasan loket.</p>
            </div>
        </div>
    @else
        {{-- Loket Status & Control --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-xl bg-slate-900 flex items-center justify-center">
                        <span class="text-2xl font-bold text-white">{{ $myCounter->number }}</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $myCounter->name }}</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $myCounter->service ? $myCounter->service->name : 'Layanan Umum' }}
                        </p>
                    </div>
                </div>

                {{-- Status Control --}}
                <div class="flex items-center gap-3">
                    @if($counterStatus === 'closed')
                        <button wire:click="openCounter" 
                                class="px-6 py-3 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                            </svg>
                            Buka Loket
                        </button>
                    @elseif($counterStatus === 'active')
                        <div class="flex items-center gap-2 px-4 py-2 bg-emerald-50 border border-emerald-200 rounded-lg">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-sm font-semibold text-emerald-700">Loket Aktif</span>
                        </div>
                        <button wire:click="takeBreak" 
                                class="px-4 py-2 bg-amber-500 text-white font-medium rounded-lg hover:bg-amber-600 transition-colors">
                            Istirahat
                        </button>
                        <button wire:click="closeCounter" 
                                class="px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                            Tutup Loket
                        </button>
                    @else
                        <div class="flex items-center gap-2 px-4 py-2 bg-amber-50 border border-amber-200 rounded-lg">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span class="text-sm font-semibold text-amber-700">Istirahat</span>
                        </div>
                        <button wire:click="openCounter" 
                                class="px-4 py-2 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                            Aktifkan Kembali
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex-1 grid grid-cols-3 gap-6">
            {{-- Left: Current Ticket Display --}}
            <div class="col-span-2 bg-white rounded-lg border border-gray-200 flex flex-col">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Nomor Antrian Saat Ini</h3>
                </div>
                
                <div class="flex-1 flex flex-col items-center justify-center p-8">
                    @if($myCounter->currentTicket)
                        <div class="text-center">
                            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Sedang Dilayani</div>
                            <div class="text-8xl font-black text-slate-900 mb-6 tracking-tight font-mono">
                                {{ $myCounter->currentTicket->ticket_number }}
                            </div>
                            <div class="text-sm text-gray-600 mb-8">
                                {{ $myCounter->currentTicket->service ? $myCounter->currentTicket->service->name : '' }}
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-center justify-center gap-3">
                                <button wire:click="recallCurrent"
                                        class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.933 12.8a1 1 0 000-1.6L6.6 7.2A1 1 0 005 8v8a1 1 0 001.6.8l5.333-4zM19.933 12.8a1 1 0 000-1.6l-5.333-4A1 1 0 0013 8v8a1 1 0 001.6.8l5.333-4z"/>
                                    </svg>
                                    Panggil Ulang
                                </button>

                                <button wire:click="completeCurrent"
                                        class="px-6 py-3 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Selesai
                                </button>

                                <button wire:click="rejectCurrent"
                                        class="px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Tolak
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="text-center">
                            <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <div class="text-xl font-semibold text-gray-400 mb-2">Tidak Ada Antrian Aktif</div>
                            <p class="text-sm text-gray-500 mb-6">Klik tombol "Panggil Berikutnya" untuk memanggil antrian</p>

                            @if($counterStatus === 'active')
                                <button wire:click="callNext"
                                        class="px-8 py-4 bg-slate-900 text-white font-bold rounded-lg hover:bg-slate-800 transition-colors flex items-center gap-3 mx-auto">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Panggil Berikutnya
                                </button>
                            @else
                                <div class="text-sm text-gray-500">Buka loket untuk mulai melayani</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right: Queue Info & Stats --}}
            <div class="space-y-6">
                {{-- Next in Queue --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Antrian Berikutnya</h3>
                    
                    @if($nextTicket)
                        <div class="text-center py-4">
                            <div class="text-4xl font-bold text-gray-900 mb-2 font-mono">{{ $nextTicket->ticket_number }}</div>
                            <div class="text-xs text-gray-500">{{ $nextTicket->service ? $nextTicket->service->name : '' }}</div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="text-2xl font-bold text-gray-300 mb-2">—</div>
                            <div class="text-xs text-gray-400">Tidak ada antrian</div>
                        </div>
                    @endif

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Total Menunggu</span>
                            <span class="text-lg font-bold text-gray-900">{{ $waitingCount }}</span>
                        </div>
                    </div>
                </div>

                {{-- Today Stats --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Statistik Hari Ini</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-600">Dilayani</span>
                            </div>
                            <span class="text-xl font-bold text-gray-900">{{ $todayServed }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-600">Ditolak</span>
                            </div>
                            <span class="text-xl font-bold text-gray-900">{{ $todayRejected }}</span>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-700">Total</span>
                                <span class="text-2xl font-bold text-slate-900">{{ $todayServed + $todayRejected }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Call Button (when has current ticket) --}}
                @if($myCounter->currentTicket && $counterStatus === 'active')
                    <button wire:click="callNext"
                            class="w-full px-6 py-4 bg-slate-900 text-white font-bold rounded-lg hover:bg-slate-800 transition-colors flex items-center justify-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Panggil Berikutnya
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>