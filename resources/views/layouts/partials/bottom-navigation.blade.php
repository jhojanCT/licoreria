{{-- Navegación inferior tipo app (< lg). Requiere Alpine moreOpen en el contenedor padre. --}}
@php
    $u = auth()->user();
    $candidates = [];
    if ($u->can('sales.view')) {
        $candidates[] = [
            'href' => route('sales.index'),
            'label' => 'Ventas',
            'active' => request()->routeIs('sales.*'),
            'icon' => 'bag',
        ];
    }
    if ($u->can('cash.close_basic')) {
        $candidates[] = [
            'href' => route('daily-cash-closures.index'),
            'label' => 'Caja',
            'active' => request()->routeIs('daily-cash-closures.*') || request()->routeIs('special-cash-operations.*'),
            'icon' => 'cash',
        ];
    }
    if ($u->can('products.view')) {
        $candidates[] = [
            'href' => route('products.index'),
            'label' => 'Stock',
            'active' => request()->routeIs('products.*'),
            'icon' => 'cube',
        ];
    }
    if ($u->can('purchases.view')) {
        $candidates[] = [
            'href' => route('purchases.index'),
            'label' => 'Compras',
            'active' => request()->routeIs('purchases.*'),
            'icon' => 'truck',
        ];
    }
    $middleTabs = array_slice($candidates, 0, 3);
@endphp

<div
    x-show="moreOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[60] lg:hidden"
    x-cloak
    @keydown.escape.window="moreOpen = false"
>
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-[2px]" @click="moreOpen = false" aria-hidden="true"></div>
    <div
        class="absolute bottom-0 left-0 right-0 flex max-h-[88dvh] flex-col rounded-t-2xl bg-white shadow-[0_-8px_30px_rgba(0,0,0,0.12)] ring-1 ring-slate-200/80"
        style="padding-bottom: max(0.5rem, env(safe-area-inset-bottom))"
        @click.stop
    >
        <div class="flex shrink-0 items-center justify-between border-b border-slate-100 px-4 py-3">
            <span class="text-base font-semibold text-slate-900">Menú</span>
            <button
                type="button"
                class="flex h-11 w-11 items-center justify-center rounded-full text-2xl leading-none text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 touch-manipulation"
                @click="moreOpen = false"
                aria-label="Cerrar menú"
            >×</button>
        </div>
        <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-2 py-2">
            <div class="space-y-1">
                <a href="{{ route('dashboard') }}" @click="moreOpen = false" class="flex min-h-[52px] items-center rounded-xl px-4 text-base font-medium text-slate-800 hover:bg-slate-50 active:bg-slate-100 touch-manipulation">Inicio</a>
                @can('products.view')
                <a href="{{ route('products.index') }}" @click="moreOpen = false" class="flex min-h-[52px] items-center rounded-xl px-4 text-base font-medium text-slate-800 hover:bg-slate-50 active:bg-slate-100 touch-manipulation">Productos</a>
                @endcan
                @can('purchases.view')
                <a href="{{ route('suppliers.index') }}" @click="moreOpen = false" class="flex min-h-[52px] items-center rounded-xl px-4 text-base font-medium text-slate-800 hover:bg-slate-50 active:bg-slate-100 touch-manipulation">Proveedores</a>
                <a href="{{ route('purchases.index') }}" @click="moreOpen = false" class="flex min-h-[52px] items-center rounded-xl px-4 text-base font-medium text-slate-800 hover:bg-slate-50 active:bg-slate-100 touch-manipulation">Compras</a>
                @endcan
                @can('sales.view')
                <a href="{{ route('sales.index') }}" @click="moreOpen = false" class="flex min-h-[52px] items-center rounded-xl px-4 text-base font-medium text-slate-800 hover:bg-slate-50 active:bg-slate-100 touch-manipulation">Ventas</a>
                @endcan
                @can('cash.close_basic')
                <a href="{{ route('daily-cash-closures.index') }}" @click="moreOpen = false" class="flex min-h-[52px] items-center rounded-xl px-4 text-base font-medium text-slate-800 hover:bg-slate-50 active:bg-slate-100 touch-manipulation">Cierre de caja</a>
                <a href="{{ route('special-cash-operations.index') }}" @click="moreOpen = false" class="flex min-h-[52px] items-center rounded-xl px-4 text-base font-medium text-slate-800 hover:bg-slate-50 active:bg-slate-100 touch-manipulation">Operaciones especiales</a>
                @endcan
                @can('users.manage')
                <a href="{{ route('users.index') }}" @click="moreOpen = false" class="flex min-h-[52px] items-center rounded-xl px-4 text-base font-medium text-slate-800 hover:bg-slate-50 active:bg-slate-100 touch-manipulation">Usuarios</a>
                @endcan
                @can('roles.manage')
                <a href="{{ route('roles.index') }}" @click="moreOpen = false" class="flex min-h-[52px] items-center rounded-xl px-4 text-base font-medium text-slate-800 hover:bg-slate-50 active:bg-slate-100 touch-manipulation">Roles y permisos</a>
                @endcan
            </div>
            <div class="mt-4 border-t border-slate-100 pt-3">
                <a href="{{ route('profile.edit') }}" @click="moreOpen = false" class="flex min-h-[52px] items-center rounded-xl px-4 text-base font-medium text-indigo-700 hover:bg-indigo-50 touch-manipulation">Mi perfil</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="flex w-full min-h-[52px] items-center rounded-xl px-4 text-left text-base font-medium text-red-700 hover:bg-red-50 touch-manipulation">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </div>
