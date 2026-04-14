<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="ui-section-label">Seguridad</p>
            <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Editar rol</h2>
            <p class="mt-1 text-sm font-medium text-slate-500">{{ $role->name }}</p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <div class="ui-form-card">
                <form method="post" action="{{ route('roles.update', $role) }}">
                    @csrf
                    @method('patch')
                    <div class="mb-6">
                        <x-input-label for="name" value="Nombre del rol" />
                        <x-text-input id="name" name="name" value="{{ old('name', $role->name) }}" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    <div class="mb-6">
                        <x-input-label value="Permisos" />
                        <div class="mt-2 space-y-2 max-h-64 overflow-y-auto border border-gray-200 rounded p-3">
                            @php $rolePermIds = $role->permissions->pluck('id')->toArray(); @endphp
                            @foreach ($permissions as $perm)
                            <label class="flex items-center">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" {{ in_array($perm->id, old('permissions', $rolePermIds)) ? 'checked' : '' }} class="rounded border-gray-300">
                                <span class="ml-2 text-sm">{{ $perm->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <x-primary-button>Actualizar</x-primary-button>
                        <a href="{{ route('roles.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
