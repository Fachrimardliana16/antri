<div wire:poll.3s
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
     "
     class="h-full flex flex-col justify-between gap-4">

    {{-- Audio Unlock Banner --}}
    <div x-show="!audioUnlocked"
         @click="unlockAudio()"
         class="rounded-lg px-5 py-3 flex items-center justify-between cursor-pointer transition hover:opacity-90 border border-blue-500/30"
         style="background-color: var(--primary, #1a56a8);">
        <div class="flex items-center gap-3 text-white">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
            </svg>
            <span class="text-sm font-semibold">Klik di sini untuk mengaktifkan Suara Panggilan Antrean</span>
        </div>
        <span class="text-xs bg-white/20 px-3 py-1 rounded font-bold text-white flex-shrink-0">Aktifkan</span>
    </div>

    {{-- Main Content Grid --}}
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-4 min-h-0 overflow-hidden">

        {{-- Left: Active Counters Grid --}}
        <div class="lg:col-span-7 flex flex-col h-full overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-400"></span>
                    <h2 class="text-base font-bold text-white uppercase tracking-wide">Loket Pelayanan Aktif</h2>
                </div>
                <span class="text-xs text-blue-300">{{ $activeCounters->count() }} Loket Buka</span>
            </div>

            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3 overflow-y-auto pr-1">
                @forelse($activeCounters as $counter)
                    @php
                        $isCurrentCalled = $activeCall && isset($activeCall['counter_number']) && $activeCall['counter_number'] == $counter->number;
                    @endphp
                    <div class="rounded-xl p-4 flex flex-col justify-between transition-all duration-200 relative overflow-hidden border
                                {{ $isCurrentCalled
                                   ? 'border-white/30 animate-call-pulse'
                                   : 'border-white/10' }}"
                         style="background-color: #142040; {{ $isCurrentCalled ? 'border-color: var(--secondary, #2d7dd2);' : '' }}">

                        {{-- Counter Header --}}
                        <div class="flex items-center justify-between pb-3 border-b border-white/10">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-400"></span>
                                <span class="font-bold text-lg text-white uppercase">{{ $counter->name }}</span>
                            </div>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded text-blue-300"
                                  style="background-color: rgba(255,255,255,0.07);">
                                {{ $counter->service ? $counter->service->name : 'Umum' }}
                            </span>
                        </div>

                        {{-- Ticket Number --}}
                        <div class="py-4 text-center">
                            @if($counter->currentTicket)
                                <div class="text-xs font-semibold uppercase tracking-widest text-blue-300 mb-1">Nomor Panggilan</div>
                                <div class="text-5xl font-black font-mono tracking-wider {{ $isCurrentCalled ? 'animate-status-blink' : '' }}"
                                     style="color: {{ $isCurrentCalled ? 'var(--secondary, #2d7dd2)' : 'white' }};">
                                    {{ $counter->currentTicket->ticket_number }}
                                </div>
                            @else
                                <div class="text-xs font-semibold uppercase tracking-widest text-blue-300 mb-1">Status</div>
                                <div class="text-xl font-bold text-white/40 italic">Siap Melayani</div>
                            @endif
                        </div>

                        {{-- Operator --}}
                        <div class="pt-3 border-t border-white/10 flex items-center justify-between text-xs text-blue-300">
                            <span>Petugas:</span>
                            <span class="font-semibold text-white">{{ $counter->currentOperator ? $counter->currentOperator->name : 'Operator' }}</span>
                        </div>

                        {{-- Bottom accent --}}
                        <div class="absolute bottom-0 left-0 right-0 h-1" style="background-color: {{ $counter->service->color ?? 'var(--primary)' }};"></div>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-16 rounded-xl border border-white/10 flex flex-col items-center justify-center"
                         style="background-color: #142040;">
                        <svg class="w-12 h-12 text-blue-400/30 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-base font-semibold text-blue-200">Semua Loket Sedang Tutup</h3>
                        <p class="text-xs text-blue-400 mt-1">Silakan tunggu hingga petugas membuka loket.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right: Calling Spotlight + Media --}}
        <div class="lg:col-span-5 flex flex-col h-full gap-4 overflow-hidden">

            {{-- Calling Spotlight --}}
            <div class="rounded-xl p-5 text-center border"
                 style="background-color: #142040; border-color: rgba(255,255,255,0.1);">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider text-blue-300 mb-3"
                     style="background-color: rgba(255,255,255,0.07);">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-ping" style="animation-duration: 1.5s;"></span>
                    Panggilan Terakhir
                </div>

                @if($activeCall)
                    <div class="text-5xl font-black text-white font-mono tracking-widest my-1">
                        {{ $activeCall['ticket_number'] }}
                    </div>
                    <div class="text-sm font-bold mt-1" style="color: var(--secondary, #2d7dd2);">
                        Menuju {{ $activeCall['counter_name'] }}
                    </div>
                    <div class="text-xs text-blue-300 mt-0.5">{{ $activeCall['service_name'] }}</div>
                @else
                    <div class="text-xl font-semibold text-blue-300/40 py-4">
                        Menunggu Panggilan...
                    </div>
                @endif
            </div>

            {{-- Video Player --}}
            <div class="flex-1 rounded-xl overflow-hidden border flex flex-col"
                 style="background-color: #0a1020; border-color: rgba(255,255,255,0.07);">
                <div class="px-4 py-2 border-b border-white/07 flex items-center justify-between text-xs text-blue-300 font-semibold"
                     style="background-color: rgba(255,255,255,0.04);">
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                        <span>INFORMASI PUBLIK</span>
                    </div>
                    <span x-show="isDucked" class="text-blue-400 text-[10px] font-bold uppercase animate-status-blink">
                        [Audio Ducking Aktif]
                    </span>
                </div>

                <div class="flex-1 w-full h-full bg-black relative">
                    <iframe id="tv-video-player"
                            class="w-full h-full border-0 absolute inset-0 transition-opacity duration-300"
                            :style="isDucked ? 'opacity: 0.2;' : 'opacity: 1;'"
                            src="{{ $videoUrl }}"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                    </iframe>
                </div>
            </div>

        </div>
    </div>
</div>
