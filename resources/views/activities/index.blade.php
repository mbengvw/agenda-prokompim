<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between items-center w-full gap-2">
            <h2 class="font-bold text-lg sm:text-xl text-primary-900 leading-tight truncate">
                {{ __('Kelola Agenda Kegiatan') }}
            </h2>
            <a href="{{ route('activities.create', ['date' => $dateFilter ?? '', 'leader' => $leaderFilter]) }}"
                id="create-activity-link" data-sync-href="create-activity"
                class="shrink-0 inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 bg-secondary-500 hover:bg-secondary-600 border border-transparent rounded-full font-bold text-[10px] sm:text-xs text-primary-900 uppercase tracking-widest shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1 sm:mr-1.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Agenda
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="activitiesIndex(@js($initialView))" @filters-changed.window="loadResults()"
        :aria-busy="loading">
        {{-- Progress bar saat memuat ulang hasil filter --}}
        <div x-show="loading" x-cloak class="fixed inset-x-0 top-0 z-[110] h-1 bg-primary-600 animate-pulse"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-primary-50 border border-primary-200 text-primary-800 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Active Leader Context Icon for Protocol -->
            @if (!auth()->user()->leader_id && $leaderFilter)
                <div class="mb-6 flex justify-center">
                    @php
                        $quickLeaders = [
                            [
                                'id' => 'Bupati',
                                'label' => 'Bupati',
                                'image' => asset('images/bupati.jpeg'),
                                'icon' => '',
                            ],
                            [
                                'id' => 'Wakil Bupati',
                                'label' => 'Wakil Bupati',
                                'image' => asset('images/wabup.jpeg'),
                                'icon' => '',
                            ],
                            [
                                'id' => 'Sekda',
                                'label' => 'Sekda',
                                'image' => asset('images/sekda.jpeg'),
                                'icon' =>
                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
                            ],
                        ];
                        $activeLeader = collect($quickLeaders)->firstWhere('id', $leaderFilter);
                    @endphp

                    @if ($activeLeader)
                        <a href="{{ route('activities.index', ['leader' => '', 'date' => $dateFilter, 'view' => request('view')]) }}"
                            data-sync-href="leader-context"
                            class="flex flex-col items-center justify-center p-4 sm:p-6 w-48 sm:w-64 rounded-2xl shadow-sm border bg-gradient-to-br from-primary-50 to-primary-100 border-primary-500 ring-2 ring-primary-300 ring-offset-1 transform -translate-y-1 shadow-md hover:shadow-lg transition-all duration-300 group"
                            title="Klik untuk Ganti Pimpinan">
                            <div
                                class="w-12 h-12 sm:w-16 sm:h-16 rounded-full overflow-hidden flex items-center justify-center mb-3 transition-colors duration-300 bg-primary-600 text-white shadow-inner group-hover:bg-primary-700 relative">
                                @if (isset($activeLeader['image']))
                                    <img src="{{ $activeLeader['image'] }}" alt="{{ $activeLeader['label'] }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        {!! $activeLeader['icon'] !!}
                                    </svg>
                                @endif
                            </div>
                            <span
                                class="font-extrabold text-sm sm:text-base tracking-wider uppercase text-primary-800 text-center leading-tight">
                                {{ $activeLeader['label'] }}
                            </span>
                            <span
                                class="mt-2 text-[10px] text-primary-600 font-medium bg-white px-2 py-0.5 rounded-full border border-primary-200 group-hover:bg-primary-50">
                                Ganti Pimpinan &rarr;
                            </span>
                        </a>
                    @endif
                </div>
            @endif

            <div id="activity-results" @click="handleResultsClick($event)">
                @include('activities._content')
            </div>

        </div>
    </div>

    <!-- Datalists for Autocomplete -->
    <datalist id="locations_list">
        @foreach ($locations as $loc)
            <option value="{{ $loc->name }}">
        @endforeach
    </datalist>

    <datalist id="organizers_list">
        @foreach ($organizations as $org)
            <option value="{{ $org->name }}">
        @endforeach
    </datalist>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('activitiesIndex', (initialView = 'table') => ({
                viewMode: initialView,
                loading: false,
                requestController: null,

                filterForm() {
                    return this.$el.querySelector('[data-filter-form]');
                },

                buildFilterUrl() {
                    const form = this.filterForm();
                    const url = new URL(form?.action || window.location.href, window.location.origin);

                    if (!form) {
                        return url;
                    }

                    new FormData(form).forEach((value, key) => {
                        const normalized = typeof value === 'string' ? value.trim() : value;

                        if (normalized === '' || normalized === null) {
                            url.searchParams.delete(key);
                        } else {
                            url.searchParams.set(key, normalized);
                        }
                    });

                    return url;
                },

                async loadResults(url = null) {
                    const target = url
                        ? new URL(url, window.location.origin)
                        : this.buildFilterUrl();

                    // Batalkan request sebelumnya supaya perubahan filter terakhir yang menang.
                    this.requestController?.abort();
                    const controller = new AbortController();
                    this.requestController = controller;
                    this.loading = true;

                    try {
                        const response = await fetch(target.toString(), {
                            signal: controller.signal,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Gagal memuat data agenda.');
                        }

                        const freshDocument = new DOMParser().parseFromString(await response.text(), 'text/html');
                        const freshResults = freshDocument.querySelector('[data-activity-content]');
                        const currentResults = document.querySelector('#activity-results');

                        if (!freshResults || !currentResults) {
                            throw new Error('Hasil agenda tidak ditemukan.');
                        }

                        currentResults.innerHTML = freshResults.innerHTML;
                        this.syncOutboundLinks(target);

                        window.history.replaceState({}, '', target.toString());
                    } catch (error) {
                        if (error.name === 'AbortError') {
                            return;
                        }

                        // Fallback ke navigasi biasa kalau partial gagal dimuat.
                        window.location.href = target.toString();
                    } finally {
                        if (this.requestController === controller) {
                            this.requestController = null;
                            this.loading = false;
                        }
                    }
                },

                syncOutboundLinks(target) {
                    const date = target.searchParams.get('date');
                    const leader = target.searchParams.get('leader');

                    const createLink = document.getElementById('create-activity-link');
                    if (createLink) {
                        const url = new URL(createLink.href, window.location.origin);

                        if (date) {
                            url.searchParams.set('date', date);
                        } else {
                            url.searchParams.delete('date');
                        }

                        if (leader) {
                            url.searchParams.set('leader', leader);
                        } else {
                            url.searchParams.delete('leader');
                        }

                        createLink.setAttribute('href', url.toString());
                    }

                    const leaderContextLink = document.querySelector('[data-sync-href="leader-context"]');
                    if (leaderContextLink) {
                        const url = new URL(leaderContextLink.href, window.location.origin);

                        if (date) {
                            url.searchParams.set('date', date);
                        } else {
                            url.searchParams.delete('date');
                        }

                        url.searchParams.set('view', this.viewMode);
                        leaderContextLink.setAttribute('href', url.toString());
                    }
                },

                resetFilters() {
                    const form = this.filterForm();
                    const url = new URL(form?.action || window.location.pathname, window.location.origin);
                    url.searchParams.set('view', this.viewMode);

                    this.loadResults(url.toString());
                },

                setViewMode(mode) {
                    this.viewMode = mode;

                    const viewInput = this.$el.querySelector('#view_input');
                    if (viewInput) {
                        viewInput.value = mode;
                    }

                    const url = new URL(window.location.href);
                    url.searchParams.set('view', mode);
                    window.history.replaceState({}, '', url);
                },

                handleResultsClick(event) {
                    const link = event.target.closest('nav[role="navigation"] a[href]');

                    if (!link) {
                        return;
                    }

                    event.preventDefault();

                    const url = new URL(link.href);
                    url.searchParams.set('view', this.viewMode);

                    this.loadResults(url.toString());
                },
            }));
        });
    </script>

</x-app-layout>
