<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Live Tracking Antrean - {{ $settings['app_name'] ?? 'Sistem Antrian Terpadu' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,600,700,800" rel="stylesheet" />

    <style>
        :root {
            --primary: {{ $settings['primary_color'] ?? '#2563eb' }};
            --secondary: {{ $settings['secondary_color'] ?? '#06b6d4' }};
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full flex flex-col bg-slate-950 text-slate-100 font-sans antialiased selection:bg-blue-600 selection:text-white">
    <div class="min-h-full max-w-lg mx-auto w-full flex flex-col p-4 sm:p-6">
        <!-- Top App Bar -->
        <header class="flex items-center justify-between py-4 border-b border-slate-800">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-black shadow-lg" style="background: linear-gradient(135deg, var(--primary), var(--secondary));">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-bold text-white leading-tight">{{ $settings['app_name'] ?? 'Sistem Antrian' }}</h1>
                    <p class="text-xs text-cyan-400 font-medium">Live QR Tracking Antrean</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Live</span>
            </div>
        </header>

        <!-- Main Content Slot -->
        <main class="flex-1 py-6 flex flex-col">
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        <footer class="py-4 text-center text-xs text-slate-500 border-t border-slate-900">
            Halaman ini terhubung secara langsung & otomatis terupdate saat nomor Anda dipanggil.
        </footer>
    </div>

    @livewireScripts
</body>
</html>
