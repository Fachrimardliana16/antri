<div class="h-screen overflow-hidden flex flex-col bg-gray-50">
    @if(!$myCounter)
        <div class="flex-1 flex items-center justify-center">
            <div class="text-center max-w-md bg-white rounded-2xl p-12 shadow-lg">
                <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Loket Belum Ditugaskan</h3>
                <p class="text-gray-600">Anda belum ditugaskan ke loket manapun. Silakan hubungi administrator untuk mendapatkan penugasan loket.</p>
            </div>
        </div>
    @else
        {{-- Main Content Area --}}
        <div class="flex-1 grid grid-cols-12 gap-6 p-6 overflow-hidden">
            {{-- Left: Current Ticket Display (70%) --}}
            <div class="col-span-8 flex flex-col gap-6">
                {{-- Counter Header --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-20 h-20 rounded-2xl bg-slate-900 flex items-center justify-center shadow-lg">
                                <span class="text-4xl font-black text-white">{{ $myCounter->number }}</span>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900">{{ $myCounter->name }}</h2>
                                <p class="text-gray-600 mt-1">
                                    {{ $myCounter->service ? $myCounter->service->name : 'Layanan Umum' }}
                                </p>
                            </div>
                        </div>

                        {{-- Status Buttons --}}
                        <div class="flex items-center gap-3">
                            @if($counterStatus === 'closed')
                                <button wire:click="openCounter" 
                                        class="px-8 py-4 bg-emerald-600 text-white text-lg font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-lg hover:shadow-xl">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                        </svg>
                                        <span>Buka Loket</span>
                                    </div>
                                </button>
                            @elseif($counterStatus === 'active')
                                <div class="flex items-center gap-3 px-6 py-3 bg-emerald-50 border-2 border-emerald-200 rounded-xl">
                                    <div class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <span class="text-lg font-bold text-emerald-700">Loket Aktif</span>
                                </div>
                                <button wire:click="takeBreak" 
                                        class="px-6 py-3 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-600 transition-colors">
                                    Istirahat
                                </button>
                                <button wire:click="closeCounter" 
                                        class="px-6 py-3 bg-gray-600 text-white font-bold rounded-xl hover:bg-gray-700 transition-colors">
                                    Tutup Loket
                                </button>
                            @else
                                <div class="flex items-center gap-3 px-6 py-3 bg-amber-50 border-2 border-amber-200 rounded-xl">
                                    <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                                    <span class="text-lg font-bold text-amber-700">Istirahat</span>
                                </div>
                                <button wire:click="openCounter" 
                                        class="px-6 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition-colors">
                                    Aktifkan Kembali
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Current Ticket Display --}}
                <div class="flex-1 bg-white rounded-2xl shadow-sm border border-gray-200 flex flex-col overflow-hidden">
                    @if($myCounter->currentTicket)
                        {{-- Ada Tiket Aktif --}}
                        <div class="flex-1 flex flex-col items-center justify-center p-12">
                            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Sedang Dilayani</div>
                            <div class="text-[150px] leading-none font-black text-slate-900 mb-8 font-mono">
                                {{ $myCounter->currentTicket->ticket_number }}
                            </div>
                            <div class="text-xl text-gray-600 mb-12">
                                {{ $myCounter->currentTicket->service ? $myCounter->currentTicket->service->name : '' }}
                            </div>

                            {{-- Action Buttons - SUPER BESAR --}}
                            <div class="grid grid-cols-3 gap-4 w-full max-w-4xl">
                                <button wire:click="recallCurrent"
                                        class="py-8 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 transition-all shadow-lg hover:shadow-xl group">
                                    <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.933 12.8a1 1 0 000-1.6L6.6 7.2A1 1 0 005 8v8a1 1 0 001.6.8l5.333-4zM19.933 12.8a1 1 0 000-1.6l-5.333-4A1 1 0 0013 8v8a1 1 0 001.6.8l5.333-4z"/>
                                    </svg>
                                    <span class="text-2xl font-bold">Panggil Ulang</span>
                                </button>

                                <button wire:click="completeCurrent"
                                        class="py-8 bg-emerald-600 text-white rounded-2xl hover:bg-emerald-700 transition-all shadow-lg hover:shadow-xl group">
                                    <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-2xl font-bold">Selesai</span>
                                </button>

                                <button wire:click="rejectCurrent"
                                        class="py-8 bg-red-600 text-white rounded-2xl hover:bg-red-700 transition-all shadow-lg hover:shadow-xl group">
                                    <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-2xl font-bold">Tolak</span>
                                </button>
                            </div>
                        </div>
                    @else
                        {{-- Tidak Ada Tiket --}}
                        <div class="flex-1 flex flex-col items-center justify-center p-12">
                            <svg class="w-32 h-32 text-gray-300 mb-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <div class="text-3xl font-bold text-gray-400 mb-4">Tidak Ada Antrian Aktif</div>
                            <p class="text-gray-500 mb-12 text-lg">Klik tombol di bawah untuk memanggil antrian berikutnya</p>

                            @if($counterStatus === 'active')
                                <button wire:click="callNext"
                                        class="px-16 py-8 bg-slate-900 text-white rounded-2xl hover:bg-slate-800 transition-all shadow-2xl hover:shadow-3xl group">
                                    <div class="flex items-center gap-4">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-3xl font-black">PANGGIL BERIKUTNYA</span>
                                    </div>
                                </button>
                            @else
                                <div class="text-gray-400 text-lg">Buka loket untuk mulai melayani</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right: Info & Stats (30%) --}}
            <div class="col-span-4 flex flex-col gap-6">
                {{-- Next in Queue --}}
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-8 text-white shadow-lg">
                    <h3 class="text-sm font-bold uppercase tracking-wider opacity-75 mb-4">Antrian Berikutnya</h3>
                    
                    @if($nextTicket)
                        <div class="text-center py-6">
                            <div class="text-7xl font-black mb-3 font-mono">{{ $nextTicket->ticket_number }}</div>
                            <div class="text-sm opacity-75">{{ $nextTicket->service ? $nextTicket->service->name : '' }}</div>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <div class="text-5xl font-black mb-3 opacity-30">—</div>
                            <div class="text-sm opacity-50">Tidak ada antrian</div>
                        </div>
                    @endif

                    <div class="mt-6 pt-6 border-t border-white/20">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold opacity-75">Total Menunggu</span>
                            <span class="text-3xl font-black">{{ $waitingCount }}</span>
                        </div>
                    </div>
                </div>

                {{-- Today Stats --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 flex-1">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-6">Statistik Hari Ini</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm text-gray-500 font-semibold">Dilayani</div>
                                <div class="text-4xl font-black text-gray-900">{{ $todayServed }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm text-gray-500 font-semibold">Ditolak</div>
                                <div class="text-4xl font-black text-gray-900">{{ $todayRejected }}</div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-gray-700">Total</span>
                                <span class="text-5xl font-black text-slate-900">{{ $todayServed + $todayRejected }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Call Button (when has ticket) --}}
                @if($myCounter->currentTicket && $counterStatus === 'active')
                    <button wire:click="callNext"
                            class="w-full py-6 bg-slate-900 text-white text-xl font-black rounded-2xl hover:bg-slate-800 transition-all shadow-lg hover:shadow-xl">
                        <div class="flex items-center justify-center gap-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>PANGGIL BERIKUTNYA</span>
                        </div>
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>