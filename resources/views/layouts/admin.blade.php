<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} - {{ $settings['app_name'] ?? 'Sistem Antrian' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|instrument-sans:400,600,700,800" rel="stylesheet" />
    <style>
        :root {
            --primary: {{ $settings['primary_color'] ?? '#1a56a8' }};
            --primary-dark: {{ $settings['primary_color'] ?? '#154091' }};
            --secondary: {{ $settings['secondary_color'] ?? '#3b82f6' }};
        }
        body { font-family: 'Inter', 'Instrument Sans', sans-serif; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full transition-colors duration-300"
      x-data="{
          sidebarOpen: false,
          profileOpen: false,
          theme: window.ThemeManager.getStoredTheme(),
          toggleTheme() {
              this.theme = window.ThemeManager.toggleTheme();
          }
      }"
      style="background-color: var(--bg-base);">

    <div class="flex h-full">
        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-50 w-64 transform transition-all duration-300 lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               style="background-color: var(--sidebar-bg); border-right: 1px solid var(--sidebar-border);">

            {{-- Logo --}}
            <div class="h-16 flex items-center px-6 border-b" style="border-color: var(--sidebar-border);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white"
                         style="background-color: var(--primary);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold" style="color: var(--sidebar-text-active);">Admin Panel</div>
                        <div class="text-xs" style="color: var(--sidebar-text); opacity: 0.7;">Sistem Antrian</div>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto" style="height: calc(100vh - 12rem);">
                @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                        <a href="{{ route('admin.analytics') }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.analytics') ? 'text-white' : '' }}"
                           style="{{ request()->routeIs('admin.analytics') ? 'background-color: var(--primary); color: white;' : 'color: var(--sidebar-text);' }}"
                           onmouseover="if (!this.classList.contains('text-white')) this.style.backgroundColor='var(--sidebar-hover)'"
                           onmouseout="if (!this.classList.contains('text-white')) this.style.backgroundColor=''">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span>Analitik</span>
                        </a>

                        <a href="{{ route('admin.services') }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.services') ? 'text-white' : '' }}"
                           style="{{ request()->routeIs('admin.services') ? 'background-color: var(--primary); color: white;' : 'color: var(--sidebar-text);' }}"
                           onmouseover="if (!this.classList.contains('text-white')) this.style.backgroundColor='var(--sidebar-hover)'"
                           onmouseout="if (!this.classList.contains('text-white')) this.style.backgroundColor=''">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span>Layanan</span>
                        </a>

                        <a href="{{ route('admin.counters') }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.counters') ? 'text-white' : '' }}"
                           style="{{ request()->routeIs('admin.counters') ? 'background-color: var(--primary); color: white;' : 'color: var(--sidebar-text);' }}"
                           onmouseover="if (!this.classList.contains('text-white')) this.style.backgroundColor='var(--sidebar-hover)'"
                           onmouseout="if (!this.classList.contains('text-white')) this.style.backgroundColor=''">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            <span>Loket</span>
                        </a>

                        <a href="{{ route('admin.users') }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.users') ? 'text-white' : '' }}"
                           style="{{ request()->routeIs('admin.users') ? 'background-color: var(--primary); color: white;' : 'color: var(--sidebar-text);' }}"
                           onmouseover="if (!this.classList.contains('text-white')) this.style.backgroundColor='var(--sidebar-hover)'"
                           onmouseout="if (!this.classList.contains('text-white')) this.style.backgroundColor=''">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Pengguna</span>
                        </a>

                        <a href="{{ route('admin.announcements') }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.announcements') ? 'text-white' : '' }}"
                           style="{{ request()->routeIs('admin.announcements') ? 'background-color: var(--primary); color: white;' : 'color: var(--sidebar-text);' }}"
                           onmouseover="if (!this.classList.contains('text-white')) this.style.backgroundColor='var(--sidebar-hover)'"
                           onmouseout="if (!this.classList.contains('text-white')) this.style.backgroundColor=''">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                            <span>Pengumuman</span>
                        </a>
                    @endif

                    @if(auth()->user()->isSuperAdmin())
                        <div class="pt-4 pb-2">
                            <div class="px-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--sidebar-text); opacity: 0.5;">System</div>
                        </div>

                        <a href="{{ route('admin.settings') }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.settings') ? 'text-white' : '' }}"
                           style="{{ request()->routeIs('admin.settings') ? 'background-color: var(--primary); color: white;' : 'color: var(--sidebar-text);' }}"
                           onmouseover="if (!this.classList.contains('text-white')) this.style.backgroundColor='var(--sidebar-hover)'"
                           onmouseout="if (!this.classList.contains('text-white')) this.style.backgroundColor=''">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Pengaturan</span>
                        </a>
                    @endif

                    <div class="pt-4 pb-2">
                        <div class="px-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--sidebar-text); opacity: 0.5;">Display</div>
                    </div>

                    <a href="{{ route('kiosk.take-ticket') }}" target="_blank"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all"
                       style="color: var(--sidebar-text);"
                       onmouseover="this.style.backgroundColor='var(--sidebar-hover)'"
                       onmouseout="this.style.backgroundColor=''">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>Kiosk</span>
                        <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>

                    <a href="{{ route('display.tv') }}" target="_blank"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all"
                       style="color: var(--sidebar-text);"
                       onmouseover="this.style.backgroundColor='var(--sidebar-hover)'"
                       onmouseout="this.style.backgroundColor=''">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span>TV Monitor</span>
                        <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                @endauth
            </nav>

            {{-- User Profile with Dropdown --}}
            <div class="border-t p-4 relative" style="border-color: var(--sidebar-border);">
                @auth
                    <button @click="profileOpen = !profileOpen"
                            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg transition-all"
                            style="color: var(--sidebar-text);"
                            onmouseover="this.style.backgroundColor='var(--sidebar-hover)'"
                            onmouseout="this.style.backgroundColor=''">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm"
                             style="background-color: var(--sidebar-hover); color: var(--sidebar-text-active);">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0 text-left">
                            <div class="text-sm font-medium truncate" style="color: var(--sidebar-text-active);">{{ auth()->user()->name }}</div>
                            <div class="text-xs" style="color: var(--sidebar-text);">{{ ucfirst(auth()->user()->role) }}</div>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="profileOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div x-show="profileOpen"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         @click.away="profileOpen = false"
                         class="absolute bottom-full left-4 right-4 mb-2 rounded-lg shadow-lg border overflow-hidden"
                         style="background-color: var(--sidebar-bg); border-color: var(--sidebar-border); display: none;">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-3 text-sm font-medium transition-all"
                                    style="color: var(--sidebar-text);"
                                    onmouseover="this.style.backgroundColor='var(--sidebar-hover)'; this.style.color='var(--sidebar-text-active)'"
                                    onmouseout="this.style.backgroundColor=''; this.style.color='var(--sidebar-text)'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 lg:ml-64 flex flex-col h-full">
            {{-- Top Bar --}}
            <header class="sticky top-0 z-40 transition-colors duration-300"
                    style="background-color: var(--bg-surface); border-bottom: 1px solid var(--border-default);">
                <div class="flex items-center justify-between h-16 px-6">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen"
                                class="lg:hidden transition-colors"
                                style="color: var(--text-secondary);"
                                onmouseover="this.style.color='var(--text-primary)'"
                                onmouseout="this.style.color='var(--text-secondary)'">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <h1 class="text-lg font-bold" style="color: var(--text-primary);">{{ $title ?? 'Dashboard' }}</h1>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="text-xs" style="color: var(--text-muted);">{{ now()->translatedFormat('l, d F Y') }}</div>

                        {{-- Theme Toggle --}}
                        <button @click="toggleTheme()"
                                class="theme-toggle"
                                title="Toggle Dark Mode">
                            <svg x-show="theme === 'light'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                            <svg x-show="theme === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-6 overflow-auto">
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-lg border-l-4 animate-fade-in"
                         style="background-color: #dcfce7; border-color: #16a34a; color: #166534;">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-lg border-l-4 animate-fade-in"
                         style="background-color: #fee2e2; border-color: #ef4444; color: #991b1b;">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Mobile Sidebar Overlay --}}
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
         style="display: none;"></div>

    @livewireScripts
</body>
</html>
