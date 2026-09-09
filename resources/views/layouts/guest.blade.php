<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Masuk' }} - {{ $settings['app_name'] ?? 'Sistem Antrian Terpadu' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,600,700" rel="stylesheet" />

    <style>
        :root {
            --primary: {{ $settings['primary_color'] ?? '#2563eb' }};
            --secondary: {{ $settings['secondary_color'] ?? '#06b6d4' }};
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full flex items-center justify-center bg-slate-950 font-sans antialiased selection:bg-blue-600 selection:text-white relative overflow-hidden">
    <!-- Ambient glowing backgrounds -->
    <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-blue-600/20 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-cyan-600/20 blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md p-6 z-10">
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 rounded-2xl items-center justify-center text-white font-black text-2xl shadow-2xl mb-4" style="background: linear-gradient(135deg, var(--primary), var(--secondary));">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black tracking-tight text-white">{{ $settings['app_name'] ?? 'Sistem Antrian Terpadu' }}</h1>
            <p class="text-sm text-slate-400 mt-1">Silakan masuk dengan akun Operator atau Administrator</p>
        </div>

        <div class="glass-panel rounded-2xl p-8 shadow-2xl border border-slate-800/80">
            {{ $slot ?? '' }}
            @yield('content')
        </div>

        <div class="mt-6 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} {{ $settings['app_name'] ?? 'Sistem Antrian Terpadu' }}
        </div>
    </div>

    @livewireScripts
</body>
</html>
