<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 min-w-0 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
            <div>
                <p class="ui-section-label">Punto de venta</p>
                <h2 class="mt-1 min-w-0 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Venta #{{ $sale->id }}</h2>
            </div>
            <a href="{{ route('sales.index') }}" class="inline-flex shrink-0 items-center justify-center rounded-2xl border border-slate-200/90 bg-white/90 px-5 py-2.5 text-sm font-bold text-indigo-600 shadow-soft backdrop-blur-sm transition hover:bg-white sm:py-2.5 w-full sm:w-auto touch-manipulation">Volver</a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="ui-form-card !p-5 sm:!p-8">
                <dl class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-gray-500">Fecha / Hora</dt>
                        <dd>{{ $sale->sold_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Tipo</dt>
                        <dd>
                            @if($sale->sale_kind->value === 'credit')
                                <span class="text-amber-600">Por cobrar</span> — {{ $sale->credit_status?->value ?? '-' }}
                            @else
                                <span class="text-green-600">Al contado</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Vendido por</dt>
                        <dd>{{ $sale->soldBy->name }}</dd>
                    </div>
                    @if($sale->sale_kind->value === 'credit')
                    <div>
                        <dt class="text-sm text-gray-500">Cliente</dt>
                        <dd>{{ $sale->customer_name }} — {{ $sale->customer_phone }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm text-gray-500">Total</dt>
                        <dd class="font-medium">{{ number_format($sale->total, 2) }} Bs</dd>
                    </div>
                </dl>

                <h3 class="font-medium mb-2">Productos</h3>
                <div class="-mx-4 mb-6 overflow-x-auto overscroll-x-contain sm:mx-0">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                            <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                            <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">Precio unit.</th>
                            <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($sale->lines as $line)
                        <tr>
                            <td class="px-6 py-3">{{ $line->product->name }}</td>
                            <td class="px-6 py-3 text-right">{{ $line->quantityLabel() }}</td>
                            <td class="px-6 py-3 text-right">{{ number_format($line->unit_price, 2) }} Bs</td>
                            <td class="px-6 py-3 text-right font-medium">{{ number_format($line->line_total, 2) }} Bs</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

                @if($sale->payments->isNotEmpty())
                <h3 class="font-medium mb-2">Pagos (efectivo / QR)</h3>
                <div class="-mx-4 mb-6 overflow-x-auto overscroll-x-contain sm:mx-0">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                            <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Registrado por</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($sale->payments as $p)
                        <tr>
                            <td class="px-6 py-3">{{ $p->method->value === 'cash' ? 'Efectivo' : 'QR' }}</td>
                            <td class="px-6 py-3 text-right">{{ number_format($p->amount, 2) }} Bs</td>
                            <td class="px-6 py-3">{{ $p->recordedBy->name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                @endif

                @if($sale->specialCashOperations->isNotEmpty())
                <h3 class="font-medium mb-2">Operación especial de caja (vuelto por QR)</h3>
                <div class="space-y-3 rounded-2xl border border-indigo-200/70 bg-gradient-to-br from-indigo-50/90 to-violet-50/60 p-5 text-sm shadow-soft ring-1 ring-indigo-900/5">
                    @foreach ($sale->specialCashOperations as $sco)
                        <div class="border-b border-indigo-100 pb-3 last:border-0 last:pb-0">
                            <p class="font-medium text-gray-900">Registro #{{ $sco->id }} · {{ $sco->created_at->format('d/m/Y H:i') }}</p>
                            <p class="mt-1 text-gray-700">Pagado por QR (suma en venta): <strong>{{ number_format($sco->qr_amount, 2) }} Bs</strong> — Vuelto en efectivo entregado: <strong>{{ number_format($sco->cash_out, 2) }} Bs</strong></p>
                            @if ($sco->description)
                                <p class="mt-1 text-xs text-gray-600">{{ $sco->description }}</p>
                            @endif
                            <p class="mt-1 text-xs text-gray-500">Registró: {{ $sco->performedBy->name }}</p>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
