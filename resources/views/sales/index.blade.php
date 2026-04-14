<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 min-w-0 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
            <div>
                <p class="ui-section-label">Punto de venta</p>
                <h2 class="mt-1 min-w-0 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Ventas</h2>
            </div>
            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                <a href="{{ route('sales.credit-accounts') }}" class="inline-flex shrink-0 items-center justify-center rounded-2xl border border-slate-200/90 bg-white/90 px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-soft transition hover:bg-white w-full sm:w-auto touch-manipulation">Cuentas por cobrar</a>
                <a href="{{ route('sales.create') }}" class="inline-flex shrink-0 items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:brightness-110 w-full sm:w-auto touch-manipulation">Nueva venta</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @include('layouts.partials.flash-success')

            <form method="get" class="mb-6 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-end">
                <select name="kind" class="min-h-[44px] w-full min-w-0 rounded-2xl border-slate-200/80 bg-white/90 text-base shadow-soft backdrop-blur-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-auto">
                    <option value="">Todas</option>
                    <option value="cash" {{ request('kind') === 'cash' ? 'selected' : '' }}>Al contado</option>
                    <option value="credit" {{ request('kind') === 'credit' ? 'selected' : '' }}>Por cobrar</option>
                </select>
                <input type="date" name="from" value="{{ request('from') }}" class="min-h-[44px] w-full min-w-0 rounded-2xl border-slate-200/80 bg-white/90 text-base shadow-soft backdrop-blur-sm sm:w-auto sm:min-w-[10rem]">
                <input type="date" name="to" value="{{ request('to') }}" class="min-h-[44px] w-full min-w-0 rounded-2xl border-slate-200/80 bg-white/90 text-base shadow-soft backdrop-blur-sm sm:w-auto sm:min-w-[10rem]">
                <button type="submit" class="inline-flex min-h-[44px] items-center justify-center rounded-2xl border border-slate-200/80 bg-white px-5 text-sm font-semibold text-slate-800 shadow-soft transition hover:bg-slate-50 touch-manipulation sm:shrink-0">Filtrar</button>
            </form>

            <div class="ui-table-wrap">
                <div class="-mx-px overflow-x-auto overscroll-x-contain">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/95">
                        <tr>
                            <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Fecha</th>
                            <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Tipo</th>
                            <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Cliente / Vendedor</th>
                            <th class="px-4 py-3.5 text-right text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Total</th>
                            <th class="sm:px-6"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white/50">
                        @forelse ($sales as $sale)
                        <tr class="transition hover:bg-indigo-50/40">
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-700 sm:px-6">{{ $sale->sold_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-4 text-sm sm:px-6">
                                @if($sale->sale_kind->value === 'credit')
                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">Por cobrar</span>
                                @else
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">Al contado</span>
                                @endif
                            </td>
                            <td class="max-w-[12rem] truncate px-4 py-4 text-sm text-slate-700 sm:max-w-none sm:px-6">
                                @if($sale->sale_kind->value === 'credit')
                                    {{ $sale->customer_name }} ({{ $sale->customer_phone }})
                                @else
                                    {{ $sale->soldBy->name }}
                                @endif
                            </td>
                            <td class="px-4 py-4 text-right text-sm font-bold tabular-nums text-slate-900 sm:px-6">{{ number_format($sale->total, 2) }} Bs</td>
                            <td class="px-4 py-4 sm:px-6"><a href="{{ route('sales.show', $sale) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">Ver</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-sm font-medium text-slate-500">No hay ventas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
                <div class="border-t border-slate-100/80 bg-slate-50/50 px-4 py-3">{{ $sales->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
