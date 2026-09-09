<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? $settings['app_name'] ?? 'Sistem Antrian Terpadu' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Dynamic Theme -->
    <style>
        :root {
            --primary: {{ $settings['primary_color'] ?? '#1a56a8' }};
            --primary-dark: color-mix(in srgb, {{ $settings['primary_color'] ?? '#1a56a8' }} 85%, black);
            --primary-light: color-mix(in srgb, {{ $settings['primary_color'] ?? '#1a56a8' }} 10%, white);
            --secondary: {{ $settings['secondary_color'] ?? '#2d7dd2' }};
        }
        .gov-primary-bg { background-color: var(--primary); }
        .gov-primary-text { color: var(--primary); }
        .gov-primary-border { border-color: var(--primary); }
        .gov-primary-light-bg { background-color: var(--primary-light); }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full flex flex-col bg-gray-50" style="font-family: 'Instrument Sans', sans-serif;">

    {{-- Top Stripe --}}
    <div class="h-1 gov-primary-bg"></div>

    {{-- Header --}}
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Brand --}}
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg gov-primary-bg flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-900 leading-none">{{ $settings['app_name'] ?? 'MAL PELAYANAN PUBLIK' }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">Sistem Manajemen Antrean Terpadu</div>
                    </div>
                </div>

                {{-- Navigation --}}
                <nav class="hidden md:flex items-center space-x-1">
                    @auth
                        @if(auth()->user()->isOperator() || auth()->user()->isAdmin())
                            <a href="{{ route('operator.dashboard') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('operator.*') ? 'gov-primary-bg text-white' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                Pelayanan Loket
                            </a>
                        @endif
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.analytics') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('admin.analytics') ? 'gov-primary-bg text-white' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                Laporan & SLA
                            </a>
                            <a href="{{ route('admin.services') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('admin.services') ? 'gov-primary-bg text-white' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                Data Layanan
                            </a>
                            <a href="{{ route('admin.counters') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('admin.counters') ? 'gov-primary-bg text-white' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                Data Loket
                            </a>
                            <a href="{{ route('admin.users') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('admin.users') ? 'gov-primary-bg text-white' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                Pengguna
                            </a>
                        @endif
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('admin.settings') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('admin.settings') ? 'gov-primary-bg text-white' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                Pengaturan
                            </a>
                        @endif
                    @endauth

                    <div class="h-5 w-px bg-gray-200 mx-2"></div>

                    <a href="{{ route('kiosk.take-ticket') }}" target="_blank"
                       class="px-3 py-1.5 rounded-md text-xs font-medium text-gray-600 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Kiosk
                    </a>
                    <a href="{{ route('display.tv') }}" target="_blank"
                       class="px-3 py-1.5 rounded-md text-xs font-medium text-gray-600 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                        Display TV
                    </a>
                </nav>

                {{-- User Actions --}}
                <div class="flex items-center space-x-3">
                    @auth
                        <div class="hidden sm:block text-right">
                            <div class="text-xs font-semibold text-gray-800">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-gray-500 uppercase tracking-wide">{{ auth()->user()->role }}</div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="px-3 py-1.5 rounded-md text-xs font-medium text-gray-600 border border-gray-200 hover:border-red-300 hover:text-red-600 hover:bg-red-50 transition">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           class="px-4 py-2 rounded-md text-sm font-semibold gov-primary-bg text-white hover:opacity-90 transition">
                            Masuk
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if(session('success'))
            <div class="mb-5 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-5 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm flex items-center gap-3">
                <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{ $slot ?? '' }}
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t border-gray-200 bg-white py-4 text-center text-xs text-gray-400">
        <div class="max-w-7xl mx-auto px-4">
            &copy; {{ date('Y') }} {{ $settings['app_name'] ?? 'Mal Pelayanan Publik' }} &mdash; Sistem Antrean Terpadu Layanan Publik
        </div>
    </footer>

    @livewireScripts
</body>
</html>
