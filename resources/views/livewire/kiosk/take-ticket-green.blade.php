<div wire:poll.5s class="h-screen flex flex-col bg-gradient-to-br from-green-400 via-green-500 to-green-600 overflow-hidden"
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

    {{-- Header Banner --}}
    <div class="bg-white/20 backdrop-blur-sm text-white text-center py-6 shadow-lg">
        <h1 class="text-5xl font-black mb-2">SILAHKAN PILIH LAYANAN</h1>
        <p class="text-xl opacity-90">Sentuh tombol layanan yang Anda butuhkan</p>
    </div>

    {{-- Logo/Icon Center --}}
    <div class="absolute top-32 left-1/2 -translate-x-1/2 z-10">
        <div class="w-32 h-32 bg-white rounded-full shadow-2xl flex items-center justify-center">
            <svg class="w-20 h-20 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
        </div>
    </div>

    {{-- Service List - Centered --}}
    <div class="flex-1 flex items-center justify-center px-12 pb-12 pt-24">
        <div class="w-full max-w-4xl space-y-4">
            @forelse($services as $service)
                @php
                    $hasActiveCounter = $service->hasActiveCounter();
                @endphp

                <button type="button"
                        wire:click="takeTicket({{ $service->id }})"
                        @if(!$hasActiveCounter) disabled @endif
                        class="group w-full bg-white rounded-2xl shadow-2xl hover:shadow-3xl transition-all duration-200 overflow-hidden {{ $hasActiveCounter ? 'hover:scale-105 cursor-pointer' : 'opacity-50 cursor-not-allowed' }}">
                    <div class="flex items-center p-6 gap-6">
                        {{-- Icon --}}
                        <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center flex-shrink-0 shadow-lg">
                            <span class="text-4xl font-black text-white">{{ $service->code }}</span>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 text-left">
                            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $service->name }}</h3>
                            <p class="text-gray-600">{{ $service->description }}</p>
                        </div>

                        {{-- Stats --}}
                        <div class="flex items-center gap-8 px-8 border-l-2 border-gray-200">
                            <div class="text-center">
                                <div class="text-sm text-gray-500 font-semibold">ANTRI</div>
                                <div class="text-4xl font-black text-gray-900">{{ $service->waiting_tickets_count }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm text-gray-500 font-semibold">MENIT</div>
                                <div class="text-4xl font-black text-gray-900">~{{ $service->estimated_time_minutes }}</div>
                            </div>
                        </div>

                        {{-- Arrow --}}
                        @if($hasActiveCounter)
                            <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0 group-hover:bg-green-600 transition-colors">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        @else
                            <div class="w-12 h-12 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                </button>
            @empty
                <div class="text-center py-20 text-white">
                    <div class="text-6xl mb-4">📋</div>
                    <div class="text-3xl font-bold">Tidak ada layanan tersedia</div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Success Modal --}}
    @if($showSuccessModal && $latestTicket)
        <div class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-8"
             @click="$wire.closeModal()">
            <div class="bg-white rounded-3xl p-12 max-w-lg w-full text-center shadow-2xl"
                 @click.stop>

                {{-- Success Icon --}}
                <div class="w-28 h-28 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center mx-auto mb-6 shadow-xl">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h3 class="text-4xl font-black text-gray-900 mb-2">BERHASIL!</h3>
                <p class="text-gray-600 mb-8">Tiket Anda telah dicetak</p>

                {{-- Ticket Number - HUGE --}}
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-10 mb-8 shadow-xl">
                    <div class="text-sm font-bold text-white/70 uppercase tracking-wider mb-3">Nomor Antrian</div>
                    <div class="text-9xl font-black text-white font-mono tracking-tight leading-none">
                        {{ $latestTicket['ticket_number'] }}
                    </div>
                </div>

                {{-- Service Name --}}
                <div class="mb-8">
                    <div class="text-lg font-bold text-gray-900">{{ $latestTicket['service_name'] }}</div>
                </div>

                {{-- QR Code --}}
                <div class="bg-gray-50 rounded-2xl p-6 mb-8">
                    <div class="bg-white inline-block p-4 rounded-xl shadow-sm">
                        {!! $latestTicket['qr_svg'] !!}
                    </div>
                    <p class="text-sm text-gray-600 mt-3">Scan untuk pantau antrian</p>
                </div>

                {{-- Info --}}
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-blue-50 rounded-xl p-6">
                        <div class="text-blue-600 text-sm font-semibold mb-2">Sisa Antrian</div>
                        <div class="text-5xl font-black text-blue-900">{{ $latestTicket['ahead_count'] }}</div>
                        <div class="text-blue-600 text-xs mt-1">orang di depan</div>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-6">
                        <div class="text-amber-600 text-sm font-semibold mb-2">Perkiraan</div>
                        <div class="text-5xl font-black text-amber-900">{{ $latestTicket['estimated_wait'] }}</div>
                        <div class="text-amber-600 text-xs mt-1">menit tunggu</div>
                    </div>
                </div>

                {{-- Close Button --}}
                <button wire:click="closeModal"
                        class="w-full py-6 bg-gradient-to-r from-green-500 to-green-600 text-white text-2xl font-black rounded-xl hover:from-green-600 hover:to-green-700 transition-all shadow-lg">
                    SELESAI (<span x-text="countdown"></span>s)
                </button>
            </div>
        </div>
    @endif
</div>