</div>

<nav
    class="fixed bottom-0 left-0 right-0 z-50 border-t border-white/60 bg-white/75 shadow-[0_-8px_32px_-8px_rgb(15_23_42/0.12)] backdrop-blur-2xl supports-[backdrop-filter]:bg-white/65 lg:hidden"
    style="padding-bottom: env(safe-area-inset-bottom)"
    aria-label="Navegación principal"
>
    <div class="mx-auto flex max-w-lg items-stretch justify-around px-1 pt-1">
        <a
            href="{{ route('dashboard') }}"
            class="flex min-h-[3.5rem] min-w-0 flex-1 flex-col items-center justify-center gap-0.5 rounded-xl px-1 py-1 text-[11px] font-semibold transition touch-manipulation active:scale-[0.98] {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-500' }}"
        >
            <svg class="{{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-400' }} h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
            <span class="truncate">Inicio</span>
        </a>

        @foreach ($middleTabs as $tab)
            @if ($tab['icon'] === 'bag')
                <a href="{{ $tab['href'] }}" class="flex min-h-[3.5rem] min-w-0 flex-1 flex-col items-center justify-center gap-0.5 rounded-xl px-1 py-1 text-[11px] font-semibold transition touch-manipulation active:scale-[0.98] {{ $tab['active'] ? 'text-indigo-600' : 'text-slate-500' }}">
                    <svg class="{{ $tab['active'] ? 'text-indigo-600' : 'text-slate-400' }} h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
                    <span class="truncate">{{ $tab['label'] }}</span>
                </a>
            @elseif ($tab['icon'] === 'cube')
                <a href="{{ $tab['href'] }}" class="flex min-h-[3.5rem] min-w-0 flex-1 flex-col items-center justify-center gap-0.5 rounded-xl px-1 py-1 text-[11px] font-semibold transition touch-manipulation active:scale-[0.98] {{ $tab['active'] ? 'text-indigo-600' : 'text-slate-500' }}">
                    <svg class="{{ $tab['active'] ? 'text-indigo-600' : 'text-slate-400' }} h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                    <span class="truncate">{{ $tab['label'] }}</span>
                </a>
            @elseif ($tab['icon'] === 'truck')
                <a href="{{ $tab['href'] }}" class="flex min-h-[3.5rem] min-w-0 flex-1 flex-col items-center justify-center gap-0.5 rounded-xl px-1 py-1 text-[11px] font-semibold transition touch-manipulation active:scale-[0.98] {{ $tab['active'] ? 'text-indigo-600' : 'text-slate-500' }}">
                    <svg class="{{ $tab['active'] ? 'text-indigo-600' : 'text-slate-400' }} h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 3.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                    <span class="truncate">{{ $tab['label'] }}</span>
                </a>
            @elseif ($tab['icon'] === 'cash')
                <a href="{{ $tab['href'] }}" class="flex min-h-[3.5rem] min-w-0 flex-1 flex-col items-center justify-center gap-0.5 rounded-xl px-1 py-1 text-[11px] font-semibold transition touch-manipulation active:scale-[0.98] {{ $tab['active'] ? 'text-indigo-600' : 'text-slate-500' }}">
                    <svg class="{{ $tab['active'] ? 'text-indigo-600' : 'text-slate-400' }} h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 5.25h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M3.75 4.5H7.5m0 0v-.375A1.125 1.125 0 018.625 3h2.25M7.5 4.5v.375m0 0v8.25m3-8.25h7.5a2.25 2.25 0 012.25 2.25v6a2.25 2.25 0 01-2.25 2.25h-7.5m-6-3h.008v.008H7.5V15.75z"/></svg>
                    <span class="truncate">{{ $tab['label'] }}</span>
                </a>
            @endif
        @endforeach

        <button
            type="button"
            @click="moreOpen = true"
            class="flex min-h-[3.5rem] min-w-0 flex-1 flex-col items-center justify-center gap-0.5 rounded-xl px-1 py-1 text-[11px] font-semibold text-slate-500 transition touch-manipulation active:scale-[0.98] active:bg-slate-50"
        >
            <svg class="h-6 w-6 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
            <span class="truncate">Más</span>
        </button>
    </div>
</nav>
