<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 min-w-0 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
            <div>
                <p class="ui-section-label">Administración</p>
                <h2 class="mt-1 min-w-0 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Usuarios</h2>
            </div>
            <a href="{{ route('users.create') }}" class="inline-flex shrink-0 items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:brightness-110 w-full sm:w-auto touch-manipulation">Nuevo usuario</a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @include('layouts.partials.flash-success')

            <div class="ui-table-wrap">
                <div class="-mx-px overflow-x-auto overscroll-x-contain">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/95">
                        <tr>
                            <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Nombre</th>
                            <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Correo</th>
                            <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Teléfono</th>
                            <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Rol</th>
                            <th class="sm:px-6"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white/50">
                        @forelse ($users as $u)
                        <tr class="transition hover:bg-violet-50/40">
                            <td class="px-4 py-4 text-sm font-semibold text-slate-900 sm:px-6">{{ $u->name }}</td>
                            <td class="px-4 py-4 text-sm text-slate-600 sm:px-6">{{ $u->email }}</td>
                            <td class="px-4 py-4 text-sm text-slate-600 sm:px-6">{{ $u->phone ?: '—' }}</td>
                            <td class="px-4 py-4 text-sm text-slate-600 sm:px-6">{{ $u->roles->pluck('name')->join(', ') ?: '—' }}</td>
                            <td class="px-4 py-4 text-right sm:px-6">
                                <a href="{{ route('users.edit', $u) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">Editar</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-sm font-medium text-slate-500">No hay usuarios.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
                <div class="border-t border-slate-100/80 bg-slate-50/50 px-4 py-3">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
