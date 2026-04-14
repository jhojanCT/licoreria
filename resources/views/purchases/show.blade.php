<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 min-w-0 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
            <div>
                <p class="ui-section-label">Almacén</p>
                <h2 class="mt-1 min-w-0 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Compra #{{ $purchase->id }}</h2>
            </div>
            <a href="{{ route('purchases.index') }}" class="inline-flex shrink-0 items-center justify-center rounded-2xl border border-slate-200/90 bg-white/90 px-5 py-2.5 text-sm font-bold text-indigo-600 shadow-soft backdrop-blur-sm transition hover:bg-white w-full sm:w-auto touch-manipulation">Volver</a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="ui-form-card !p-5 sm:!p-8">
                <dl class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-gray-500">Proveedor</dt>
                        <dd class="font-medium">{{ $purchase->supplier->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Fecha</dt>
                        <dd>{{ $purchase->received_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Recibido por</dt>
                        <dd>{{ $purchase->receivedBy->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Total</dt>
                        <dd class="font-medium">{{ number_format($purchase->total_cost, 2) }} Bs</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Pago</dt>
                        <dd class="font-medium">{{ ($purchase->payment_method ?? 'cash') === 'qr' ? 'QR' : 'Efectivo' }}</dd>
                    </div>
                    @if($purchase->notes)
                    <div class="col-span-2">
                        <dt class="text-sm text-gray-500">Notas</dt>
                        <dd>{{ $purchase->notes }}</dd>
                    </div>
                    @endif
                </dl>
                <div class="-mx-4 overflow-x-auto overscroll-x-contain sm:mx-0">
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
                        @foreach ($purchase->lines as $line)
                        <tr>
                            <td class="px-6 py-3">{{ $line->product->name }}</td>
                            <td class="px-6 py-3 text-right">{{ $line->quantity }}</td>
                            <td class="px-6 py-3 text-right">{{ number_format($line->unit_purchase_price, 2) }} Bs</td>
                            <td class="px-6 py-3 text-right font-medium">{{ number_format($line->line_total, 2) }} Bs</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
