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
                     () => { this.isDucked = true; }, // onStart: Audio Ducking
                     () => { this.isDucked = false; } // onEnd: Restore Audio
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

    <!-- Browser Audio Autoplay Unlock Prompt if not clicked yet -->
    <div x-show="!audioUnlocked"
         @click="unlockAudio()"
         class="bg-gradient-to-r from-blue-600/90 to-cyan-600/90 text-white px-6 py-3 rounded-2xl flex items-center justify-between shadow-xl cursor-pointer hover:brightness-110 transition animate-pulse border border-white/20">
        <div class="flex items-center space-x-3">
            <svg class="w-6 h-6 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
            </svg>
            <span class="text-sm font-bold tracking-wide">Klik di sini untuk mengaktifkan Suara Panggilan Antrean (Audio/TTS Engine)</span>
        </div>
        <span class="text-xs uppercase bg-white/20 px-3 py-1 rounded-full font-bold">Aktifkan Suara</span>
    </div>

    <!-- Main TV Screen Split: Left (Counters Grid) & Right (Media & Spotlight) -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 min-h-0 overflow-hidden">
        
        <!-- Left 7 Cols: Active Counters Grid (REQ-F-07) -->
        <div class="lg:col-span-7 flex flex-col h-full overflow-hidden">
            <div class="flex items-center justify-between mb-3 px-1">
                <h2 class="text-lg font-black text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>LOKET PELAYANAN AKTIF</span>
                </h2>
                <span class="text-xs font-semibold text-slate-400">Total: {{ $activeCounters->count() }} Loket Buka</span>
            </div>

            <!-- CSS Grid of Active Counters -->
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4 overflow-y-auto pr-1">
                @forelse($activeCounters as $counter)
                    @php
                        $isCurrentCalled = $activeCall && isset($activeCall['counter_number']) && $activeCall['counter_number'] == $counter->number;
                    @endphp
                    <div class="glass-card rounded-3xl p-5 border flex flex-col justify-between transition-all duration-300 relative overflow-hidden {{ $isCurrentCalled ? 'border-cyan-400 ring-4 ring-cyan-500/30 bg-slate-900/90 animate-call-glow' : 'border-slate-800/80 bg-slate-900/60' }}">
                        
                        <!-- Top Counter Name & Status -->
                        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                            <div class="flex items-center space-x-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></span>
                                <span class="font-black text-xl text-white tracking-tight uppercase">{{ $counter->name }}</span>
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-800 text-cyan-400 font-mono">
                                {{ $counter->service ? $counter->service->name : 'Umum' }}
                            </span>
                        </div>

                        <!-- Big Center Ticket Number -->
                        <div class="py-4 text-center">
                            @if($counter->currentTicket)
                                <div class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Nomor Panggilan</div>
                                <div class="text-5xl sm:text-6xl font-black font-mono tracking-wider {{ $isCurrentCalled ? 'text-cyan-300' : 'text-white' }}">
                                    {{ $counter->currentTicket->ticket_number }}
                                </div>
                            @else
                                <div class="text-xs font-semibold uppercase tracking-widest text-slate-500 mb-1">Status Loket</div>
                                <div class="text-2xl font-bold text-slate-500 italic">Siap Melayani</div>
                            @endif
                        </div>

                        <!-- Bottom Operator Badge -->
                        <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                            <span>Petugas:</span>
                            <span class="font-semibold text-slate-300">{{ $counter->currentOperator ? $counter->currentOperator->name : 'Operator' }}</span>
                        </div>

                        <!-- Accent bottom bar -->
                        <div class="absolute bottom-0 left-0 right-0 h-1.5" style="background: {{ $counter->service->color ?? 'var(--primary)' }};"></div>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-20 glass-panel rounded-3xl flex flex-col items-center justify-center">
                        <svg class="w-16 h-16 text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-xl font-bold text-slate-400">Semua Loket Sedang Tutup</h3>
                        <p class="text-xs text-slate-500 mt-1">Silakan tunggu hingga petugas membuka loket pelayanan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right 5 Cols: Video Player with Audio Ducking & Calling Spotlight -->
        <div class="lg:col-span-5 flex flex-col h-full gap-4 overflow-hidden">
            
            <!-- Latest Calling Spotlight Banner (High Visibility) -->
            <div class="glass-panel p-5 rounded-3xl border-2 border-blue-500/40 shadow-2xl relative overflow-hidden bg-slate-900/90 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-cyan-500/20 text-cyan-300 mb-2">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                    <span>PANGGILAN TERAKHIR</span>
                </div>

                @if($activeCall)
                    <div class="text-5xl font-black text-white font-mono tracking-widest my-1">
                        {{ $activeCall['ticket_number'] }}
                    </div>
                    <div class="text-base font-bold text-cyan-400">
                        Menuju {{ $activeCall['counter_name'] }}
                    </div>
                    <div class="text-xs text-slate-400 mt-1">
                        {{ $activeCall['service_name'] }}
                    </div>
                @else
                    <div class="text-2xl font-bold text-slate-500 py-4">
                        Menunggu Panggilan...
                    </div>
                @endif
            </div>

            <!-- Media / Video Player with Audio Ducking (REQ-F-09) -->
            <div class="flex-1 glass-panel rounded-3xl border border-slate-800 overflow-hidden relative shadow-2xl flex flex-col">
                <div class="px-4 py-2 bg-slate-900/80 border-b border-slate-800 flex items-center justify-between text-xs text-slate-400 font-semibold">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                        <span>INFORMASI & EDUKASI PUBLIK</span>
                    </div>
                    <!-- Audio ducking indicator -->
                    <span x-show="isDucked" class="text-cyan-400 text-[10px] font-bold uppercase animate-pulse">
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
