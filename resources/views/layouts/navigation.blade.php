<nav x-data="{ open: false }" class="bg-primary-600 border-b border-primary-700 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex w-full justify-between sm:w-auto sm:justify-start">
                <!-- Hamburger -->
                <div class="-ms-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-primary-100 hover:text-white hover:bg-primary-700 focus:outline-none focus:bg-primary-700 focus:text-white transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-white" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-primary-50 hover:text-white hover:border-secondary-400 focus:text-white focus:border-secondary-400 {{ request()->routeIs('dashboard') ? 'border-secondary-400 text-white font-bold' : 'border-transparent' }}">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('leaders.index')" :active="request()->routeIs('leaders.*')" class="text-primary-50 hover:text-white hover:border-secondary-400 focus:text-white focus:border-secondary-400 {{ request()->routeIs('leaders.*') ? 'border-secondary-400 text-white font-bold' : 'border-transparent' }}">
                        {{ __('Pimpinan') }}
                    </x-nav-link>

                    <x-nav-link :href="route('activities.index')" :active="request()->routeIs('activities.*')" class="text-primary-50 hover:text-white hover:border-secondary-400 focus:text-white focus:border-secondary-400 {{ request()->routeIs('activities.*') ? 'border-secondary-400 text-white font-bold' : 'border-transparent' }}">
                        {{ __('Agenda') }}
                    </x-nav-link>

                    <x-nav-link :href="route('locations.index')" :active="request()->routeIs('locations.*')" class="text-primary-50 hover:text-white hover:border-secondary-400 focus:text-white focus:border-secondary-400 {{ request()->routeIs('locations.*') ? 'border-secondary-400 text-white font-bold' : 'border-transparent' }}">
                        {{ __('Lokasi') }}
                    </x-nav-link>

                    <x-nav-link :href="route('organizations.index')" :active="request()->routeIs('organizations.*')" class="text-primary-50 hover:text-white hover:border-secondary-400 focus:text-white focus:border-secondary-400 {{ request()->routeIs('organizations.*') ? 'border-secondary-400 text-white font-bold' : 'border-transparent' }}">
                        {{ __('Organisasi') }}
                    </x-nav-link>

                    <x-nav-link :href="route('protocol-officers.index')" :active="request()->routeIs('protocol-officers.*')" class="text-primary-50 hover:text-white hover:border-secondary-400 focus:text-white focus:border-secondary-400 {{ request()->routeIs('protocol-officers.*') ? 'border-secondary-400 text-white font-bold' : 'border-transparent' }}">
                        {{ __('Petugas Protokol') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-50 bg-primary-700 hover:text-white hover:bg-primary-800 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Drawer -->
    <div x-show="open" class="sm:hidden relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" style="display: none;">
        <!-- Background backdrop -->
        <div x-show="open" 
             x-transition:enter="ease-in-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in-out duration-300" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" 
             @click="open = false"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 left-0 flex max-w-full">
                    <div x-show="open" 
                         x-transition:enter="transform transition ease-in-out duration-300" 
                         x-transition:enter-start="-translate-x-full" 
                         x-transition:enter-end="translate-x-0" 
                         x-transition:leave="transform transition ease-in-out duration-300" 
                         x-transition:leave-start="translate-x-0" 
                         x-transition:leave-end="-translate-x-full" 
                         class="pointer-events-auto w-64 max-w-xs">
                        <div class="flex h-full flex-col overflow-y-auto bg-primary-700 shadow-xl">
                            <!-- Drawer Header -->
                            <div class="flex items-center justify-between px-4 h-16 border-b border-primary-600 bg-primary-600">
                                <a href="{{ route('dashboard') }}">
                                    <x-application-logo class="block h-8 w-auto fill-current text-white" />
                                </a>
                                <button type="button" @click="open = false" class="rounded-md text-primary-200 hover:text-white focus:outline-none">
                                    <span class="sr-only">Close menu</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Drawer Content -->
                            <div class="pt-4 pb-3 space-y-1">
                                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-primary-50 hover:bg-primary-800 hover:text-white {{ request()->routeIs('dashboard') ? 'bg-primary-800 text-secondary-300 border-l-4 border-secondary-400' : '' }}">
                                    {{ __('Dashboard') }}
                                </x-responsive-nav-link>
                                <x-responsive-nav-link :href="route('leaders.index')" :active="request()->routeIs('leaders.*')" class="text-primary-50 hover:bg-primary-800 hover:text-white {{ request()->routeIs('leaders.*') ? 'bg-primary-800 text-secondary-300 border-l-4 border-secondary-400' : '' }}">
                                    {{ __('Pimpinan') }}
                                </x-responsive-nav-link>
                                <x-responsive-nav-link :href="route('activities.index')" :active="request()->routeIs('activities.*')" class="text-primary-50 hover:bg-primary-800 hover:text-white {{ request()->routeIs('activities.*') ? 'bg-primary-800 text-secondary-300 border-l-4 border-secondary-400' : '' }}">
                                    {{ __('Agenda') }}
                                </x-responsive-nav-link>
                                <x-responsive-nav-link :href="route('locations.index')" :active="request()->routeIs('locations.*')" class="text-primary-50 hover:bg-primary-800 hover:text-white {{ request()->routeIs('locations.*') ? 'bg-primary-800 text-secondary-300 border-l-4 border-secondary-400' : '' }}">
                                    {{ __('Lokasi') }}
                                </x-responsive-nav-link>
                                <x-responsive-nav-link :href="route('organizations.index')" :active="request()->routeIs('organizations.*')" class="text-primary-50 hover:bg-primary-800 hover:text-white {{ request()->routeIs('organizations.*') ? 'bg-primary-800 text-secondary-300 border-l-4 border-secondary-400' : '' }}">
                                    {{ __('Organisasi') }}
                                </x-responsive-nav-link>
                                <x-responsive-nav-link :href="route('protocol-officers.index')" :active="request()->routeIs('protocol-officers.*')" class="text-primary-50 hover:bg-primary-800 hover:text-white {{ request()->routeIs('protocol-officers.*') ? 'bg-primary-800 text-secondary-300 border-l-4 border-secondary-400' : '' }}">
                                    {{ __('Petugas Protokol') }}
                                </x-responsive-nav-link>
                            </div>

                            <!-- Responsive Settings Options -->
                            <div class="pt-4 pb-1 border-t border-primary-600 mt-auto">
                                <div class="px-4 mb-3">
                                    <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                                    <div class="font-medium text-sm text-primary-200">{{ Auth::user()->email }}</div>
                                </div>

                                <div class="space-y-1 pb-4">
                                    <x-responsive-nav-link :href="route('profile.edit')" class="text-primary-50 hover:bg-primary-800 hover:text-white">
                                        {{ __('Profile') }}
                                    </x-responsive-nav-link>

                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <x-responsive-nav-link :href="route('logout')" class="text-primary-50 hover:bg-primary-800 hover:text-white"
                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-responsive-nav-link>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
