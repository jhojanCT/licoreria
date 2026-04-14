<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 min-w-0 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
            <div>
                <p class="ui-section-label">Almacén</p>
                <h2 class="mt-1 min-w-0 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Compras</h2>
            </div>
            <a href="{{ route('purchases.create') }}" class="inline-flex shrink-0 items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:brightness-110 w-full sm:w-auto touch-manipulation">Nueva compra</a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @include('layouts.partials.flash-success')

            <form method="get" class="mb-6 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-end">
                <input type="date" name="from" value="{{ request('from') }}" class="min-h-[44px] w-full min-w-0 rounded-2xl border-slate-200/80 bg-white/90 text-base shadow-soft backdrop-blur-sm sm:w-auto sm:min-w-[10rem]">
                <input type="date" name="to" value="{{ request('to') }}" class="min-h-[44px] w-full min-w-0 rounded-2xl border-slate-200/80 bg-white/90 text-base shadow-soft backdrop-blur-sm sm:w-auto sm:min-w-[10rem]">
                <select name="supplier" class="min-h-[44px] w-full min-w-0 rounded-2xl border-slate-200/80 bg-white/90 text-base shadow-soft backdrop-blur-sm sm:min-w-[12rem] sm:flex-1">
                    <option value="">Todos los proveedores</option>
                    @foreach (\App\Models\Supplier::orderBy('name')->get() as $s)
                        <option value="{{ $s->id }}" {{ request('supplier') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex min-h-[44px] items-center justify-center rounded-2xl border border-slate-200/80 bg-white px-5 text-sm font-semibold text-slate-800 shadow-soft transition hover:bg-slate-50 touch-manipulation sm:shrink-0">Filtrar</button>
            </form>

            <div class="ui-table-wrap">
                <div class="-mx-px overflow-x-auto overscroll-x-contain">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/95">
                        <tr>
                            <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Fecha</th>
                            <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Proveedor</th>
                            <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Recibido por</th>
                            <th class="px-4 py-3.5 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Pago</th>
                            <th class="px-4 py-3.5 text-right text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Total</th>
                            <th class="sm:px-6"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white/50">
                        @forelse ($purchases as $purchase)
                        <tr class="transition hover:bg-emerald-50/40">
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-700 sm:px-6">{{ $purchase->received_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-4 text-sm font-medium text-slate-900 sm:px-6">{{ $purchase->supplier->name }}</td>
                            <td class="px-4 py-4 text-sm text-slate-600 sm:px-6">{{ $purchase->receivedBy->name }}</td>
                            <td class="px-4 py-4 text-center text-sm sm:px-6">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {{ ($purchase->payment_method ?? 'cash') === 'qr' ? 'bg-sky-100 text-sky-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ ($purchase->payment_method ?? 'cash') === 'qr' ? 'QR' : 'Efectivo' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right text-sm font-bold tabular-nums text-slate-900 sm:px-6">{{ number_format($purchase->total_cost, 2) }} Bs</td>
                            <td class="px-4 py-4 sm:px-6"><a href="{{ route('purchases.show', $purchase) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">Ver</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-sm font-medium text-slate-500">No hay compras.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
                <div class="border-t border-slate-100/80 bg-slate-50/50 px-4 py-3">{{ $purchases->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
