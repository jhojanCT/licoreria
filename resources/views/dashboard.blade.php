<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="ui-section-label">Resumen</p>
            <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Panel principal</h2>
            <p class="mt-1 text-sm font-medium capitalize text-slate-500">{{ $today_label }}</p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8 lg:py-10">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:space-y-10 sm:px-6 lg:px-8">
            @include('layouts.partials.flash-success')

            {{-- Hero --}}
            <div class="relative overflow-hidden rounded-3xl border border-white/60 bg-gradient-to-br from-slate-900 via-indigo-950 to-violet-950 px-6 py-10 shadow-soft-lg ring-1 ring-slate-900/20 sm:px-10">
                <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-indigo-500/30 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-16 left-10 h-48 w-48 rounded-full bg-violet-500/25 blur-3xl"></div>
                <div class="relative">
                    <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-indigo-200/90">{{ config('app.name', 'Licorería') }}</p>
                    <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                        Hola, {{ Auth::user()->name }}
                    </h1>
                    <p class="mt-3 max-w-2xl text-sm leading-relaxed text-indigo-100/85 sm:text-base">
                        Resumen del día y accesos rápidos. Los importes respetan tus permisos en el sistema.
                    </p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-3xl border border-emerald-200/70 bg-gradient-to-br from-emerald-50/95 to-white p-6 shadow-soft ring-1 ring-emerald-900/5">
                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-emerald-700">Tiempo real</p>
                    <p class="mt-2 text-sm font-semibold text-slate-600">Caja actual</p>
                    <p class="mt-1 text-3xl font-extrabold tabular-nums text-emerald-700">
                        {{ number_format($stats['cash_balance_now'], 2) }} <span class="text-base font-bold">Bs</span>
                    </p>
                </div>
                <div class="rounded-3xl border border-sky-200/70 bg-gradient-to-br from-sky-50/95 to-white p-6 shadow-soft ring-1 ring-sky-900/5">
                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-sky-700">Tiempo real</p>
                    <p class="mt-2 text-sm font-semibold text-slate-600">Saldo QR actual</p>
                    <p class="mt-1 text-3xl font-extrabold tabular-nums text-sky-700">
                        {{ number_format($stats['qr_balance_now'], 2) }} <span class="text-base font-bold">Bs</span>
                    </p>
                </div>
            </div>

            {{-- Acciones rápidas --}}
            <div>
                <p class="ui-section-label">Accesos rápidos</p>
                <div class="mt-4 flex flex-wrap gap-2.5">
                    @can('sales.create')
                    <a href="{{ route('sales.create') }}" class="inline-flex min-h-[44px] items-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:brightness-110 active:scale-[0.98] touch-manipulation">
                        <svg class="h-4 w-4 shrink-0 opacity-95" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nueva venta
                    </a>
                    @endcan
                    @can('purchases.create')
                    <a href="{{ route('purchases.create') }}" class="inline-flex min-h-[44px] items-center gap-2 rounded-2xl border border-slate-200/80 bg-white/90 px-5 py-2.5 text-sm font-semibold text-slate-800 shadow-soft backdrop-blur-sm transition hover:border-emerald-200 hover:bg-white hover:shadow-soft-lg touch-manipulation">
                        <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Registrar compra
                    </a>
                    @endcan
                    @can('cash.close_basic')
                    <a href="{{ route('daily-cash-closures.create') }}" class="inline-flex min-h-[44px] items-center gap-2 rounded-2xl border border-slate-200/80 bg-white/90 px-5 py-2.5 text-sm font-semibold text-slate-800 shadow-soft backdrop-blur-sm transition hover:border-amber-200 hover:bg-white touch-manipulation">
                        <svg class="h-4 w-4 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Cierre de caja
                    </a>
                    <a href="{{ route('special-cash-operations.index') }}" class="inline-flex min-h-[44px] items-center gap-2 rounded-2xl border border-slate-200/80 bg-white/90 px-5 py-2.5 text-sm font-semibold text-slate-800 shadow-soft backdrop-blur-sm transition hover:bg-white touch-manipulation">
                        Operaciones especiales
                    </a>
                    @endcan
                    @can('products.view')
                    <a href="{{ route('products.index') }}" class="inline-flex min-h-[44px] items-center gap-2 rounded-2xl border border-slate-200/80 bg-white/90 px-5 py-2.5 text-sm font-semibold text-slate-800 shadow-soft backdrop-blur-sm transition hover:bg-white touch-manipulation">Productos</a>
                    @endcan
                </div>
            </div>

            <div class="grid gap-8 {{ $show_catalog_column ? 'lg:grid-cols-2' : '' }}">
                <div class="space-y-4 {{ ! $show_catalog_column ? 'max-w-3xl' : '' }}">
                    <p class="ui-section-label">Hoy</p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        @can('sales.view')
                        <a href="{{ route('sales.index', ['from' => $today->toDateString(), 'to' => $today->toDateString()]) }}" class="group relative overflow-hidden rounded-3xl border border-slate-200/70 bg-white/85 p-6 shadow-soft ring-1 ring-slate-900/[0.04] backdrop-blur-md transition hover:border-indigo-200/80 hover:shadow-soft-lg">
                            <div class="absolute right-4 top-4 rounded-full bg-indigo-500/10 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-indigo-700">Hoy</div>
                            <span class="text-sm font-semibold text-slate-600">Ventas</span>
                            <p class="mt-3 text-4xl font-extrabold tabular-nums tracking-tight text-slate-900">{{ $stats['today_sales_count'] }}</p>
                            <p class="mt-0.5 text-sm text-slate-500">operaciones</p>
                            <p class="mt-4 text-lg font-bold tabular-nums text-indigo-600">{{ number_format($stats['today_sales_total'], 2) }} <span class="text-sm font-semibold text-slate-400">Bs</span></p>
                            <p class="mt-3 text-xs font-bold uppercase tracking-wider text-indigo-600 opacity-0 transition group-hover:opacity-100">Ver listado →</p>
                        </a>
                        @endcan

                        @can('purchases.view')
                        <a href="{{ route('purchases.index', ['from' => $today->toDateString(), 'to' => $today->toDateString()]) }}" class="group relative overflow-hidden rounded-3xl border border-slate-200/70 bg-white/85 p-6 shadow-soft ring-1 ring-slate-900/[0.04] backdrop-blur-md transition hover:border-emerald-200/80 hover:shadow-soft-lg">
                            <div class="absolute right-4 top-4 rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-800">Hoy</div>
                            <span class="text-sm font-semibold text-slate-600">Compras recibidas</span>
                            <p class="mt-3 text-4xl font-extrabold tabular-nums tracking-tight text-slate-900">{{ $stats['today_purchases_count'] }}</p>
                            <p class="mt-0.5 text-sm text-slate-500">ingresos</p>
                            <p class="mt-4 text-lg font-bold tabular-nums text-emerald-600">{{ number_format($stats['today_purchases_total'], 2) }} <span class="text-sm font-semibold text-slate-400">Bs</span></p>
                            <p class="mt-3 text-xs font-bold uppercase tracking-wider text-emerald-600 opacity-0 transition group-hover:opacity-100">Ver compras →</p>
                        </a>
                        @endcan

                        @can('cash.close_basic')
                        <div class="sm:col-span-2 overflow-hidden rounded-3xl border p-6 shadow-soft ring-1 backdrop-blur-md {{ $stats['closure_today'] ? 'border-emerald-200/80 bg-emerald-50/50 ring-emerald-900/5' : 'border-amber-200/80 bg-amber-50/40 ring-amber-900/5' }}">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Cierre de caja del día</p>
                                    @if ($stats['closure_today'])
                                        <p class="mt-1 text-sm text-slate-600">Registrado por {{ $stats['closure_today']->closedBy->name }}.</p>
                                    @else
                                        <p class="mt-1 text-sm font-medium text-amber-900/90">Aún no hay cierre para hoy.</p>
                                    @endif
                                </div>
                                <div class="flex shrink-0 flex-wrap gap-2">
                                    @if ($stats['closure_today'])
                                        <a href="{{ route('daily-cash-closures.show', $stats['closure_today']) }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200/80 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 shadow-soft transition hover:bg-slate-50">Ver cierre</a>
                                    @else
                                        <a href="{{ route('daily-cash-closures.create') }}" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:brightness-110">Registrar</a>
                                    @endif
                                    <a href="{{ route('daily-cash-closures.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200/80 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-soft transition hover:bg-slate-50">Historial</a>
                                </div>
                            </div>
                        </div>
                        @endcan
                    </div>
                </div>

                @if ($show_catalog_column)
                <div class="space-y-4">
                    <p class="ui-section-label">Datos generales</p>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @can('products.view')
                        <a href="{{ route('products.index') }}" class="flex items-center justify-between gap-3 rounded-3xl border border-slate-200/70 bg-white/85 p-5 shadow-soft ring-1 ring-slate-900/[0.04] backdrop-blur-md transition hover:border-slate-300 hover:shadow-soft-lg">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-600">Productos activos</p>
                                <p class="mt-1 text-3xl font-extrabold tabular-nums text-slate-900">{{ $stats['products_active'] }}</p>
                                <p class="text-xs font-medium text-slate-500">de {{ $stats['products_total'] }} en catálogo</p>
                            </div>
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200/80 text-slate-600 shadow-inner">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </span>
                        </a>
                        @endcan
                        @can('purchases.view')
                        <a href="{{ route('suppliers.index') }}" class="flex items-center justify-between gap-3 rounded-3xl border border-slate-200/70 bg-white/85 p-5 shadow-soft ring-1 ring-slate-900/[0.04] backdrop-blur-md transition hover:border-slate-300 hover:shadow-soft-lg">
                            <div>
                                <p class="text-sm font-semibold text-slate-600">Proveedores</p>
                                <p class="mt-1 text-3xl font-extrabold tabular-nums text-slate-900">{{ $stats['suppliers_count'] }}</p>
                            </div>
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200/80 text-slate-600 shadow-inner">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </span>
                        </a>
                        @endcan
                        @can('users.manage')
                        <a href="{{ route('users.index') }}" class="flex items-center justify-between gap-3 rounded-3xl border border-slate-200/70 bg-white/85 p-5 shadow-soft ring-1 ring-slate-900/[0.04] backdrop-blur-md transition hover:border-violet-200 sm:col-span-2">
                            <div>
                                <p class="text-sm font-semibold text-slate-600">Usuarios del sistema</p>
                                <p class="mt-1 text-3xl font-extrabold tabular-nums text-slate-900">{{ $stats['users_count'] }}</p>
                            </div>
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 text-white shadow-soft">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </span>
                        </a>
                        @endcan
                        @can('roles.manage')
                        <a href="{{ route('roles.index') }}" class="flex items-center justify-between gap-3 rounded-3xl border border-slate-200/70 bg-white/85 p-5 shadow-soft ring-1 ring-slate-900/[0.04] backdrop-blur-md transition hover:border-indigo-200 sm:col-span-2">
                            <div>
                                <p class="text-sm font-semibold text-slate-600">Roles y permisos</p>
                                <p class="mt-1 text-sm font-medium text-slate-500">Gestionar accesos por rol</p>
                            </div>
                            <span class="text-sm font-bold text-indigo-600">Abrir →</span>
                        </a>
                        @endcan
                    </div>
                </div>
                @endif
            </div>

            @can('products.view')
            @if ($stats['low_stock']->isNotEmpty())
            <div class="overflow-hidden rounded-3xl border border-amber-200/80 bg-gradient-to-b from-amber-50/95 to-white shadow-soft-lg ring-1 ring-amber-900/5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-amber-100/80 bg-amber-100/50 px-6 py-4 backdrop-blur-sm">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-400/30 text-amber-900 shadow-inner">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </span>
                        <div>
                            <h3 class="font-bold text-amber-950">Stock bajo el mínimo</h3>
                            <p class="text-sm text-amber-900/80">
                                {{ $stats['low_stock_critical_count'] ?? 0 }} críticos · {{ $stats['low_stock_warning_count'] ?? 0 }} en advertencia
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('products.index') }}" class="text-sm font-bold text-amber-900 underline decoration-amber-400/80 decoration-2 underline-offset-4 hover:text-amber-950">Ver productos</a>
                </div>
                <ul class="divide-y divide-amber-100/80">
                    @foreach ($stats['low_stock'] as $p)
                    <li class="flex flex-wrap items-center justify-between gap-2 px-6 py-3.5 text-sm">
                        <span class="flex items-center gap-2">
                            <span class="font-semibold text-slate-900">{{ $p->name }}</span>
                            @if (($p->stock_alert_level ?? 'warning') === 'critical')
                                <span class="inline-flex rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-rose-700">Crítico</span>
                            @else
                                <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-700">Advertencia</span>
                            @endif
                        </span>
                        <span class="tabular-nums text-slate-600">
                            Stock <strong class="text-amber-800">{{ rtrim(rtrim(number_format((float) $p->stock_quantity, 3, '.', ''), '0'), '.') }}</strong>
                            · mín. {{ number_format($p->min_stock_alert, 2) }} · falta {{ number_format((float) ($p->stock_deficit ?? 0), 2) }}
                        </span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
            @endcan
        </div>
    </div>
</x-app-layout>
