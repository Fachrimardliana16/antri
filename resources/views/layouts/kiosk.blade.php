<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full select-none bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kiosk Tiket - {{ $settings['app_name'] ?? 'Sistem Antrian Terpadu' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,600,700,800" rel="stylesheet" />

    <style>
        :root {
            --primary: {{ $settings['primary_color'] ?? '#2563eb' }};
            --secondary: {{ $settings['secondary_color'] ?? '#06b6d4' }};
        }
        /* Disable touch zoom and text selection */
        * {
            -webkit-user-select: none;
            user-select: none;
            touch-action: manipulation;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full flex flex-col bg-slate-950 text-slate-100 overflow-hidden font-sans antialiased">
    <!-- Header Banner -->
    <header class="border-b border-slate-800/80 bg-slate-900/90 px-8 py-5 flex items-center justify-between shadow-2xl backdrop-blur-md">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-xl" style="background: linear-gradient(135deg, var(--primary), var(--secondary));">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white uppercase">{{ $settings['app_name'] ?? 'SISTEM ANTRIAN TERPADU' }}</h1>
                <p class="text-sm font-medium text-slate-400">Silakan pilih layanan yang Anda butuhkan di bawah ini</p>
            </div>
        </div>

        <div x-data="{ time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }), date: new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }"
             x-init="setInterval(() => {
                 time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                 date = new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
             }, 1000)"
             class="text-right">
            <div class="text-3xl font-black tracking-wider text-cyan-400 font-mono" x-text="time"></div>
            <div class="text-sm font-medium text-slate-400" x-text="date"></div>
        </div>
    </header>

    <!-- Kiosk Interactive Content -->
    <main class="flex-1 flex flex-col p-6 sm:p-10 overflow-y-auto">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Footer Bar -->
    <footer class="border-t border-slate-800 bg-slate-900/60 px-8 py-3.5 flex items-center justify-between text-xs text-slate-400">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
            <span class="font-semibold text-slate-300">Sistem Kiosk Siap & Online</span>
        </div>
        <div class="text-slate-400">
            Struk akan dicetak otomatis setelah memilih layanan. Simpan nomor tiket Anda.
        </div>
    </footer>

    @livewireScripts
</body>
</html>
