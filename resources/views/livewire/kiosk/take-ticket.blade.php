<div wire:poll.5s class="flex-1 flex flex-col justify-between"
     x-data="{
         modalOpen: @entangle('showSuccessModal'),
         countdown: 8,
         timer: null,
         startCountdown() {
             this.countdown = 8;
             clearInterval(this.timer);
             this.timer = setInterval(() => {
                 this.countdown--;
                 if (this.countdown <= 0) {
                     clearInterval(this.timer);
                     $wire.closeModal();
                 }
             }, 1000);
         }
     }"
     x-init="
         $watch('modalOpen', value => {
             if (value) startCountdown();
             else clearInterval(timer);
         });
         window.addEventListener('print-ticket', (event) => {
             if (window.AntriPrinter) {
                 window.AntriPrinter.printTicket(event.detail.ticket);
             }
         });
     ">

    {{-- Service Selection Grid --}}
    <div class="flex-1 flex flex-col justify-center max-w-5xl mx-auto w-full py-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @forelse($services as $service)
                @php
                    $hasActiveCounter = $service->hasActiveCounter();
                    $activeCounterCount = $service->counters->where('status', 'active')->count();
                    $color = $service->color ?? '#1a56a8';
                @endphp
                <div class="relative">
                    <button type="button"
                            wire:click="takeTicket({{ $service->id }})"
                            @if(!$hasActiveCounter) disabled @endif
                            class="w-full text-left rounded-xl transition-all duration-200 flex flex-col justify-between h-52 relative overflow-hidden border-2 active:scale-98
                                   {{ $hasActiveCounter
                                      ? 'bg-white border-white/20 hover:border-white/40 cursor-pointer shadow-lg hover:shadow-xl'
                                      : 'opacity-50 cursor-not-allowed bg-white/50 border-white/10' }}"
                            style="{{ $hasActiveCounter ? 'box-shadow: 0 4px 16px rgba(0,0,0,0.25);' : '' }}">

                        {{-- Color accent top bar --}}
                        <div class="absolute top-0 left-0 right-0 h-1" style="background-color: {{ $color }};"></div>

                        {{-- Content --}}
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            {{-- Top: Icon + Name + Status --}}
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-xl flex items-center justify-center font-black text-2xl text-white"
                                         style="background-color: {{ $color }};">
                                        {{ $service->code }}
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-white leading-tight">{{ $service->name }}</h2>
                                        <p class="text-xs text-blue-200 mt-1 max-w-xs">{{ $service->description }}</p>
                                    </div>
                                </div>

                                <div>
                                    @if($hasActiveCounter)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-300 border border-green-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                                            {{ $activeCounterCount }} Buka
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-300 border border-red-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                            Tutup
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Bottom Stats & CTA --}}
                            <div class="flex items-center justify-between mt-5 pt-4 border-t border-white/10">
                                <div class="flex items-center gap-6 text-sm">
                                    <div>
                                        <div class="text-xs text-blue-300">Menunggu</div>
                                        <div class="font-bold text-white font-mono text-lg">{{ $service->waiting_tickets_count }}</div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-blue-300">Est. / Orang</div>
                                        <div class="font-bold text-white font-mono text-lg">{{ $service->estimated_time_minutes }} Mnt</div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1.5 text-sm font-semibold {{ $hasActiveCounter ? 'text-white/80' : 'text-white/30' }}">
                                    {{ $hasActiveCounter ? 'Sentuh untuk Ambil' : 'Tidak Tersedia' }}
                                    @if($hasActiveCounter)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </button>
                </div>
            @empty
                <div class="col-span-2 text-center py-16 rounded-xl bg-white/5 border border-white/10">
                    <p class="text-blue-200">Belum ada data layanan yang aktif saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Success Modal --}}
    @if($showSuccessModal && $latestTicket)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm animate-fade-in"
             @keydown.escape.window="$wire.closeModal()">
            <div class="w-full max-w-sm rounded-2xl p-8 text-center flex flex-col items-center"
                 style="background-color: #0f1d35; border: 1px solid rgba(255,255,255,0.1);">

                {{-- Success Icon --}}
                <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 text-white"
                     style="background-color: var(--primary, #1a56a8);">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </div>

                <h3 class="text-lg font-bold text-white">Tiket Berhasil Dicetak</h3>
                <p class="text-sm text-blue-300 mt-0.5">{{ $latestTicket['service_name'] }}</p>

                {{-- Big Number --}}
                <div class="my-6 py-5 px-10 rounded-xl w-full border border-white/10" style="background-color: rgba(255,255,255,0.05);">
                    <div class="text-xs font-semibold uppercase tracking-widest text-blue-300 mb-1">Nomor Antrean Anda</div>
                    <div class="text-6xl font-black text-white font-mono tracking-wider" style="color: var(--secondary, #2d7dd2);">
                        {{ $latestTicket['ticket_number'] }}
                    </div>
                </div>

                {{-- QR Code --}}
                <div class="bg-white p-3 rounded-xl mb-3">
                    {!! $latestTicket['qr_svg'] !!}
                </div>
                <p class="text-xs text-blue-300 mb-4">Scan QR untuk pantau antrean via HP</p>

                {{-- Info Grid --}}
                <div class="grid grid-cols-2 gap-3 w-full text-xs mb-5">
                    <div class="rounded-lg p-3 border border-white/10" style="background-color: rgba(255,255,255,0.05);">
                        <div class="text-blue-300">Di Depan Anda</div>
                        <div class="text-base font-bold text-white font-mono mt-0.5">{{ $latestTicket['ahead_count'] }} Orang</div>
                    </div>
                    <div class="rounded-lg p-3 border border-white/10" style="background-color: rgba(255,255,255,0.05);">
                        <div class="text-blue-300">Estimasi Tunggu</div>
                        <div class="text-base font-bold text-amber-300 font-mono mt-0.5">{{ $latestTicket['estimated_wait'] }} Menit</div>
                    </div>
                </div>

                {{-- Close Button --}}
                <button type="button" wire:click="closeModal"
                        class="w-full py-3 rounded-xl text-white font-bold text-sm transition hover:opacity-90 active:scale-98"
                        style="background-color: var(--primary, #1a56a8);">
                    Selesai (<span x-text="countdown"></span>s)
                </button>
            </div>
        </div>
    @endif
</div>
