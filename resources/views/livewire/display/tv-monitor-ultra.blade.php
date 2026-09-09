<div wire:poll.3s
     class="h-screen flex flex-col bg-slate-900 overflow-hidden"
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
         class="absolute top-0 left-0 right-0 z-50 bg-blue-600 text-white py-4 cursor-pointer hover:bg-blue-700">
        <div class="text-center text-xl font-bold">
            🔊 KLIK UNTUK AKTIFKAN SUARA
        </div>
    </div>

    {{-- Header - Minimal --}}
    <div class="absolute top-4 right-6 z-10 text-right">
        <div class="text-4xl font-black text-white font-mono drop-shadow-lg">{{ now()->format('H:i') }}</div>
        <div class="text-white/80 text-sm mt-1">{{ now()->translatedFormat('d M Y') }}</div>
    </div>

    {{-- Main Content - Full Screen --}}
    <div class="h-full grid grid-cols-3 gap-4 p-6 overflow-hidden">
        {{-- Left: Current Call (Big) --}}
        <div class="col-span-2 flex flex-col gap-4 overflow-hidden">
            {{-- Spotlight --}}
            @if($activeCall)
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-6 text-center text-white shadow-xl">
                    <div class="text-xs font-semibold opacity-75 mb-2">Harap menuju</div>
                    <div class="text-7xl leading-none font-black mb-3 font-mono">
                        {{ $activeCall['ticket_number'] }}
                    </div>
                    <div class="text-lg font-semibold mb-3">{{ $activeCall['service_name'] }}</div>
                    <div class="inline-flex items-center gap-2 bg-white/20 rounded-full px-5 py-2 text-base font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        {{ $activeCall['counter_name'] }}
                    </div>
                </div>
            @endif

            {{-- Counter Grid --}}
            <div class="flex-1 grid grid-cols-2 gap-3 overflow-y-auto">
                @forelse($activeCounters as $counter)
                    @php
                        $isCurrentCalled = $activeCall && isset($activeCall['counter_number']) && $activeCall['counter_number'] == $counter->number;
                    @endphp
                    <div class="bg-white rounded-xl p-4 {{ $isCurrentCalled ? 'ring-4 ring-yellow-400 animate-pulse' : '' }}">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <div class="text-lg font-black text-gray-900">{{ $counter->name }}</div>
                                <div class="text-gray-500 text-xs">{{ $counter->service ? $counter->service->name : 'Umum' }}</div>
                            </div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        </div>

                        <div class="bg-gray-50 rounded-lg py-6 text-center">
                            @if($counter->currentTicket)
                                <div class="text-4xl font-black text-gray-900 font-mono">
                                    {{ $counter->currentTicket->ticket_number }}
                                </div>
                            @else
                                <div class="text-3xl font-bold text-gray-300">—</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 flex items-center justify-center bg-white/5 rounded-2xl">
                        <div class="text-center">
                            <div class="text-6xl mb-4">🔒</div>
                            <div class="text-white/40 text-2xl font-semibold">Semua Loket Tutup</div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right: Video & Stats --}}
        <div class="flex flex-col gap-4">
            {{-- Video --}}
            <div class="flex-1 bg-black rounded-2xl overflow-hidden relative">
                <iframe id="tv-video-player"
                        class="w-full h-full absolute inset-0"
                        :style="isDucked ? 'opacity: 0.2;' : 'opacity: 1;'"
                        src="{{ $videoUrl }}"
                        allow="autoplay; encrypted-media"
                        allowfullscreen>
                </iframe>
                <div x-show="isDucked"
                     class="absolute inset-0 flex items-center justify-center bg-black/80">
                    <div class="text-white text-center">
                        <svg class="w-20 h-20 mx-auto mb-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                        </svg>
                        <div class="text-xl font-bold">MENDENGARKAN...</div>
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="bg-white rounded-xl p-4">
                <div class="text-center">
                    <div class="text-gray-500 text-xs font-bold mb-1">HARI INI</div>
                    <div class="text-4xl font-black text-slate-900 mb-1">
                        {{ \App\Models\QueueTicket::where('status', 'completed')->whereDate('queue_date', today())->count() }}
                    </div>
                    <div class="text-gray-500 text-xs font-semibold">Dilayani</div>
                </div>
            </div>
        </div>
    </div>
</div>
