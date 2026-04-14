<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 min-w-0 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
            <div>
                <p class="ui-section-label">Seguridad</p>
                <h2 class="mt-1 min-w-0 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Roles y permisos</h2>
            </div>
            <a href="{{ route('roles.create') }}" class="inline-flex shrink-0 items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:brightness-110 w-full sm:w-auto touch-manipulation">Nuevo rol</a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            @include('layouts.partials.flash-success')

            <div class="ui-table-wrap">
                <div class="-mx-px overflow-x-auto overscroll-x-contain">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/95">
                        <tr>
                            <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Rol</th>
                            <th class="px-4 py-3.5 text-right text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Permisos</th>
                            <th class="sm:px-6"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white/50">
                        @forelse ($roles as $role)
                        <tr class="transition hover:bg-indigo-50/40">
                            <td class="px-4 py-4 text-sm font-bold text-slate-900 sm:px-6">{{ $role->name }}</td>
                            <td class="px-4 py-4 text-right text-sm tabular-nums text-slate-600 sm:px-6">{{ $role->permissions_count }}</td>
                            <td class="px-4 py-4 sm:px-6"><a href="{{ route('roles.edit', $role) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">Editar</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-6 py-12 text-center text-sm font-medium text-slate-500">No hay roles.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
