<div wire:poll.3s
     x-data="{
         status: '{{ $ticket ? $ticket->status : 'unknown' }}',
         vibrateUser() {
             if (navigator.vibrate) {
                 navigator.vibrate([300, 100, 300, 100, 500]);
             }
         }
     }"
     x-init="
         $watch('status', val => {
             if (val === 'calling' || val === 'serving') {
                 vibrateUser();
             }
         });
     "
     class="flex-1 flex flex-col justify-between">

    @if(!$ticket)
        <div class="glass-panel p-8 rounded-3xl text-center border border-rose-500/30 my-auto">
            <div class="w-16 h-16 rounded-full bg-rose-500/20 text-rose-400 mx-auto flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-white">Tiket Tidak Ditemukan</h2>
            <p class="text-xs text-slate-400 mt-2">Kode QR tidak valid atau masa berlaku antrean telah berakhir.</p>
        </div>
    @else
        <div class="space-y-6">
            
            <!-- Status Alert Banner -->
            @if($ticket->status === 'calling')
                <div class="p-4 rounded-2xl bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-center shadow-xl animate-bounce">
                    <div class="text-sm">🔔 NOMOR ANDA SEDANG DIPANGGIL!</div>
                    <div class="text-xs font-normal mt-0.5">Segera menuju {{ $ticket->counter ? $ticket->counter->name : 'Loket' }}</div>
                </div>
            @elseif($ticket->status === 'serving')
                <div class="p-4 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-center shadow-xl">
                    <div class="text-sm">✨ SEDANG DILAYANI DI {{ $ticket->counter ? $ticket->counter->name : 'LOKET' }}</div>
                </div>
            @elseif($ticket->status === 'completed')
                <div class="p-4 rounded-2xl bg-slate-800 border border-slate-700 text-slate-300 font-semibold text-center text-xs">
                    Pelayanan telah selesai. Terima kasih telah berkunjung.
                </div>
            @elseif($ticket->status === 'skipped')
                <div class="p-4 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 font-semibold text-center text-xs">
                    Nomor antrean telah dilewati karena tidak hadir saat dipanggil.
                </div>
            @endif

            <!-- Big Ticket Card -->
            <div class="glass-panel rounded-3xl p-6 border border-slate-800 text-center shadow-2xl relative overflow-hidden">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-slate-800 text-cyan-400 mb-2">
                    {{ $ticket->service ? $ticket->service->name : 'Layanan' }}
                </div>

                <div class="text-xs font-semibold uppercase tracking-widest text-slate-400 mt-2">Nomor Antrean Anda</div>
                <div class="text-6xl sm:text-7xl font-black text-white font-mono tracking-wider my-3 {{ in_array($ticket->status, ['calling', 'serving']) ? 'text-cyan-400 animate-pulse' : '' }}">
                    {{ $ticket->ticket_number }}
                </div>

                <div class="text-xs text-slate-400">
                    Waktu Ambil Tiket: <strong class="text-slate-200">{{ $ticket->created_at->format('H:i:s') }}</strong>
                </div>

                <div class="absolute bottom-0 left-0 right-0 h-1.5" style="background: {{ $ticket->service->color ?? 'var(--primary)' }};"></div>
            </div>

            <!-- Wait Info Grid -->
            @if($ticket->status === 'waiting')
                <div class="grid grid-cols-2 gap-4">
                    <div class="glass-panel p-5 rounded-2xl border border-slate-800 text-center">
                        <div class="text-xs text-slate-400 font-medium">Antrean di Depan</div>
                        <div class="text-3xl font-black text-cyan-400 font-mono mt-1">{{ $aheadCount }}</div>
                        <div class="text-[10px] text-slate-500 mt-1">Orang lagi</div>
                    </div>
                    <div class="glass-panel p-5 rounded-2xl border border-slate-800 text-center">
                        <div class="text-xs text-slate-400 font-medium">Estimasi Tunggu</div>
                        <div class="text-3xl font-black text-amber-400 font-mono mt-1">{{ $estimatedWait }}</div>
                        <div class="text-[10px] text-slate-500 mt-1">Menit perkiraan</div>
                    </div>
                </div>
            @endif

            <!-- Loket Destination if assigned -->
            @if($ticket->counter)
                <div class="glass-panel p-4 rounded-2xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <div class="text-xs text-slate-400">Loket Tujuan:</div>
                        <div class="text-lg font-bold text-white">{{ $ticket->counter->name }}</div>
                    </div>
                    @if($ticket->operator)
                        <div class="text-right">
                            <div class="text-xs text-slate-400">Petugas:</div>
                            <div class="text-xs font-semibold text-slate-300">{{ $ticket->operator->name }}</div>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    @endif
</div>
