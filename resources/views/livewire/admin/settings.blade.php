<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Kustomisasi Tema & Pengaturan Sistem</h1>
            <p class="text-sm text-slate-400 mt-1">Ubah warna visual, identitas sistem, suara panggilan (TTS), dan tampilan publik.</p>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        
        <!-- Theme Colors Card -->
        <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800 space-y-6">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
                <span>Warna Tema & Branding Visual</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Primary Color -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Primary Color (Warna Utama)</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" wire:model="primaryColor" class="w-12 h-12 rounded-xl bg-transparent border-0 cursor-pointer p-0">
                        <input type="text" wire:model="primaryColor" class="flex-1 bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white font-mono focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <!-- Secondary Color -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Secondary / Accent Color</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" wire:model="secondaryColor" class="w-12 h-12 rounded-xl bg-transparent border-0 cursor-pointer p-0">
                        <input type="text" wire:model="secondaryColor" class="flex-1 bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white font-mono focus:outline-none focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Application Name -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-2">Nama Instansi / Aplikasi</label>
                <input type="text" wire:model="appName" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500" placeholder="Sistem Antrian Terpadu">
                @error('appName') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- TV Display & Audio Card -->
        <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800 space-y-6">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                </svg>
                <span>Pengaturan Suara TTS & Media TV</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Voice Speed Rate -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Kecepatan Bicara (Rate: {{ $voiceRate }})</label>
                    <input type="range" min="0.5" max="1.5" step="0.1" wire:model.live="voiceRate" class="w-full accent-blue-500">
                </div>

                <!-- Voice Pitch -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Pitch Suara (Pitch: {{ $voicePitch }})</label>
                    <input type="range" min="0.5" max="1.5" step="0.1" wire:model.live="voicePitch" class="w-full accent-cyan-500">
                </div>
            </div>

            <!-- Running Text Marquee -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-2">Teks Berjalan Layar TV (Running Text / Marquee)</label>
                <textarea wire:model="marqueeText" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500" placeholder="Teks informasi yang berjalan di bagian bawah TV monitor..."></textarea>
            </div>

            <!-- Video Embed URL -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-2">URL Video TV (YouTube Embed URL / MP4)</label>
                <input type="text" wire:model="videoUrl" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500" placeholder="https://www.youtube.com/embed/...">
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold text-sm shadow-xl shadow-blue-600/30 hover:brightness-110 active:scale-98 transition">
                Simpan & Sinkronkan Pengaturan
            </button>
        </div>
    </form>
</div>
