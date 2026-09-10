<div wire:poll.3s
     class="h-screen flex flex-col bg-gradient-to-br from-pink-50 to-red-50 overflow-hidden"
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
         class="absolute top-0 left-0 right-0 z-50 bg-blue-600 text-white py-3 cursor-pointer hover:bg-blue-700">
        <div class="text-center text-lg font-bold">
            🔊 KLIK UNTUK AKTIFKAN SUARA
        </div>
    </div>

    {{-- Header - Red PMI Style --}}
    <div class="bg-gradient-to-r from-red-600 to-red-700 text-white px-8 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-16 h-16 bg-white rounded-lg flex items-center justify-center">
                        <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black">SISTEM ANTRIAN TERPADU</h1>
                        <p class="text-red-100 text-sm">Papan Informasi Antrian</p>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-5xl font-black font-mono">{{ now()->format('H:i:s') }}</div>
                <div class="text-red-200 text-sm">{{ now()->translatedFormat('l, d F Y') }}</div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 flex gap-4 p-4 overflow-hidden">
        {{-- Left Sidebar - Running Text --}}
        <div class="w-64 bg-white rounded-lg shadow-lg p-4 overflow-hidden">
            <h3 class="text-sm font-bold text-gray-700 mb-4 text-center uppercase">Informasi</h3>
            <div class="space-y-4 text-sm text-gray-600 leading-relaxed">
                <div class="bg-gray-50 rounded p-3 border-l-4 border-blue-500">
                    <p class="font-semibold text-gray-800 mb-1">Perhatian</p>
                    <p>Harap perhatikan nomor antrian Anda di layar monitor</p>
                </div>
                <div class="bg-gray-50 rounded p-3 border-l-4 border-green-500">
                    <p class="font-semibold text-gray-800 mb-1">Informasi</p>
                    <p>Pastikan datang saat nomor Anda dipanggil</p>
                </div>
                <div class="bg-gray-50 rounded p-3 border-l-4 border-amber-500">
                    <p class="font-semibold text-gray-800 mb-1">Catatan</p>
                    <p>Pelayanan dimulai pukul 08:00 - 16:00</p>
                </div>
            </div>
        </div>

        {{-- Center - Current Call + Counters --}}
        <div class="flex-1 flex flex-col gap-4">
            {{-- Current Call Spotlight --}}
            @if($activeCall)
                <div class="bg-gradient-to-br from-red-600 to-red-700 rounded-2xl p-8 text-white shadow-2xl">
                    <div class="text-center">
                        <div class="text-lg font-bold mb-2 opacity-90">ANTRIAN</div>
                        <div class="text-[120px] leading-none font-black mb-4 font-mono tracking-tight">
                            {{ $activeCall['ticket_number'] }}
                        </div>
                        <div class="text-2xl font-bold mb-4 opacity-90">{{ $activeCall['counter_name'] }}</div>
                    </div>
                </div>
            @endif

            {{-- Counter Grid --}}
            <div class="flex-1 grid grid-cols-3 gap-3 overflow-hidden">
                @forelse($activeCounters->take(6) as $index => $counter)
                    @php
                        $isCurrentCalled = $activeCall && isset($activeCall['counter_number']) && $activeCall['counter_number'] == $counter->number;
                        $colors = ['from-amber-500 to-orange-600', 'from-orange-500 to-red-600', 'from-red-500 to-red-700'];
                        $colorClass = $colors[$index % 3];
                    @endphp
                    <div class="bg-gradient-to-br {{ $colorClass }} rounded-xl p-6 text-white text-center shadow-lg {{ $isCurrentCalled ? 'ring-4 ring-yellow-300 scale-105' : '' }} transition-all">
                        <div class="text-8xl font-black font-mono mb-2">
                            @if($counter->currentTicket)
                                {{ $counter->currentTicket->ticket_number }}
                            @else
                                —
                            @endif
                        </div>
                        <div class="text-lg font-bold opacity-90">{{ $counter->name }}</div>
                    </div>
                @empty
                    <div class="col-span-3 flex items-center justify-center bg-white/50 rounded-xl">
                        <div class="text-center text-gray-400">
                            <div class="text-6xl mb-4">🔒</div>
                            <div class="text-xl font-semibold">Semua Loket Tutup</div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right - Video --}}
        <div class="w-96 flex flex-col gap-4">
            <div class="flex-1 bg-black rounded-xl overflow-hidden relative shadow-lg">
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
                        <svg class="w-16 h-16 mx-auto mb-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                        </svg>
                        <div class="text-lg font-bold">MENDENGARKAN...</div>
                    </div>
                </div>
            </div>

            {{-- Stats Today --}}
            <div class="bg-white rounded-xl p-6 shadow-lg">
                <div class="text-center">
                    <div class="text-gray-500 text-sm font-bold mb-2">DILAYANI HARI INI</div>
                    <div class="text-5xl font-black text-gray-900">
                        {{ \App\Models\QueueTicket::where('status', 'completed')->whereDate('queue_date', today())->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>