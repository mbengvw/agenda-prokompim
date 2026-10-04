<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between items-center w-full gap-2">
            <h2 class="font-bold text-lg sm:text-xl text-primary-900 leading-tight truncate">
                {{ __('Detail Agenda') }}
            </h2>
            <a href="{{ route('activities.index') }}" class="shrink-0 inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 bg-gray-200 hover:bg-gray-300 border border-transparent rounded-full font-bold text-[10px] sm:text-xs text-gray-800 uppercase tracking-widest shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row gap-6">
            
            <!-- Kolom Utama: Detail Agenda -->
            <div class="flex-1 space-y-6">
                <!-- Status Bar -->
                @if(session('success'))
                    <div class="bg-primary-50 border border-primary-200 text-primary-800 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                
                @if($activity->status === 'revision' && !empty($activity->revision_notes))
                    <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 flex items-start gap-3 shadow-sm">
                        <svg class="w-6 h-6 text-red-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <div>
                            <h4 class="font-bold text-red-900 mb-1">Catatan Revisi dari Kabag:</h4>
                            <p class="text-sm leading-relaxed">{{ $activity->revision_notes }}</p>
                        </div>
                    </div>
                @endif

                <!-- Card Detail -->
                <div class="bg-white rounded-2xl shadow-xl border border-primary-100 overflow-hidden">
                    
                    <div class="p-6 sm:p-8 bg-gradient-to-br from-teal-50 to-white border-b border-teal-100">
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            @php
                                $statusStyles = [
                                    'draft' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    'submitted' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'revision' => 'bg-orange-100 text-orange-800 border-orange-200',
                                    'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                    'cancelled' => 'bg-slate-800 text-white border-slate-900',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border {{ $statusStyles[$activity->status] ?? 'bg-gray-100' }}">
                                {{ $activity->status }}
                            </span>
                            
                            @if($activity->is_disposition)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-100 text-indigo-800 border border-indigo-200">
                                    Didisposisikan
                                </span>
                            @endif
                        </div>
                        
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight mb-4">{{ $activity->title }}</h1>
                        
                        <div class="flex items-center gap-2 text-sm text-primary-700 font-bold bg-primary-100/50 inline-flex px-4 py-2 rounded-lg border border-primary-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>{{ \Carbon\Carbon::parse($activity->activity_date)->translatedFormat('l, d F Y') }}</span>
                            <span class="text-primary-300 mx-1">|</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }} - {{ $activity->end_time ? \Carbon\Carbon::parse($activity->end_time)->format('H:i') : 'Selesai' }}</span>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8 space-y-8">
                        @if($activity->description)
                            <div>
                                <h3 class="text-sm font-bold text-teal-800 uppercase tracking-wider mb-2">Deskripsi Kegiatan</h3>
                                <div class="prose prose-sm prose-teal max-w-none text-gray-700 bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    {!! nl2br(e($activity->description)) !!}
                                </div>
                            </div>
                        @endif

                        <div class="space-y-6">
                            <!-- Section: Pimpinan & Pendamping -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-xs font-bold text-teal-800 uppercase tracking-wider mb-1">Pimpinan (Utama)</h3>
                                    <p class="text-gray-900 font-semibold">{{ $activity->leader?->name ?: '-' }} <span class="text-gray-500 font-normal text-sm">({{ $activity->leader?->position ?: '-' }})</span></p>
                                </div>
                                
                                <div>
                                    <h3 class="text-xs font-bold text-teal-800 uppercase tracking-wider mb-1">Pejabat Pendamping</h3>
                                    @if($activity->companions->count() > 0)
                                        <ul class="list-disc list-inside text-gray-900">
                                            @foreach($activity->companions as $comp)
                                                <li>{{ $comp->name }} <span class="text-gray-500 text-sm">({{ $comp->position }})</span></li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-gray-500 italic">-</p>
                                    @endif
                                </div>
                            </div>
                            
                            @if($activity->is_disposition && $activity->disposition_to_id)
                                <div class="bg-indigo-50 border-l-4 border-indigo-500 p-3 rounded-r-lg max-w-lg">
                                    <h3 class="text-xs font-bold text-indigo-800 uppercase tracking-wider mb-1">Disposisi Kepada</h3>
                                    @php $dispoTo = \App\Models\Leader::find($activity->disposition_to_id); @endphp
                                    <p class="text-indigo-900 font-bold">{{ $dispoTo?->name }} <span class="font-normal text-sm">({{ $dispoTo?->position }})</span></p>
                                </div>
                            @endif

                            <hr class="border-gray-200">

                            <!-- Section: Teknis Kegiatan -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                                <div>
                                    <h3 class="text-xs font-bold text-teal-800 uppercase tracking-wider mb-1">Lokasi</h3>
                                    <p class="text-gray-900">{{ $activity->location_id ? $activity->location?->name : ($activity->location_text ?: '-') }}</p>
                                    @if($activity->location_id && $activity->location?->address)
                                        <p class="text-teal-600 text-sm mt-0.5">{{ $activity->location->address }}</p>
                                    @endif
                                </div>
                                
                                <div>
                                    <h3 class="text-xs font-bold text-teal-800 uppercase tracking-wider mb-1">Penyelenggara</h3>
                                    <p class="text-gray-900">{{ $activity->organization_id ? $activity->organization?->name : ($activity->organizer_text ?: '-') }}</p>
                                </div>
                                
                                <div>
                                    <h3 class="text-xs font-bold text-teal-800 uppercase tracking-wider mb-1">Pakaian (Dress Code)</h3>
                                    <p class="text-gray-900">{{ $activity->dress_code ?: '-' }}</p>
                                </div>
                            </div>

                            <hr class="border-gray-200">

                            <!-- Section: Narahubung & Petugas -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                                <div>
                                    <h3 class="text-xs font-bold text-teal-800 uppercase tracking-wider mb-1">Narahubung</h3>
                                    <p class="text-gray-900">{{ $activity->contact_person_name ?: '-' }}</p>
                                    <p class="text-teal-600 text-sm">{{ $activity->contact_person_phone }}</p>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-teal-800 uppercase tracking-wider mb-1">Protokol</h3>
                                    <p class="text-gray-900">{{ $activity->protocolOfficer?->name ?: '-' }}</p>
                                    <p class="text-teal-600 text-sm">{{ $activity->protocolOfficer?->phone }}</p>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-teal-800 uppercase tracking-wider mb-1">Ajudan (ADC)</h3>
                                    <p class="text-gray-900">{{ $activity->adc ?: '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 pt-6 border-t border-gray-100 flex flex-wrap gap-2 items-center">
                            <!-- Tombol Staf/Admin -->
                            @hasanyrole('staf_protokol|kabag_protokol|admin')
                                <a href="{{ route('activities.edit', $activity->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-xs font-bold shadow-sm transition-all whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Edit Redaksional
                                </a>
                            @endhasanyrole

                            <!-- Tombol Action Workflow Staf -->
                            @role('staf_protokol')
                                @if(in_array($activity->status, ['draft', 'revision']))
                                    <form action="{{ route('activities.update-status', $activity->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="submitted">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-600 text-white rounded-md hover:bg-primary-700 text-xs font-bold shadow-sm transition-all whitespace-nowrap" onclick="return confirm('Kirim agenda ini ke Kabag Protokol?');">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                            Ajukan ke Kabag
                                        </button>
                                    </form>
                                @endif
                            @endrole

                            <!-- Tombol Kabag -->
                            @role('kabag_protokol')
                                @if($activity->status === 'submitted')
                                    <form action="{{ route('activities.update-status', $activity->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500 text-white rounded-md hover:bg-emerald-600 text-xs font-bold shadow-sm transition-all whitespace-nowrap" onclick="return confirm('Setujui agenda ini?');">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Approve
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('activities.update-status', $activity->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-500 text-white rounded-md hover:bg-red-600 text-xs font-bold shadow-sm transition-all whitespace-nowrap" onclick="return confirm('Tolak agenda ini?');">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            Reject
                                        </button>
                                    </form>

                                    <button x-data x-on:click.prevent="$dispatch('open-modal', 'revise-activity-{{ $activity->id }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-100 text-orange-700 border border-orange-300 rounded-md hover:bg-orange-200 text-xs font-bold shadow-sm transition-all whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Revisi
                                    </button>
                                @endif
                            @endrole

                            <!-- Tombol Ajudan -->
                            @php
                                $canManageDisposition = false;
                                $user = auth()->user();
                                
                                $userLeaderIds = [];
                                if ($user->hasRole('ajudan_bupati')) {
                                    $userLeaderIds = \App\Models\Leader::whereRaw('LOWER(position) LIKE ?', ['%bupati%'])->whereRaw('LOWER(position) NOT LIKE ?', ['%wakil%'])->pluck('id')->toArray();
                                } elseif ($user->hasRole('ajudan_wabup')) {
                                    $userLeaderIds = \App\Models\Leader::whereRaw('LOWER(position) LIKE ?', ['%wakil bupati%'])->pluck('id')->toArray();
                                } elseif ($user->hasRole('ajudan_sekda')) {
                                    $userLeaderIds = \App\Models\Leader::whereRaw('LOWER(position) LIKE ?', ['%sekda%'])->orWhereRaw('LOWER(position) LIKE ?', ['%sekretaris daerah%'])->pluck('id')->toArray();
                                }

                                $existingDisposition = null;
                                if (!empty($userLeaderIds)) {
                                    $existingDisposition = \App\Models\ActivityDisposition::where('activity_id', $activity->id)
                                        ->whereIn('from_leader_id', $userLeaderIds)
                                        ->latest()
                                        ->first();
                                }

                                if (in_array($activity->status, ['submitted', 'revision', 'approved'])) {
                                    if ($existingDisposition) {
                                        $canManageDisposition = true;
                                    } elseif (!empty($userLeaderIds) && in_array($activity->leader_id, $userLeaderIds)) {
                                        $canManageDisposition = true;
                                    }
                                }
                                
                                $userLeaderId = $existingDisposition ? $existingDisposition->from_leader_id : (empty($userLeaderIds) ? $activity->leader_id : $userLeaderIds[0]);
                            @endphp

                            @if($canManageDisposition)
                                @if($existingDisposition)
                                    <div class="px-4 py-2 bg-gray-50 text-gray-700 rounded-md text-sm font-bold shadow-sm border border-gray-200 inline-flex items-center gap-2 mb-2">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Terkonfirmasi: {{ strtoupper($existingDisposition->status) }}
                                        @if($existingDisposition->status === 'disposisi')
                                            @php $dispoTo = \App\Models\Leader::find($existingDisposition->to_leader_id); @endphp
                                            ke {{ $dispoTo?->name }}
                                        @endif
                                    </div>
                                    <div class="w-full"></div>
                                @endif
                                <form action="{{ route('activities.disposition', $activity->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="hadir">
                                    <input type="hidden" name="from_leader_id" value="{{ $userLeaderId }}">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 text-xs font-bold shadow-sm transition-all whitespace-nowrap" onclick="return confirm('Tandai Pimpinan HADIR pada kegiatan ini?');">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Hadir
                                    </button>
                                </form>
                                <form action="{{ route('activities.disposition', $activity->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="skip">
                                    <input type="hidden" name="from_leader_id" value="{{ $userLeaderId }}">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 text-xs font-bold shadow-sm transition-all whitespace-nowrap" onclick="return confirm('Tandai Pimpinan SKIP (Batal Hadir) pada kegiatan ini?');">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Skip
                                    </button>
                                </form>
                                <button x-data x-on:click.prevent="$dispatch('open-modal', 'disposition-activity-{{ $activity->id }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-xs font-bold shadow-sm transition-all whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                    Ubah Disposisi
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Samping: Riwayat Log -->
            <div class="w-full lg:w-80 shrink-0">
                <div class="bg-white rounded-2xl shadow-xl border border-primary-100 overflow-hidden sticky top-6">
                    <div class="p-5 bg-gray-50 border-b border-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h3 class="font-bold text-gray-800">Riwayat Agenda</h3>
                    </div>
                    
                    <div class="p-5 max-h-[600px] overflow-y-auto">
                        @if($activityLogs->count() > 0)
                            <div class="relative border-l-2 border-gray-200 ml-3 space-y-6">
                                @foreach($activityLogs as $log)
                                    <div class="relative pl-6">
                                        <!-- Dot marker -->
                                        <div class="absolute w-3 h-3 bg-primary-500 rounded-full -left-[7px] top-1.5 ring-4 ring-white"></div>
                                        
                                        <p class="text-xs text-gray-500 mb-1 font-medium">{{ $log->created_at->format('d M Y, H:i') }}</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ $log->causer ? $log->causer->name : 'Sistem' }}</p>
                                        
                                        <div class="mt-1 text-sm text-gray-600 bg-gray-50 p-2 rounded border border-gray-100">
                                            {{ $log->description }}
                                            @if($log->event === 'updated' && $log->properties->has('attributes'))
                                                <ul class="mt-2 text-xs text-gray-500 list-disc list-inside">
                                                    @foreach($log->properties['attributes'] as $key => $value)
                                                        @if(!in_array($key, ['updated_at', 'status']))
                                                            <li>Update: {{ $key }}</li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 text-center py-4 italic">Belum ada riwayat aktivitas.</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Revisi -->
    <x-modal name="revise-activity-{{ $activity->id }}" focusable>
        <form method="post" action="{{ route('activities.update-status', $activity->id) }}" class="p-6">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="revision">
            <h2 class="text-lg font-bold text-orange-700 mb-4 border-b pb-2">Minta Revisi Agenda</h2>
            <div class="mb-4">
                <x-input-label for="revision_notes_{{ $activity->id }}" value="Catatan Revisi" />
                <textarea id="revision_notes_{{ $activity->id }}" name="revision_notes" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 font-medium">Kirim</button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Disposisi -->
    <x-modal name="disposition-activity-{{ $activity->id }}" focusable>
        <form method="post" action="{{ route('activities.disposition', $activity->id) }}" class="p-6">
            @csrf
            <input type="hidden" name="status" value="disposisi">
            <input type="hidden" name="from_leader_id" value="{{ $userLeaderId ?? $activity->leader_id }}">
            <h2 class="text-lg font-bold text-indigo-700 mb-4 border-b pb-2">Disposisikan Agenda</h2>
            <div class="mb-4">
                <x-input-label for="disposition_to_{{ $activity->id }}" value="Disposisikan Kepada" />
                <select id="disposition_to_{{ $activity->id }}" name="to_leader_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="">-- Pilih Pejabat --</option>
                    @php
                        $currentLeaderLevel = 1;
                        if ($activity->leader) {
                            $pos = strtolower($activity->leader->position);
                            if (str_contains($pos, 'bupati') && !str_contains($pos, 'wakil')) {
                                $currentLeaderLevel = 1;
                            } elseif (str_contains($pos, 'wakil bupati')) {
                                $currentLeaderLevel = 2;
                            } elseif (str_contains($pos, 'sekda') || str_contains($pos, 'sekretaris daerah')) {
                                $currentLeaderLevel = 3;
                            } else {
                                $currentLeaderLevel = 4;
                            }
                        }

                        $dispoLeaders = \App\Models\Leader::where('is_active', true)
                            ->where('id', '!=', $activity->leader_id)
                            ->get()
                            ->map(function($leader) {
                                $pos = strtolower($leader->position);
                                if (str_contains($pos, 'bupati') && !str_contains($pos, 'wakil')) {
                                    $leader->level = 1;
                                } elseif (str_contains($pos, 'wakil bupati')) {
                                    $leader->level = 2;
                                } elseif (str_contains($pos, 'sekda') || str_contains($pos, 'sekretaris daerah')) {
                                    $leader->level = 3;
                                } else {
                                    $leader->level = 4;
                                }
                                return $leader;
                            })->filter(function($leader) use ($currentLeaderLevel) {
                                return $leader->level > $currentLeaderLevel;
                            })->sortBy('level');
                    @endphp
                    @foreach($dispoLeaders as $dl)
                        <option value="{{ $dl->id }}">{{ $dl->name }} ({{ $dl->position }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <x-input-label for="notes_{{ $activity->id }}" value="Catatan Tambahan (Opsional)" />
                <textarea id="notes_{{ $activity->id }}" name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium">Proses Disposisi</button>
            </div>
        </form>
    </x-modal>

</x-app-layout>
