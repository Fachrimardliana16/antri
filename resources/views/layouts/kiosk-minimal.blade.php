<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kiosk Tiket</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,600,700,800" rel="stylesheet" />

    <style>
        * { -webkit-user-select: none; user-select: none; touch-action: manipulation; }
        body { font-family: 'Instrument Sans', sans-serif; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full w-full overflow-hidden">
    {{ $slot ?? '' }}
    @yield('content')

    @livewireScripts

    <script>
        // Thermal printer integration for kiosk
        window.AntriPrinter = {
            printTicket(ticket) {
                console.log('Print ticket:', ticket);
                // Integration point for actual thermal printer via USB/network
                // Example: fetch('/api/print', { method: 'POST', body: JSON.stringify(ticket) })
            }
        };
    </script>
</body>
</html>
