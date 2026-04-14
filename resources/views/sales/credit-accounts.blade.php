<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 min-w-0 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
            <div>
                <p class="ui-section-label">Punto de venta</p>
                <h2 class="mt-1 min-w-0 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Cuentas por cobrar</h2>
            </div>
            <a href="{{ route('sales.index') }}" class="inline-flex shrink-0 items-center justify-center rounded-2xl border border-slate-200/90 bg-white/90 px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-soft transition hover:bg-white w-full sm:w-auto touch-manipulation">Ver ventas</a>
        </div>
    </x-slot>

    @php
        $customersCount = $accounts->count();
        $debtsCount = $accounts->sum(fn ($account) => $account['sales']->count());
        $pendingTotal = $accounts->sum('pending_total');
    @endphp

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('layouts.partials.flash-success')
            @if ($errors->has('credit'))
                <div class="rounded-2xl border border-rose-200/80 bg-rose-50/95 p-4 text-sm font-medium text-rose-900 shadow-soft">
                    {{ $errors->first('credit') }}
                </div>
            @endif

            <section class="relative overflow-hidden rounded-3xl border border-indigo-200/60 bg-gradient-to-br from-indigo-900 via-violet-900 to-slate-900 p-6 text-white shadow-soft-lg ring-1 ring-indigo-950/40 sm:p-8">
                <div class="pointer-events-none absolute -right-16 -top-16 h-52 w-52 rounded-full bg-indigo-400/30 blur-3xl"></div>
                <div class="pointer-events-none absolute bottom-0 left-1/3 h-40 w-56 rounded-full bg-violet-400/20 blur-3xl"></div>
                <div class="relative">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-indigo-200/90">Resumen cartera</p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 backdrop-blur-sm">
                            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-100/90">Clientes</p>
                            <p class="mt-1 text-2xl font-extrabold">{{ $customersCount }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 backdrop-blur-sm">
                            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-100/90">Deudas</p>
                            <p class="mt-1 text-2xl font-extrabold">{{ $debtsCount }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 backdrop-blur-sm">
                            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-100/90">Saldo pendiente</p>
                            <p class="mt-1 text-2xl font-extrabold">{{ number_format($pendingTotal, 2) }} Bs</p>
                        </div>
                    </div>
                </div>
            </section>

            @forelse ($accounts as $account)
                <details class="ui-form-card !p-0 overflow-hidden" open>
                    <summary class="list-none cursor-pointer px-5 py-4 sm:px-6">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <h3 class="truncate text-lg font-bold text-slate-900">{{ $account['customer_name'] }}</h3>
                                <p class="text-sm text-slate-600">{{ $account['customer_phone'] }}@if($account['customer_address']) · {{ $account['customer_address'] }}@endif</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">
                                    {{ $account['sales']->count() }} {{ $account['sales']->count() === 1 ? 'deuda pendiente' : 'deudas pendientes' }}
                                </span>
                                <span class="inline-flex rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-bold text-indigo-800">
                                    Total: {{ number_format($account['pending_total'], 2) }} Bs
                                </span>
                            </div>
                        </div>
                    </summary>

                    <div class="border-t border-slate-200/70 px-5 py-4 sm:px-6">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Desglose de deudas ({{ $account['sales']->count() }})
                        </p>
                        <div class="space-y-3">
                            @foreach ($account['sales'] as $sale)
                                <div class="rounded-2xl border border-slate-200/80 bg-white/90 p-4">
                                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2 text-sm">
                                        <p class="font-semibold text-slate-900">
                                            Venta #{{ $sale->id }} · {{ $sale->sold_at->format('d/m/Y H:i') }} · {{ $sale->soldBy?->name ?? '—' }}
                                        </p>
                                        <p class="text-slate-600">
                                            Total: <strong>{{ number_format($sale->total, 2) }} Bs</strong> ·
                                            Pagado: <strong>{{ number_format($sale->paid_total, 2) }} Bs</strong> ·
                                            Saldo: <strong class="text-amber-700">{{ number_format($sale->pending_total, 2) }} Bs</strong>
                                        </p>
                                    </div>

                                    <form method="POST" action="{{ route('sales.settle-credit', $sale) }}" class="grid gap-2 sm:grid-cols-4">
                                        @csrf
                                        <input
                                            type="number"
                                            name="amount"
                                            min="0.01"
                                            max="{{ number_format($sale->pending_total, 2, '.', '') }}"
                                            step="0.01"
                                            required
                                            class="sm:col-span-2"
                                            placeholder="Monto a cobrar (max {{ number_format($sale->pending_total, 2) }})"
                                        >
                                        <select name="method" class="sm:col-span-1" required>
                                            <option value="cash">Efectivo</option>
                                            <option value="qr">QR</option>
                                        </select>
                                        <button type="submit" class="inline-flex min-h-[44px] items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-4 py-2 text-sm font-bold text-white shadow-soft transition hover:brightness-110">
                                            Registrar cobro
                                        </button>
                                    </form>

                                    <details class="mt-3 rounded-xl border border-slate-200/70 bg-slate-50/70 p-3">
                                        <summary class="cursor-pointer text-xs font-bold uppercase tracking-wide text-indigo-700">
                                            Ver detalle de la venta
                                        </summary>

                                        <div class="mt-3 grid gap-3 lg:grid-cols-2">
                                            <div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white">
                                                <div class="border-b border-slate-100 px-3 py-2 text-xs font-bold uppercase tracking-wide text-slate-500">
                                                    Productos ({{ $sale->lines->count() }})
                                                </div>
                                                <div class="max-h-56 overflow-auto">
                                                    <table class="min-w-full text-xs">
                                                        <thead class="bg-slate-50">
                                                            <tr>
                                                                <th class="px-3 py-2 text-left font-semibold text-slate-500">Producto</th>
                                                                <th class="px-3 py-2 text-right font-semibold text-slate-500">Cant.</th>
                                                                <th class="px-3 py-2 text-right font-semibold text-slate-500">P/U</th>
                                                                <th class="px-3 py-2 text-right font-semibold text-slate-500">Subtotal</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-slate-100">
                                                            @foreach ($sale->lines as $line)
                                                                <tr>
                                                                    <td class="px-3 py-2">{{ $line->product?->name ?? '—' }}</td>
                                                                    <td class="px-3 py-2 text-right">{{ $line->quantityLabel() }}</td>
                                                                    <td class="px-3 py-2 text-right">{{ number_format($line->unit_price, 2) }}</td>
                                                                    <td class="px-3 py-2 text-right font-semibold">{{ number_format($line->line_total, 2) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white">
                                                <div class="border-b border-slate-100 px-3 py-2 text-xs font-bold uppercase tracking-wide text-slate-500">
                                                    Historial de cobros ({{ $sale->payments->count() }})
                                                </div>
                                                <div class="max-h-56 overflow-auto">
                                                    <table class="min-w-full text-xs">
                                                        <thead class="bg-slate-50">
                                                            <tr>
                                                                <th class="px-3 py-2 text-left font-semibold text-slate-500">Fecha</th>
                                                                <th class="px-3 py-2 text-left font-semibold text-slate-500">Método</th>
                                                                <th class="px-3 py-2 text-right font-semibold text-slate-500">Monto</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-slate-100">
                                                            @forelse ($sale->payments as $payment)
                                                                <tr>
                                                                    <td class="px-3 py-2">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                                                    <td class="px-3 py-2">{{ $payment->method->value === 'cash' ? 'Efectivo' : 'QR' }}</td>
                                                                    <td class="px-3 py-2 text-right font-semibold">{{ number_format($payment->amount, 2) }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr><td colspan="3" class="px-3 py-3 text-center text-slate-500">Sin cobros registrados.</td></tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        @if($sale->notes)
                                            <p class="mt-3 rounded-lg border border-slate-200/80 bg-white px-3 py-2 text-xs text-slate-600">
                                                <span class="font-semibold text-slate-800">Nota:</span> {{ $sale->notes }}
                                            </p>
                                        @endif
                                    </details>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </details>
            @empty
                <div class="ui-form-card text-center text-sm font-medium text-slate-500">
                    No hay ventas pendientes por cobrar.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>

