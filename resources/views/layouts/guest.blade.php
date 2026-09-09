<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Masuk' }} - {{ $settings['app_name'] ?? 'Sistem Antrian Terpadu' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <style>
        :root {
            --primary: {{ $settings['primary_color'] ?? '#1a56a8' }};
            --secondary: {{ $settings['secondary_color'] ?? '#2d7dd2' }};
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full flex items-center justify-center bg-gray-50" style="font-family: 'Instrument Sans', sans-serif;">

    {{-- Background decorative --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-0 right-0 h-1" style="background-color: var(--primary);"></div>
    </div>

    <div class="w-full max-w-md px-4 py-12 z-10">
        {{-- Logo & Title --}}
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 rounded-xl items-center justify-center text-white shadow-md mb-4"
                 style="background-color: var(--primary);">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900">{{ $settings['app_name'] ?? 'Sistem Antrian Terpadu' }}</h1>
            <p class="text-sm text-gray-500 mt-1">Silakan masuk dengan akun Operator atau Administrator</p>
        </div>

        {{-- Card --}}
        <div class="gov-panel p-8">
            {{ $slot ?? '' }}
            @yield('content')
        </div>

        <div class="mt-6 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} {{ $settings['app_name'] ?? 'Sistem Antrian Terpadu' }}
        </div>
    </div>

    @livewireScripts
</body>
</html>
