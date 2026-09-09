<div wire:poll.5s class="h-screen flex flex-col bg-white overflow-hidden"
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

    {{-- Header - Minimal dengan jam --}}
    <div class="absolute top-6 right-8 z-10">
        <div class="text-3xl font-black text-slate-900 font-mono">{{ now()->format('H:i') }}</div>
        <div class="text-slate-600 text-xs text-right mt-1">{{ now()->translatedFormat('d M Y') }}</div>
    </div>

    {{-- Service Grid - Full Space --}}
    <div class="h-full p-8 overflow-y-auto">
        <div class="grid grid-cols-2 gap-4 max-w-6xl mx-auto">
            @forelse($services as $service)
                @php
                    $hasActiveCounter = $service->hasActiveCounter();
                @endphp

                <button type="button"
                        wire:click="takeTicket({{ $service->id }})"
                        @if(!$hasActiveCounter) disabled @endif
                        class="relative bg-white border-4 rounded-xl p-6 text-left transition-all hover:scale-105 {{ $hasActiveCounter ? 'border-slate-900 hover:shadow-2xl cursor-pointer' : 'border-gray-300 opacity-40 cursor-not-allowed' }}">

                    {{-- Service Code --}}
                    <div class="absolute top-4 right-4 w-20 h-20 rounded-xl bg-slate-900 flex items-center justify-center">
                        <span class="text-4xl font-black text-white">{{ $service->code }}</span>
                    </div>

                    {{-- Content --}}
                    <div class="pr-24">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $service->name }}</h2>

                        {{-- Stats Row --}}
                        <div class="flex items-center gap-6 mt-4">
                            <div>
                                <div class="text-gray-500 text-xs font-semibold">MENGANTRI</div>
                                <div class="text-3xl font-black text-slate-900 mt-1">{{ $service->waiting_tickets_count }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500 text-xs font-semibold">ESTIMASI</div>
                                <div class="text-3xl font-black text-slate-900 mt-1">{{ $service->estimated_time_minutes }}<span class="text-xl">m</span></div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="mt-4 pt-4 border-t-2 border-gray-100 flex items-center justify-between">
                            @if($hasActiveCounter)
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                                    <span class="text-sm font-bold text-emerald-600">Tersedia</span>
                                </div>
                                <svg class="w-6 h-6 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            @else
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                    <span class="text-sm font-bold text-red-600">Tutup</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </button>
            @empty
                <div class="col-span-2 text-center py-20 text-2xl text-gray-400 font-semibold">
                    Tidak ada layanan tersedia
                </div>
            @endforelse
        </div>
    </div>

    {{-- Success Modal --}}
    @if($showSuccessModal && $latestTicket)
        <div class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-8"
             @click="$wire.closeModal()">
            <div class="bg-white rounded-3xl p-12 max-w-lg w-full text-center"
                 @click.stop>

                {{-- Success Icon --}}
                <div class="w-24 h-24 rounded-full bg-emerald-500 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h3 class="text-3xl font-black text-gray-900 mb-8">Tiket Anda</h3>

                {{-- Ticket Number - HUGE --}}
                <div class="bg-slate-900 rounded-2xl p-10 mb-8">
                    <div class="text-sm font-bold text-white/50 uppercase tracking-wider mb-3">Nomor Anda</div>
                    <div class="text-8xl font-black text-white font-mono tracking-tight">
                        {{ $latestTicket['ticket_number'] }}
                    </div>
                </div>

                {{-- QR Code --}}
                <div class="bg-gray-100 rounded-xl p-6 mb-8">
                    <div class="bg-white inline-block p-4 rounded-lg">
                        {!! $latestTicket['qr_svg'] !!}
                    </div>
                </div>

                {{-- Info --}}
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-blue-50 rounded-xl p-6">
                        <div class="text-blue-600 text-sm font-semibold mb-2">Sisa antrian</div>
                        <div class="text-5xl font-black text-blue-900">{{ $latestTicket['ahead_count'] }}</div>
                        <div class="text-blue-600 text-xs mt-1">orang</div>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-6">
                        <div class="text-amber-600 text-sm font-semibold mb-2">Perkiraan waktu</div>
                        <div class="text-5xl font-black text-amber-900">{{ $latestTicket['estimated_wait'] }}</div>
                        <div class="text-amber-600 text-xs mt-1">menit</div>
                    </div>
                </div>

                {{-- Close Button --}}
                <button wire:click="closeModal"
                        class="w-full py-6 bg-slate-900 text-white text-2xl font-black rounded-xl hover:bg-slate-800">
                    Tutup (<span x-text="countdown"></span>s)
                </button>
            </div>
        </div>
    @endif
</div>
