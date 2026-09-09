<div wire:poll.5s class="h-screen overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 flex flex-col"
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
    <div class="bg-white/5 backdrop-blur-sm border-b border-white/10">
        <div class="max-w-7xl mx-auto px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">Ambil Nomor Antrian</h1>
                    <p class="text-white/60 mt-1">Pilih layanan yang Anda butuhkan</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-white/40">{{ now()->translatedFormat('l, d F Y') }}</div>
                    <div class="text-2xl font-bold text-white mt-1">{{ now()->format('H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Service Grid --}}
    <div class="flex-1 overflow-y-auto px-8 py-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($services as $service)
                    @php
                        $hasActiveCounter = $service->hasActiveCounter();
                        $activeCounterCount = $service->counters->where('status', 'active')->count();
                    @endphp

                    <button type="button"
                            wire:click="takeTicket({{ $service->id }})"
                            @if(!$hasActiveCounter) disabled @endif
                            class="group relative bg-white rounded-2xl p-8 text-left transition-all duration-200 {{ $hasActiveCounter ? 'hover:scale-[1.02] hover:shadow-2xl cursor-pointer' : 'opacity-40 cursor-not-allowed' }}"
                            style="min-height: 200px;">

                        {{-- Service Code Badge --}}
                        <div class="absolute top-8 right-8">
                            <div class="w-20 h-20 rounded-2xl bg-slate-900 flex items-center justify-center shadow-lg">
                                <span class="text-4xl font-black text-white">{{ $service->code }}</span>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="pr-24">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $service->name }}</h2>
                            <p class="text-gray-600 text-sm mb-6">{{ $service->description }}</p>

                            {{-- Stats --}}
                            <div class="flex items-center gap-6 mb-6">
                                <div class="flex items-center gap-2">
                                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500">Sedang Mengantri</div>
                                        <div class="text-lg font-bold text-gray-900">{{ $service->waiting_tickets_count }}</div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500">Estimasi</div>
                                        <div class="text-lg font-bold text-gray-900">~{{ $service->estimated_time_minutes }} menit</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Status & CTA --}}
                            <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                                @if($hasActiveCounter)
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                        <span class="text-sm font-semibold text-emerald-600">{{ $activeCounterCount }} Loket Tersedia</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-slate-900 group-hover:text-blue-600 transition-colors">
                                        <span class="text-sm font-bold">Sentuh untuk Ambil</span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                        <span class="text-sm font-semibold text-red-600">Loket Tutup</span>
                                    </div>
                                    <span class="text-sm text-gray-400">Tidak Tersedia</span>
                                @endif
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="col-span-2 text-center py-20">
                        <div class="inline-flex w-20 h-20 rounded-full bg-white/5 items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                        </div>
                        <p class="text-xl font-semibold text-white/60">Belum ada layanan tersedia</p>
                        <p class="text-white/40 mt-2">Silakan hubungi petugas untuk informasi lebih lanjut</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Success Modal --}}
    @if($showSuccessModal && $latestTicket)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-8 bg-black/80 backdrop-blur-md"
             @click="$wire.closeModal()">
            <div class="w-full max-w-md bg-white rounded-3xl p-10 text-center shadow-2xl"
                 @click.stop>

                {{-- Success Icon --}}
                <div class="w-20 h-20 rounded-full bg-emerald-500 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h3 class="text-2xl font-bold text-gray-900 mb-2">Tiket Berhasil Dicetak</h3>
                <p class="text-gray-600 mb-8">{{ $latestTicket['service_name'] }}</p>

                {{-- Ticket Number --}}
                <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-8 mb-6">
                    <div class="text-sm font-semibold text-white/60 uppercase tracking-wider mb-2">Nomor Antrian Anda</div>
                    <div class="text-7xl font-black text-white tracking-tight font-mono">
                        {{ $latestTicket['ticket_number'] }}
                    </div>
                </div>

                {{-- QR Code --}}
                <div class="bg-gray-50 rounded-2xl p-6 mb-6">
                    <div class="bg-white inline-block p-4 rounded-xl">
                        {!! $latestTicket['qr_svg'] !!}
                    </div>
                    <p class="text-sm text-gray-600 mt-3">Scan untuk pantau antrian di ponsel Anda</p>
                </div>

                {{-- Info Cards --}}
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-blue-50 rounded-xl p-4">
                        <div class="text-xs text-blue-600 font-semibold mb-1">Di Depan Anda</div>
                        <div class="text-2xl font-bold text-blue-900">{{ $latestTicket['ahead_count'] }}</div>
                        <div class="text-xs text-blue-600">Orang</div>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-4">
                        <div class="text-xs text-amber-600 font-semibold mb-1">Est. Tunggu</div>
                        <div class="text-2xl font-bold text-amber-900">{{ $latestTicket['estimated_wait'] }}</div>
                        <div class="text-xs text-amber-600">Menit</div>
                    </div>
                </div>

                {{-- Close Button --}}
                <button type="button" wire:click="closeModal"
                        class="w-full py-4 bg-slate-900 text-white text-lg font-bold rounded-xl hover:bg-slate-800 transition-colors">
                    Selesai (<span x-text="countdown"></span>s)
                </button>
            </div>
        </div>
    @endif
</div>
