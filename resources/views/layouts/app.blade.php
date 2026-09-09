<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? $settings['app_name'] ?? 'Sistem Antrian Terpadu' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Dynamic Theme Styling via CSS Variables -->
    <style>
        :root {
            --primary: {{ $settings['primary_color'] ?? '#2563eb' }};
            --secondary: {{ $settings['secondary_color'] ?? '#06b6d4' }};
        }
        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
        }
        .btn-primary:hover {
            filter: brightness(1.1);
        }
        .text-primary-custom {
            color: var(--primary);
        }
        .border-primary-custom {
            border-color: var(--primary);
        }
        .bg-primary-custom {
            background-color: var(--primary);
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full flex flex-col bg-slate-950 font-sans text-slate-100 antialiased selection:bg-blue-600 selection:text-white">
    <div class="min-h-full flex flex-col">
        <!-- Top Navbar -->
        <header class="sticky top-0 z-40 border-b border-slate-800 bg-slate-900/80 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Brand -->
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold shadow-lg" style="background: linear-gradient(135deg, var(--primary), var(--secondary));">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-lg font-bold tracking-tight text-white block leading-tight">{{ $settings['app_name'] ?? 'Sistem Antrian' }}</span>
                            <span class="text-xs text-slate-400">TALL Stack Edition</span>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <nav class="hidden md:flex items-center space-x-1">
                        @auth
                            @if(auth()->user()->isOperator() || auth()->user()->isAdmin())
                                <a href="{{ route('operator.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('operator.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                                    Loket Operator
                                </a>
                            @endif

                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.analytics') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.analytics') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                                    Analitik & SLA
                                </a>
                                <a href="{{ route('admin.services') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.services') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                                    Master Layanan
                                </a>
                                <a href="{{ route('admin.counters') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.counters') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                                    Master Loket
                                </a>
                                <a href="{{ route('admin.users') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.users') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                                    Pengguna
                                </a>
                            @endif

                            @if(auth()->user()->isSuperAdmin())
                                <a href="{{ route('admin.settings') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.settings') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                                    Kustom Tema & UI
                                </a>
                            @endif
                        @endauth

                        <div class="h-5 w-px bg-slate-800 mx-2"></div>

                        <!-- Public Displays -->
                        <a href="{{ route('kiosk.take-ticket') }}" target="_blank" class="px-3 py-2 rounded-lg text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20 transition flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Buka Layar Kiosk
                        </a>
                        <a href="{{ route('display.tv') }}" target="_blank" class="px-3 py-2 rounded-lg text-xs font-semibold bg-cyan-500/10 text-cyan-400 border border-cyan-500/30 hover:bg-cyan-500/20 transition flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                            Buka TV Monitor
                        </a>
                    </nav>

                    <!-- User Actions -->
                    <div class="flex items-center space-x-4">
                        @auth
                            <div class="flex items-center space-x-3">
                                <div class="text-right hidden sm:block">
                                    <div class="text-sm font-medium text-white">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-slate-400 uppercase tracking-wider font-semibold">{{ auth()->user()->role }}</div>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-red-400 hover:bg-slate-800 transition" title="Keluar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg text-sm font-semibold bg-blue-600 hover:bg-blue-500 text-white transition shadow-lg shadow-blue-500/25">
                                Masuk Admin
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-900 bg-slate-950 py-4 text-center text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} {{ $settings['app_name'] ?? 'Sistem Antrian Terpadu' }} — Built with TALL Stack & Laravel Reverb</p>
        </footer>
    </div>

    @livewireScripts
</body>
</html>
