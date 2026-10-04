<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'LEAD-IT') }}</title>

    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0ea5e9">
    <link rel="apple-touch-icon" href="/images/logo.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50 selection:bg-primary-500 selection:text-white">
    <div class="relative min-h-screen flex flex-col overflow-hidden">
        <!-- Background decoration -->
        <div class="absolute inset-0 z-0 pointer-events-none">
            <div
                class="absolute -top-[30%] -right-[10%] w-[70%] h-[70%] rounded-full bg-gradient-to-br from-primary-200/40 to-primary-100/10 blur-3xl">
            </div>
            <div
                class="absolute -bottom-[20%] -left-[10%] w-[60%] h-[60%] rounded-full bg-gradient-to-tr from-secondary-200/40 to-secondary-100/10 blur-3xl">
            </div>
        </div>

        <nav
            class="relative z-10 w-full px-6 py-5 md:px-12 lg:px-24 flex justify-between items-center backdrop-blur-md bg-white/60 border-b border-gray-100/50 sticky top-0">
            <div class="flex items-center gap-3 group cursor-pointer">
                <img src="{{ asset('images/logo.png') }}" alt="Logo"
                    class="w-24 h-24 object-contain group-hover:scale-105 transition-transform duration-300">
                {{-- <span class="font-bold text-xl tracking-tight text-gray-800">LEAD <span
                        class="text-primary-600">IT</span></span> --}}
            </div>

            @if (Route::has('login'))
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="px-5 py-2 text-sm font-semibold text-white bg-primary-600 rounded-full hover:bg-primary-700 transition-all shadow-md hover:shadow-lg shadow-primary-500/30 active:scale-95">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-5 py-2 text-sm font-semibold text-gray-700 hover:text-primary-600 transition-colors">
                            Log in
                        </a>
                    @endauth
                </div>
            @endif
        </nav>

        <main class="relative z-10 flex-grow flex items-center justify-center px-6 py-16 md:px-12 lg:px-24">
            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-8 items-center">
                <div class="flex flex-col gap-6 text-center lg:text-left order-2 lg:order-1">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary-50 border border-primary-100 text-primary-700 font-medium text-sm w-fit mx-auto lg:mx-0 shadow-sm">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-primary-500"></span>
                        </span>
                        Leadership Agenda & Integration Tool
                    </div>

                    <h1
                        class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight text-gray-900 leading-[1.15]">
                        Kelola Agenda Pimpinan dengan <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-primary-400">Lebih
                            Cerdas</span>
                    </h1>

                    <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        Sistem terintegrasi untuk mengatur, memantau, dan mendokumentasikan setiap kegiatan pimpinan
                        secara efisien, modern, dan real-time.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start mt-6">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                    class="px-8 py-3.5 w-full sm:w-auto text-base font-semibold text-white bg-primary-600 rounded-full hover:bg-primary-700 transition-all shadow-xl hover:shadow-2xl shadow-primary-500/30 active:scale-95 text-center flex items-center justify-center gap-2 group">
                                    Buka Dashboard
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="px-8 py-3.5 w-full sm:w-auto text-base font-semibold text-white bg-primary-600 rounded-full hover:bg-primary-700 transition-all shadow-xl hover:shadow-2xl shadow-primary-500/30 active:scale-95 text-center flex items-center justify-center gap-2 group">
                                    Masuk Aplikasi
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                </a>
                            @endauth
                        @endif

                        <button id="installAppBtn" style="display: none;"
                            class="px-8 py-3.5 w-full sm:w-auto text-base font-semibold text-primary-700 bg-white border-2 border-primary-100 rounded-full hover:bg-primary-50 transition-all shadow-md active:scale-95 text-center flex items-center justify-center gap-2 group">
                            Install Aplikasi
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 group-hover:-translate-y-1 transition-transform" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="order-1 lg:order-2 relative mx-auto w-full max-w-lg lg:max-w-full perspective-1000">
                    <div
                        class="relative rounded-2xl bg-white shadow-2xl p-2 ring-1 ring-gray-900/5 rotate-y-[-5deg] rotate-x-[5deg] hover:rotate-0 transition-transform duration-500 ease-out">
                        <!-- Glow effect behind mockup -->
                        <div
                            class="absolute -inset-1 bg-gradient-to-r from-primary-400 to-secondary-400 rounded-3xl blur opacity-30">
                        </div>

                        <div
                            class="relative rounded-xl overflow-hidden border border-gray-100 bg-gray-50 flex items-center justify-center aspect-[4/3] shadow-inner">
                            <!-- Abstract UI Mockup -->
                            <div class="w-full h-full flex flex-col bg-white">
                                <!-- Mock Window Header -->
                                <div
                                    class="h-10 bg-gray-50 border-b border-gray-100 flex items-center px-4 justify-between">
                                    <div class="flex gap-1.5">
                                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                        <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                                    </div>
                                </div>
                                <!-- Mock App Body -->
                                <div class="flex-grow p-6 flex flex-col gap-6">
                                    <div class="flex justify-between items-center">
                                        <div class="w-32 h-6 bg-gray-200 rounded-md"></div>
                                        <div class="w-10 h-10 bg-primary-100 rounded-full"></div>
                                    </div>

                                    <!-- Main Card -->
                                    <div
                                        class="w-full p-5 bg-gradient-to-br from-primary-50 to-primary-100/50 rounded-2xl border border-primary-100 shadow-sm relative overflow-hidden">
                                        <div
                                            class="absolute -right-6 -top-6 w-24 h-24 bg-primary-200/50 rounded-full blur-xl">
                                        </div>
                                        <div class="w-24 h-4 bg-primary-300 rounded-md mb-4 relative z-10"></div>
                                        <div class="w-full h-3 bg-white/80 rounded-md mb-2 relative z-10"></div>
                                        <div class="w-2/3 h-3 bg-white/80 rounded-md relative z-10"></div>
                                    </div>

                                    <!-- Grid Cards -->
                                    <div class="grid grid-cols-2 gap-4 flex-grow">
                                        <div
                                            class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm flex flex-col justify-center">
                                            <div
                                                class="w-10 h-10 rounded-lg bg-secondary-100 mb-3 flex items-center justify-center">
                                                <div class="w-5 h-5 bg-secondary-500 rounded-sm"></div>
                                            </div>
                                            <div class="w-16 h-3 bg-gray-200 rounded-md mb-2"></div>
                                            <div class="w-24 h-4 bg-gray-800 rounded-md"></div>
                                        </div>
                                        <div
                                            class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm flex flex-col justify-center">
                                            <div
                                                class="w-10 h-10 rounded-lg bg-green-100 mb-3 flex items-center justify-center">
                                                <div class="w-5 h-5 bg-green-500 rounded-sm"></div>
                                            </div>
                                            <div class="w-16 h-3 bg-gray-200 rounded-md mb-2"></div>
                                            <div class="w-24 h-4 bg-gray-800 rounded-md"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer
            class="relative z-10 py-8 text-center text-sm text-gray-500 border-t border-gray-100/50 bg-white/30 backdrop-blur-sm">
            <p>&copy; {{ date('Y') }} LEAD-IT | Developed By. Code-91</p>
        </footer>
    </div>

    <script>
        let deferredPrompt;
        const installBtn = document.getElementById('installAppBtn');

        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent the mini-infobar from appearing on mobile
            e.preventDefault();
            // Stash the event so it can be triggered later.
            deferredPrompt = e;
            // Update UI notify the user they can install the PWA
            installBtn.style.display = 'flex';
        });

        installBtn.addEventListener('click', async () => {
            if (deferredPrompt) {
                // Show the install prompt
                deferredPrompt.prompt();
                // Wait for the user to respond to the prompt
                const {
                    outcome
                } = await deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    console.log('User accepted the install prompt');
                }
                // We've used the prompt, and can't use it again, throw it away
                deferredPrompt = null;
                installBtn.style.display = 'none';
            }
        });

        window.addEventListener('appinstalled', (evt) => {
            console.log('INSTALL: Success');
            installBtn.style.display = 'none';
        });
    </script>
</body>

</html>
