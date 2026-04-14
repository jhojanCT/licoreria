@php
    $t = $summary['totals'];
@endphp

<div class="min-w-0 space-y-8 text-gray-800">
    <div class="rounded-3xl border border-indigo-200/60 bg-gradient-to-br from-indigo-50/95 to-violet-50/80 p-5 text-sm leading-relaxed text-indigo-950 shadow-soft ring-1 ring-indigo-900/5">
        <p class="font-semibold text-indigo-900">Cómo se arma el efectivo esperado en caja</p>
        <ul class="mt-2 list-inside list-disc space-y-1">
            <li><strong>Efectivo cobrado hoy:</strong> suma de todos los cobros en efectivo registrados ese día (incluye ventas del día y cobros de crédito antiguos).</li>
            <li><strong>QR cobrado hoy:</strong> suma de cobros por QR registrados ese día (incluye cobros de crédito).</li>
            <li><strong>Compras pagadas:</strong> si una compra se pagó en efectivo, descuenta de caja; si se pagó por QR, descuenta del saldo QR.</li>
            <li><strong>Operaciones especiales de caja:</strong> entradas de efectivo (p. ej. cambio de billete) suman; salidas de efectivo (p. ej. vuelto por QR o billetes chicos entregados) restan.</li>
            <li><strong>Fórmula:</strong> efectivo esperado = efectivo ventas + efectivo que entró en operaciones especiales − efectivo que salió en operaciones especiales.</li>
            <li><strong>Compras:</strong> se listan por <em>fecha de recepción</em> (cuando ingresó la mercadería). El costo total es informativo para el día; el pago al proveedor no se modela aquí como movimiento de caja.</li>
        </ul>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-gray-900">Resumen del día ({{ \Carbon\Carbon::parse($summary['business_date'])->format('d/m/Y') }})</h3>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Ventas registradas</p>
                <p class="mt-1 text-2xl font-semibold">{{ $t['sales_count'] }}</p>
                <p class="mt-1 text-xs text-gray-600">{{ $t['cash_sales_count'] }} al contado · {{ $t['credit_sales_count'] }} a crédito</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total facturado (ventas)</p>
                <p class="mt-1 text-2xl font-semibold">{{ number_format($t['sales_total'], 2) }} Bs</p>
                <p class="mt-1 text-xs text-gray-600">Subtotal sumado: {{ number_format($t['sales_subtotal'], 2) }} Bs</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Cobros del día</p>
                <p class="mt-1 text-sm">Efectivo: <span class="font-semibold">{{ number_format($summary['cash_from_sales'], 2) }} Bs</span></p>
                <p class="mt-1 text-sm">QR: <span class="font-semibold">{{ number_format($summary['qr_total'], 2) }} Bs</span></p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Efectivo esperado acumulado</p>
                <p class="mt-1 text-2xl font-semibold text-indigo-700">{{ number_format($summary['cash_balance_expected'], 2) }} Bs</p>
                <p class="mt-1 text-xs text-gray-600">Caja inicial + cobros efectivo + ingresos especiales − salidas especiales − compras efectivo.</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Compras recibidas</p>
                <p class="mt-1 text-2xl font-semibold">{{ $t['purchases_count'] }}</p>
                <p class="mt-1 text-sm text-gray-600">Costo total: <span class="font-medium">{{ number_format($t['purchases_total_cost'], 2) }} Bs</span></p>
                <p class="text-xs text-gray-500">{{ $t['purchase_lines_count'] }} líneas · efectivo {{ number_format($t['purchases_cash_total'], 2) }} · QR {{ number_format($t['purchases_qr_total'], 2) }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Operaciones especiales</p>
                <p class="mt-1 text-2xl font-semibold">{{ $t['special_operations_count'] }}</p>
                <p class="mt-1 text-xs text-gray-600">Cambios de billete, vuelto por QR, etc.</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Cobros de crédito</p>
                <p class="mt-1 text-2xl font-semibold">{{ $t['credit_collections_count'] }}</p>
                <p class="mt-1 text-xs text-gray-600">Total cobrado: {{ number_format($t['credit_collections_total'], 2) }} Bs</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">QR esperado acumulado</p>
                <p class="mt-1 text-2xl font-semibold text-sky-700">{{ number_format($summary['qr_balance_expected'], 2) }} Bs</p>
                <p class="mt-1 text-xs text-gray-600">QR inicial + cobros QR − compras pagadas por QR.</p>
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-gray-900">Cobros de ventas por cobrar (hoy)</h3>
        <p class="mt-1 text-sm text-gray-600">Aquí se muestran los abonos/cancelaciones de deudas registrados hoy, aunque la venta original sea de otra fecha.</p>
        <div class="mt-3 overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Hora</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Venta</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Cliente</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Método</th>
                        <th class="px-3 py-2 text-right font-medium text-gray-700">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($summary['credit_collections'] as $payment)
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($payment->created_at)->format('H:i:s') }}</td>
                            <td class="px-3 py-2">#{{ $payment->sale_id }}</td>
                            <td class="px-3 py-2">{{ $payment->customer_name ?: '—' }} @if($payment->customer_phone)· {{ $payment->customer_phone }}@endif</td>
                            <td class="px-3 py-2">{{ $payment->method === 'cash' ? 'Efectivo' : 'QR' }}</td>
                            <td class="px-3 py-2 text-right font-medium">{{ number_format($payment->amount, 2) }} Bs</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-gray-500">No hubo cobros de crédito hoy.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-gray-900">Detalle de ventas</h3>
        <p class="mt-1 text-sm text-gray-600">Cada fila es una venta del día con totales y forma de cobro. Despliega para ver productos vendidos.</p>
        <div class="mt-3 overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">#</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Hora</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Vendedor</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Tipo</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Cliente</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Crédito</th>
                        <th class="px-3 py-2 text-right font-medium text-gray-700">Total</th>
                        <th class="px-3 py-2 text-right font-medium text-gray-700">Efectivo</th>
                        <th class="px-3 py-2 text-right font-medium text-gray-700">QR</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($summary['sales'] as $sale)
                        @php
                            $payCash = $sale->payments->where('method', \App\Enums\PaymentMethod::Cash)->sum('amount');
                            $payQr = $sale->payments->where('method', \App\Enums\PaymentMethod::Qr)->sum('amount');
                            $kindLabel = $sale->sale_kind === \App\Enums\SaleKind::Credit ? 'Crédito' : 'Contado';
                            $creditLabel = match ($sale->credit_status) {
                                \App\Enums\CreditStatus::Pending => 'Por cobrar',
                                \App\Enums\CreditStatus::Partial => 'Parcial',
                                \App\Enums\CreditStatus::Paid => 'Pagado',
                                default => '—',
                            };
                        @endphp
                        <tr class="align-top">
                            <td class="px-3 py-2 font-mono text-xs text-gray-600">{{ $sale->id }}</td>
                            <td class="px-3 py-2 whitespace-nowrap">{{ $sale->sold_at->format('H:i') }}</td>
                            <td class="px-3 py-2">{{ $sale->soldBy?->name ?? '—' }}</td>
                            <td class="px-3 py-2">{{ $kindLabel }}</td>
                            <td class="px-3 py-2">{{ $sale->customer_name ?: '—' }}</td>
                            <td class="px-3 py-2 text-xs">{{ $sale->sale_kind === \App\Enums\SaleKind::Credit ? $creditLabel : '—' }}</td>
                            <td class="px-3 py-2 text-right font-medium">{{ number_format($sale->total, 2) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($payCash, 2) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($payQr, 2) }}</td>
                        </tr>
                        <tr class="bg-gray-50/90">
                            <td colspan="9" class="px-3 py-2">
                                <details class="group">
                                    <summary class="cursor-pointer text-xs font-medium text-indigo-600 hover:text-indigo-800">Ver productos ({{ $sale->lines->count() }})</summary>
                                    <table class="mt-2 w-full text-xs">
                                        <thead>
                                            <tr class="text-left text-gray-500">
                                                <th class="py-1 pr-2">Producto</th>
                                                <th class="py-1 pr-2">Cant.</th>
                                                <th class="py-1 pr-2">P. unit.</th>
                                                <th class="py-1 text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sale->lines as $line)
                                                <tr>
                                                    <td class="py-1 pr-2">{{ $line->product?->name ?? '—' }}</td>
                                                    <td class="py-1 pr-2">{{ rtrim(rtrim(number_format((float) $line->quantity, 3, '.', ''), '0'), '.') }}</td>
                                                    <td class="py-1 pr-2">{{ number_format($line->unit_price, 2) }}</td>
                                                    <td class="py-1 text-right">{{ number_format($line->line_total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if ($sale->payments->isNotEmpty())
                                        <p class="mt-2 text-xs font-medium text-gray-600">Pagos registrados</p>
                                        <ul class="mt-1 list-inside list-disc text-xs text-gray-600">
                                            @foreach ($sale->payments as $p)
                                                <li>
                                                    {{ $p->method === \App\Enums\PaymentMethod::Cash ? 'Efectivo' : 'QR' }}:
                                                    {{ number_format($p->amount, 2) }} Bs
                                                    @if ($p->recordedBy)
                                                        ({{ $p->recordedBy->name }})
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if ($sale->notes)
                                        <p class="mt-2 text-xs text-gray-500"><span class="font-medium">Nota venta:</span> {{ $sale->notes }}</p>
                                    @endif
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-3 py-8 text-center text-gray-500">No hay ventas este día.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-gray-900">Detalle de compras recibidas</h3>
        <p class="mt-1 text-sm text-gray-600">Compras cuya <strong>fecha de recepción</strong> coincide con el día del cierre.</p>
        <div class="mt-3 overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">#</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Hora</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Proveedor</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Recibió</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Pago</th>
                        <th class="px-3 py-2 text-right font-medium text-gray-700">Total costo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($summary['purchases'] as $purchase)
                        <tr class="align-top">
                            <td class="px-3 py-2 font-mono text-xs text-gray-600">{{ $purchase->id }}</td>
                            <td class="px-3 py-2 whitespace-nowrap">{{ $purchase->received_at->format('H:i') }}</td>
                            <td class="px-3 py-2">{{ $purchase->supplier?->name ?? '—' }}</td>
                            <td class="px-3 py-2">{{ $purchase->receivedBy?->name ?? '—' }}</td>
                            <td class="px-3 py-2">{{ ($purchase->payment_method ?? 'cash') === 'qr' ? 'QR' : 'Efectivo' }}</td>
                            <td class="px-3 py-2 text-right font-medium">{{ number_format($purchase->total_cost, 2) }}</td>
                        </tr>
                        <tr class="bg-gray-50/90">
                            <td colspan="6" class="px-3 py-2">
                                <details>
                                    <summary class="cursor-pointer text-xs font-medium text-indigo-600 hover:text-indigo-800">Ver líneas ({{ $purchase->lines->count() }})</summary>
                                    <table class="mt-2 w-full text-xs">
                                        <thead>
                                            <tr class="text-left text-gray-500">
                                                <th class="py-1 pr-2">Producto</th>
                                                <th class="py-1 pr-2">Cant.</th>
                                                <th class="py-1 pr-2">Costo unit.</th>
                                                <th class="py-1 text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($purchase->lines as $line)
                                                <tr>
                                                    <td class="py-1 pr-2">{{ $line->product?->name ?? '—' }}</td>
                                                    <td class="py-1 pr-2">{{ rtrim(rtrim(number_format((float) $line->quantity, 3, '.', ''), '0'), '.') }}</td>
                                                    <td class="py-1 pr-2">{{ number_format($line->unit_purchase_price, 2) }}</td>
                                                    <td class="py-1 text-right">{{ number_format($line->line_total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if ($purchase->notes)
                                        <p class="mt-2 text-xs text-gray-500"><span class="font-medium">Nota:</span> {{ $purchase->notes }}</p>
                                    @endif
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-gray-500">No hay compras recibidas este día.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-gray-900">Operaciones especiales de caja</h3>
        <p class="mt-1 text-sm text-gray-600">Incluye <strong>cambios de billete</strong> y <strong>vueltos por QR en ventas</strong> (registrados al cerrar la venta). Fecha de registro = día del cierre. Despliega cada fila para ver el detalle.</p>
        <div class="mt-3 overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">#</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Hora</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Tipo</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Usuario</th>
                        <th class="px-3 py-2 text-right font-medium text-gray-700">Efectivo in</th>
                        <th class="px-3 py-2 text-right font-medium text-gray-700">Efectivo out</th>
                        <th class="px-3 py-2 text-right font-medium text-gray-700">Monto QR</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700 min-w-[10rem]">Cortes / vuelto QR</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($summary['special_operations'] as $op)
                        @php
                            $typeLabel = match ($op->operation_type) {
                                \App\Enums\SpecialCashOperationType::DepositChange => 'Vuelto por QR (venta)',
                                \App\Enums\SpecialCashOperationType::BillBreak => 'Cambio de billete',
                                default => $op->operation_type->value ?? '—',
                            };
                        @endphp
                        <tr class="align-top">
                            <td class="px-3 py-2 font-mono text-xs text-gray-600">{{ $op->id }}</td>
                            <td class="px-3 py-2 whitespace-nowrap">{{ $op->created_at->format('H:i:s') }}</td>
                            <td class="px-3 py-2">{{ $typeLabel }}</td>
                            <td class="px-3 py-2">{{ $op->performedBy?->name ?? '—' }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($op->cash_in, 2) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($op->cash_out, 2) }}</td>
                            <td class="px-3 py-2 text-right">{{ (float) $op->qr_amount > 0 ? number_format($op->qr_amount, 2) : '—' }}</td>
                            <td class="px-3 py-2 text-xs text-gray-800">
                                @if ($op->isBillBreak())
                                    @if ($op->formattedBillCortes())
                                        <span class="font-medium">{{ $op->formattedBillCortes() }}</span>
                                    @else
                                        <span class="text-amber-800">Sin desglose</span>
                                    @endif
                                @elseif ($op->operation_type === \App\Enums\SpecialCashOperationType::DepositChange)
                                    <span class="text-gray-700">QR {{ number_format($op->qr_amount, 2) }} → vuelto {{ number_format($op->cash_out, 2) }}</span>
                                    @if ($op->sale_id)
                                        <span class="block text-gray-500">Venta #{{ $op->sale_id }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                        <tr class="bg-gray-50/90">
                            <td colspan="8" class="px-3 py-2">
                                <details class="group">
                                    <summary class="cursor-pointer text-xs font-medium text-indigo-600 hover:text-indigo-800">Ver detalles de la operación #{{ $op->id }}</summary>
                                    @include('special-cash-operations.partials.detail-body', ['op' => $op])
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-8 text-center text-gray-500">No hubo operaciones especiales este día.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
