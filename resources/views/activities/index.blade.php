<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between items-center w-full gap-2">
            <h2 class="font-bold text-lg sm:text-xl text-primary-900 leading-tight truncate">
                {{ __('Kelola Agenda Kegiatan') }}
            </h2>
            <a href="{{ route('activities.create', ['date' => $dateFilter ?? '']) }}" class="shrink-0 inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 bg-secondary-500 hover:bg-secondary-600 border border-transparent rounded-full font-bold text-[10px] sm:text-xs text-primary-900 uppercase tracking-widest shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
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

            <!-- Filters -->
            <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-primary-100 flex flex-col sm:flex-row gap-4 items-center justify-between">
                <form action="{{ route('activities.index') }}" method="GET" class="w-full sm:w-auto flex flex-col sm:flex-row gap-2">
                    <input type="hidden" name="view" id="view_input" value="{{ request('view', 'list') }}">
                    <input type="date" name="date" value="{{ $dateFilter }}" onchange="this.form.submit()" class="border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm">
                    <div class="relative w-full sm:w-64">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari kegiatan..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <select name="leader" onchange="this.form.submit()" class="border border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Semua Pimpinan</option>
                        <option value="Bupati" {{ $leaderFilter == 'Bupati' ? 'selected' : '' }}>Bupati</option>
                        <option value="Wakil Bupati" {{ $leaderFilter == 'Wakil Bupati' ? 'selected' : '' }}>Wakil Bupati</option>
                        <option value="Sekda" {{ $leaderFilter == 'Sekda' ? 'selected' : '' }}>Sekda</option>
                    </select>
                    <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ $statusFilter == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ $statusFilter == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="revision" {{ $statusFilter == 'revision' ? 'selected' : '' }}>Revision</option>
                        <option value="approved" {{ $statusFilter == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $statusFilter == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ $statusFilter == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md text-sm font-medium hover:bg-primary-700">Cari</button>
                    @if($search || $leaderFilter || $statusFilter)
                        <a href="{{ route('activities.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">Reset</a>
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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" x-data>
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
                                            $isUtama = (strtolower($activity->leader->position) === strtolower($leaderFilter));
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
                                    <span>{{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }} - {{ $activity->end_time ? \Carbon\Carbon::parse($activity->end_time)->format('H:i') : 'Selesai' }}</span>
                                </div>
                            </div>
                            <a href="{{ route('activities.show', $activity->id) }}" class="block mt-4 mb-2">
                                <h3 class="text-lg font-extrabold text-gray-900 leading-snug group-hover:text-primary-600 transition-colors pr-12 line-clamp-2">{{ $activity->title }}</h3>
                            </a>
                            
                            <div class="flex items-center gap-2 mb-3">
                                @if($activity->leader)
                                    @php
                                        $position = strtolower($activity->leader->position);
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
                                        {{ $activity->leader->position }}
                                    </span>
                                    <span class="text-sm font-bold text-gray-800">{{ $activity->leader->name }}</span>
                                @endif

                                @php
                                    $dispositionStatus = null;
                                    if ($activity->leader_id && $activity->relationLoaded('dispositions')) {
                                        $leaderDisposition = $activity->dispositions->firstWhere('from_leader_id', $activity->leader_id);
                                        if ($leaderDisposition) {
                                            $dispositionStatus = $leaderDisposition->status;
                                        }
                                    }
                                @endphp

                                @if($dispositionStatus)
                                    <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border {{ $dispositionStatus === 'hadir' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : ($dispositionStatus === 'skip' ? 'bg-red-100 text-red-800 border-red-200' : 'bg-indigo-100 text-indigo-800 border-indigo-200') }}">
                                        {{ $dispositionStatus }}
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
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold shadow-sm {{ $statusStyles[$activity->status] ?? 'bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-600/10' }}">
                                    {{ ucfirst($activity->status) }}
                                </span>
                            </div>                            <div class="flex items-center gap-3">
                                <a href="{{ route('activities.edit', $activity->id) }}" class="p-1.5 inline-flex text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('activities.destroy', $activity->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus agenda ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
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
                                    <a href="{{ route('activities.show', $la['activity']->id) }}"
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
