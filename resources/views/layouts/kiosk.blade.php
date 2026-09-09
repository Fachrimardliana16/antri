<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full select-none">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kiosk Tiket - {{ $settings['app_name'] ?? 'Sistem Antrian Terpadu' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,600,700,800" rel="stylesheet" />

    <style>
        :root {
            --primary: {{ $settings['primary_color'] ?? '#1a56a8' }};
            --secondary: {{ $settings['secondary_color'] ?? '#2d7dd2' }};
        }
        * { -webkit-user-select: none; user-select: none; touch-action: manipulation; }
        body { font-family: 'Instrument Sans', sans-serif; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full flex flex-col overflow-hidden" style="background-color: #0f1d35;">

    {{-- Header --}}
    <header style="background-color: #0a1628; border-bottom: 1px solid rgba(255,255,255,0.08);"
            class="px-8 py-5 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white"
                 style="background-color: var(--primary);">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white uppercase tracking-tight">{{ $settings['app_name'] ?? 'SISTEM ANTRIAN TERPADU' }}</h1>
                <p class="text-sm text-blue-300 mt-0.5">Silakan pilih layanan yang Anda butuhkan</p>
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
            <div class="text-3xl font-bold text-white font-mono tracking-widest" x-text="time"></div>
            <div class="text-sm text-blue-300 mt-0.5" x-text="date"></div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 flex flex-col p-8 overflow-y-auto">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer style="background-color: #0a1628; border-top: 1px solid rgba(255,255,255,0.06);"
            class="px-8 py-3 flex items-center justify-between text-sm text-blue-300">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-green-400"></span>
            <span>Sistem Online &amp; Siap</span>
        </div>
        <div class="text-xs text-blue-400">
            Tiket akan dicetak otomatis setelah memilih layanan.
        </div>
    </footer>

    @livewireScripts
</body>
</html>
