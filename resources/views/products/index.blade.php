<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 min-w-0 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
            <div>
                <p class="ui-section-label">Catálogo</p>
                <h2 class="mt-1 min-w-0 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Productos</h2>
            </div>
            @can('products.manage')
            <a href="{{ route('products.create') }}" class="inline-flex shrink-0 items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:brightness-110 sm:py-2.5 w-full sm:w-auto touch-manipulation">
                Nuevo producto
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @include('layouts.partials.flash-success')

            @if (($lowStockSummary['total'] ?? 0) > 0)
                <section class="mb-6 overflow-hidden rounded-3xl border border-amber-200/80 bg-gradient-to-br from-amber-50/95 to-white shadow-soft ring-1 ring-amber-900/5">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-amber-100/80 bg-amber-100/60 px-5 py-4 sm:px-6">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-amber-700">Alerta inventario</p>
                            <h3 class="mt-1 text-base font-extrabold text-amber-950 sm:text-lg">
                                {{ $lowStockSummary['total'] }} productos bajo mínimo
                            </h3>
                            <p class="mt-1 text-sm text-amber-900/80">
                                {{ $lowStockSummary['critical'] }} críticos · {{ $lowStockSummary['warning'] }} en advertencia
                            </p>
                        </div>
                        <a href="{{ route('purchases.create') }}" class="inline-flex min-h-[42px] items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2 text-sm font-bold text-white shadow-soft transition hover:brightness-110">
                            Reponer stock
                        </a>
                    </div>
                    @if(($lowStockSummary['critical_products'] ?? collect())->isNotEmpty())
                        <div class="px-5 py-4 sm:px-6">
                            <p class="mb-2 text-xs font-bold uppercase tracking-wide text-rose-700">Críticos prioritarios</p>
                            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($lowStockSummary['critical_products'] as $critical)
                                    <div class="rounded-xl border border-rose-200/80 bg-rose-50/70 px-3 py-2">
                                        <p class="truncate text-sm font-semibold text-rose-900">{{ $critical->name }}</p>
                                        <p class="mt-0.5 text-xs text-rose-800">
                                            Stock {{ number_format((float) $critical->stock_quantity, 2) }} · mín {{ number_format((float) $critical->min_stock_alert, 2) }} · falta {{ number_format((float) ($critical->stock_deficit ?? 0), 2) }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </section>
            @endif

            <form method="get" class="mb-6 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-stretch">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar..." class="min-h-[44px] w-full min-w-0 flex-1 rounded-2xl border-slate-200/80 bg-white/90 text-base shadow-soft backdrop-blur-sm focus:border-indigo-500 focus:ring-indigo-500 sm:max-w-md">
                <button type="submit" class="inline-flex min-h-[44px] items-center justify-center rounded-2xl border border-slate-200/80 bg-white px-5 text-sm font-semibold text-slate-800 shadow-soft transition hover:bg-slate-50 touch-manipulation sm:shrink-0">Buscar</button>
            </form>

            <div class="ui-table-wrap">
                <div class="-mx-px overflow-x-auto overscroll-x-contain">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/95">
                        <tr>
                            <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Nombre</th>
                            <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">SKU</th>
                            <th class="px-4 py-3.5 text-right text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Stock</th>
                            <th class="px-4 py-3.5 text-right text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Precio</th>
                            <th class="px-4 py-3.5 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Estado</th>
                            @can('products.manage')
                            <th class="sm:px-6"></th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white/50">
                        @forelse ($products as $product)
                        @php
                            $stock = (float) $product->stock_quantity;
                            $min = (float) $product->min_stock_alert;
                            $ratio = $min > 0 ? ($stock / $min) : 1;
                            $isLow = $stock < $min && $min > 0;
                            $isCritical = $isLow && ($stock <= 0 || $ratio <= 0.35);
                        @endphp
                        <tr class="transition {{ $isCritical ? 'bg-rose-50/40 hover:bg-rose-50/70' : 'hover:bg-indigo-50/40' }}">
                            <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold text-slate-900 sm:px-6">{{ $product->name }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-500 sm:px-6">{{ $product->sku ?? '-' }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-right text-sm tabular-nums text-slate-700 sm:px-6">
                                {{ $product->stock_quantity }}
                                @if($product->isDualUnitProduct())
                                    <span class="text-xs font-normal text-slate-500 block">cig. · {{ $product->units_per_pack }}/caj.</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-right text-sm font-medium tabular-nums text-slate-800 sm:px-6">
                                @if($product->isDualUnitProduct())
                                    <span class="block">Caj. {{ number_format($product->default_sale_price, 2) }}</span>
                                    <span class="text-xs font-normal text-slate-500">Un. {{ number_format($product->price_per_single_unit, 2) }}</span>
                                @else
                                    {{ number_format($product->default_sale_price, 2) }} Bs
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-center text-sm sm:px-6">
                                @if ($isCritical)
                                    <span class="inline-flex rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-800">Crítico</span>
                                @elseif ($isLow)
                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">Bajo</span>
                                @else
                                    <span class="text-slate-500">{{ $product->is_active ? 'Activo' : 'Inactivo' }}</span>
                                @endif
                            </td>
                            @can('products.manage')
                            <td class="whitespace-nowrap px-4 py-4 sm:px-6">
                                <a href="{{ route('products.edit', $product) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">Editar</a>
                            </td>
                            @endcan
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-sm font-medium text-slate-500">No hay productos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
                <div class="border-t border-slate-100/80 bg-slate-50/50 px-4 py-3">{{ $products->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
