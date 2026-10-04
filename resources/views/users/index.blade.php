<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center" x-data>
            <h2 class="font-bold text-xl text-primary-800 leading-tight">
                {{ __('Kelola Pengguna') }}
            </h2>
            <button x-on:click.prevent="$dispatch('open-modal', 'create-user')" class="inline-flex items-center px-4 py-2 bg-secondary-500 border border-transparent rounded-full font-semibold text-xs text-primary-900 uppercase tracking-widest hover:bg-secondary-600 focus:bg-secondary-600 active:bg-secondary-700 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                + Tambah Pengguna
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

            <!-- 1. Mobile View (Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:hidden" x-data>
                @forelse($users as $user)
                    <div class="bg-gradient-to-br from-primary-200 to-secondary-100 rounded-xl shadow-sm border border-primary-200 hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden">
                        <div class="p-4 flex justify-between items-start gap-3 flex-grow">
                            <div>
                                <h3 class="text-sm font-bold text-primary-900 leading-tight mb-0.5">{{ $user->name }}</h3>
                                <p class="text-xs font-medium text-primary-800">{{ $user->email }}</p>
                            </div>
                            <div class="flex flex-col gap-1">
                                @foreach($user->roles as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-secondary-400 text-primary-900 whitespace-nowrap">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="px-4 py-2.5 bg-white border-t border-primary-100 flex justify-end gap-4 items-center">
                            <button x-on:click.prevent="$dispatch('open-modal', 'edit-user-{{ $user->id }}')" class="text-primary-600 hover:text-primary-800 font-medium text-xs transition-colors">
                                Edit
                            </button>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-xs transition-colors">
                                    Hapus
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-xl shadow p-8 text-center border border-primary-100">
                        <p class="mt-1 text-sm text-gray-500">Belum ada data pengguna.</p>
                    </div>
                @endforelse
            </div>

            <!-- 2. Desktop View (Table) -->
            <div class="hidden md:block bg-white rounded-xl shadow-sm border border-primary-100 overflow-hidden" x-data>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-primary-200">
                        <thead class="bg-primary-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-primary-800 uppercase tracking-wider">Nama</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-primary-800 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-primary-800 uppercase tracking-wider">Role</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-primary-800 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-primary-100">
                            @forelse($users as $user)
                                <tr class="hover:bg-primary-50/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-primary-900">{{ $user->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-primary-800">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($user->roles as $role)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-secondary-400 text-primary-900 whitespace-nowrap">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button x-on:click.prevent="$dispatch('open-modal', 'edit-user-{{ $user->id }}')" class="text-primary-600 hover:text-primary-900 bg-primary-100 hover:bg-primary-200 px-3 py-1.5 rounded-md transition-colors mr-2">
                                            Edit
                                        </button>
                                        @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1.5 rounded-md transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center">
                                        <div class="text-primary-300 mb-4">
                                            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada data pengguna</h3>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Edit Modals -->
            @foreach($users as $user)
                <x-modal name="edit-user-{{ $user->id }}" focusable>
                    <form method="post" action="{{ route('users.update', $user->id) }}" class="p-6">
                        @csrf
                        @method('PUT')

                        <h2 class="text-lg font-bold text-primary-900 mb-6 border-b pb-2">
                            {{ __('Edit Data Pengguna') }}
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <x-input-label for="name_{{ $user->id }}" value="{{ __('Nama Lengkap') }}" />
                                <x-text-input id="name_{{ $user->id }}" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="email_{{ $user->id }}" value="{{ __('Email') }}" />
                                <x-text-input id="email_{{ $user->id }}" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="password_{{ $user->id }}" value="{{ __('Password Baru (Opsional)') }}" />
                                <x-text-input id="password_{{ $user->id }}" name="password" type="password" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="password_confirmation_{{ $user->id }}" value="{{ __('Konfirmasi Password') }}" />
                                <x-text-input id="password_confirmation_{{ $user->id }}" name="password_confirmation" type="password" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label value="{{ __('Role') }}" class="mb-2" />
                                @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                    <div class="flex items-center mt-1">
                                        <input id="role_{{ $user->id }}_{{ $role->id }}" name="roles[]" type="checkbox" value="{{ $role->name }}" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500" {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                        <label for="role_{{ $user->id }}_{{ $role->id }}" class="ml-2 text-sm font-medium text-gray-900">{{ $role->name }}</label>
                                    </div>
                                @endforeach
                                <x-input-error :messages="$errors->get('roles')" class="mt-2" />
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
        </div>
    </div>

    <!-- Create Modal -->
    <x-modal name="create-user" focusable>
        <form method="post" action="{{ route('users.store') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-bold text-primary-900 mb-6 border-b pb-2">
                {{ __('Tambah Pengguna Baru') }}
            </h2>

            <div class="space-y-4">
                <div>
                    <x-input-label for="name" value="{{ __('Nama Lengkap') }}" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="email" value="{{ __('Email') }}" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="password" value="{{ __('Password') }}" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="password_confirmation" value="{{ __('Konfirmasi Password') }}" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                </div>
                <div>
                    <x-input-label value="{{ __('Role') }}" class="mb-2" />
                    @foreach(\Spatie\Permission\Models\Role::all() as $role)
                        <div class="flex items-center mt-1">
                            <input id="role_new_{{ $role->id }}" name="roles[]" type="checkbox" value="{{ $role->name }}" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                            <label for="role_new_{{ $role->id }}" class="ml-2 text-sm font-medium text-gray-900">{{ $role->name }}</label>
                        </div>
                    @endforeach
                    <x-input-error :messages="$errors->get('roles')" class="mt-2" />
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
