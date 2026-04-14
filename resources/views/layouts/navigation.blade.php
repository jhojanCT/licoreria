@php
    $initial = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(Auth::user()->name, 0, 1));
@endphp

<div x-data="{ moreOpen: false }">
    <nav
        class="sticky top-0 z-40 border-b border-white/50 bg-white/65 shadow-[0_4px_24px_-8px_rgb(15_23_42/0.12)] backdrop-blur-xl supports-[backdrop-filter]:bg-white/50"
        style="padding-top: env(safe-area-inset-top)"
    >
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-14 justify-between gap-3 sm:h-16 sm:gap-4">
                <div class="flex min-w-0 flex-1 items-center gap-4 sm:gap-6">
                    <div class="flex min-w-0 shrink-0 items-center gap-2">
                        <a href="{{ route('dashboard') }}" class="group flex min-h-[44px] min-w-0 items-center gap-2.5 rounded-xl outline-none ring-indigo-500/30 transition hover:opacity-95 focus-visible:ring-2 touch-manipulation">
                            <x-application-logo class="block h-8 w-auto shrink-0 fill-current text-indigo-600 transition group-hover:text-indigo-500 sm:h-9" />
                            <span class="hidden min-w-0 truncate text-base font-bold tracking-tight text-slate-900 sm:block sm:max-w-[10rem] md:max-w-xs">
                                {{ config('app.name', 'Licorería') }}
                            </span>
                        </a>
                    </div>

                    <div class="-mx-1 hidden min-w-0 flex-1 overflow-x-auto pb-1 lg:flex">
                        <div class="flex h-14 items-center gap-1 sm:h-16 lg:gap-2">
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                Inicio
                            </x-nav-link>
                            @can('products.view')
                            <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                                Productos
                            </x-nav-link>
                            @endcan
                            @can('purchases.view')
                            <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                                Proveedores
                            </x-nav-link>
                            <x-nav-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')">
                                Compras
                            </x-nav-link>
                            @endcan
                            @can('sales.view')
                            <x-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')">
                                Ventas
                            </x-nav-link>
                            @endcan
                            @can('cash.close_basic')
                            <x-nav-link :href="route('daily-cash-closures.index')" :active="request()->routeIs('daily-cash-closures.*')">
                                Cierre caja
                            </x-nav-link>
                            <x-nav-link :href="route('special-cash-operations.index')" :active="request()->routeIs('special-cash-operations.*')">
                                Op. especiales
                            </x-nav-link>
                            @endcan
                            @can('users.manage')
                            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                Usuarios
                            </x-nav-link>
                            @endcan
                            @can('roles.manage')
                            <x-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.*')">
                                Roles
                            </x-nav-link>
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="flex shrink-0 items-center">
                    <x-dropdown align="right" width="48" contentClasses="bg-white py-0">
                        <x-slot name="trigger">
                            <button
                                type="button"
                                class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center gap-2 rounded-full border border-slate-200/80 bg-white/90 px-1.5 py-1.5 text-sm font-semibold text-slate-800 shadow-soft transition hover:border-indigo-200/80 hover:bg-white hover:shadow-soft-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 sm:min-h-0 sm:min-w-0 sm:justify-start sm:px-2 sm:py-1.5 sm:pl-2 sm:pr-3 touch-manipulation"
                            >
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 text-xs font-bold text-white shadow-inner sm:h-8 sm:w-8">{{ $initial }}</span>
                                <span class="hidden max-w-[7rem] truncate lg:inline lg:max-w-[11rem]">{{ Auth::user()->name }}</span>
                                <svg class="hidden h-4 w-4 shrink-0 text-slate-400 sm:block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="border-b border-slate-100 px-4 py-3">
                                <p class="truncate text-sm font-medium text-slate-900">{{ Auth::user()->name }}</p>
                                <p class="truncate text-xs text-slate-500">{{ Auth::user()->email }}</p>
                            </div>
                            <x-dropdown-link :href="route('profile.edit')">
                                Perfil
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    Cerrar sesión
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </nav>

    @include('layouts.partials.bottom-navigation')
</div>
