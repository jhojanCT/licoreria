{{-- Cuerpo del detalle de una operación especial (reutilizable en cierre de caja y listado). Requiere $op con relaciones performedBy y sale si aplica. --}}
@php
    $typeHelp = match ($op->operation_type) {
        \App\Enums\SpecialCashOperationType::DepositChange => 'El cliente pagó (o depositó) con QR un monto mayor al de la venta; desde caja se entregó la diferencia en efectivo como vuelto.',
        \App\Enums\SpecialCashOperationType::BillBreak => 'Se recibió efectivo (típicamente un billete grande) y se entregó el mismo valor total en billetes o monedas más pequeñas.',
        default => 'Operación registrada en el sistema de caja especial.',
    };
@endphp
<div class="mt-3 space-y-4 border-l-2 border-indigo-200 pl-4 text-sm text-gray-800">
    <dl class="grid gap-2 sm:grid-cols-2">
        <div>
            <dt class="text-xs font-medium uppercase text-gray-500">Registro en sistema</dt>
            <dd class="mt-0.5">#{{ $op->id }} · {{ $op->created_at->format('d/m/Y H:i:s') }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase text-gray-500">Código de tipo</dt>
            <dd class="mt-0.5 font-mono text-xs">{{ $op->operation_type->value }}</dd>
        </div>
    </dl>

    @if ($op->operation_type === \App\Enums\SpecialCashOperationType::DepositChange)
        <div class="rounded-lg border border-indigo-300 bg-indigo-50 p-4 space-y-2">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-950">Vuelto por QR (registrado con la venta)</p>
            @if ($op->sale)
                <ul class="list-inside list-disc space-y-1 text-sm text-gray-900">
                    <li><strong>Total de la venta:</strong> {{ number_format($op->sale->total, 2) }} Bs</li>
                    <li><strong>Total pagado por QR en la venta:</strong> {{ number_format($op->qr_amount, 2) }} Bs</li>
                    <li><strong>Vuelto entregado en efectivo:</strong> {{ number_format($op->cash_out, 2) }} Bs</li>
                    <li><strong>Neto (QR − vuelto):</strong> {{ number_format((float) $op->qr_amount - (float) $op->cash_out, 2) }} Bs — debe coincidir con el total de la venta.</li>
                </ul>
                @can('sales.view')
                    <p class="pt-2"><a href="{{ route('sales.show', $op->sale) }}" class="text-sm font-medium text-indigo-700 underline">Abrir venta #{{ $op->sale->id }}</a></p>
                @endcan
            @else
                <p class="text-sm text-gray-800">Pagos por QR (registrado): {{ number_format($op->qr_amount, 2) }} Bs · Vuelto en efectivo: {{ number_format($op->cash_out, 2) }} Bs</p>
                @if ($op->sale_id)
                    <p class="text-xs text-gray-600">Venta #{{ $op->sale_id }} ya no está en el sistema.</p>
                @endif
            @endif
        </div>
    @endif

    @if ($op->isBillBreak())
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-900">Cortes entregados al cliente</p>
            @if ($op->hasBillBreakdown())
                <p class="mt-2 text-base font-semibold text-gray-900">{{ $op->formattedBillCortes() }}</p>
                <div class="mt-3 overflow-x-auto rounded border border-amber-100 bg-white">
                    <table class="min-w-full text-xs">
                        <thead class="bg-amber-100/80">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium text-gray-800">Denominación (Bs)</th>
                                <th class="px-3 py-2 text-right font-medium text-gray-800">Cantidad</th>
                                <th class="px-3 py-2 text-right font-medium text-gray-800">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-amber-50">
                            @php
                                $bd = $op->bill_breakdown;
                                uksort($bd, fn ($a, $b) => (float) $b <=> (float) $a);
                            @endphp
                            @foreach ($bd as $denom => $qty)
                                @php
                                    $denomF = (float) $denom;
                                    $qtyI = (int) $qty;
                                    $sub = $denomF * $qtyI;
                                @endphp
                                @if ($qtyI > 0)
                                    <tr>
                                        <td class="px-3 py-2">{{ number_format($denomF, 2) }}</td>
                                        <td class="px-3 py-2 text-right">{{ $qtyI }}</td>
                                        <td class="px-3 py-2 text-right font-medium">{{ number_format($sub, 2) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="mt-2 text-sm text-amber-950/90">No se registró el desglose de cortes al crear la operación (el campo era opcional). Para ver billete por billete en próximos cambios, completa el campo <strong>Desglose</strong> al registrar (ej. <code class="rounded bg-white px-1">5x20, 2x10</code>).</p>
            @endif
        </div>
    @endif

    <p class="text-xs leading-relaxed text-gray-600">{{ $typeHelp }}</p>
    <div>
        <p class="text-xs font-semibold uppercase text-gray-500">Montos (Bs)</p>
        <ul class="mt-1 list-inside list-disc text-sm text-gray-800">
            <li><strong>Efectivo que entró a caja:</strong> {{ number_format($op->cash_in, 2) }} (billetes/monedas recibidos en el mostrador).</li>
            <li><strong>Efectivo que salió de caja:</strong> {{ number_format($op->cash_out, 2) }} (entregado al cliente o como cambio).</li>
            <li><strong>Monto QR involucrado:</strong> {{ (float) $op->qr_amount > 0 ? number_format($op->qr_amount, 2).' Bs' : 'No aplica o 0,00 Bs.' }}</li>
        </ul>
    </div>
    <div>
        <p class="text-xs font-semibold uppercase text-gray-500">Descripción / nota</p>
        <p class="mt-1 whitespace-pre-wrap rounded bg-white px-3 py-2 text-sm text-gray-800 ring-1 ring-gray-200">{{ $op->description ?: 'Sin texto adicional.' }}</p>
    </div>
    @if ($op->sale_id && $op->operation_type !== \App\Enums\SpecialCashOperationType::DepositChange)
        <div>
            <p class="text-xs font-semibold uppercase text-gray-500">Venta vinculada</p>
            <div class="mt-1 rounded bg-white px-3 py-2 text-sm ring-1 ring-gray-200">
                @if ($op->sale)
                    <p>ID venta <span class="font-mono">#{{ $op->sale->id }}</span>
                        @can('sales.view')
                            — <a href="{{ route('sales.show', $op->sale) }}" class="text-indigo-600 hover:text-indigo-800 underline">Abrir venta</a>
                        @endcan
                    </p>
                    <p class="mt-1 text-xs text-gray-600">Fecha venta: {{ $op->sale->sold_at->format('d/m/Y H:i') }} · Total: {{ number_format($op->sale->total, 2) }} Bs</p>
                    @if ($op->sale->customer_name)
                        <p class="mt-1 text-xs text-gray-600">Cliente: {{ $op->sale->customer_name }}</p>
                    @endif
                @else
                    <p class="text-gray-600">Venta #{{ $op->sale_id }} (ya no existe en el sistema o fue eliminada).</p>
                @endif
            </div>
        </div>
    @endif
</div>
