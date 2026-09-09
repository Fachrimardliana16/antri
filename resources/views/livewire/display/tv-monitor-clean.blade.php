<div wire:poll.3s
     class="h-screen overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 flex flex-col"
     x-data="{
         audioUnlocked: false,
         isDucked: false,
         activeCallState: @entangle('activeCall'),
         unlockAudio() {
             if (window.AntriAudio) {
                 window.AntriAudio.getAudioContext();
             }
             this.audioUnlocked = true;
         },
         callTicket(data) {
             if (!data || !data.voice_text) return;
             if (window.AntriAudio) {
                 window.AntriAudio.speak(
                     data.voice_text,
                     0.9,
                     1.0,
                     () => { this.isDucked = true; },
                     () => { this.isDucked = false; }
                 );
             }
         }
     }"
     x-init="
         window.addEventListener('play-queue-call', (event) => {
             callTicket(event.detail.data);
         });
     ">

    {{-- Audio Unlock Banner --}}
    <div x-show="!audioUnlocked"
         @click="unlockAudio()"
         class="absolute top-0 left-0 right-0 z-50 bg-blue-600 text-white py-3 px-6 cursor-pointer hover:bg-blue-700 transition-colors">
        <div class="flex items-center justify-center gap-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
            </svg>
            <span class="text-lg font-bold">Klik untuk Aktifkan Suara Panggilan</span>
        </div>
    </div>

    {{-- Header --}}
    <div class="bg-white/5 backdrop-blur-sm border-b border-white/10">
        <div class="px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">Sistem Antrian Terpadu</h1>
                    <p class="text-white/60 mt-1">Harap perhatikan nomor antrian Anda di layar</p>
                </div>
                <div class="flex items-center gap-8">
                    <div class="text-center">
                        <div class="text-sm text-white/40">{{ now()->translatedFormat('l') }}</div>
                        <div class="text-2xl font-bold text-white">{{ now()->translatedFormat('d M Y') }}</div>
                    </div>
                    <div class="w-px h-12 bg-white/20"></div>
                    <div class="text-center">
                        <div class="text-sm text-white/40">Waktu</div>
                        <div class="text-3xl font-bold text-white font-mono">{{ now()->format('H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 grid grid-cols-12 gap-4 p-4 overflow-hidden">
        {{-- Left: Counters Grid --}}
        <div class="col-span-8 flex flex-col gap-4 overflow-y-auto">
            {{-- Current Call Spotlight --}}
            @if($activeCall)
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-3xl p-10 text-white shadow-2xl">
                    <div class="text-center">
                        <div class="text-lg font-semibold opacity-90 mb-3">PANGGILAN SEKARANG</div>
                        <div class="text-9xl font-black mb-4 tracking-tight font-mono">
                            {{ $activeCall['ticket_number'] }}
                        </div>
                        <div class="text-2xl font-bold mb-2">{{ $activeCall['service_name'] }}</div>
                        <div class="inline-flex items-center gap-3 bg-white/20 rounded-full px-6 py-3 text-xl font-bold">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                            <span>{{ $activeCall['counter_name'] }}</span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Active Counters Grid --}}
            <div>
                <h2 class="text-white/60 text-sm font-semibold uppercase tracking-wide mb-4 px-2">Loket Pelayanan Aktif</h2>
                <div class="grid grid-cols-2 gap-4">
                    @forelse($activeCounters as $counter)
                        @php
                            $isCurrentCalled = $activeCall && isset($activeCall['counter_number']) && $activeCall['counter_number'] == $counter->number;
                        @endphp
                        <div class="bg-white rounded-2xl p-6 {{ $isCurrentCalled ? 'ring-4 ring-blue-500 shadow-2xl' : '' }}">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <div class="text-sm text-gray-500 font-semibold">{{ $counter->name }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $counter->service ? $counter->service->name : 'Umum' }}</div>
                                </div>
                                <div class="flex items-center gap-1 px-2 py-1 rounded-lg bg-emerald-50">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                    <span class="text-xs font-semibold text-emerald-700">Aktif</span>
                                </div>
                            </div>

                            <div class="text-center py-8 bg-gray-50 rounded-xl">
                                @if($counter->currentTicket)
                                    <div class="text-5xl font-black text-gray-900 font-mono {{ $isCurrentCalled ? 'animate-pulse' : '' }}">
                                        {{ $counter->currentTicket->ticket_number }}
                                    </div>
                                @else
                                    <div class="text-3xl font-bold text-gray-300">—</div>
                                    <div class="text-xs text-gray-400 mt-2">Menunggu</div>
                                @endif
                            </div>

                            @if($counter->currentOperator)
                                <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                                    <div class="text-xs text-gray-500">Petugas: {{ $counter->currentOperator->name }}</div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-2 bg-white/5 rounded-2xl p-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-white/20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-white/40 text-lg">Semua loket sedang tutup</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right: Video Player --}}
        <div class="col-span-4 flex flex-col gap-6">
            {{-- Video --}}
            <div class="flex-1 bg-black rounded-2xl overflow-hidden relative">
                <iframe id="tv-video-player"
                        class="w-full h-full absolute inset-0 transition-opacity duration-300"
                        :style="isDucked ? 'opacity: 0.2;' : 'opacity: 1;'"
                        src="{{ $videoUrl }}"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                </iframe>
                <div x-show="isDucked"
                     class="absolute inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                    <div class="text-white text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                        </svg>
                        <div class="text-lg font-bold">Sedang Memanggil Antrian</div>
                    </div>
                </div>
            </div>

            {{-- Queue Stats --}}
            <div class="bg-white rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Statistik Hari Ini</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Dilayani</span>
                        <span class="text-2xl font-bold text-gray-900">{{ \App\Models\QueueTicket::where('status', 'completed')->whereDate('queue_date', today())->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Sedang Menunggu</span>
                        <span class="text-2xl font-bold text-blue-600">{{ \App\Models\QueueTicket::where('status', 'waiting')->whereDate('queue_date', today())->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
