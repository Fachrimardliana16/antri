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

    <!-- Service Selection Grid -->
    <div class="flex-1 flex flex-col justify-center max-w-6xl mx-auto w-full py-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
            @forelse($services as $service)
                @php
                    $hasActiveCounter = $service->hasActiveCounter();
                    $activeCounterCount = $service->counters->where('status', 'active')->count();
                @endphp
                <div class="relative group">
                    <button type="button"
                            wire:click="takeTicket({{ $service->id }})"
                            @if(!$hasActiveCounter) disabled @endif
                            class="w-full text-left p-8 rounded-3xl transition-all duration-300 transform active:scale-95 flex flex-col justify-between h-64 border shadow-2xl relative overflow-hidden {{ $hasActiveCounter ? 'bg-slate-900/80 hover:bg-slate-850 border-slate-700/80 hover:border-slate-500 cursor-pointer' : 'bg-slate-900/40 border-slate-800/40 opacity-60 cursor-not-allowed' }}">
                        
                        <!-- Top Header in Card -->
                        <div class="flex items-start justify-between w-full">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 rounded-2xl flex items-center justify-center font-black text-3xl text-white shadow-lg"
                                     style="background: {{ $service->color ?? 'var(--primary)' }};">
                                    {{ $service->code }}
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white tracking-tight leading-tight">{{ $service->name }}</h2>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm line-clamp-2">{{ $service->description }}</p>
                                </div>
                            </div>

                            <!-- Counter Status Pill -->
                            <div>
                                @if($hasActiveCounter)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                        {{ $activeCounterCount }} Loket Buka
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/40">
                                        <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                                        Loket Tutup
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Card Bottom Stats & Action -->
                        <div class="w-full flex items-center justify-between border-t border-slate-800/80 pt-4 mt-auto">
                            <div class="flex items-center space-x-6">
                                <div>
                                    <div class="text-xs text-slate-400 font-medium">Antrean Saat Ini</div>
                                    <div class="text-xl font-bold text-cyan-400 font-mono">{{ $service->waiting_tickets_count }} Orang</div>
                                </div>
                                <div class="border-l border-slate-800 pl-6">
                                    <div class="text-xs text-slate-400 font-medium">Estimasi / Orang</div>
                                    <div class="text-xl font-bold text-slate-300 font-mono">{{ $service->estimated_time_minutes }} Menit</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 font-bold text-sm {{ $hasActiveCounter ? 'text-blue-400 group-hover:translate-x-1 transition' : 'text-slate-600' }}">
                                <span>{{ $hasActiveCounter ? 'Sentuh untuk Ambil Tiket' : 'Tidak Tersedia' }}</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Subtle Color Glow Strip -->
                        <div class="absolute bottom-0 left-0 right-0 h-1.5" style="background: {{ $service->color ?? 'var(--primary)' }};"></div>
                    </button>
                </div>
            @empty
                <div class="col-span-2 text-center py-16 glass-panel rounded-3xl">
                    <p class="text-lg text-slate-400">Belum ada data layanan yang aktif saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Ticket Success & Print Modal -->
    @if($showSuccessModal && $latestTicket)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md animate-fade-in"
             @keydown.escape.window="$wire.closeModal()">
            <div class="glass-panel w-full max-w-md rounded-3xl p-8 border-2 border-cyan-500/40 shadow-2xl relative text-center flex flex-col items-center">
                
                <!-- Print Animation Icon -->
                <div class="w-20 h-20 rounded-full bg-cyan-500/20 text-cyan-400 flex items-center justify-center mb-4 border border-cyan-500/40 animate-bounce">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-white uppercase tracking-wider">Tiket Berhasil Dicetak</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $latestTicket['service_name'] }}</p>

                <!-- Ticket Big Number -->
                <div class="my-6 py-6 px-10 rounded-2xl bg-slate-900/90 border border-slate-700 w-full shadow-inner">
                    <div class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Nomor Antrean Anda</div>
                    <div class="text-6xl font-black text-cyan-400 font-mono tracking-wider">{{ $latestTicket['ticket_number'] }}</div>
                </div>

                <!-- QR Code & Mobile Tracking -->
                <div class="bg-white p-3 rounded-xl shadow-lg mb-4">
                    {!! $latestTicket['qr_svg'] !!}
                </div>
                <div class="text-xs font-semibold text-slate-300">
                    Scan QR di atas untuk pantau antrean secara live via HP
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-4 w-full text-xs text-slate-300 mt-4 pt-4 border-t border-slate-800">
                    <div class="bg-slate-900/60 p-2.5 rounded-xl border border-slate-800">
                        <div class="text-slate-400">Antrean di Depan</div>
                        <div class="text-base font-bold text-white font-mono mt-0.5">{{ $latestTicket['ahead_count'] }} Orang</div>
                    </div>
                    <div class="bg-slate-900/60 p-2.5 rounded-xl border border-slate-800">
                        <div class="text-slate-400">Estimasi Tunggu</div>
                        <div class="text-base font-bold text-amber-400 font-mono mt-0.5">{{ $latestTicket['estimated_wait'] }} Menit</div>
                    </div>
                </div>

                <!-- Close & Countdown Button -->
                <button type="button"
                        wire:click="closeModal"
                        class="mt-6 w-full py-3.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold text-sm shadow-lg shadow-blue-600/30 hover:brightness-110 active:scale-95 transition">
                    Selesai (<span x-text="countdown"></span>s)
                </button>
            </div>
        </div>
    @endif
</div>
