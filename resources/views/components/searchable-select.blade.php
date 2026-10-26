@props([
    'name',
    'options' => [],
    'value' => '',
    'placeholder' => '-- Pilih --',
    'searchPlaceholder' => 'Cari...',
    'emptyText' => 'Pilihan tidak ditemukan.',
])

@php
    $normalizedOptions = collect($options)
        ->map(function ($option) {
            if (is_array($option)) {
                return $option['name'] ?? $option['label'] ?? $option['value'] ?? null;
            }

            if (is_object($option)) {
                return $option->name ?? null;
            }

            return $option;
        })
        ->filter(fn ($option) => filled($option))
        ->map(fn ($option) => (string) $option)
        ->unique()
        ->values()
        ->all();
@endphp

<div
    x-data="{
        selected: @js((string) old($name, $value)),
        options: @js($normalizedOptions),
        open: false,
        search: '',
        init() {
            if (this.selected && ! this.options.includes(this.selected)) {
                this.options.unshift(this.selected);
            }
        },
        get filteredOptions() {
            const keyword = this.search.trim().toLowerCase();
            if (keyword === '') return this.options;
            return this.options.filter(option => option.toLowerCase().includes(keyword));
        },
        openList() {
            this.open = true;
            this.search = '';
            this.$nextTick(() => this.$refs.searchInput.focus());
        },
        closeList() {
            this.open = false;
            this.search = '';
        },
        choose(option) {
            this.selected = option;
            this.closeList();
        },
        clear() {
            this.selected = '';
            this.closeList();
        }
    }"
    @master-option-created.window="
        if ($event.detail.field === '{{ $name }}') {
            if (! options.includes($event.detail.value)) options.unshift($event.detail.value);
            choose($event.detail.value);
        }
    "
    @click.away="closeList()"
    {{ $attributes->merge(['class' => 'relative mt-2']) }}
>
    <input type="hidden" name="{{ $name }}" x-model="selected">

    <div class="relative">
        <input
            x-ref="searchInput"
            x-model="search"
            type="text"
            autocomplete="off"
            spellcheck="false"
            @focus="openList()"
            @input="open = true"
            @keydown.escape="closeList(); $event.target.blur()"
            @keydown.enter="if (open && filteredOptions.length) { $event.preventDefault(); choose(filteredOptions[0]); }"
            :placeholder="selected && ! open ? '' : (open ? @js($searchPlaceholder) : @js($placeholder))"
            class="block min-h-[42px] w-full rounded-md border-gray-300 pr-16 text-sm shadow-sm transition-colors focus:border-primary-500 focus:ring-primary-500"
        >

        <!-- Label pilihan saat panel tertutup -->
        <div x-show="! open && selected" x-cloak
             class="pointer-events-none absolute inset-y-0 left-3 right-16 flex items-center truncate text-sm font-medium text-gray-900"
             x-text="selected"></div>

        <div class="absolute inset-y-0 right-0 flex items-center gap-0.5 pr-2">
            <button type="button" x-show="selected" x-cloak @click.stop="clear()"
                    class="rounded-full p-1 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 focus:outline-none"
                    title="Kosongkan pilihan">
                <span class="sr-only">Kosongkan pilihan</span>
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <button type="button" @click.stop="open ? closeList() : openList()"
                    class="rounded-full p-1 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 focus:outline-none"
                    title="Buka pilihan">
                <span class="sr-only">Buka pilihan</span>
                <svg class="h-4 w-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
        </div>
    </div>

    <div x-show="open" x-cloak x-transition.opacity.duration.200ms
         class="absolute z-50 mt-1 w-full overflow-hidden rounded-md border border-gray-200 bg-white shadow-xl">
        <div class="max-h-60 overflow-y-auto py-1">
            <button type="button" x-show="search === ''" @click="clear()"
                    class="flex w-full items-center justify-between px-3 py-2 text-left text-sm font-medium text-red-600 transition-colors hover:bg-red-50">
                <span>Kosongkan Pilihan</span>
            </button>

            <template x-for="option in filteredOptions" :key="option">
                <button type="button" @click="choose(option)"
                        class="flex w-full items-center justify-between gap-2 border-b border-gray-50 px-3 py-2 text-left text-sm transition-colors last:border-0"
                        :class="selected === option ? 'bg-primary-50 font-bold text-primary-900' : 'text-gray-700 hover:bg-gray-50'">
                    <span class="truncate" x-text="option"></span>
                    <svg x-show="selected === option" class="h-4 w-4 shrink-0 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </button>
            </template>

            <p x-show="filteredOptions.length === 0" class="px-3 py-3 text-sm italic text-gray-500">{{ $emptyText }}</p>
        </div>
    </div>
</div>
