<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="ui-section-label">Administración</p>
            <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Editar usuario</h2>
            <p class="mt-1 text-sm font-medium text-slate-500">{{ $user->name }}</p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <div class="ui-form-card">
                <form method="post" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('patch')
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="name" value="Nombre" />
                            <x-text-input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="email" value="Correo electrónico" />
                            <x-text-input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="phone" value="Teléfono (opcional)" />
                            <x-text-input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="role_id" value="Rol" />
                            @php $currentRoleId = old('role_id', $user->roles->first()?->id); @endphp
                            <select id="role_id" name="role_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Seleccionar</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ (string) $currentRoleId === (string) $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role_id')" class="mt-1" />
                        </div>
                        <p class="text-sm text-gray-600">Deje en blanco si no desea cambiar la contraseña.</p>
                        <div>
                            <x-input-label for="password" value="Nueva contraseña" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" value="Confirmar nueva contraseña" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                        </div>
                    </div>
                    <div class="mt-6 flex gap-2">
                        <x-primary-button>Actualizar</x-primary-button>
                        <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
