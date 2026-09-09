<div class="space-y-5 max-w-3xl">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Kustomisasi Tema & Pengaturan Sistem</h1>
        <p class="text-sm text-gray-500 mt-0.5">Ubah warna visual, identitas sistem, suara panggilan (TTS), dan tampilan publik.</p>
    </div>

    <form wire:submit.prevent="save" class="space-y-5">

        {{-- Theme Colors --}}
        <div class="gov-card p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
                Warna Tema & Branding Visual
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Primary Color (Warna Utama)</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model="primaryColor" class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer p-0.5">
                        <input type="text" wire:model="primaryColor"
                               class="flex-1 border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm font-mono text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Secondary / Accent Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model="secondaryColor" class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer p-0.5">
                        <input type="text" wire:model="secondaryColor"
                               class="flex-1 border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm font-mono text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Instansi / Aplikasi</label>
                <input type="text" wire:model="appName"
                       class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                       placeholder="Sistem Antrian Terpadu">
                @error('appName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Audio & TV --}}
        <div class="gov-card p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                </svg>
                Pengaturan Suara TTS & Media TV
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kecepatan Bicara (Rate: {{ $voiceRate }})</label>
                    <input type="range" min="0.5" max="1.5" step="0.1" wire:model.live="voiceRate"
                           class="w-full accent-blue-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Pitch Suara (Pitch: {{ $voicePitch }})</label>
                    <input type="range" min="0.5" max="1.5" step="0.1" wire:model.live="voicePitch"
                           class="w-full accent-blue-600">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Teks Berjalan Layar TV (Running Text / Marquee)</label>
                <textarea wire:model="marqueeText" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                          placeholder="Teks informasi yang berjalan di bagian bawah TV monitor..."></textarea>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">URL Video TV (YouTube Embed / MP4)</label>
                <input type="text" wire:model="videoUrl"
                       class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                       placeholder="https://www.youtube.com/embed/...">
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end">
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg text-sm font-semibold text-white transition hover:opacity-90"
                    style="background-color: var(--primary, #1a56a8);">
                Simpan & Sinkronkan Pengaturan
            </button>
        </div>
    </form>
</div>
