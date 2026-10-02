<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center" x-data>
            <h2 class="font-bold text-xl text-primary-800 leading-tight">
                {{ __('Kelola Pimpinan') }}
            </h2>
            <button x-on:click.prevent="$dispatch('open-modal', 'create-leader')" class="inline-flex items-center px-4 py-2 bg-secondary-500 border border-transparent rounded-full font-semibold text-xs text-primary-900 uppercase tracking-widest hover:bg-secondary-600 focus:bg-secondary-600 active:bg-secondary-700 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                + Tambah Pimpinan
            </button>
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
                <h3 class="font-bold text-primary-900 hidden sm:block">Data Pimpinan</h3>
                <form action="{{ route('leaders.index') }}" method="GET" class="w-full sm:w-auto relative flex">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-secondary-500 focus:border-secondary-500 sm:text-sm transition duration-150 ease-in-out" placeholder="Cari nama / jabatan...">
                    </div>
                    <button type="submit" class="ml-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-primary-900 bg-secondary-400 hover:bg-secondary-500 focus:outline-none">
                        Cari
                    </button>
                    @if(isset($search) && $search !== '')
                        <a href="{{ route('leaders.index') }}" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Mobile View (Cards) & Desktop View (Grid/Table) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4" x-data>
                @forelse($leaders as $leader)
                    <div class="bg-gradient-to-br from-primary-200 to-secondary-100 rounded-xl shadow-sm border border-primary-200 hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden">
                        <div class="p-4 flex justify-between items-start gap-3 flex-grow">
                            <div>
                                <h3 class="text-sm font-bold text-primary-900 leading-tight mb-0.5 line-clamp-2">{{ $leader->name }}</h3>
                                <p class="text-xs font-medium text-primary-800 line-clamp-1">{{ $leader->position }}</p>
                            </div>
                            @if($leader->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-secondary-400 text-primary-900 whitespace-nowrap">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-200 text-gray-500 whitespace-nowrap">
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                        
                        <div class="px-4 py-2.5 bg-white border-t border-primary-100 flex justify-end gap-4 items-center">
                            <button x-on:click.prevent="$dispatch('open-modal', 'edit-leader-{{ $leader->id }}')" class="text-primary-600 hover:text-primary-800 font-medium text-xs transition-colors">
                                Edit
                            </button>
                            <form action="{{ route('leaders.destroy', $leader->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-xs transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Edit Modal for this Leader -->
                    <x-modal name="edit-leader-{{ $leader->id }}" focusable>
                        <form method="post" action="{{ route('leaders.update', $leader->id) }}" class="p-6">
                            @csrf
                            @method('PUT')

                            <h2 class="text-lg font-bold text-primary-900 mb-6 border-b pb-2">
                                {{ __('Edit Data Pimpinan') }}
                            </h2>

                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="name_{{ $leader->id }}" value="{{ __('Nama Lengkap') }}" />
                                    <x-text-input id="name_{{ $leader->id }}" name="name" type="text" class="mt-1 block w-full" :value="old('name', $leader->name)" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="position_{{ $leader->id }}" value="{{ __('Jabatan') }}" />
                                    <x-text-input id="position_{{ $leader->id }}" name="position" type="text" class="mt-1 block w-full" :value="old('position', $leader->position)" required />
                                    <x-input-error :messages="$errors->get('position')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="short_name_{{ $leader->id }}" value="{{ __('Singkatan Jabatan (Opsional)') }}" />
                                    <x-text-input id="short_name_{{ $leader->id }}" name="short_name" type="text" class="mt-1 block w-full" :value="old('short_name', $leader->short_name)" />
                                    <x-input-error :messages="$errors->get('short_name')" class="mt-2" />
                                </div>
                                <div class="flex items-center pt-2">
                                    <input type="hidden" name="is_active" value="0">
                                    <input id="is_active_{{ $leader->id }}" name="is_active" type="checkbox" value="1" class="w-5 h-5 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 focus:ring-2 cursor-pointer" {{ old('is_active', $leader->is_active) ? 'checked' : '' }}>
                                    <label for="is_active_{{ $leader->id }}" class="ml-3 text-sm font-medium text-primary-900 cursor-pointer">Status Aktif</label>
                                    <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
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

                @empty
                    <div class="col-span-full bg-white rounded-xl shadow p-8 text-center border border-primary-100">
                        <div class="text-primary-300 mb-4">
                            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada data pimpinan</h3>
                        <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan data pimpinan baru.</p>
                        <div class="mt-6">
                            <button x-on:click.prevent="$dispatch('open-modal', 'create-leader')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-full text-primary-900 bg-secondary-500 hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                                + Tambah Pimpinan
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $leaders->links() }}
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <x-modal name="create-leader" focusable>
        <form method="post" action="{{ route('leaders.store') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-bold text-primary-900 mb-6 border-b pb-2">
                {{ __('Tambah Data Pimpinan Baru') }}
            </h2>

            <div class="space-y-4">
                <div>
                    <x-input-label for="name" value="{{ __('Nama Lengkap') }}" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="position" value="{{ __('Jabatan') }}" />
                    <x-text-input id="position" name="position" type="text" class="mt-1 block w-full" :value="old('position')" required />
                    <x-input-error :messages="$errors->get('position')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="short_name" value="{{ __('Singkatan Jabatan (Opsional)') }}" />
                    <x-text-input id="short_name" name="short_name" type="text" class="mt-1 block w-full" :value="old('short_name')" />
                    <x-input-error :messages="$errors->get('short_name')" class="mt-2" />
                </div>
                <div class="flex items-center pt-2">
                    <input type="hidden" name="is_active" value="0">
                    <input id="is_active" name="is_active" type="checkbox" value="1" class="w-5 h-5 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 focus:ring-2 cursor-pointer" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="ml-3 text-sm font-medium text-primary-900 cursor-pointer">Status Aktif</label>
                    <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
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
