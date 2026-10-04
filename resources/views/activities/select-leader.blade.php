<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-primary-900 leading-tight truncate text-center">
            {{ __('Pilih Pimpinan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-10">
                <h3 class="text-2xl font-extrabold text-gray-900">Kelola Agenda Kegiatan</h3>
                <p class="mt-2 text-sm text-gray-600">Silakan pilih pimpinan terlebih dahulu untuk mengelola agenda kegiatan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                @php
                    $quickLeaders = [
                        ['id' => 'Bupati', 'label' => 'Bupati', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />'],
                        ['id' => 'Wakil Bupati', 'label' => 'Wakil Bupati', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />'],
                        ['id' => 'Sekda', 'label' => 'Sekda', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />']
                    ];
                @endphp
                
                @foreach($quickLeaders as $ql)
                <a href="{{ route('activities.index', ['leader' => $ql['id'], 'date' => $dateFilter]) }}" 
                   class="flex flex-col items-center justify-center p-8 bg-white rounded-3xl shadow-lg border border-gray-100 hover:border-primary-300 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 group">
                    <div class="w-24 h-24 rounded-full flex items-center justify-center mb-6 bg-primary-50 text-primary-500 group-hover:bg-gradient-to-br group-hover:from-primary-500 group-hover:to-primary-600 group-hover:text-white transition-all duration-300 shadow-inner group-hover:shadow-primary-500/50">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $ql['icon'] !!}
                        </svg>
                    </div>
                    <span class="font-extrabold text-lg text-gray-800 tracking-wider uppercase group-hover:text-primary-700 transition-colors text-center leading-tight">
                        {{ $ql['label'] }}
                    </span>
                    
                    <div class="mt-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-primary-100 text-primary-700">
                            Kelola Agenda &rarr;
                        </span>
                    </div>
                </a>
                @endforeach
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-primary-600 transition-colors">
                    &larr; Kembali ke Dashboard
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
