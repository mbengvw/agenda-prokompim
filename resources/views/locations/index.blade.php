<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center" x-data>
            <h2 class="font-bold text-xl text-primary-800 leading-tight">
                {{ __('Kelola Lokasi') }}
            </h2>
            @role('admin')
            <button x-on:click.prevent="$dispatch('open-modal', 'create-location')" class="inline-flex items-center px-4 py-2 bg-secondary-500 border border-transparent rounded-full font-semibold text-xs text-primary-900 uppercase tracking-widest hover:bg-secondary-600 focus:bg-secondary-600 active:bg-secondary-700 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                + Tambah Lokasi
            </button>
            @endrole
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-primary-100 border-l-4 border-primary-500 text-primary-800 p-4 rounded shadow-sm" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-800 p-4 rounded shadow-sm" role="alert">
                    <p>Mohon periksa kembali form anda. Ada kesalahan input.</p>
                </div>
            @endif

            <!-- Search Form -->
            <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-primary-100 flex flex-col sm:flex-row gap-4 justify-between items-center">
                <h3 class="font-bold text-primary-900 hidden sm:block">Data Lokasi</h3>
                <form action="{{ route('locations.index') }}" method="GET" class="w-full sm:w-auto relative flex">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-secondary-500 focus:border-secondary-500 sm:text-sm transition duration-150 ease-in-out" placeholder="Cari nama / kota / alamat...">
                    </div>
                    <button type="submit" class="ml-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-primary-900 bg-secondary-400 hover:bg-secondary-500 focus:outline-none">
                        Cari
                    </button>
                    @if(isset($search) && $search !== '')
                        <a href="{{ route('locations.index') }}" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- 1. Mobile View (Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:hidden" x-data>
                @forelse($locations as $location)
                    <div class="bg-gradient-to-br from-primary-200 to-secondary-100 rounded-xl shadow-sm border border-primary-200 hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden">
                        <div class="p-4 flex justify-between items-start gap-3 flex-grow">
                            <div>
                                <h3 class="text-sm font-bold text-primary-900 leading-tight mb-0.5 line-clamp-2">{{ $location->name }}</h3>
                                <p class="text-xs font-medium text-primary-800 line-clamp-1">
                                    <svg class="inline-block w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $location->city ?: 'Kota tidak diatur' }}
                                </p>
                                @if($location->address)
                                    <p class="text-xs text-primary-700 mt-2 line-clamp-2 italic">{{ $location->address }}</p>
                                @endif
                            </div>
                        </div>
                        
                        @role('admin')
                        <div class="px-4 py-2.5 bg-white border-t border-primary-100 flex justify-end gap-4 items-center">
                            <button x-on:click.prevent="$dispatch('open-modal', 'edit-location-{{ $location->id }}')" class="text-primary-600 hover:text-primary-800 font-medium text-xs transition-colors">
                                Edit
                            </button>
                            <form action="{{ route('locations.destroy', $location->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-xs transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                        @endrole
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-xl shadow p-8 text-center border border-primary-100">
                        <div class="text-primary-300 mb-4">
                            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada data lokasi</h3>
                        <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan data lokasi baru.</p>
                        @role('admin')
                        <div class="mt-6">
                            <button x-on:click.prevent="$dispatch('open-modal', 'create-location')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-full text-primary-900 bg-secondary-500 hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                                + Tambah Lokasi
                            </button>
                        </div>
                        @endrole
                    </div>
                @endforelse
            </div>

            <!-- 2. Desktop View (Table) -->
            <div class="hidden md:block bg-white rounded-xl shadow-sm border border-primary-100 overflow-hidden" x-data>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-primary-200">
                        <thead class="bg-primary-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-primary-800 uppercase tracking-wider">Nama Lokasi</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-primary-800 uppercase tracking-wider">Kota</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-primary-800 uppercase tracking-wider">Alamat Lengkap</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-primary-800 uppercase tracking-wider">Catatan</th>
                                @role('admin')
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-primary-800 uppercase tracking-wider">Aksi</th>
                                @endrole
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-primary-100">
                            @forelse($locations as $location)
                                <tr class="hover:bg-primary-50/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-primary-900">{{ $location->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-primary-800">
                                            <svg class="inline-block w-4 h-4 mr-1 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            {{ $location->city ?: '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-primary-700 line-clamp-2 max-w-xs">{{ $location->address ?: '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-primary-700 line-clamp-2 max-w-xs">{{ $location->notes ?: '-' }}</div>
                                    </td>
                                    @role('admin')
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button x-on:click.prevent="$dispatch('open-modal', 'edit-location-{{ $location->id }}')" class="text-primary-600 hover:text-primary-900 bg-primary-100 hover:bg-primary-200 px-3 py-1.5 rounded-md transition-colors mr-2">
                                            Edit
                                        </button>
                                        <form action="{{ route('locations.destroy', $location->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1.5 rounded-md transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                    @endrole
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center">
                                        <div class="text-primary-300 mb-4">
                                            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada data lokasi</h3>
                                        <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan data lokasi baru.</p>
                                        @role('admin')
                                        <div class="mt-6">
                                            <button x-on:click.prevent="$dispatch('open-modal', 'create-location')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-full text-primary-900 bg-secondary-500 hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                                                + Tambah Lokasi
                                            </button>
                                        </div>
                                        @endrole
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Edit Modals -->
            @foreach($locations as $location)
                <x-modal name="edit-location-{{ $location->id }}" focusable>
                    <form method="post" action="{{ route('locations.update', $location->id) }}" class="p-6">
                        @csrf
                        @method('PUT')

                        <h2 class="text-lg font-bold text-primary-900 mb-6 border-b pb-2">
                            {{ __('Edit Data Lokasi') }}
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <x-input-label for="name_{{ $location->id }}" value="{{ __('Nama Lokasi') }}" />
                                <x-text-input id="name_{{ $location->id }}" name="name" type="text" class="mt-1 block w-full" :value="old('name', $location->name)" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="address_{{ $location->id }}" value="{{ __('Alamat Lengkap') }}" />
                                <textarea id="address_{{ $location->id }}" name="address" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" rows="2">{{ old('address', $location->address) }}</textarea>
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="city_{{ $location->id }}" value="{{ __('Kota') }}" />
                                    <x-text-input id="city_{{ $location->id }}" name="city" type="text" class="mt-1 block w-full" :value="old('city', $location->city)" />
                                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="notes_{{ $location->id }}" value="{{ __('Catatan Singkat') }}" />
                                    <x-text-input id="notes_{{ $location->id }}" name="notes" type="text" class="mt-1 block w-full" :value="old('notes', $location->notes)" />
                                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="latitude_{{ $location->id }}" value="{{ __('Latitude (Opsional)') }}" />
                                    <x-text-input id="latitude_{{ $location->id }}" name="latitude" type="text" class="mt-1 block w-full" :value="old('latitude', $location->latitude)" />
                                    <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="longitude_{{ $location->id }}" value="{{ __('Longitude (Opsional)') }}" />
                                    <x-text-input id="longitude_{{ $location->id }}" name="longitude" type="text" class="mt-1 block w-full" :value="old('longitude', $location->longitude)" />
                                    <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-300 transition">
                                {{ __('Batal') }}
                            </button>
                            <button type="submit" class="px-4 py-2 bg-secondary-500 text-primary-900 rounded-md font-bold text-xs uppercase tracking-widest hover:bg-secondary-600 transition">
                                {{ __('Simpan Perubahan') }}
                            </button>
                        </div>
                    </form>
                </x-modal>
            @endforeach

            <div class="mt-6">
                {{ $locations->links() }}
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <x-modal name="create-location" focusable>
        <form method="post" action="{{ route('locations.store') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-bold text-primary-900 mb-6 border-b pb-2">
                {{ __('Tambah Data Lokasi Baru') }}
            </h2>

            <div class="space-y-4">
                <div>
                    <x-input-label for="name" value="{{ __('Nama Lokasi') }}" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="address" value="{{ __('Alamat Lengkap') }}" />
                    <textarea id="address" name="address" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" rows="2">{{ old('address') }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="city" value="{{ __('Kota') }}" />
                        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city')" />
                        <x-input-error :messages="$errors->get('city')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="notes" value="{{ __('Catatan Singkat') }}" />
                        <x-text-input id="notes" name="notes" type="text" class="mt-1 block w-full" :value="old('notes')" />
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="latitude" value="{{ __('Latitude (Opsional)') }}" />
                        <x-text-input id="latitude" name="latitude" type="text" class="mt-1 block w-full" :value="old('latitude')" />
                        <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="longitude" value="{{ __('Longitude (Opsional)') }}" />
                        <x-text-input id="longitude" name="longitude" type="text" class="mt-1 block w-full" :value="old('longitude')" />
                        <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-300 transition">
                    {{ __('Batal') }}
                </button>
                <button type="submit" class="px-4 py-2 bg-secondary-500 text-primary-900 rounded-md font-bold text-xs uppercase tracking-widest hover:bg-secondary-600 transition">
                    {{ __('Simpan Data') }}
                </button>
            </div>
        </form>
    </x-modal>

</x-app-layout>
