@props(['label' => 'Waktu', 'modelValue' => ''])

@php
    $hours = array_map(static fn (int $hour): string => str_pad((string) $hour, 2, '0', STR_PAD_LEFT), range(0, 23));
    $minutes = array_map(static fn (int $minute): string => str_pad((string) $minute, 2, '0', STR_PAD_LEFT), range(0, 55, 5));
@endphp

<div
    x-data="{
        value: @js($modelValue),
        open: false,
        tab: 'hour',
        hours: @js($hours),
        minutes: @js($minutes),
        get selectedHour() { return this.value ? this.value.slice(0, 2) : ''; },
        get selectedMinute() { return this.value ? this.value.slice(3, 5) : ''; },
        openPicker(tab) {
            this.tab = tab;
            this.open = true;
        },
        selectHour(hour) {
            this.value = hour + ':' + (this.selectedMinute || '00');
            this.tab = 'minute';
        },
        selectMinute(minute) {
            this.value = (this.selectedHour || '00') + ':' + minute;
            this.open = false;
        },
        clearValue() {
            this.value = '';
            this.open = false;
        }
    }"
    x-modelable="value"
    @keydown.escape.window="open = false"
    {{ $attributes }}
>
    <!-- Trigger: dua tombol besar agar mudah disentuh di layar kecil -->
    <div class="mt-2 flex items-stretch overflow-hidden rounded-lg border border-gray-300 bg-white shadow-sm transition-colors focus-within:border-primary-500 focus-within:ring-1 focus-within:ring-primary-500">
        <button type="button" @click="openPicker('hour')"
                class="flex min-h-[44px] flex-1 items-center justify-center px-2 py-2 text-base font-bold tabular-nums transition-colors hover:bg-primary-50 focus:bg-primary-50 focus:outline-none"
                :class="selectedHour ? 'text-gray-900' : 'text-gray-400'"
                aria-label="Pilih jam {{ $label }}">
            <span x-text="selectedHour || '--'"></span>
        </button>
        <span class="flex items-center px-0.5 text-base font-bold text-gray-400" aria-hidden="true">:</span>
        <button type="button" @click="openPicker('minute')"
                class="flex min-h-[44px] flex-1 items-center justify-center px-2 py-2 text-base font-bold tabular-nums transition-colors hover:bg-primary-50 focus:bg-primary-50 focus:outline-none"
                :class="selectedMinute ? 'text-gray-900' : 'text-gray-400'"
                aria-label="Pilih menit {{ $label }}">
            <span x-text="selectedMinute || '--'"></span>
        </button>
    </div>

    <!-- Bottom sheet di mobile, modal di layar besar -->
    <template x-teleport="body">
        <div x-show="open" x-cloak class="fixed inset-0 z-[100] flex items-end justify-center sm:items-center" style="display: none;">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"
                 @click="open = false" aria-hidden="true"></div>

            <div x-show="open"
                 x-transition:enter="transform transition ease-out duration-300 sm:duration-200"
                 x-transition:enter-start="translate-y-full sm:translate-y-4 sm:opacity-0"
                 x-transition:enter-end="translate-y-0 sm:translate-y-0 sm:opacity-100"
                 x-transition:leave="transform transition ease-in duration-200"
                 x-transition:leave-start="translate-y-0 sm:translate-y-0 sm:opacity-100"
                 x-transition:leave-end="translate-y-full sm:translate-y-4 sm:opacity-0"
                 class="relative w-full max-w-md transform overflow-hidden rounded-t-2xl bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:rounded-2xl sm:align-middle"
                 role="dialog" aria-modal="true" aria-label="Pilih waktu {{ $label }}">
                <!-- Header -->
                <div class="flex items-start justify-between border-b border-gray-100 px-4 py-3">
                    <div>
                        <h3 class="text-base font-bold text-primary-900">Pilih Waktu {{ $label }}</h3>
                        <p class="text-xs text-gray-500">Pilih jam, lalu pilih menit.</p>
                    </div>
                    <button type="button" @click="open = false"
                            class="rounded-full bg-gray-50 p-1.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 focus:outline-none">
                        <span class="sr-only">Tutup</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Tab Jam / Menit -->
                <div class="px-4 pt-3">
                    <div class="grid grid-cols-2 gap-1 rounded-xl bg-gray-100 p-1">
                        <button type="button" @click="tab = 'hour'"
                                class="rounded-lg px-3 py-2 text-sm font-semibold transition-colors"
                                :class="tab === 'hour' ? 'bg-white text-primary-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                            Jam <span x-show="selectedHour" x-text="'· ' + selectedHour" class="tabular-nums"></span>
                        </button>
                        <button type="button" @click="tab = 'minute'"
                                class="rounded-lg px-3 py-2 text-sm font-semibold transition-colors"
                                :class="tab === 'minute' ? 'bg-white text-primary-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                            Menit <span x-show="selectedMinute" x-text="'· ' + selectedMinute" class="tabular-nums"></span>
                        </button>
                    </div>
                </div>

                <!-- Pilihan angka -->
                <div class="max-h-[50vh] overflow-y-auto px-4 py-3">
                    <div x-show="tab === 'hour'" class="grid grid-cols-4 gap-2 sm:grid-cols-6">
                        <template x-for="hour in hours" :key="hour">
                            <button type="button" @click="selectHour(hour)"
                                    class="rounded-xl py-3 text-base font-semibold tabular-nums transition-colors"
                                    :class="hour === selectedHour ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-50 text-gray-700 hover:bg-primary-50 hover:text-primary-800'"
                                    x-text="hour"></button>
                        </template>
                    </div>

                    <div x-show="tab === 'minute'" class="grid grid-cols-4 gap-2 sm:grid-cols-6">
                        <template x-for="minute in minutes" :key="minute">
                            <button type="button" @click="selectMinute(minute)"
                                    class="rounded-xl py-3 text-base font-semibold tabular-nums transition-colors"
                                    :class="minute === selectedMinute ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-50 text-gray-700 hover:bg-primary-50 hover:text-primary-800'"
                                    x-text="minute"></button>
                        </template>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between gap-3 border-t border-gray-100 bg-gray-50 px-4 py-3"
                     style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom));">
                    <button type="button" @click="clearValue()"
                            class="rounded-lg px-2 py-1 text-sm font-semibold text-red-600 transition-colors hover:bg-red-50 hover:text-red-700 focus:outline-none">
                        Kosongkan
                    </button>
                    <p class="text-sm text-gray-500">
                        Dipilih <span class="font-bold tabular-nums text-primary-900" x-text="value || '--:--'"></span>
                    </p>
                </div>
            </div>
        </div>
    </template>
</div>
