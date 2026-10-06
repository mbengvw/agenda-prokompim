<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center" x-data>
            <h2 class="font-bold text-xl text-primary-800 leading-tight">
                {{ __('Kelola Permission') }}
            </h2>
            <button x-on:click.prevent="$dispatch('open-modal', 'create-permission')" class="inline-flex items-center px-4 py-2 bg-secondary-500 border border-transparent rounded-full font-semibold text-xs text-primary-900 uppercase tracking-widest hover:bg-secondary-600 focus:bg-secondary-600 active:bg-secondary-700 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                + Tambah Permission
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

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" x-data>
                @forelse($permissions as $permission)
                    <div class="bg-gradient-to-br from-primary-200 to-secondary-100 rounded-xl shadow-sm border border-primary-200 hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden">
                        <div class="p-4 flex flex-col gap-2 flex-grow">
                            <h3 class="text-sm font-bold text-primary-900 leading-tight mb-0.5">{{ $permission->name }}</h3>
                            <p class="text-xs text-gray-500">Guard: {{ $permission->guard_name }}</p>
                        </div>
                        
                        <div class="px-4 py-2.5 bg-white border-t border-primary-100 flex justify-end gap-4 items-center">
                            <button x-on:click.prevent="$dispatch('open-modal', 'edit-permission-{{ $permission->id }}')" class="text-primary-600 hover:text-primary-800 font-medium text-xs transition-colors">
                                Edit
                            </button>
                            <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus permission ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-xs transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Edit Modal -->
                    <x-modal name="edit-permission-{{ $permission->id }}" focusable>
                        <form method="post" action="{{ route('permissions.update', $permission->id) }}" class="p-6">
                            @csrf
                            @method('PUT')

                            <h2 class="text-lg font-bold text-primary-900 mb-6 border-b pb-2">
                                {{ __('Edit Permission') }}
                            </h2>

                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="name_{{ $permission->id }}" value="{{ __('Nama Permission') }}" />
                                    <x-text-input id="name_{{ $permission->id }}" name="name" type="text" class="mt-1 block w-full" :value="old('name', $permission->name)" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
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
                        <p class="mt-1 text-sm text-gray-500">Belum ada data permission.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <x-modal name="create-permission" focusable>
        <form method="post" action="{{ route('permissions.store') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-bold text-primary-900 mb-6 border-b pb-2">
                {{ __('Tambah Permission Baru') }}
            </h2>

            <div class="space-y-4">
                <div>
                    <x-input-label for="name" value="{{ __('Nama Permission') }}" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                    <p class="text-xs text-gray-500 mt-1">Contoh: 'create-users', 'edit-activities'</p>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
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
