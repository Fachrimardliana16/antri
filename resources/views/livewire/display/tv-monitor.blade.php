<div wire:poll.3s
     class="h-screen flex flex-col bg-slate-900 overflow-hidden"
     x-data="{
         isDucked: false,
         activeCallState: @entangle('activeCall'),
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
         // Auto-unlock audio context on load
         if (window.AntriAudio) {
             window.AntriAudio.getAudioContext();
         }

         window.addEventListener('play-queue-call', (event) => {
             callTicket(event.detail.data);
         });
     ">

    {{-- Header --}}
    <div class="bg-slate-800 border-b border-slate-700 px-8 py-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                @php
                    $logoUrl = \App\Models\AppSetting::getValue('logo_url', '');
                    $appName = \App\Models\AppSetting::getValue('app_name', 'Sistem Antrian Terpadu');
                    $appSubtitle = \App\Models\AppSetting::getValue('tv_subtitle', 'Monitor Panggilan Antrian');
                @endphp

                <div class="w-14 h-14 bg-blue-600 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo" class="w-full h-full object-contain">
                    @else
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white uppercase tracking-tight">{{ $appName }}</h1>
                    <p class="text-blue-400 text-sm">{{ $appSubtitle }}</p>
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
    <div class="flex-1 flex gap-4 p-4 min-h-0 overflow-hidden">
        {{-- Left Sidebar - Info --}}
        <div class="w-64 bg-slate-800 border border-slate-700 rounded-xl p-4 flex-shrink-0">
            <h3 class="text-sm font-bold text-slate-400 mb-4 text-center uppercase tracking-wider">Informasi</h3>
            <div class="space-y-3 text-sm">
                @php
                    try {
                        $announcements = \App\Models\Announcement::active()->ordered()->get();
                    } catch (\Exception $e) {
                        $announcements = collect();
                    }
                @endphp

                @forelse($announcements as $announcement)
                    <div class="bg-slate-900 rounded-lg p-3 border-l-4 border-{{ $announcement->icon_color }}-500">
                        <p class="font-semibold text-white mb-1">{{ $announcement->title }}</p>
                        <p class="text-slate-400">{{ $announcement->content }}</p>
                    </div>
                @empty
                    <div class="bg-slate-900 rounded-lg p-3 border-l-4 border-blue-500">
                        <p class="font-semibold text-white mb-1">Perhatian</p>
                        <p class="text-slate-400">Harap perhatikan nomor antrian Anda di layar monitor</p>
                    </div>
                    <div class="bg-slate-900 rounded-lg p-3 border-l-4 border-green-500">
                        <p class="font-semibold text-white mb-1">Informasi</p>
                        <p class="text-slate-400">Pastikan datang saat nomor Anda dipanggil</p>
                    </div>
                    <div class="bg-slate-900 rounded-lg p-3 border-l-4 border-amber-500">
                        <p class="font-semibold text-white mb-1">Catatan</p>
                        <p class="text-slate-400">Pelayanan dimulai pukul 08:00 - 16:00</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Center - Current Call + Counters --}}
        <div class="flex-1 flex flex-col gap-4 min-h-0 overflow-hidden">
            {{-- Current Call Spotlight - Horizontal Layout --}}
            @if($activeCall)
                <div class="bg-blue-600 border border-blue-500 rounded-2xl p-8 text-white flex items-center justify-center gap-8 flex-shrink-0">
                    <div class="text-right">
                        <div class="text-xl font-bold text-blue-200 uppercase tracking-wider">Calling</div>
                    </div>
                    <div class="text-center">
                        <div class="text-[140px] leading-none font-black font-mono">
                            {{ $activeCall['ticket_number'] }}
                        </div>
                    </div>
                    <div class="text-left">
                        <div class="text-3xl font-bold text-blue-100">({{ $activeCall['counter_name'] }})</div>
                    </div>
                </div>
            @else
                <div class="bg-slate-800 border border-slate-700 rounded-2xl p-8 text-center flex-shrink-0">
                    <div class="text-slate-500">
                        <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <div class="text-xl font-semibold">Menunggu Panggilan</div>
                    </div>
                </div>
            @endif

            {{-- Counter Grid - Max 6 counters --}}
            <div class="flex-1 min-h-0 overflow-y-auto">
                <div class="grid grid-cols-3 gap-4 pb-4">
                    @forelse($activeCounters->take(6) as $counter)
                        @php
                            $isCurrentCalled = $activeCall && isset($activeCall['counter_number']) && $activeCall['counter_number'] == $counter->number;
                            $waitingCount = $counter->service ? $counter->service->waitingTickets()->count() : 0;
                        @endphp
                        <div class="bg-slate-800 border-2 {{ $isCurrentCalled ? 'border-blue-500 ring-4 ring-blue-500/50' : 'border-slate-700' }} rounded-xl p-4 text-center transition-all {{ $isCurrentCalled ? 'scale-105' : '' }}">
                            <div class="text-xs font-bold {{ $isCurrentCalled ? 'text-blue-400' : 'text-slate-400' }} mb-2 uppercase tracking-wider">
                                {{ $counter->name }}
                            </div>
                            <div class="text-6xl font-black font-mono mb-2 {{ $isCurrentCalled ? 'text-blue-400' : 'text-white' }} leading-none">
                                @if($counter->currentTicket)
                                    {{ $counter->currentTicket->ticket_number }}
                                @else
                                    <span class="text-slate-600">—</span>
                                @endif
                            </div>
                            @if($counter->service)
                                <div class="text-xs font-semibold {{ $isCurrentCalled ? 'text-blue-300' : 'text-slate-400' }} mb-2 truncate">
                                    {{ $counter->service->name }}
                                </div>
                            @endif
                            <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full {{ $isCurrentCalled ? 'bg-blue-500/20 text-blue-300' : 'bg-slate-700 text-slate-400' }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span class="text-xs font-bold">{{ $waitingCount }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 flex items-center justify-center bg-slate-800 border border-slate-700 rounded-xl py-20">
                            <div class="text-center text-slate-500">
                                <div class="text-6xl mb-3">🔒</div>
                                <div class="text-xl font-semibold">Semua Loket Tutup</div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right - Video --}}
        <div class="w-96 flex-shrink-0">
            <div class="h-full bg-black border border-slate-700 rounded-xl overflow-hidden relative">
                @php
                    $videoUrl = $videoUrl ?? \App\Models\AppSetting::getValue('video_url', '');
                    $isYouTube = str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be');
                @endphp

                @if($videoUrl && $isYouTube)
                    <iframe id="tv-video-player"
                            class="w-full h-full absolute inset-0"
                            :style="isDucked ? 'opacity: 0.2;' : 'opacity: 1;'"
                            src="{{ $videoUrl }}"
                            allow="autoplay; encrypted-media"
                            allowfullscreen>
                    </iframe>
                @elseif($videoUrl)
                    <video id="tv-video-player"
                           class="w-full h-full absolute inset-0 object-cover"
                           :style="isDucked ? 'opacity: 0.2;' : 'opacity: 1;'"
                           autoplay
                           loop
                           muted
                           playsinline>
                        <source src="{{ $videoUrl }}" type="video/mp4">
                        <source src="{{ $videoUrl }}" type="video/webm">
                        Browser Anda tidak mendukung video player.
                    </video>
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-600">
                        <div class="text-center">
                            <svg class="w-20 h-20 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm font-semibold">Video tidak tersedia</p>
                            <p class="text-xs mt-1">Set video URL di Settings</p>
                        </div>
                    </div>
                @endif

                <div x-show="isDucked"
                     class="absolute inset-0 flex items-center justify-center bg-black/90">
                    <div class="text-white text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                        </svg>
                        <div class="text-lg font-bold">Sedang Memanggil...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer - Running Text --}}
    <div class="bg-slate-800 border-t border-slate-700 px-8 py-3 overflow-hidden">
        <div class="relative flex items-center">
            <div class="flex items-center gap-2 bg-blue-600 px-3 py-1 rounded text-white font-bold text-xs uppercase tracking-wide mr-4 flex-shrink-0 z-10">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
                INFO
            </div>
            <div class="flex-1 overflow-hidden relative">
                @php
                    $marqueeText = \App\Models\AppSetting::getValue('marquee_text', 'Harap perhatikan nomor antrian Anda dan segera datang saat dipanggil');
                @endphp
                <div class="flex animate-marquee">
                    <span class="text-slate-300 text-sm font-medium whitespace-nowrap px-4">
                        {{ $marqueeText }} •
                    </span>
                    <span class="text-slate-300 text-sm font-medium whitespace-nowrap px-4">
                        {{ $marqueeText }} •
                    </span>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            display: flex;
            animation: marquee 30s linear infinite;
        }
    </style>
</div>
