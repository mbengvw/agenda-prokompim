<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center" x-data>
            <h2 class="font-bold text-xl text-primary-800 leading-tight">
                {{ __('Kelola Master Pakaian') }}
            </h2>
            @role('admin')
            <button x-on:click.prevent="$dispatch('open-modal', 'create-dress-code')" class="inline-flex items-center px-4 py-2 bg-secondary-500 border border-transparent rounded-full font-semibold text-xs text-primary-900 uppercase tracking-widest hover:bg-secondary-600 focus:bg-secondary-600 active:bg-secondary-700 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                + Tambah Pakaian
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
                <h3 class="font-bold text-primary-900 hidden sm:block">Data Pakaian / Dress Code</h3>
                <form action="{{ route('dress-codes.index') }}" method="GET" class="w-full sm:w-auto relative flex">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-secondary-500 focus:border-secondary-500 sm:text-sm transition duration-150 ease-in-out" placeholder="Cari nama pakaian / deskripsi...">
                    </div>
                    <button type="submit" class="ml-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-primary-900 bg-secondary-400 hover:bg-secondary-500 focus:outline-none">
                        Cari
                    </button>
                    @if(isset($search) && $search !== '')
                        <a href="{{ route('dress-codes.index') }}" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- 1. Mobile View (Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:hidden" x-data>
                @forelse($dressCodes as $dressCode)
                    <div class="bg-gradient-to-br from-primary-200 to-secondary-100 rounded-xl shadow-sm border border-primary-200 hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden">
                        <div class="p-4 flex justify-between items-start gap-3 flex-grow">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-sm font-bold text-primary-900 leading-tight line-clamp-1">{{ $dressCode->name }}</h3>
                                    @if($dressCode->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-800">
                                            Nonaktif
                                        </span>
                                    @endif
                                </div>
                                @if($dressCode->description)
                                    <p class="text-xs text-primary-700 mt-1 line-clamp-2 italic">{{ $dressCode->description }}</p>
                                @endif
                            </div>
                        </div>
                        
                        @role('admin')
                        <div class="px-4 py-2.5 bg-white border-t border-primary-100 flex justify-end gap-4 items-center">
                            <button x-on:click.prevent="$dispatch('open-modal', 'edit-dress-code-{{ $dressCode->id }}')" class="text-primary-600 hover:text-primary-800 font-medium text-xs transition-colors">
                                Edit
                            </button>
                            <form action="{{ route('dress-codes.destroy', $dressCode->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data pakaian ini?');">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h10" />
                            </svg>
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada data pakaian</h3>
                        <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan jenis pakaian baru.</p>
                        @role('admin')
                        <div class="mt-6">
                            <button x-on:click.prevent="$dispatch('open-modal', 'create-dress-code')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-full text-primary-900 bg-secondary-500 hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                                + Tambah Pakaian
                            </button>
                        </div>
                        @endrole
                    </div>
                @endforelse
            </div>

            <!-- 2. Desktop View (Table) -->
            <div class="hidden md:block bg-white overflow-hidden shadow-sm rounded-xl border border-primary-100" x-data>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-primary-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-primary-900 uppercase tracking-wider">Nama Pakaian</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-primary-900 uppercase tracking-wider">Deskripsi</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-primary-900 uppercase tracking-wider">Status</th>
                                @role('admin')
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-primary-900 uppercase tracking-wider">Aksi</th>
                                @endrole
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($dressCodes as $dressCode)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">{{ $dressCode->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500 max-w-md">{{ $dressCode->description ?: '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($dressCode->is_active)
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    @role('admin')
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button x-on:click.prevent="$dispatch('open-modal', 'edit-dress-code-{{ $dressCode->id }}')" class="text-primary-600 hover:text-primary-900 mr-3">Edit</button>
                                        <form action="{{ route('dress-codes.destroy', $dressCode->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data pakaian ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    </td>
                                    @endrole
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        Tidak ada data pakaian ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $dressCodes->links() }}
            </div>
        </div>
    </div>

    @role('admin')
    <!-- Modal Create Dress Code -->
    <x-modal name="create-dress-code" focusable>
        <form method="POST" action="{{ route('dress-codes.store') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-bold text-primary-900">
                {{ __('Tambah Master Pakaian') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Masukkan nama jenis pakaian atau dress code yang digunakan oleh pimpinan.') }}
            </p>

            <div class="mt-6 space-y-4">
                <div>
                    <x-input-label for="name" value="Nama Pakaian *" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" placeholder="misal: PSL / Batik / PDH Khaki" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" value="Deskripsi / Catatan" />
                    <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-secondary-500 focus:ring-secondary-500" rows="3" placeholder="Keterangan pakaian..."></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="flex items-center">
                    <input id="is_active" name="is_active" type="checkbox" value="1" checked class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900 font-medium">Status Aktif</label>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-primary-button class="bg-secondary-500 hover:bg-secondary-600 text-primary-900 font-bold">
                    {{ __('Simpan') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Edit Dress Code -->
    @foreach($dressCodes as $dressCode)
        <x-modal name="edit-dress-code-{{ $dressCode->id }}" focusable>
            <form method="POST" action="{{ route('dress-codes.update', $dressCode->id) }}" class="p-6">
                @csrf
                @method('PUT')

                <h2 class="text-lg font-bold text-primary-900">
                    {{ __('Edit Data Pakaian') }}
                </h2>

                <div class="mt-6 space-y-4">
                    <div>
                        <x-input-label for="edit_name_{{ $dressCode->id }}" value="Nama Pakaian *" />
                        <x-text-input id="edit_name_{{ $dressCode->id }}" name="name" type="text" class="mt-1 block w-full" :value="old('name', $dressCode->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="edit_description_{{ $dressCode->id }}" value="Deskripsi / Catatan" />
                        <textarea id="edit_description_{{ $dressCode->id }}" name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-secondary-500 focus:ring-secondary-500" rows="3">{{ old('description', $dressCode->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="flex items-center">
                        <input id="edit_is_active_{{ $dressCode->id }}" name="is_active" type="checkbox" value="1" {{ $dressCode->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                        <label for="edit_is_active_{{ $dressCode->id }}" class="ml-2 block text-sm text-gray-900 font-medium">Status Aktif</label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Batal') }}
                    </x-secondary-button>

                    <x-primary-button class="bg-secondary-500 hover:bg-secondary-600 text-primary-900 font-bold">
                        {{ __('Perbarui') }}
                    </x-primary-button>
                </div>
            </form>
        </x-modal>
    @endforeach
    @endrole
</x-app-layout>
