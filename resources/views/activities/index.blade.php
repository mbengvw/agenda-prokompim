<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between items-center w-full gap-2">
            <h2 class="font-bold text-lg sm:text-xl text-primary-900 leading-tight truncate">
                {{ __('Kelola Agenda Kegiatan') }}
            </h2>
            <a href="{{ route('activities.create', ['date' => $dateFilter ?? '', 'leader' => $leaderFilter]) }}" class="shrink-0 inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 bg-secondary-500 hover:bg-secondary-600 border border-transparent rounded-full font-bold text-[10px] sm:text-xs text-primary-900 uppercase tracking-widest shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Agenda
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ viewMode: '{{ request('view', 'list') }}' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-primary-50 border border-primary-200 text-primary-800 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Active Leader Context Icon for Protocol -->
            @if(!auth()->user()->leader_id && $leaderFilter)
            <div class="mb-6 flex justify-center">
                @php
                    $quickLeaders = [
                        ['id' => 'Bupati', 'label' => 'Bupati', 'image' => asset('images/bupati.jpeg'), 'icon' => ''],
                        ['id' => 'Wakil Bupati', 'label' => 'Wakil Bupati', 'image' => asset('images/wabup.jpeg'), 'icon' => ''],
                        ['id' => 'Sekda', 'label' => 'Sekda', 'image' => asset('images/sekda.jpeg'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />']
                    ];
                    $activeLeader = collect($quickLeaders)->firstWhere('id', $leaderFilter);
                @endphp
                
                @if($activeLeader)
                <a href="{{ route('activities.index', ['leader' => '', 'date' => $dateFilter, 'view' => request('view')]) }}" 
                   class="flex flex-col items-center justify-center p-4 sm:p-6 w-48 sm:w-64 rounded-2xl shadow-sm border bg-gradient-to-br from-primary-50 to-primary-100 border-primary-500 ring-2 ring-primary-300 ring-offset-1 transform -translate-y-1 shadow-md hover:shadow-lg transition-all duration-300 group" title="Klik untuk Ganti Pimpinan">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full overflow-hidden flex items-center justify-center mb-3 transition-colors duration-300 bg-primary-600 text-white shadow-inner group-hover:bg-primary-700 relative">
                        @if(isset($activeLeader['image']))
                            <img src="{{ $activeLeader['image'] }}" alt="{{ $activeLeader['label'] }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $activeLeader['icon'] !!}
                            </svg>
                        @endif
                    </div>
                    <span class="font-extrabold text-sm sm:text-base tracking-wider uppercase text-primary-800 text-center leading-tight">
                        {{ $activeLeader['label'] }}
                    </span>
                    <span class="mt-2 text-[10px] text-primary-600 font-medium bg-white px-2 py-0.5 rounded-full border border-primary-200 group-hover:bg-primary-50">
                        Ganti Pimpinan &rarr;
                    </span>
                </a>
                @endif
            </div>
            @endif

            <!-- Filters -->
            <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-primary-100 flex flex-col sm:flex-row gap-4 items-center justify-between">
                <form action="{{ route('activities.index') }}" method="GET" class="w-full flex flex-col sm:flex-row gap-3 items-center">
                    <input type="hidden" name="view" id="view_input" value="{{ request('view', 'list') }}">
                    <input type="date" name="date" value="{{ $dateFilter }}" onchange="this.form.submit()" class="h-[42px] px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm">
                    <div class="relative w-full sm:w-64 h-[42px]">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari kegiatan..." class="h-full w-full pl-10 pr-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    @if($leaderFilter)
                        <input type="hidden" name="leader" value="{{ $leaderFilter }}">
                    @endif
                    @php
                        $statusOptions = [
                            ['value' => 'draft', 'label' => 'Draft'],
                            ['value' => 'submitted', 'label' => 'Submitted'],
                            ['value' => 'revision', 'label' => 'Revision'],
                            ['value' => 'approved', 'label' => 'Approved'],
                            ['value' => 'rejected', 'label' => 'Rejected'],
                            ['value' => 'cancelled', 'label' => 'Cancelled'],
                        ];
                    @endphp
                    <div class="w-48 h-[42px]">
                        <x-custom-select 
                            name="status" 
                            placeholder="Semua Status" 
                            :options="$statusOptions" 
                            :value="$statusFilter"
                            onchange="this.form.submit()"
                        />
                    </div>
                    <button type="submit" class="h-[42px] px-4 flex items-center justify-center bg-primary-600 text-white rounded-md text-sm font-medium hover:bg-primary-700">Cari</button>
                    @if($search || $leaderFilter || $statusFilter)
                        <a href="{{ route('activities.index') }}" class="h-[42px] inline-flex items-center justify-center px-4 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">Reset</a>
                    @endif
                </form>
            </div>

            <!-- View Mode Toggle -->
            <div class="mb-4 flex justify-end">
                <div class="bg-gray-100 p-1 rounded-lg inline-flex shadow-inner">
                    <button @click="viewMode = 'list'; document.getElementById('view_input').value = 'list'; const u = new URL(window.location); u.searchParams.set('view', 'list'); window.history.replaceState({}, '', u);" :class="{'bg-white shadow text-primary-700': viewMode === 'list', 'text-gray-500 hover:text-gray-700': viewMode !== 'list'}" class="px-4 py-1.5 rounded-md text-sm font-bold transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Grid View
                    </button>
                    <button @click="viewMode = 'timeline'; document.getElementById('view_input').value = 'timeline'; const u = new URL(window.location); u.searchParams.set('view', 'timeline'); window.history.replaceState({}, '', u);" :class="{'bg-white shadow text-primary-700': viewMode === 'timeline', 'text-gray-500 hover:text-gray-700': viewMode !== 'timeline'}" class="px-4 py-1.5 rounded-md text-sm font-bold transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H4a2 2 0 00-2 2v6a2 2 0 002 2h3a2 2 0 002-2zm0 0V9a2 2 0 012-2h3a2 2 0 012 2v10m-2 0h2a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4h4m-4 0v4m0-4v-4m0 0H9m1 0h4"></path></svg>
                        Timeline View
                    </button>
                </div>
            </div>

            <!-- List View -->
            <div x-show="viewMode === 'list'">
                <!-- 1. Mobile View (Cards) --><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 xl:hidden" x-data>
                @forelse($activities as $activity)
                    <div class="bg-gradient-to-br from-teal-100 via-white to-yellow-100 rounded-2xl shadow-sm border border-teal-100 hover:shadow-xl hover:shadow-teal-500/20 hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col relative group">
                        <!-- Top decorative bar -->
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary-400 to-secondary-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-500 ease-out z-20"></div>


                        <div class="p-6 flex-grow">
                            <!-- Role & Date Info -->
                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                @if($leaderFilter)
                                    @php
                                        $isUtama = false;
                                        if ($activity->leader) {
                                            $isUtama = (strtolower(trim($activity->leader->position)) === strtolower(trim($leaderFilter)));
                                        }
                                    @endphp
                                    @if($isUtama)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-gradient-to-r from-teal-600 to-emerald-500 text-white text-[10px] font-extrabold uppercase tracking-wider shadow-md shadow-teal-500/40 border border-teal-400/50">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            Utama
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-[10px] font-bold uppercase tracking-wider shadow-sm border border-yellow-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Pendamping
                                        </span>
                                    @endif
                                @endif
                                <div class="flex items-center gap-2 text-xs text-primary-600 font-bold bg-primary-50 inline-flex px-3 py-1 rounded-full">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span>{{ \Carbon\Carbon::parse($activity->activity_date)->translatedFormat('l, d M') }}</span>
                                    <span class="text-primary-300">•</span>
                                    @if($activity->is_tentative)
                                        <span>Waktu Tentatif</span>
                                    @else
                                        <div class="flex items-center gap-1">
                                            <span>{{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }} - {{ $activity->end_time ? \Carbon\Carbon::parse($activity->end_time)->format('H:i') : 'Selesai' }}</span>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-extrabold bg-yellow-300 text-yellow-900 shadow-sm border border-yellow-400">
                                                <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $activity->duration }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('activities.show', ['activity' => $activity->id, 'date' => $dateFilter, 'leader' => $leaderFilter]) }}" class="block mt-4 mb-2">
                                <h3 class="text-lg font-extrabold text-gray-900 leading-snug group-hover:text-primary-600 transition-colors pr-12 line-clamp-2">{{ $activity->title }}</h3>
                            </a>
                            
                            <div class="flex items-center gap-2 mb-3">
                                @php
                                    $isOriginalLeader = auth()->user()->leader_id && auth()->user()->leader_id == $activity->original_leader_id;
                                    $displayLeader = $isOriginalLeader ? ($activity->originalLeader ?? $activity->leader) : $activity->leader;
                                @endphp
                                @if($displayLeader)
                                    @php
                                        $position = strtolower($displayLeader->position);
                                        if (str_contains($position, 'wakil bupati')) {
                                            $badgeClass = 'bg-purple-100 text-purple-800 border-purple-200';
                                        } elseif (str_contains($position, 'bupati')) {
                                            $badgeClass = 'bg-primary-100 text-primary-800 border-primary-200';
                                        } elseif (str_contains($position, 'sekda') || str_contains($position, 'sekretaris daerah')) {
                                            $badgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                                        } else {
                                            $badgeClass = 'bg-slate-100 text-slate-800 border-slate-200';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border {{ $badgeClass }}">
                                        {{ $displayLeader->position }}
                                    </span>
                                    <span class="text-sm font-bold text-gray-800">{{ $displayLeader->name }}</span>
                                @endif

                                @if($isOriginalLeader && $activity->is_disposition && $activity->leader_id !== $activity->original_leader_id && $activity->leader)
                                    <div class="ml-2 text-[10px] font-semibold text-primary-700 bg-primary-50 px-2 py-0.5 rounded border border-primary-200 shadow-sm inline-flex items-center">
                                        <span class="uppercase tracking-wider mr-1">Disposisi ke:</span> {{ $activity->leader->position }}
                                    </div>
                                @endif

                                @php
                                    $dispositionStatus = null;
                                    $needsConfirmation = false;
                                    if ($activity->leader_id && $activity->relationLoaded('dispositions')) {
                                        $leaderDisposition = $activity->dispositions->firstWhere('from_leader_id', $activity->leader_id);
                                        if ($leaderDisposition) {
                                            $dispositionStatus = $leaderDisposition->status;
                                        } elseif (in_array($activity->status, ['draft', 'submitted', 'approved', 'revision'])) {
                                            $needsConfirmation = true;
                                        }
                                    }
                                @endphp

                                @if($dispositionStatus)
                                    <span class="ml-auto inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-wider shadow-sm border {{ $dispositionStatus === 'hadir' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : ($dispositionStatus === 'skip' ? 'bg-red-100 text-red-800 border-red-200' : 'bg-indigo-100 text-indigo-800 border-indigo-200') }}">
                                        Kehadiran: {{ $dispositionStatus }}
                                    </span>
                                @elseif($needsConfirmation)
                                    <span class="ml-auto inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-wider shadow-sm border bg-red-100 text-red-800 border-red-300 animate-pulse">
                                        Belum Konfirmasi
                                    </span>
                                @endif
                            </div>
                            
                            <div class="space-y-3 mt-4 bg-white/60 backdrop-blur-sm rounded-xl p-4 border border-teal-100/50">
                                <!-- Lokasi -->
                                <div class="flex items-start text-xs text-gray-700">
                                    <div class="bg-white p-1.5 rounded-md shadow-sm border border-teal-50 mr-3 shrink-0">
                                        <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    <div class="flex-1 mt-0.5">
                                        <div class="font-bold text-teal-800 mb-0.5">Lokasi</div>
                                        <div class="line-clamp-2 leading-relaxed">{{ $activity->location_id ? $activity->location?->name : ($activity->location_text ?: '-') }}</div>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3 border-t border-teal-100/50 pt-3">
                                    <!-- Penyelenggara -->
                                    <div class="text-xs">
                                        <div class="font-bold text-teal-800 mb-0.5">Penyelenggara</div>
                                        <div class="text-gray-700 truncate" title="{{ $activity->organization_id ? $activity->organization?->name : ($activity->organizer_text ?: '-') }}">{{ $activity->organization_id ? $activity->organization?->name : ($activity->organizer_text ?: '-') }}</div>
                                    </div>
                                    <!-- Pendamping -->
                                    <div class="text-xs">
                                        <div class="font-bold text-teal-800 mb-0.5">Pendamping</div>
                                        <div class="text-gray-700 truncate" title="{{ $activity->companions->count() > 0 ? $activity->companions->pluck('name')->join(', ') : '-' }}">
                                            @if($activity->companions->count() > 0)
                                                {{ $activity->companions->pluck('name')->join(', ') }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3 border-t border-teal-100/50 pt-3">
                                    <!-- Narahubung -->
                                    <div class="text-xs">
                                        <div class="font-bold text-teal-800 mb-0.5">Narahubung</div>
                                        <div class="text-gray-700 truncate">{{ $activity->contact_person_name ?: '-' }}</div>
                                        @if($activity->contact_person_phone)
                                            <div class="text-teal-600 font-medium mt-0.5"><a href="tel:{{ $activity->contact_person_phone }}" class="hover:underline">📞 {{ $activity->contact_person_phone }}</a></div>
                                        @endif
                                    </div>
                                    <!-- Protokol -->
                                    <div class="text-xs">
                                        <div class="font-bold text-teal-800 mb-0.5">PIC Protokol</div>
                                        <div class="text-gray-700 truncate">{{ $activity->protocolOfficer?->name ?: '-' }}</div>
                                        @if($activity->protocolOfficer?->phone)
                                            <div class="text-teal-600 font-medium mt-0.5"><a href="tel:{{ $activity->protocolOfficer->phone }}" class="hover:underline">📞 {{ $activity->protocolOfficer->phone }}</a></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($activity->status === 'revision' && !empty($activity->revision_notes))
                            <div class="mx-6 mb-4 p-3 bg-red-50 border border-red-100 rounded-xl text-xs text-red-700 relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-1 h-full bg-red-400"></div>
                                <span class="font-bold block mb-1 uppercase tracking-wide flex items-center gap-1.5 text-red-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Catatan Revisi
                                </span>
                                <p class="pl-6">{{ $activity->revision_notes }}</p>
                            </div>
                        @endif
                        
                        <div class="px-5 py-3.5 bg-white/50 backdrop-blur-sm border-t border-teal-100/50 flex flex-row justify-between items-center gap-2 group-hover:bg-white/70 transition-colors">
                            <div class="flex flex-wrap items-center gap-2">
                                @php
                                    $statusStyles = [
                                        'draft' => 'bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-600/10',
                                        'submitted' => 'bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20',
                                        'revision' => 'bg-orange-50 text-orange-700 ring-1 ring-inset ring-orange-600/20',
                                        'approved' => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20',
                                        'rejected' => 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10',
                                        'cancelled' => 'bg-slate-800 text-white ring-1 ring-inset ring-slate-900/10',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-extrabold uppercase tracking-wide shadow-sm {{ $statusStyles[$activity->status] ?? 'bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-600/10' }} {{ $activity->status === 'submitted' ? 'animate-pulse ring-2 ring-yellow-400' : '' }}">
                                    {{ ucfirst($activity->status) }}
                                </span>
                            </div>                            <div class="flex items-center gap-3">
                                <a href="{{ route('activities.edit', ['activity' => $activity->id, 'redirect_to' => request()->fullUrl(), 'date' => $dateFilter, 'leader' => $leaderFilter]) }}" class="p-1.5 inline-flex text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                @if($activity->created_by === auth()->id() || str_contains(auth()->user()->roles->first()?->name ?? '', 'ajudan_'))
                                <form action="{{ route('activities.destroy', $activity->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus agenda ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-xl shadow p-8 text-center border border-primary-100">
                        <div class="text-primary-300 mb-4">
                            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada agenda</h3>
                    </div>
                @endforelse
                </div>
                
                
                <!-- 2. Desktop View (Table) -->
                <div class="hidden xl:block bg-white rounded-xl shadow-sm border border-primary-100 overflow-hidden mb-6" x-data>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-primary-200">
                            <thead class="bg-primary-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-primary-800 uppercase tracking-wider">Waktu & Lokasi</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-primary-800 uppercase tracking-wider">Kegiatan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-primary-800 uppercase tracking-wider">Agenda & Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-primary-800 uppercase tracking-wider">Keterangan</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-primary-800 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-primary-100">
                                @forelse($activities as $activity)
                                    <tr class="hover:bg-primary-50/50 transition-colors duration-150">
                                        <!-- Waktu & Lokasi -->
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-primary-900 mb-1">
                                                {{ \Carbon\Carbon::parse($activity->activity_date)->translatedFormat('d M Y') }}
                                            </div>
                                            <div class="text-xs font-medium text-primary-600 mb-2">
                                                @if($activity->is_tentative)
                                                    Waktu Tentatif
                                                @else
                                                    <div class="flex items-center gap-1.5 mt-0.5">
                                                        <span>{{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }} - {{ $activity->end_time ? \Carbon\Carbon::parse($activity->end_time)->format('H:i') : 'Selesai' }}</span>
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-yellow-300 text-yellow-900 border border-yellow-400 shadow-sm">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            {{ $activity->duration }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-700 flex items-start gap-1">
                                                <svg class="w-3.5 h-3.5 text-primary-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                                <span class="line-clamp-2">{{ $activity->location_id ? $activity->location?->name : ($activity->location_text ?: '-') }}</span>
                                            </div>
                                        </td>
                                        
                                        <!-- Kegiatan -->
                                        <td class="px-6 py-4">
                                            <a href="{{ route('activities.show', ['activity' => $activity->id, 'date' => $dateFilter, 'leader' => $leaderFilter]) }}" class="text-sm font-bold text-gray-900 hover:text-primary-600 line-clamp-2 mb-2">
                                                {{ $activity->title }}
                                            </a>
                                            <div class="text-xs text-gray-600">
                                                <span class="font-semibold">Penyelenggara:</span> 
                                                {{ $activity->organization_id ? $activity->organization?->name : ($activity->organizer_text ?: '-') }}
                                            </div>
                                        </td>
                                        
                                        <!-- Agenda & Status -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $isOriginalLeader = auth()->user()->leader_id && auth()->user()->leader_id == $activity->original_leader_id;
                                                $displayLeader = $isOriginalLeader ? ($activity->originalLeader ?? $activity->leader) : $activity->leader;
                                            @endphp
                                            @if($displayLeader)
                                                <div class="text-sm font-bold text-gray-900 uppercase">{{ $displayLeader->position }}</div>
                                                <div class="text-xs text-gray-500 mb-2">{{ $displayLeader->name }}</div>
                                            @endif
                                            
                                            @if($isOriginalLeader && $activity->is_disposition && $activity->leader_id !== $activity->original_leader_id && $activity->leader)
                                                <div class="mb-2 text-xs font-semibold text-primary-700 bg-primary-50 p-1.5 rounded border border-primary-200 shadow-sm inline-block">
                                                    <span class="block text-[10px] text-primary-500 uppercase tracking-wider mb-0.5">Didisposisikan ke:</span>
                                                    {{ $activity->leader->position }}
                                                </div>
                                            @endif
                                            
                                            @php
                                                $statusStyles = [
                                                    'draft' => 'bg-gray-50 text-gray-700 border-gray-200',
                                                    'submitted' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                    'revision' => 'bg-orange-50 text-orange-700 border-orange-200',
                                                    'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                    'rejected' => 'bg-red-50 text-red-700 border-red-200',
                                                    'cancelled' => 'bg-slate-800 text-white border-slate-900',
                                                ];
                                            @endphp
                                            <div class="flex flex-col gap-1.5 items-start mt-2">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-wider shadow-sm border {{ $statusStyles[$activity->status] ?? 'bg-gray-50 text-gray-700 border-gray-200' }} {{ $activity->status === 'submitted' ? 'animate-pulse ring-2 ring-yellow-400' : '' }}">
                                                    Status: {{ ucfirst($activity->status) }}
                                                </span>
                                                
                                                @php
                                                    $dispositionStatus = null;
                                                    $needsConfirmation = false;
                                                    if ($activity->leader_id && $activity->relationLoaded('dispositions')) {
                                                        $leaderDisposition = $activity->dispositions->firstWhere('from_leader_id', $activity->leader_id);
                                                        if ($leaderDisposition) {
                                                            $dispositionStatus = $leaderDisposition->status;
                                                        } elseif (in_array($activity->status, ['draft', 'submitted', 'approved', 'revision'])) {
                                                            if (!$activity->is_disposition) {
                                                                $needsConfirmation = true;
                                                            }
                                                        }
                                                    }
                                                @endphp

                                                @if($dispositionStatus)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-wider shadow-sm border {{ $dispositionStatus === 'hadir' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : ($dispositionStatus === 'skip' ? 'bg-red-100 text-red-800 border-red-200' : 'bg-indigo-100 text-indigo-800 border-indigo-200') }}">
                                                        Kehadiran: {{ $dispositionStatus }}
                                                    </span>
                                                @elseif($needsConfirmation)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-wider shadow-sm border bg-red-100 text-red-800 border-red-300 animate-pulse">
                                                        Belum Konfirmasi
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        
                                        <!-- Keterangan (Pendamping & Kontak) -->
                                        <td class="px-6 py-4">
                                            <div class="text-xs mb-1">
                                                <span class="font-bold text-teal-800">Pendamping:</span>
                                                <span class="text-gray-700 line-clamp-1">{{ $activity->companions->count() > 0 ? $activity->companions->pluck('name')->join(', ') : '-' }}</span>
                                            </div>
                                            <div class="text-xs mb-1">
                                                <span class="font-bold text-teal-800">Narahubung:</span>
                                                <span class="text-gray-700">{{ $activity->contact_person_name ?: '-' }}</span>
                                            </div>
                                            <div class="text-xs">
                                                <span class="font-bold text-teal-800">PIC Protokol:</span>
                                                <span class="text-gray-700">{{ $activity->protocolOfficer?->name ?: '-' }}</span>
                                            </div>
                                        </td>
                                        
                                        <!-- Aksi -->
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex flex-col items-end gap-2">
                                                <a href="{{ route('activities.show', ['activity' => $activity->id, 'date' => $dateFilter, 'leader' => $leaderFilter]) }}" class="text-teal-600 hover:text-teal-900 bg-teal-50 hover:bg-teal-100 px-3 py-1 rounded transition-colors text-xs inline-block text-center min-w-[70px]">Detail</a>
                                                <a href="{{ route('activities.edit', ['activity' => $activity->id, 'redirect_to' => request()->fullUrl(), 'date' => $dateFilter, 'leader' => $leaderFilter]) }}" class="text-primary-600 hover:text-primary-900 bg-primary-50 hover:bg-primary-100 px-3 py-1 rounded transition-colors text-xs inline-block text-center min-w-[70px]">Edit</a>
                                                @if($activity->created_by === auth()->id() || str_contains(auth()->user()->roles->first()?->name ?? '', 'ajudan_'))
                                                <form action="{{ route('activities.destroy', $activity->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus agenda ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded transition-colors text-xs w-full min-w-[70px]">Hapus</button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center">
                                            <div class="text-primary-300 mb-4">
                                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada agenda</h3>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
<div class="mt-6">
                    {{ $activities->links() }}
                </div>
            </div>
            
            <!-- Timeline View -->
            <div x-show="viewMode === 'timeline'" class="bg-white rounded-xl shadow-sm border border-primary-100 overflow-hidden overflow-x-auto" x-cloak>
                <div class="min-w-[1000px]">
                    <div class="flex border-b border-gray-200 bg-gray-50">
                        <div class="w-48 p-3 border-r border-gray-200 flex-shrink-0 bg-gray-100 sticky left-0 z-20 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                            <span class="font-bold text-xs text-gray-500 uppercase tracking-wider">Pimpinan</span>
                        </div>
                        <div class="flex-1 relative h-10">
                            @for($h = $timelineData['start_hour']; $h <= $timelineData['end_hour']; $h++)
                                @php $leftPct = (($h - $timelineData['start_hour']) / ($timelineData['end_hour'] - $timelineData['start_hour'])) * 100; @endphp
                                <div class="absolute top-0 bottom-0 border-l border-gray-300" style="left: {{ $leftPct }}%;">
                                    <span class="absolute -translate-x-1/2 mt-3 text-[10px] font-bold text-gray-400 bg-gray-50 px-1">{{ sprintf('%02d:00', $h) }}</span>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- Timeline Rows -->
                    @foreach($timelineData['data'] as $td)
                        <div class="flex border-b border-gray-100 relative group hover:bg-gray-50 transition-colors">
                            <div class="w-48 p-4 border-r border-gray-200 flex-shrink-0 flex items-center justify-between bg-white sticky left-0 z-20 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] group-hover:bg-gray-50 transition-colors">
                                <div>
                                    <h3 class="font-bold text-gray-800 text-sm leading-tight">{{ $td['leader']->name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $td['leader']->position }}</p>
                                </div>
                                @if($td['has_conflict'])
                                    <span title="Terdapat jadwal bentrok" class="flex h-3 w-3 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                    </span>
                                @endif
                            </div>
                            
                            <div class="flex-1 relative overflow-hidden bg-slate-50" style="min-height: {{ max(60, $td['total_rows'] * 45 + 15) }}px;">
                                <!-- Grid lines -->
                                <div class="absolute inset-0 pointer-events-none">
                                    @for($h = $timelineData['start_hour']; $h <= $timelineData['end_hour']; $h++)
                                        @php $leftPct = (($h - $timelineData['start_hour']) / ($timelineData['end_hour'] - $timelineData['start_hour'])) * 100; @endphp
                                        <div class="absolute top-0 bottom-0 border-l border-dashed border-gray-200" style="left: {{ $leftPct }}%;"></div>
                                    @endfor
                                </div>
                                
                                <!-- Activities Blocks -->
                                @foreach($td['activities'] as $la)
                                    @php
                                        $position = strtolower($td['leader']->position ?? '');
                                        if (str_contains($position, 'wakil bupati')) {
                                            $bgClass = $la['is_primary'] ? 'bg-purple-500 border-purple-600 text-white' : 'bg-purple-100 border-purple-300 text-purple-800 border-dashed';
                                        } elseif (str_contains($position, 'bupati')) {
                                            $bgClass = $la['is_primary'] ? 'bg-primary-500 border-primary-600 text-white' : 'bg-primary-100 border-primary-300 text-primary-800 border-dashed';
                                        } elseif (str_contains($position, 'sekretaris daerah') || str_contains($position, 'sekda')) {
                                            $bgClass = $la['is_primary'] ? 'bg-emerald-500 border-emerald-600 text-white' : 'bg-emerald-100 border-emerald-300 text-emerald-800 border-dashed';
                                        } else {
                                            $bgClass = $la['is_primary'] ? 'bg-slate-600 border-slate-700 text-white' : 'bg-slate-100 border-slate-300 text-slate-800 border-dashed';
                                        }
                                    @endphp
                                    <a href="{{ route('activities.show', ['activity' => $la['activity']->id, 'date' => $dateFilter, 'leader' => $leaderFilter]) }}"
                                       class="absolute rounded p-1.5 shadow-sm border cursor-pointer hover:shadow-md hover:ring-2 hover:ring-offset-1 hover:ring-primary-400 transition-all overflow-hidden flex flex-col justify-center {{ $bgClass }}"
                                       style="left: {{ $la['left'] }}%; width: {{ max($la['width'], 2) }}%; top: {{ $la['row'] * 45 + 10 }}px; height: 38px; z-index: 10;"
                                       title="{{ $la['start_time'] }} - {{ $la['end_time'] }} | {{ $la['activity']->title }}">
                                        <div class="text-[10px] font-bold leading-tight truncate">
                                            {{ $la['start_time'] }} - {{ $la['activity']->title }}
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>



    <!-- Floating PDF Export Button -->
    @php
        $exportLeaderId = auth()->user()->leader_id;
        if (!$exportLeaderId && $leaderFilter) {
            $exportLeader = $leaders->first(fn($l) => strtolower($l->position) === strtolower($leaderFilter));
            $exportLeaderId = $exportLeader ? $exportLeader->id : null;
        }
    @endphp

    @if($exportLeaderId)
        <a href="{{ route('activities.export-pdf', ['date' => $dateFilter ?? \Carbon\Carbon::today()->toDateString(), 'leader_id' => $exportLeaderId]) }}" target="_blank"
           class="fixed bottom-6 right-6 z-50 bg-red-600 text-white p-3.5 rounded-full shadow-lg hover:bg-red-700 hover:shadow-xl hover:scale-105 transition-all duration-300 flex items-center justify-center group"
           title="Export to PDF">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="max-w-0 overflow-hidden whitespace-nowrap opacity-0 group-hover:max-w-xs group-hover:opacity-100 group-hover:ml-2 group-hover:mr-1 transition-all duration-300 ease-in-out font-bold text-sm">
                Cetak PDF
            </span>
        </a>
    @endif

    <!-- Datalists for Autocomplete -->
    <datalist id="locations_list">
        @foreach($locations as $loc)
            <option value="{{ $loc->name }}">
        @endforeach
    </datalist>

    <datalist id="organizers_list">
        @foreach($organizations as $org)
            <option value="{{ $org->name }}">
        @endforeach
    </datalist>

</x-app-layout>
