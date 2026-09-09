<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full select-none bg-slate-950 text-slate-100 overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Display Antrean TV - {{ $settings['app_name'] ?? 'Sistem Antrian Terpadu' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,600,700,800,900" rel="stylesheet" />

    <style>
        :root {
            --primary: {{ $settings['primary_color'] ?? '#2563eb' }};
            --secondary: {{ $settings['secondary_color'] ?? '#06b6d4' }};
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full w-full flex flex-col bg-slate-950 text-slate-100 font-sans antialiased overflow-hidden">
    <!-- Top Header -->
    <header class="border-b border-slate-800 bg-slate-900/90 px-8 py-3.5 flex items-center justify-between shadow-2xl backdrop-blur-md">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-black text-2xl shadow-xl" style="background: linear-gradient(135deg, var(--primary), var(--secondary));">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-black tracking-tight text-white uppercase">{{ $settings['app_name'] ?? 'SISTEM ANTRIAN TERPADU' }}</h1>
                <p class="text-xs font-semibold text-cyan-400 tracking-wider uppercase">Papan Informasi Panggilan Antrean</p>
            </div>
        </div>

        <!-- Live Clock & Date -->
        <div x-data="{
                time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }),
                date: new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
             }"
             x-init="setInterval(() => {
                 time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                 date = new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
             }, 1000)"
             class="flex items-center space-x-6">
            <div class="text-right">
                <div class="text-xs font-semibold uppercase text-slate-400" x-text="date"></div>
                <div class="text-3xl font-black text-white font-mono tracking-widest" x-text="time"></div>
            </div>
        </div>
    </header>

    <!-- TV Main Workspace Grid -->
    <main class="flex-1 overflow-hidden p-6">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Bottom Marquee Running Text -->
    <footer class="h-12 border-t border-slate-800 bg-slate-900 flex items-center px-4 overflow-hidden relative shadow-inner">
        <div class="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-600 px-4 py-1.5 rounded-lg text-white font-bold text-xs uppercase tracking-wider z-10 shadow-md">
            <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
            </svg>
            <span>INFO</span>
        </div>
        <div class="flex-1 overflow-hidden ml-4 text-slate-200 text-sm font-medium tracking-wide">
            <div class="animate-marquee">
                {{ $settings['marquee_text'] ?? 'Selamat datang di Kantor Pelayanan Terpadu. Mohon perhatikan nomor antrean Anda.' }}
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
