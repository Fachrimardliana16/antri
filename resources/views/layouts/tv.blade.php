<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full select-none">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Display Antrean - {{ $settings['app_name'] ?? 'Sistem Antrian Terpadu' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,600,700,800,900" rel="stylesheet" />

    <style>
        :root {
            --primary: {{ $settings['primary_color'] ?? '#1a56a8' }};
            --secondary: {{ $settings['secondary_color'] ?? '#2d7dd2' }};
        }
        body { font-family: 'Instrument Sans', sans-serif; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full w-full flex flex-col overflow-hidden" style="background-color: #0a1628;">

    {{-- Header --}}
    <header style="background-color: #071020; border-bottom: 1px solid rgba(255,255,255,0.07);"
            class="px-8 py-3 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white"
                 style="background-color: var(--primary);">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-lg font-bold text-white uppercase tracking-tight">{{ $settings['app_name'] ?? 'SISTEM ANTRIAN TERPADU' }}</h1>
                <p class="text-xs text-blue-300 tracking-wider uppercase">Papan Informasi Antrean</p>
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
            <div class="text-xs text-blue-300 uppercase" x-text="date"></div>
            <div class="text-2xl font-bold text-white font-mono tracking-widest" x-text="time"></div>
        </div>
    </header>

    {{-- Main --}}
    <main class="flex-1 overflow-hidden p-5">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    {{-- Footer marquee --}}
    <footer style="height: 44px; background-color: var(--primary); border-top: 1px solid rgba(255,255,255,0.1);"
            class="flex items-center px-4 overflow-hidden">
        <div class="flex items-center gap-2 bg-white/20 px-3 py-1 rounded text-white font-bold text-xs uppercase tracking-wide mr-4 flex-shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
            </svg>
            INFO
        </div>
        <div class="flex-1 overflow-hidden text-white text-sm font-medium">
            <div class="animate-marquee">
                {{ $settings['marquee_text'] ?? 'Selamat datang di Kantor Pelayanan Terpadu. Mohon perhatikan nomor antrean Anda.' }}
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
