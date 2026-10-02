<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-bold text-xl text-primary-900 leading-tight">
                {{ __('Kelola Agenda Kegiatan') }}
            </h2>
            <button x-data x-on:click.prevent="$dispatch('open-modal', 'create-activity')" class="inline-flex items-center px-4 py-2 bg-secondary-500 hover:bg-secondary-600 border border-transparent rounded-full font-bold text-xs text-primary-900 uppercase tracking-widest shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Agenda
            </button>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ viewMode: 'list' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-primary-50 border border-primary-200 text-primary-800 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Filters -->
            <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-primary-100 flex flex-col sm:flex-row gap-4 items-center justify-between">
                <form action="{{ route('activities.index') }}" method="GET" class="w-full sm:w-auto flex flex-col sm:flex-row gap-2">
                    <input type="date" name="date" value="{{ $dateFilter }}" onchange="this.form.submit()" class="border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm">
                    <div class="relative w-full sm:w-64">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari kegiatan..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ $statusFilter == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ $statusFilter == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="revision" {{ $statusFilter == 'revision' ? 'selected' : '' }}>Revision</option>
                        <option value="approved" {{ $statusFilter == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="cancelled" {{ $statusFilter == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md text-sm font-medium hover:bg-primary-700">Cari</button>
                    @if($search || $statusFilter)
                        <a href="{{ route('activities.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">Reset</a>
                    @endif
                </form>
            </div>

            <!-- View Mode Toggle -->
            <div class="mb-4 flex justify-end">
                <div class="bg-gray-100 p-1 rounded-lg inline-flex shadow-inner">
                    <button @click="viewMode = 'list'" :class="{'bg-white shadow text-primary-700': viewMode === 'list', 'text-gray-500 hover:text-gray-700': viewMode !== 'list'}" class="px-4 py-1.5 rounded-md text-sm font-bold transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Grid View
                    </button>
                    <button @click="viewMode = 'timeline'" :class="{'bg-white shadow text-primary-700': viewMode === 'timeline', 'text-gray-500 hover:text-gray-700': viewMode !== 'timeline'}" class="px-4 py-1.5 rounded-md text-sm font-bold transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H4a2 2 0 00-2 2v6a2 2 0 002 2h3a2 2 0 002-2zm0 0V9a2 2 0 012-2h3a2 2 0 012 2v10m-2 0h2a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4h4m-4 0v4m0-4v-4m0 0H9m1 0h4"></path></svg>
                        Timeline View
                    </button>
                </div>
            </div>

            <!-- List View -->
            <div x-show="viewMode === 'list'">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" x-data>
                @forelse($activities as $activity)
                    <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-sm border border-primary-100 hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col relative">
                        <!-- Status Badge -->
                        <div class="absolute top-0 right-0 m-3">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-gray-200 text-gray-800',
                                    'submitted' => 'bg-blue-100 text-blue-800',
                                    'revision' => 'bg-red-100 text-red-800',
                                    'approved' => 'bg-primary-100 text-primary-800',
                                    'cancelled' => 'bg-gray-800 text-white',
                                ];
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$activity->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ strtoupper($activity->status) }}
                            </span>
                        </div>

                        <div class="p-5 flex-grow">
                            <div class="text-xs text-primary-600 font-bold mb-1">
                                {{ \Carbon\Carbon::parse($activity->activity_date)->translatedFormat('l, d M Y') }} | {{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }} - {{ $activity->end_time ? \Carbon\Carbon::parse($activity->end_time)->format('H:i') : 'Selesai' }}
                            </div>
                            <h3 class="text-md font-bold text-gray-900 leading-tight mb-2 pr-16 line-clamp-2">{{ $activity->title }}</h3>
                            
                            <div class="space-y-1.5 mt-3">
                                <div class="flex items-start text-xs text-gray-600">
                                    <svg class="w-3.5 h-3.5 mr-1.5 mt-0.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="line-clamp-1">{{ $activity->location_text ?: 'Lokasi tidak diset' }}</span>
                                </div>
                                <div class="flex items-start text-xs text-gray-600">
                                    <svg class="w-3.5 h-3.5 mr-1.5 mt-0.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span class="line-clamp-1">PIC: {{ $activity->protocolOfficer?->name ?: '-' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        @if($activity->status === 'revision' && !empty($activity->revision_notes))
                            <div class="mx-5 mb-3 p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-800 shadow-sm relative">
                                <span class="font-bold block mb-1 uppercase tracking-wide flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Catatan Revisi dari Kabag
                                </span>
                                {{ $activity->revision_notes }}
                            </div>
                        @endif
                        
                        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                            <div>
                                <button x-on:click.prevent="$dispatch('open-modal', 'status-activity-{{ $activity->id }}')" class="text-xs font-semibold text-primary-600 hover:text-primary-800">Ubah Status</button>
                            </div>
                            <div class="flex gap-3">
                                <button x-on:click.prevent="$dispatch('open-modal', 'edit-activity-{{ $activity->id }}')" class="text-primary-600 hover:text-primary-800 font-medium text-xs">Edit</button>
                                <form action="{{ route('activities.destroy', $activity->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus agenda ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-xs">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Status Modal -->
                    <x-modal name="status-activity-{{ $activity->id }}" focusable>
                        <form method="post" action="{{ route('activities.update-status', $activity->id) }}" class="p-6" x-data="{ status: '{{ $activity->status }}' }">
                            @csrf
                            @method('PATCH')
                            <h2 class="text-lg font-bold text-primary-900 mb-4 border-b pb-2">Ubah Status Agenda</h2>
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="status_{{ $activity->id }}" value="Status" />
                                    <select x-model="status" id="status_{{ $activity->id }}" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 font-medium">
                                        <option value="draft">Draft (Petugas Input)</option>
                                        <option value="submitted">Submitted (Kirim ke Kabag)</option>
                                        <option value="revision">Revision (Revisi/Kembalikan)</option>
                                        <option value="approved">Approved (Setujui)</option>
                                        <option value="cancelled">Cancelled (Batalkan)</option>
                                    </select>
                                </div>
                                <div x-show="status === 'revision' || status === 'cancelled'" x-transition>
                                    <x-input-label for="revision_notes_{{ $activity->id }}" value="Catatan / Alasan" />
                                    <textarea id="revision_notes_{{ $activity->id }}" name="revision_notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Mohon sertakan alasan spesifik...">{{ $activity->revision_notes }}</textarea>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md font-semibold text-xs uppercase hover:bg-gray-300">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-secondary-500 text-primary-900 rounded-md font-bold text-xs uppercase hover:bg-secondary-600">Simpan Status</button>
                            </div>
                        </form>
                    </x-modal>
                    
                    <!-- Edit Modal (Simplified for brevity, we will expand it) -->
                    <x-modal name="edit-activity-{{ $activity->id }}" focusable maxWidth="2xl">
                        <form method="post" action="{{ route('activities.update', $activity->id) }}" class="p-6">
                            @csrf
                            @method('PUT')
                            <h2 class="text-lg font-bold text-primary-900 mb-4 border-b pb-2">Edit Agenda</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="col-span-full">
                                    <x-input-label value="Nama Kegiatan" />
                                    <textarea name="title" rows="3" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" required>{{ old('title', $activity->title) }}</textarea>
                                </div>
                                <div>
                                    <x-input-label value="Tanggal" />
                                    <x-text-input name="activity_date" type="date" class="mt-1 block w-full" :value="old('activity_date', $activity->activity_date?->format('Y-m-d'))" required />
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <x-input-label value="Mulai" />
                                        <x-text-input name="start_time" type="time" class="mt-1 block w-full" :value="old('start_time', $activity->start_time?->format('H:i'))" required />
                                    </div>
                                    <div>
                                        <x-input-label value="Selesai" />
                                        <x-text-input name="end_time" type="time" class="mt-1 block w-full" :value="old('end_time', $activity->end_time?->format('H:i'))" />
                                    </div>
                                </div>
                                <div class="col-span-full">
                                    <x-input-label value="Penyelenggara" />
                                    <x-text-input name="organizer_input" list="organizers_list" type="text" class="mt-1 block w-full" :value="old('organizer_input', $activity->organization_id ? $activity->organization?->name : $activity->organizer_text)" placeholder="Ketik nama penyelenggara..." />
                                </div>
                                <div class="col-span-full">
                                    <x-input-label value="Lokasi" />
                                    <x-text-input name="location_input" list="locations_list" type="text" class="mt-1 block w-full" :value="old('location_input', $activity->location_id ? $activity->location?->name : $activity->location_text)" placeholder="Ketik nama lokasi..." />
                                </div>
                                <div>
                                    <x-input-label value="Pimpinan (Utama)" />
                                    <select name="leader_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">-- Pilih --</option>
                                        @foreach($leaders as $ld)
                                            <option value="{{ $ld->id }}" {{ old('leader_id', $activity->leader_id) == $ld->id ? 'selected' : '' }}>{{ $ld->name }} ({{ $ld->position }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label value="Pendamping" />
                                    <div x-data="{
                                        options: [
                                            @foreach($leaders as $ld)
                                                { value: '{{ $ld->id }}', text: '{{ addslashes($ld->name) }} ({{ addslashes($ld->position) }})' },
                                            @endforeach
                                        ],
                                        selected: {{ json_encode(array_map('strval', old('companion_ids', $activity->companions->pluck('id')->toArray()))) }},
                                        open: false,
                                        search: '',
                                        get filteredOptions() {
                                            if (this.search === '') return this.options;
                                            return this.options.filter(o => o.text.toLowerCase().includes(this.search.toLowerCase()));
                                        },
                                        isSelected(val) { return this.selected.includes(val.toString()); },
                                        toggle(val) {
                                            val = val.toString();
                                            if (this.isSelected(val)) {
                                                this.selected = this.selected.filter(i => i !== val);
                                            } else {
                                                this.selected.push(val);
                                                this.search = '';
                                            }
                                        },
                                        remove(val) {
                                            this.selected = this.selected.filter(i => i !== val.toString());
                                        },
                                        getOptionText(val) {
                                            let opt = this.options.find(o => o.value === val.toString());
                                            return opt ? opt.text : '';
                                        }
                                    }" class="relative mt-1">
                                        <!-- Hidden inputs for form submission -->
                                        <template x-for="val in selected" :key="val">
                                            <input type="hidden" name="companion_ids[]" :value="val">
                                        </template>
                                        
                                        <!-- Display Box -->
                                        <div @click="open = !open; $refs.searchInput.focus()" @click.away="open = false" class="min-h-[42px] p-1.5 w-full border border-gray-300 rounded-md shadow-sm bg-white cursor-text flex flex-col justify-center focus-within:ring-1 focus-within:ring-primary-500 focus-within:border-primary-500 transition-all duration-200">
                                            
                                            <!-- Selected Badges -->
                                            <div x-show="selected.length > 0" class="flex flex-wrap gap-1 mb-1.5">
                                                <template x-for="(val, index) in selected" :key="index">
                                                    <span class="inline-flex items-center px-2 py-1 rounded bg-secondary-100 text-primary-900 text-xs font-bold border border-secondary-200 shadow-sm">
                                                        <span x-text="getOptionText(val)"></span>
                                                        <button type="button" @click.stop="remove(val)" class="ml-1.5 text-secondary-600 hover:text-red-500 focus:outline-none transition-colors">
                                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    </span>
                                                </template>
                                            </div>
                                            
                                            <!-- Input & Arrow -->
                                            <div class="flex items-center w-full">
                                                <input x-ref="searchInput" x-model="search" type="text" class="flex-1 w-full outline-none border-none focus:ring-0 text-sm p-0" placeholder="Cari pendamping...">
                                                <div class="text-gray-400 px-1 cursor-pointer">
                                                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'transform rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Dropdown List -->
                                        <div x-show="open" x-transition.opacity.duration.200ms class="absolute z-50 w-full mt-1 bg-white rounded-md shadow-xl border border-gray-200 max-h-60 overflow-y-auto" style="display: none;">
                                            <template x-for="option in filteredOptions" :key="option.value">
                                                <div @click="toggle(option.value)" class="px-3 py-2 cursor-pointer text-sm transition-colors flex justify-between items-center border-b border-gray-50 last:border-0" :class="isSelected(option.value) ? 'bg-primary-50 text-primary-900' : 'hover:bg-gray-50 text-gray-700'">
                                                    <span x-text="option.text" :class="isSelected(option.value) ? 'font-bold' : ''"></span>
                                                    <svg x-show="isSelected(option.value)" class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                            </template>
                                            <div x-show="filteredOptions.length === 0" class="px-4 py-3 text-sm text-gray-500 italic">Pimpinan tidak ditemukan...</div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <x-input-label value="Narahubung (Nama)" />
                                    <x-text-input name="contact_person_name" type="text" class="mt-1 block w-full" :value="old('contact_person_name', $activity->contact_person_name)" />
                                </div>
                                <div>
                                    <x-input-label value="Narahubung (No. HP)" />
                                    <x-text-input name="contact_person_phone" type="text" class="mt-1 block w-full" :value="old('contact_person_phone', $activity->contact_person_phone)" />
                                </div>
                                <div>
                                    <x-input-label value="PIC Protokol" />
                                    <select name="protocol_officer_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">-- Pilih --</option>
                                        @foreach($protocolOfficers as $po)
                                            <option value="{{ $po->id }}" {{ old('protocol_officer_id', $activity->protocol_officer_id) == $po->id ? 'selected' : '' }}>{{ $po->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label value="Ajudan (ADC)" />
                                    <x-text-input name="adc" type="text" class="mt-1 block w-full" :value="old('adc', $activity->adc)" />
                                </div>
                                <div>
                                    <x-input-label value="Pakaian" />
                                    <x-text-input name="dress_code" type="text" class="mt-1 block w-full" :value="old('dress_code', $activity->dress_code)" placeholder="Contoh: PSL, Batik" />
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md font-semibold text-xs uppercase hover:bg-gray-300">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-secondary-500 text-primary-900 rounded-md font-bold text-xs uppercase hover:bg-secondary-600">Simpan Perubahan</button>
                            </div>
                        </form>
                    </x-modal>

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
            <div x-show="viewMode === 'timeline'" style="display: none;" class="bg-white rounded-xl shadow-sm border border-primary-100 overflow-hidden overflow-x-auto">
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
                                        $position = strtolower($td['leader']->position);
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
                                    <div class="absolute rounded p-1.5 shadow-sm border cursor-pointer hover:shadow-md transition-all overflow-hidden flex flex-col justify-center {{ $bgClass }}"
                                         style="left: {{ $la['left'] }}%; width: max({{ $la['width'] }}%, 2%); top: {{ $la['row'] * 45 + 10 }}px; height: 38px; z-index: 10;"
                                         title="{{ $la['start_time'] }} - {{ $la['end_time'] }} | {{ $la['activity']->title }}">
                                        <div class="text-[10px] font-bold leading-tight truncate">
                                            {{ $la['start_time'] }} - {{ $la['activity']->title }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <!-- Create Modal (Simplified for brevity) -->
    <x-modal name="create-activity" focusable maxWidth="2xl">
        <form method="post" action="{{ route('activities.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-bold text-primary-900 mb-4 border-b pb-2">Tambah Agenda Baru</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-full">
                    <x-input-label value="Nama Kegiatan" />
                    <textarea name="title" rows="3" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" required>{{ old('title') }}</textarea>
                </div>
                <div>
                    <x-input-label value="Tanggal" />
                    <x-text-input name="activity_date" type="date" class="mt-1 block w-full" :value="old('activity_date', $dateFilter)" required />
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <x-input-label value="Mulai" />
                        <x-text-input name="start_time" type="time" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label value="Selesai" />
                        <x-text-input name="end_time" type="time" class="mt-1 block w-full" />
                    </div>
                </div>
                <div class="col-span-full">
                    <x-input-label value="Penyelenggara" />
                    <x-text-input name="organizer_input" list="organizers_list" type="text" class="mt-1 block w-full" placeholder="Ketik nama penyelenggara..." />
                </div>
                <div class="col-span-full">
                    <x-input-label value="Lokasi" />
                    <x-text-input name="location_input" list="locations_list" type="text" class="mt-1 block w-full" placeholder="Ketik nama lokasi..." />
                </div>
                <div>
                    <x-input-label value="Pimpinan (Utama)" />
                    <select name="leader_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Pilih --</option>
                        @foreach($leaders as $ld)
                            <option value="{{ $ld->id }}">{{ $ld->name }} ({{ $ld->position }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="Pendamping" />
                    <div x-data="{
                        options: [
                            @foreach($leaders as $ld)
                                { value: '{{ $ld->id }}', text: '{{ addslashes($ld->name) }} ({{ addslashes($ld->position) }})' },
                            @endforeach
                        ],
                        selected: {{ json_encode(array_map('strval', old('companion_ids', []))) }},
                        open: false,
                        search: '',
                        get filteredOptions() {
                            if (this.search === '') return this.options;
                            return this.options.filter(o => o.text.toLowerCase().includes(this.search.toLowerCase()));
                        },
                        isSelected(val) { return this.selected.includes(val.toString()); },
                        toggle(val) {
                            val = val.toString();
                            if (this.isSelected(val)) {
                                this.selected = this.selected.filter(i => i !== val);
                            } else {
                                this.selected.push(val);
                                this.search = '';
                            }
                        },
                        remove(val) {
                            this.selected = this.selected.filter(i => i !== val.toString());
                        },
                        getOptionText(val) {
                            let opt = this.options.find(o => o.value === val.toString());
                            return opt ? opt.text : '';
                        }
                    }" class="relative mt-1">
                        <!-- Hidden inputs for form submission -->
                        <template x-for="val in selected" :key="val">
                            <input type="hidden" name="companion_ids[]" :value="val">
                        </template>
                        
                        <!-- Display Box -->
                        <div @click="open = !open; $refs.searchInput.focus()" @click.away="open = false" class="min-h-[42px] p-1.5 w-full border border-gray-300 rounded-md shadow-sm bg-white cursor-text flex flex-col justify-center focus-within:ring-1 focus-within:ring-primary-500 focus-within:border-primary-500 transition-all duration-200">
                            
                            <!-- Selected Badges -->
                            <div x-show="selected.length > 0" class="flex flex-wrap gap-1 mb-1.5">
                                <template x-for="(val, index) in selected" :key="index">
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-secondary-100 text-primary-900 text-xs font-bold border border-secondary-200 shadow-sm">
                                        <span x-text="getOptionText(val)"></span>
                                        <button type="button" @click.stop="remove(val)" class="ml-1.5 text-secondary-600 hover:text-red-500 focus:outline-none transition-colors">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </span>
                                </template>
                            </div>
                            
                            <!-- Input & Arrow -->
                            <div class="flex items-center w-full">
                                <input x-ref="searchInput" x-model="search" type="text" class="flex-1 w-full outline-none border-none focus:ring-0 text-sm p-0" placeholder="Cari pendamping...">
                                <div class="text-gray-400 px-1 cursor-pointer">
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'transform rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dropdown List -->
                        <div x-show="open" x-transition.opacity.duration.200ms class="absolute z-50 w-full mt-1 bg-white rounded-md shadow-xl border border-gray-200 max-h-60 overflow-y-auto" style="display: none;">
                            <template x-for="option in filteredOptions" :key="option.value">
                                <div @click="toggle(option.value)" class="px-3 py-2 cursor-pointer text-sm transition-colors flex justify-between items-center border-b border-gray-50 last:border-0" :class="isSelected(option.value) ? 'bg-primary-50 text-primary-900' : 'hover:bg-gray-50 text-gray-700'">
                                    <span x-text="option.text" :class="isSelected(option.value) ? 'font-bold' : ''"></span>
                                    <svg x-show="isSelected(option.value)" class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </template>
                            <div x-show="filteredOptions.length === 0" class="px-4 py-3 text-sm text-gray-500 italic">Pimpinan tidak ditemukan...</div>
                        </div>
                    </div>
                </div>
                <div>
                    <x-input-label value="Narahubung (Nama)" />
                    <x-text-input name="contact_person_name" type="text" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label value="Narahubung (No. HP)" />
                    <x-text-input name="contact_person_phone" type="text" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label value="PIC Protokol" />
                    <select name="protocol_officer_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Pilih --</option>
                        @foreach($protocolOfficers as $po)
                            <option value="{{ $po->id }}">{{ $po->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="Ajudan (ADC)" />
                    <x-text-input name="adc" type="text" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label value="Pakaian" />
                    <x-text-input name="dress_code" type="text" class="mt-1 block w-full" placeholder="Contoh: PSL, Batik" />
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md font-semibold text-xs uppercase hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-secondary-500 text-primary-900 rounded-md font-bold text-xs uppercase hover:bg-secondary-600">Simpan Draf</button>
            </div>
        </form>
    </x-modal>

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
