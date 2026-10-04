<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center" x-data>
            <h2 class="font-bold text-xl text-primary-800 leading-tight">
                {{ __('Kelola Role') }}
            </h2>
            <button x-on:click.prevent="$dispatch('open-modal', 'create-role')" class="inline-flex items-center px-4 py-2 bg-secondary-500 border border-transparent rounded-full font-semibold text-xs text-primary-900 uppercase tracking-widest hover:bg-secondary-600 focus:bg-secondary-600 active:bg-secondary-700 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                + Tambah Role
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
                @forelse($roles as $role)
                    <div class="bg-gradient-to-br from-primary-200 to-secondary-100 rounded-xl shadow-sm border border-primary-200 hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden">
                        <div class="p-4 flex flex-col gap-2 flex-grow">
                            <h3 class="text-sm font-bold text-primary-900 leading-tight mb-0.5">{{ $role->name }}</h3>
                            <div class="flex flex-wrap gap-1 mt-2">
                                @forelse($role->permissions as $permission)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-200 text-gray-700 whitespace-nowrap">
                                        {{ $permission->name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-500 italic">Tidak ada permission</span>
                                @endforelse
                            </div>
                        </div>
                        
                        <div class="px-4 py-2.5 bg-white border-t border-primary-100 flex justify-end gap-4 items-center">
                            <button x-on:click.prevent="$dispatch('open-modal', 'edit-role-{{ $role->id }}')" class="text-primary-600 hover:text-primary-800 font-medium text-xs transition-colors">
                                Edit
                            </button>
                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus role ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-xs transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Edit Modal -->
                    <x-modal name="edit-role-{{ $role->id }}" focusable>
                        <form method="post" action="{{ route('roles.update', $role->id) }}" class="p-6">
                            @csrf
                            @method('PUT')

                            <h2 class="text-lg font-bold text-primary-900 mb-6 border-b pb-2">
                                {{ __('Edit Role') }}
                            </h2>

                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="name_{{ $role->id }}" value="{{ __('Nama Role') }}" />
                                    <x-text-input id="name_{{ $role->id }}" name="name" type="text" class="mt-1 block w-full" :value="old('name', $role->name)" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label value="{{ __('Permissions (Hak Akses)') }}" class="mb-2" />
                                    <div class="grid grid-cols-2 gap-2">
                                    @foreach(\Spatie\Permission\Models\Permission::all() as $permission)
                                        <div class="flex items-center mt-1">
                                            <input id="perm_{{ $role->id }}_{{ $permission->id }}" name="permissions[]" type="checkbox" value="{{ $permission->name }}" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500" {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                            <label for="perm_{{ $role->id }}_{{ $permission->id }}" class="ml-2 text-sm font-medium text-gray-900">{{ $permission->name }}</label>
                                        </div>
                                    @endforeach
                                    </div>
                                    <x-input-error :messages="$errors->get('permissions')" class="mt-2" />
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
                        <p class="mt-1 text-sm text-gray-500">Belum ada data role.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <x-modal name="create-role" focusable>
        <form method="post" action="{{ route('roles.store') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-bold text-primary-900 mb-6 border-b pb-2">
                {{ __('Tambah Role Baru') }}
            </h2>

            <div class="space-y-4">
                <div>
                    <x-input-label for="name" value="{{ __('Nama Role') }}" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label value="{{ __('Permissions (Hak Akses)') }}" class="mb-2" />
                    <div class="grid grid-cols-2 gap-2">
                    @foreach(\Spatie\Permission\Models\Permission::all() as $permission)
                        <div class="flex items-center mt-1">
                            <input id="perm_new_{{ $permission->id }}" name="permissions[]" type="checkbox" value="{{ $permission->name }}" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                            <label for="perm_new_{{ $permission->id }}" class="ml-2 text-sm font-medium text-gray-900">{{ $permission->name }}</label>
                        </div>
                    @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('permissions')" class="mt-2" />
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
