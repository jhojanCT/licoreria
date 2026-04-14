<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="ui-section-label">Almacén</p>
            <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Nueva compra</h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-5 rounded-2xl border border-rose-200/80 bg-rose-50/95 p-4 text-sm text-rose-900 shadow-soft ring-1 ring-rose-900/5">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="ui-form-card">
                <form method="post" action="{{ route('purchases.store') }}" id="purchase-form">
                    @csrf
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="supplier_id" value="Proveedor" />
                                <select id="supplier_id" name="supplier_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Seleccionar</option>
                                    @foreach ($suppliers as $s)
                                        <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="received_at" value="Fecha entrada" />
                                <x-text-input id="received_at" name="received_at" type="datetime-local" value="{{ old('received_at', now()->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full" required />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="payment_method" value="Método de pago de la compra" />
                            <select id="payment_method" name="payment_method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="cash" {{ old('payment_method', 'cash') === 'cash' ? 'selected' : '' }}>Efectivo</option>
                                <option value="qr" {{ old('payment_method') === 'qr' ? 'selected' : '' }}>QR</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="notes" value="Notas" />
                            <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="flex justify-between items-center mb-2">
                            <x-input-label value="Líneas de compra" />
                            <button type="button" onclick="addLine()" class="text-sm text-indigo-600 hover:text-indigo-900">+ Agregar línea</button>
                        </div>
                        <div id="lines-container" class="space-y-2">
                            <div class="line-row grid grid-cols-12 gap-2 items-end p-2 bg-gray-50 rounded">
                                <div class="col-span-6">
                                    <label class="block text-xs text-gray-500">Producto</label>
                                    <select name="lines[0][product_id]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        <option value="">Seleccionar</option>
                                        @foreach ($products as $p)
                                            <option value="{{ $p->id }}" data-price="{{ $p->default_sale_price }}">{{ $p->name }}@if($p->isDualUnitProduct()) · stock en cigarros @endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500">Cantidad</label>
                                    <input type="number" name="lines[0][quantity]" step="0.001" min="0.001" required class="purchase-qty mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500">Precio unit. compra</label>
                                    <input type="number" name="lines[0][unit_purchase_price]" step="0.01" min="0" required class="purchase-unit mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                </div>
                                <div class="col-span-1">
                                    <button type="button" onclick="removeLine(this)" class="text-red-600 hover:text-red-800 text-sm">Quitar</button>
                                </div>
                            </div>
                        </div>
                        <p id="purchase-units-hint" class="mt-3 hidden text-xs leading-relaxed text-amber-900/90"></p>
                        <div id="purchase-total-banner" class="mt-4 rounded-xl border-2 border-emerald-200 bg-emerald-50/90 px-4 py-4 shadow-sm">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="text-sm font-semibold uppercase tracking-wide text-emerald-950">Total estimado de la compra</span>
                                <span id="purchase-total-display" class="text-2xl font-bold tabular-nums text-emerald-900">0,00 Bs</span>
                            </div>
                            <p class="mt-1 text-xs text-emerald-900/80">Suma de cantidad × precio unitario de cada línea (coincide con el total guardado).</p>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-2">
                        <x-primary-button>Guardar compra</x-primary-button>
                        <a href="{{ route('purchases.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let lineIndex = 1;
        const products = @json($products->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'default_sale_price' => (float) $p->default_sale_price,
            'units_per_pack' => $p->units_per_pack,
        ]));

        function formatMoneyBs(n) {
            return (Math.round(n * 100) / 100).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function purchaseLinesTotal() {
            let t = 0;
            document.querySelectorAll('.line-row').forEach(row => {
                const q = parseFloat(row.querySelector('.purchase-qty')?.value) || 0;
                const u = parseFloat(row.querySelector('.purchase-unit')?.value) || 0;
                t += q * u;
            });
            return Math.round(t * 100) / 100;
        }

        function updatePurchaseTotalUi() {
            const el = document.getElementById('purchase-total-display');
            if (el) el.textContent = formatMoneyBs(purchaseLinesTotal()) + ' Bs';
        }

        function updatePurchaseDualHints() {
            const hint = document.getElementById('purchase-units-hint');
            if (!hint) return;
            const parts = [];
            document.querySelectorAll('.line-row').forEach(row => {
                const sel = row.querySelector('select[name^="lines"]');
                const pid = sel?.value;
                const p = products.find(x => String(x.id) === String(pid));
                if (p && p.units_per_pack) {
                    parts.push(`${p.name}: la cantidad es en cigarros (${p.units_per_pack} por cajetilla).`);
                }
            });
            if (parts.length) {
                hint.textContent = parts.join(' ');
                hint.classList.remove('hidden');
            } else {
                hint.classList.add('hidden');
            }
        }

        function addLine() {
            const html = `
                <div class="line-row grid grid-cols-12 gap-2 items-end p-2 bg-gray-50 rounded">
                    <div class="col-span-6">
                        <label class="block text-xs text-gray-500">Producto</label>
                        <select name="lines[${lineIndex}][product_id]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="">Seleccionar</option>
                            ${products.map(p => `<option value="${p.id}" data-price="${p.default_sale_price}">${p.name}${p.units_per_pack ? ' · stock en cigarros' : ''}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs text-gray-500">Cantidad</label>
                        <input type="number" name="lines[${lineIndex}][quantity]" step="0.001" min="0.001" required class="purchase-qty mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs text-gray-500">Precio unit. compra</label>
                        <input type="number" name="lines[${lineIndex}][unit_purchase_price]" step="0.01" min="0" required class="purchase-unit mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div class="col-span-1">
                        <button type="button" onclick="removeLine(this)" class="text-red-600 hover:text-red-800 text-sm">Quitar</button>
                    </div>
                </div>
            `;
            document.getElementById('lines-container').insertAdjacentHTML('beforeend', html);
            lineIndex++;
            updatePurchaseTotalUi();
        }

        function removeLine(btn) {
            const rows = document.querySelectorAll('.line-row');
            if (rows.length > 1) btn.closest('.line-row').remove();
            updatePurchaseTotalUi();
        }

        document.getElementById('purchase-form').addEventListener('input', function() {
            updatePurchaseTotalUi();
        });
        document.getElementById('purchase-form').addEventListener('change', function() {
            updatePurchaseDualHints();
            updatePurchaseTotalUi();
        });
        updatePurchaseDualHints();
        updatePurchaseTotalUi();
    </script>
</x-app-layout>
