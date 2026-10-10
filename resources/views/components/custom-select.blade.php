@props(['name', 'options' => [], 'value' => '', 'placeholder' => '-- Pilih --'])

@php
    $formattedOptions = [];
    $optionsArray = $options instanceof \Illuminate\Support\Collection ? $options->all() : (is_array($options) ? $options : (array) $options);
    $isAssoc = array_keys($optionsArray) !== range(0, count($optionsArray) - 1);
    foreach($optionsArray as $optKey => $optValue) {
        if (is_array($optValue) && isset($optValue['value']) && isset($optValue['label'])) {
            $formattedOptions[] = $optValue;
        } elseif (is_object($optValue) && isset($optValue->id) && isset($optValue->name)) {
            $formattedOptions[] = ['value' => $optValue->id, 'label' => $optValue->name];
        } elseif (is_array($optValue) && isset($optValue['id']) && isset($optValue['name'])) {
            $formattedOptions[] = ['value' => $optValue['id'], 'label' => $optValue['name']];
        } else {
            $formattedOptions[] = [
                'value' => $isAssoc ? $optKey : $optValue,
                'label' => $optValue
            ];
        }
    }
@endphp

<div x-data="{
        open: false,
        selected: '{{ old($name, $value) }}',
        options: {{ json_encode($formattedOptions) }},
        get selectedLabel() {
            let opt = this.options.find(o => o.value == this.selected);
            return opt ? opt.label : '{{ $placeholder }}';
        }
    }" 
    class="relative w-full"
>
    <!-- Hidden input to hold the actual value for form submission -->
    <input type="hidden" name="{{ $name }}" x-model="selected" x-ref="hiddenInput" {{ $attributes }}>
    
    <!-- Trigger Button -->
    <button type="button" @click="open = true" class="flex w-full h-full min-h-[38px] items-center justify-between border border-gray-300 bg-white px-3 py-2 text-left focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 rounded-md shadow-sm transition-colors sm:text-sm">
        <span class="block truncate" :class="{'text-gray-900 font-medium': selected, 'text-gray-500': !selected}" x-text="selectedLabel"></span>
        <span class="pointer-events-none flex items-center">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </span>
    </button>

    <!-- Overlay & Bottom Sheet -->
    <template x-teleport="body">
        <div x-show="open" class="fixed inset-0 z-[100] flex items-end justify-center sm:items-center" style="display: none;">
            <!-- Backdrop -->
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" @click="open = false" aria-hidden="true"></div>
            
            <!-- Panel -->
            <div x-show="open" x-transition:enter="transform transition ease-out duration-300 sm:duration-200" x-transition:enter-start="translate-y-full sm:translate-y-4 sm:opacity-0" x-transition:enter-end="translate-y-0 sm:translate-y-0 sm:opacity-100" x-transition:leave="transform transition ease-in duration-200" x-transition:leave-start="translate-y-0 sm:translate-y-0 sm:opacity-100" x-transition:leave-end="translate-y-full sm:translate-y-4 sm:opacity-0" class="relative w-full max-w-md transform overflow-hidden rounded-t-2xl sm:rounded-2xl bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:align-middle">
                
                <!-- Header -->
                <div class="border-b border-gray-100 bg-white px-4 py-3 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900" id="modal-title">{{ $placeholder }}</h3>
                    <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500 bg-gray-50 hover:bg-gray-100 rounded-full p-1 transition-colors focus:outline-none">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Options List -->
                <div class="max-h-[60vh] overflow-y-auto px-2 py-3 sm:px-4">
                    <!-- Clear selection option -->
                    <button type="button" @click="selected = ''; open = false; $nextTick(() => $refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true })));" class="flex w-full items-center justify-between rounded-xl px-4 py-3 mb-1 text-left transition-colors text-red-600 hover:bg-red-50 font-medium">
                        <span class="block truncate">Kosongkan Pilihan</span>
                        <span x-show="selected === ''" class="text-red-600">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                        </span>
                    </button>
                    
                    <!-- Dynamic Options -->
                    <template x-for="option in options" :key="option.value">
                        <button type="button" @click="selected = option.value; open = false; $nextTick(() => $refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true })));" class="flex w-full items-center justify-between rounded-xl px-4 py-3 mb-1 text-left transition-colors" :class="selected == option.value ? 'bg-primary-50 text-primary-900 font-bold' : 'text-gray-700 hover:bg-gray-50 font-medium'">
                            <span class="block truncate" x-text="option.label"></span>
                            <span x-show="selected == option.value" class="text-primary-600">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            </span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>
