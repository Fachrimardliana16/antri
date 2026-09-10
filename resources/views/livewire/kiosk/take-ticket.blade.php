<div wire:poll.5s class="h-screen flex flex-col bg-slate-900 overflow-hidden"
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

    {{-- Header --}}
    <div class="bg-slate-800 border-b border-slate-700 px-8 py-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                @php
                    $logoUrl = \App\Models\AppSetting::getValue('logo_url', '');
                    $appName = \App\Models\AppSetting::getValue('app_name', 'Sistem Antrian Terpadu');
                    $kioskSubtitle = \App\Models\AppSetting::getValue('kiosk_subtitle', 'Ambil Nomor Antrian');
                @endphp

                <div class="w-14 h-14 bg-blue-600 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo" class="w-full h-full object-contain">
                    @else
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white uppercase tracking-tight">{{ $appName }}</h1>
                    <p class="text-blue-400 text-sm">{{ $kioskSubtitle }}</p>
                </div>
            </div>
            <div x-data="{
                     time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }),
                     date: new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
                 }"
                 x-init="setInterval(() => {
                     time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                     date = new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                 }, 1000)"
                 class="text-right">
                <div class="text-3xl font-bold text-white font-mono" x-text="time"></div>
                <div class="text-sm text-blue-400" x-text="date"></div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 flex items-center justify-center px-8 py-6 overflow-hidden">
        <div class="w-full max-w-6xl">
            <div class="text-center mb-6">
                <h2 class="text-4xl font-bold text-white mb-2">Pilih Layanan</h2>
                <p class="text-slate-400 text-lg">Sentuh tombol layanan yang Anda butuhkan</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                @forelse($services as $service)
                    @php
                        $hasActiveCounter = $service->hasActiveCounter();
                    @endphp

                    <button type="button"
                            wire:click="takeTicket({{ $service->id }})"
                            @if(!$hasActiveCounter) disabled @endif
                            class="group bg-slate-800 border border-slate-700 rounded-xl hover:border-blue-500 transition-all duration-200 overflow-hidden {{ $hasActiveCounter ? 'hover:bg-slate-750 cursor-pointer' : 'opacity-50 cursor-not-allowed' }}">
                        <div class="flex items-center p-5 gap-4">
                            {{-- Icon --}}
                            <div class="w-16 h-16 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
                                <span class="text-3xl font-black text-white">{{ $service->code }}</span>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 text-left">
                                <h3 class="text-xl font-bold text-white mb-1">{{ $service->name }}</h3>
                                <p class="text-slate-400 text-sm">{{ $service->description }}</p>
                            </div>

                            {{-- Stats --}}
                            <div class="flex items-center gap-6 px-6 border-l border-slate-700">
                                <div class="text-center">
                                    <div class="text-xs text-slate-500 font-semibold uppercase">Antri</div>
                                    <div class="text-3xl font-black text-white">{{ $service->waiting_tickets_count }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xs text-slate-500 font-semibold uppercase mb-1">Status</div>
                                    @if($hasActiveCounter)
                                        <div class="px-3 py-1 rounded-full bg-green-500 text-white text-xs font-black uppercase">AKTIF</div>
                                    @else
                                        <div class="px-3 py-1 rounded-full bg-red-500 text-white text-xs font-black uppercase">TUTUP</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Arrow --}}
                            @if($hasActiveCounter)
                                <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-700 transition-colors">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-lg bg-slate-700 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </button>
                @empty
                    <div class="col-span-2 text-center py-20 text-slate-400">
                        <div class="text-6xl mb-4">📋</div>
                        <div class="text-2xl font-bold">Tidak ada layanan tersedia</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="bg-slate-800 border-t border-slate-700 px-8 py-3">
        <div class="flex items-center justify-between text-sm">
            <div class="flex items-center gap-2 text-blue-400">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span>Sistem Online</span>
            </div>
            <div class="text-slate-500">
                Tiket akan dicetak otomatis setelah memilih layanan
            </div>
        </div>
    </div>

    {{-- Success Modal --}}
    @if($showSuccessModal && $latestTicket)
        <div class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4"
             @click="$wire.closeModal()">
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 max-w-md w-full text-center max-h-[90vh] overflow-y-auto"
                 @click.stop>

                {{-- Success Icon --}}
                <div class="w-16 h-16 rounded-full bg-blue-600 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h3 class="text-2xl font-bold text-white mb-1">Berhasil!</h3>
                <p class="text-slate-400 text-sm mb-6">Tiket Anda telah dicetak</p>

                {{-- Ticket Number --}}
                <div class="bg-blue-600 rounded-xl p-6 mb-4">
                    <div class="text-xs font-semibold text-blue-200 uppercase tracking-wider mb-2">Nomor Antrian</div>
                    <div class="text-7xl font-black text-white font-mono">
                        {{ $latestTicket['ticket_number'] }}
                    </div>
                </div>

                {{-- Service Name --}}
                <div class="mb-4">
                    <div class="text-base font-bold text-white">{{ $latestTicket['service_name'] }}</div>
                </div>

                {{-- QR Code --}}
                <div class="bg-slate-900 rounded-xl p-4 mb-4">
                    <div class="bg-white inline-block p-3 rounded-lg">
                        {!! $latestTicket['qr_svg'] !!}
                    </div>
                    <p class="text-xs text-slate-400 mt-2">Scan untuk pantau antrian</p>
                </div>

                {{-- Info --}}
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-slate-900 rounded-xl p-4 border border-slate-700">
                        <div class="text-blue-400 text-xs font-semibold mb-1 uppercase">Sisa Antrian</div>
                        <div class="text-3xl font-black text-white">{{ $latestTicket['ahead_count'] }}</div>
                        <div class="text-slate-500 text-xs mt-1">orang di depan</div>
                    </div>
                    <div class="bg-slate-900 rounded-xl p-4 border border-slate-700">
                        <div class="text-blue-400 text-xs font-semibold mb-1 uppercase">Perkiraan</div>
                        <div class="text-3xl font-black text-white">{{ $latestTicket['estimated_wait'] }}</div>
                        <div class="text-slate-500 text-xs mt-1">menit tunggu</div>
                    </div>
                </div>

                {{-- Close Button --}}
                <button wire:click="closeModal"
                        class="w-full py-4 bg-blue-600 text-white text-lg font-bold rounded-xl hover:bg-blue-700 transition-colors">
                    Selesai (<span x-text="countdown"></span>s)
                </button>
            </div>
        </div>
    @endif
</div>
