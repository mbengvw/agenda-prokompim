<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between items-center w-full gap-2">
            <h2 class="font-bold text-lg sm:text-xl text-primary-900 leading-tight truncate">
                {{ __('Ubah Agenda Kegiatan') }}
            </h2>
            <a href="{{ request('redirect_to', route('activities.index', array_filter(['date' => $dateFilter ?? request('date'), 'leader' => $leaderFilter ?? request('leader')]))) }}" class="shrink-0 inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 bg-gray-200 hover:bg-gray-300 border border-transparent rounded-full font-bold text-[10px] sm:text-xs text-gray-800 uppercase tracking-widest shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white sm:rounded-2xl shadow-xl border border-primary-100 overflow-hidden">
                <form method="post" action="{{ route('activities.update', ['activity' => $activity->id, 'redirect_to' => request('redirect_to'), 'date' => request('date'), 'leader' => request('leader')]) }}" class="p-5 sm:p-8"
                      x-data="{ formTimeError: false }"
                      @time-error-changed-edit.window="formTimeError = $event.detail.hasError"
                      @submit="if (formTimeError) { $event.preventDefault(); }">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-full">
                            <x-input-label value="Nama Kegiatan" />
                            <textarea name="title" rows="3" class="mt-2 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" required>{{ old('title', $activity->title) }}</textarea>
                        </div>
                        
                        <div>
                            <x-input-label value="Tanggal" />
                            <x-text-input name="activity_date" type="date" class="mt-2 block w-full" :value="old('activity_date', $activity->activity_date?->format('Y-m-d'))" required />
                        </div>
                        
                        @php
                            $startTime = old('start_time', $activity->start_time?->format('H:i'));
                            $endTime = old('end_time', $activity->end_time?->format('H:i'));
                        @endphp
                        <div x-data="{
                            isTentative: {{ old('is_tentative', $activity->is_tentative) ? 'true' : 'false' }},
                            startVal: @js($startTime ?? ''),
                            endVal: @js($endTime ?? ''),
                            get timeError() {
                                if (this.isTentative || !this.startVal || !this.endVal) return false;
                                return this.endVal <= this.startVal;
                            },
                            notifyTimeError() {
                                this.$nextTick(() => this.$dispatch('time-error-changed-edit', { hasError: this.timeError }));
                            }
                        }" x-init="$watch('startVal', () => notifyTimeError()); $watch('endVal', () => notifyTimeError())">
                            <div class="flex items-center justify-between mb-2">
                                <x-input-label value="Waktu Pelaksanaan" />
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_tentative" value="1" x-model="isTentative" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                                    <span class="ml-2 text-xs text-gray-600 font-medium">Waktu belum ditentukan (Tentatif)</span>
                                </label>
                            </div>

                            <div class="grid grid-cols-2 gap-3 sm:gap-4" x-show="!isTentative" x-transition x-cloak>
                                <div>
                                    <x-input-label value="Mulai" />
                                    <x-time-picker x-model="startVal" label="Mulai" />
                                    <input type="hidden" name="start_time" :value="startVal" x-bind:required="!isTentative" />
                                </div>
                                <div>
                                    <x-input-label value="Selesai" />
                                    <x-time-picker x-model="endVal" label="Selesai" x-bind:class="timeError ? 'ring-2 ring-red-400 rounded-lg' : ''" />
                                    <input type="hidden" name="end_time" :value="endVal" />
                                </div>
                            </div>
                            <div x-show="!isTentative && timeError" x-transition style="display: none;"
                                 class="mt-2 flex items-center gap-2 text-sm text-red-700 bg-red-50 p-2.5 rounded-lg border border-red-300">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                <span>Waktu selesai harus lebih besar dari waktu mulai.</span>
                            </div>
                            <div x-show="isTentative" style="display: none;" class="mt-2 text-sm text-amber-600 bg-amber-50 p-2.5 rounded border border-amber-200">
                                Waktu kegiatan ini bersifat tentatif (belum pasti).
                            </div>
                        </div>
                        
                        <div class="col-span-full">
                            <x-input-label value="Penyelenggara" />
                            <div class="flex gap-2">
                                <x-searchable-select
                                    class="flex-1"
                                    name="organizer_input"
                                    placeholder="-- Pilih Penyelenggara --"
                                    search-placeholder="Cari penyelenggara..."
                                    empty-text="Penyelenggara tidak ditemukan."
                                    :options="$organizations->pluck('name')"
                                    :value="old('organizer_input', $activity->organization_id ? $activity->organization?->name : $activity->organizer_text)"
                                />
                                <button type="button" x-data x-on:click.prevent="$dispatch('open-modal', 'create-organization')" class="mt-2 shrink-0 inline-flex items-center justify-center w-10 h-10 bg-primary-50 text-primary-600 rounded-md hover:bg-primary-100 border border-primary-200 transition-colors" title="Tambah Penyelenggara Baru">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-span-full">
                            <x-input-label value="Lokasi" />
                            <div class="flex gap-2">
                                <x-searchable-select
                                    class="flex-1"
                                    name="location_input"
                                    placeholder="-- Pilih Lokasi --"
                                    search-placeholder="Cari lokasi..."
                                    empty-text="Lokasi tidak ditemukan."
                                    :options="$locations->pluck('name')"
                                    :value="old('location_input', $activity->location_id ? $activity->location?->name : $activity->location_text)"
                                />
                                <button type="button" x-data x-on:click.prevent="$dispatch('open-modal', 'create-location')" class="mt-2 shrink-0 inline-flex items-center justify-center w-10 h-10 bg-primary-50 text-primary-600 rounded-md hover:bg-primary-100 border border-primary-200 transition-colors" title="Tambah Lokasi Baru">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <x-input-label value="Pimpinan (Utama)" />
                            @php
                                $leader = $activity->leader;
                                $leaderLabel = $leader ? $leader->name . ' (' . strtoupper($leader->position) . ')' : '';
                            @endphp
                            <x-text-input type="text" class="mt-2 block w-full bg-gray-100 text-gray-500 cursor-not-allowed" :value="$leaderLabel" readonly />
                            <input type="hidden" name="leader_id" value="{{ old('leader_id', $activity->leader_id) }}">
                        </div>
                        
                        <div>
                            <x-input-label value="Pendamping" />
                            <div class="flex gap-2 items-start">
                                <div x-data="{
                                    mainLeaderId: '{{ old('leader_id', $activity->leader_id) }}',
                                    options: [
                                        @foreach($leaders as $ld)
                                            { value: '{{ $ld->id }}', text: '{{ addslashes(ucwords(strtolower($ld->position))) }}', level: {{ $ld->hierarchy_level ?? 99 }} },
                                        @endforeach
                                    ],
                                    selected: {{ json_encode(array_map('strval', old('companion_ids', $activity->companions->pluck('id')->toArray()))) }},
                                    open: false,
                                    search: '',
                                    get mainLeaderLevel() {
                                        if (!this.mainLeaderId) return 0;
                                        let opt = this.options.find(o => o.value === this.mainLeaderId.toString());
                                        return opt ? parseInt(opt.level) : 0;
                                    },
                                    get filteredOptions() {
                                        let mLevel = this.mainLeaderLevel;
                                        let opts = this.options.filter(o => o.value !== this.mainLeaderId.toString());
                                        
                                        if (mLevel > 0) {
                                            opts = opts.filter(o => parseInt(o.level) >= mLevel);
                                        }
                                        
                                        if (this.search === '') return opts;
                                        return opts.filter(o => o.text.toLowerCase().includes(this.search.toLowerCase()));
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
                                }" @leader-added.window="options.push($event.detail)" @main-leader-changed.window="mainLeaderId = $event.detail; selected = selected.filter(id => { let o = options.find(opt => opt.value === id); return o && o.value !== mainLeaderId && parseInt(o.level) >= mainLeaderLevel; });" class="relative mt-2 flex-1">
                                    <!-- Hidden inputs for form submission -->
                                    <template x-for="val in selected" :key="val">
                                        <input type="hidden" name="companion_ids[]" :value="val">
                                    </template>
                                    
                                    <!-- Display Box -->
                                    <div @click="open = !open; $refs.searchInput.focus()" @click.away="open = false" class="min-h-[42px] p-2 w-full border border-gray-300 rounded-md shadow-sm bg-white cursor-text flex flex-col justify-center focus-within:ring-1 focus-within:ring-primary-500 focus-within:border-primary-500 transition-all duration-200">
                                        
                                        <!-- Selected Badges -->
                                        <div x-show="selected.length > 0" x-cloak class="flex flex-wrap gap-1 mb-1.5">
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
                                <button type="button" x-data x-on:click.prevent="$dispatch('open-modal', 'create-leader')" class="mt-2 shrink-0 inline-flex items-center justify-center min-h-[42px] w-10 bg-primary-50 text-primary-600 rounded-md hover:bg-primary-100 border border-primary-200 transition-colors" title="Tambah Pendamping Baru">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <x-input-label value="Narahubung (Nama)" />
                            <x-text-input name="contact_person_name" type="text" class="mt-2 block w-full" :value="old('contact_person_name', $activity->contact_person_name)" />
                        </div>
                        <div>
                            <x-input-label value="Narahubung (No. HP)" />
                            <x-text-input name="contact_person_phone" type="text" class="mt-2 block w-full" :value="old('contact_person_phone', $activity->contact_person_phone)" />
                        </div>
                        
                        <div x-data="{ 
                            poData: {{ json_encode(collect($protocolOfficers)->mapWithKeys(fn($po) => [$po->id => $po->phone])) }},
                            selectedPhone: '',
                            updatePhone(e) {
                                this.selectedPhone = this.poData[e.target.value] || '';
                            }
                        }" x-init="updatePhone({target: {value: '{{ old('protocol_officer_id', $activity->protocol_officer_id) }}'}})">
                            <x-input-label value="PIC Protokol" />
                            <x-custom-select 
                                name="protocol_officer_id" 
                                placeholder="-- Pilih --" 
                                :options="$protocolOfficers" 
                                :value="old('protocol_officer_id', $activity->protocol_officer_id)"
                                @change="updatePhone"
                            />
                            <p x-show="selectedPhone" x-text="selectedPhone ? 'Kontak: ' + selectedPhone : ''" class="mt-1 text-xs text-gray-500" style="display: none;"></p>
                        </div>
                        <div>
                            <x-input-label value="Ajudan (ADC)" />
                            <x-text-input name="adc" type="text" class="mt-2 block w-full" :value="old('adc', $activity->adc)" />
                        </div>
                        
                        <div class="col-span-full">
                            <x-input-label value="Pakaian" />
                            <x-custom-select 
                                name="dress_code" 
                                placeholder="-- Pilih Pakaian --" 
                                :options="$dressCodes" 
                                :value="$activity->dress_code"
                            />
                        </div>

                        <div class="col-span-full">
                            <x-input-label value="Informasi" />
                            <textarea name="info" rows="3" placeholder="Informasi tambahan terkait kegiatan (opsional)..." class="mt-2 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">{{ old('info', $activity->info) }}</textarea>
                        </div>
                    </div>
                    
                    <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
                        <a href="{{ request('redirect_to', route('activities.index', array_filter(['date' => $dateFilter ?? request('date'), 'leader' => $leaderFilter ?? request('leader')]))) }}" class="px-6 py-2.5 bg-gray-200 text-gray-800 rounded-md font-semibold text-xs uppercase hover:bg-gray-300 transition-colors">Batal</a>
                        <button type="submit"
                                :disabled="formTimeError"
                                :class="formTimeError ? 'opacity-50 cursor-not-allowed' : 'hover:bg-secondary-600'"
                                class="px-6 py-2.5 bg-secondary-500 text-primary-900 rounded-md font-bold text-xs uppercase transition-colors shadow-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-master-modals />
</x-app-layout>
