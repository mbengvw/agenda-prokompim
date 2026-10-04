<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LEAD-IT') }} - Login</title>

    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0ea5e9">
    <link rel="apple-touch-icon" href="/images/logo.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased selection:bg-primary-500 selection:text-white">
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4 sm:px-0 bg-gray-50 relative overflow-hidden">
        <!-- Background decoration -->
        <div class="absolute inset-0 z-0 pointer-events-none">
            <div
                class="absolute -top-[30%] -left-[10%] w-[70%] h-[70%] rounded-full bg-gradient-to-br from-primary-200/40 to-primary-100/10 blur-3xl">
            </div>
            <div
                class="absolute -bottom-[20%] -right-[10%] w-[60%] h-[60%] rounded-full bg-gradient-to-tr from-secondary-200/40 to-secondary-100/10 blur-3xl">
            </div>
        </div>

        <div class="relative z-10 mb-8 flex flex-col items-center text-center">
            <a href="/" class="flex items-center gap-3 group -mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo"
                    class="w-24 h-24 object-contain group-hover:scale-105 transition-transform duration-300">
                {{-- <span class="font-bold text-3xl tracking-tight text-gray-800">LEAD <span
                        class="text-primary-600">IT</span></span> --}}
            </a>

            <h2 class="text-lg font-semibold text-gray-700">Leadership Agenda & Integration Tool</h2>
            <p class="mt-1 text-sm text-gray-500">Masuk untuk mengelola agenda</p>
        </div>

        <div class="relative z-10 w-[88%] sm:w-full sm:max-w-md">
            {{-- Background layer for blur and gradient --}}
            <div class="absolute inset-0 bg-gradient-to-br from-primary-50/90 via-white/90 to-secondary-50/90 backdrop-blur-xl shadow-2xl shadow-primary-500/10 rounded-2xl sm:rounded-3xl border border-primary-100 pointer-events-none"></div>
            
            {{-- Content layer --}}
            <div class="relative px-6 sm:px-8 py-10">
                {{ $slot }}
            </div>
        </div>

        <div class="relative z-10 mt-10 text-center text-sm text-gray-500 font-medium">
            &copy; {{ date('Y') }} LEAD-IT | Developped By. Code-91
        </div>
    </div>
</body>

</html>
