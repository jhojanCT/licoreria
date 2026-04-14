<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="ui-section-label">Punto de venta</p>
            <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Nueva venta</h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-5 rounded-2xl border border-rose-200/80 bg-rose-50/95 p-4 text-sm text-rose-900 shadow-soft ring-1 ring-rose-900/5">
                    <ul class="list-inside list-disc space-y-1">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @if ($errors->has('sale'))
                <div class="mb-5 rounded-2xl border border-rose-200/80 bg-rose-50/95 p-4 text-sm font-medium text-rose-900 shadow-soft">{{ $errors->first('sale') }}</div>
            @endif

            <div class="ui-form-card">
                <form method="post" action="{{ route('sales.store') }}" id="sale-form">
                    @csrf
                    <div class="mb-6">
                        <x-input-label value="Tipo de venta" />
                        <div class="mt-2 flex gap-4">
                            <label class="flex items-center">
                                <input type="radio" name="sale_kind" value="cash" {{ old('sale_kind', 'cash') === 'cash' ? 'checked' : '' }} class="rounded border-gray-300">
                                <span class="ml-2">Al contado</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="sale_kind" value="credit" {{ old('sale_kind') === 'credit' ? 'checked' : '' }} class="rounded border-gray-300">
                                <span class="ml-2">Por cobrar</span>
                            </label>
                        </div>
                    </div>

                    <div id="credit-fields" class="mb-6 space-y-4" style="display:{{ old('sale_kind') === 'credit' ? 'block' : 'none' }}">
                        <div>
                            <x-input-label for="customer_id" value="Cliente frecuente" />
                            <select id="customer_id" name="customer_id" class="mt-1 block w-full">
                                <option value="">Seleccionar cliente registrado</option>
                                @foreach ($customers as $customer)
                                    <option
                                        value="{{ $customer->id }}"
                                        data-name="{{ $customer->name }}"
                                        data-phone="{{ $customer->phone }}"
                                        data-address="{{ $customer->address }}"
                                        {{ (string) old('customer_id') === (string) $customer->id ? 'selected' : '' }}
                                    >
                                        {{ $customer->name }} · {{ $customer->phone }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-slate-500">Si no existe, completa nombre y teléfono y se guardará para próximas ventas.</p>
                            <x-input-error :messages="$errors->get('customer_id')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="customer_name" value="Nombre del cliente" />
                            <x-text-input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('customer_name')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="customer_phone" value="Teléfono" />
                            <x-text-input id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('customer_phone')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="customer_address" value="Dirección (opcional)" />
                            <x-text-input id="customer_address" name="customer_address" value="{{ old('customer_address') }}" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('customer_address')" class="mt-1" />
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <x-input-label value="Productos" />
                            <button type="button" onclick="addSaleLine()" class="text-sm text-indigo-600 hover:text-indigo-900">+ Agregar</button>
                        </div>
                        <div id="sale-lines" class="space-y-2">
                            <div class="sale-line grid grid-cols-12 gap-2 items-end p-2 bg-gray-50 rounded" data-line-index="0">
                                <div class="col-span-6">
                                    <label class="block text-xs text-gray-500">Producto</label>
                                    <select name="lines[0][product_id]" required class="product-select mt-1 block w-full rounded-md border-gray-300 bg-white text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Seleccionar producto</option>
                                        @foreach ($products as $p)
                                            <option
                                                value="{{ $p->id }}"
                                                data-price="{{ $p->default_sale_price }}"
                                                data-units-pack="{{ $p->units_per_pack ?? '' }}"
                                                data-price-unit="{{ $p->price_per_single_unit ?? '' }}"
                                            >
                                                {{ $p->name }}@if($p->isDualUnitProduct()) — caj./unidad @else ({{ number_format($p->default_sale_price, 2) }} Bs) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="sale-unit-cell col-span-2 hidden">
                                    <label class="block text-xs text-gray-500">Modo</label>
                                    <select name="lines[0][sale_unit]" class="sale-unit-mode mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" disabled>
                                        <option value="each">Cigarro (unidad)</option>
                                        <option value="pack">Cajetilla</option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="sale-qty-label block text-xs text-gray-500">Cantidad</label>
                                    <input type="number" name="lines[0][quantity]" step="0.001" min="0.001" required class="sale-qty mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500">Precio unit.</label>
                                    <input type="number" name="lines[0][unit_price]" step="0.01" min="0" required class="unit-price sale-unit-price mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                </div>
                                <div class="col-span-1"><button type="button" onclick="removeSaleLine(this)" class="text-red-600 hover:text-red-800 text-sm">Quitar</button></div>
                            </div>
                        </div>
                        <div id="sale-total-banner" class="mt-4 rounded-xl border-2 border-indigo-200 bg-indigo-50/90 px-4 py-4 shadow-sm">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="text-sm font-semibold uppercase tracking-wide text-indigo-950">Total de la venta</span>
                                <span id="sale-total-display" class="text-2xl font-bold tabular-nums text-indigo-900">0,00 Bs</span>
                            </div>
                        </div>
                    </div>

                    <div id="payment-fields" class="mb-6" style="display:{{ old('sale_kind', 'cash') === 'cash' ? 'block' : 'none' }}">
                        <div class="flex justify-between items-center mb-2">
                            <x-input-label value="Pago (efectivo / QR)" />
                            <button type="button" onclick="addPayment()" class="text-sm text-indigo-600 hover:text-indigo-900">+ Agregar</button>
                        </div>
                        <div id="payments-container" class="space-y-2">
                            <div class="payment-row flex gap-2 items-end">
                                <div class="flex-1">
                                    <label class="block text-xs text-gray-500">Método</label>
                                    <select name="payments[0][method]" class="payment-method mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        <option value="cash">Efectivo</option>
                                        <option value="qr">QR</option>
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-xs text-gray-500">Monto (Bs)</label>
                                    <input type="number" name="payments[0][amount]" step="0.01" min="0" required class="payment-amount mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                </div>
                                <div><button type="button" onclick="removePayment(this)" class="text-red-600 hover:text-red-800 text-sm">Quitar</button></div>
                            </div>
                        </div>
                        <div id="payments-total-banner" class="mt-3 rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 text-sm text-gray-700">
                            Total cobrado: <strong id="payments-total-display" class="tabular-nums text-gray-900">0,00</strong> Bs
                            <span id="payments-vs-sale" class="ml-2 text-gray-500"></span>
                        </div>

                        <div id="qr-change-block" class="mt-4 hidden rounded-lg border border-indigo-200 bg-indigo-50/80 p-4 space-y-3">
                            <p class="text-sm font-semibold text-indigo-950">Pago mayor al total (vuelto en efectivo)</p>
                            <p class="text-xs text-indigo-900/90 leading-relaxed">Si el cliente pagó <strong>más</strong> por QR (o la suma de pagos supera la venta) y le devolviste la diferencia en efectivo desde caja, completa el vuelto. El sistema registrará también la operación especial para el cierre.</p>
                            <p id="qr-change-summary" class="text-xs font-medium text-gray-800"></p>
                            <div>
                                <label for="cash_change_delivered" class="block text-xs font-medium text-gray-700">Vuelto en efectivo entregado (Bs)</label>
                                <input type="number" name="cash_change_delivered" id="cash_change_delivered" step="0.01" min="0" value="{{ old('cash_change_delivered') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" placeholder="0.00">
                                <x-input-error :messages="$errors->get('cash_change_delivered')" class="mt-1" />
                            </div>
                            <div>
                                <label for="cash_change_note" class="block text-xs font-medium text-gray-700">Nota (opcional)</label>
                                <input type="text" name="cash_change_note" id="cash_change_note" value="{{ old('cash_change_note') }}" maxlength="500" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" placeholder="Ej. pidió billetes chicos">
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <x-input-label for="notes" value="Notas" />
                        <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex gap-2">
                        <x-primary-button>Registrar venta</x-primary-button>
                        <a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @php
        $productsPayload = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'default_sale_price' => (float) $p->default_sale_price,
                'units_per_pack' => $p->units_per_pack,
                'price_per_single_unit' => $p->price_per_single_unit !== null ? (float) $p->price_per_single_unit : null,
            ];
        })->values();
    @endphp

    <script>
        let saleLineIdx = 1, paymentIdx = 1;
        const products = @json($productsPayload);
        const customers = @json($customers);

        document.querySelectorAll('input[name="sale_kind"]').forEach(r => {
            r.addEventListener('change', function() {
                const isCredit = this.value === 'credit';
                document.getElementById('credit-fields').style.display = isCredit ? 'block' : 'none';
                document.getElementById('payment-fields').style.display = isCredit ? 'none' : 'block';
                document.querySelectorAll('#payments-container .payment-amount').forEach(i => i.removeAttribute('required'));
                if (!isCredit) document.querySelector('#payments-container .payment-amount')?.setAttribute('required', 'required');
                updateSaleTotalsUi();
            });
        });

        const customerSelect = document.getElementById('customer_id');
        const customerNameInput = document.getElementById('customer_name');
        const customerPhoneInput = document.getElementById('customer_phone');
        const customerAddressInput = document.getElementById('customer_address');

        function syncCustomerFieldsFromSelect() {
            if (!customerSelect) return;
            const selectedId = customerSelect.value;
            if (!selectedId) return;

            const customer = customers.find(c => String(c.id) === String(selectedId));
            if (!customer) return;

            if (customerNameInput) customerNameInput.value = customer.name ?? '';
            if (customerPhoneInput) customerPhoneInput.value = customer.phone ?? '';
            if (customerAddressInput) customerAddressInput.value = customer.address ?? '';
        }

        customerSelect?.addEventListener('change', syncCustomerFieldsFromSelect);

        function productOptionHtml(p) {
            const label = p.units_per_pack
                ? `${p.name.replace(/</g, '')} — caj./unidad`
                : `${p.name.replace(/</g, '')} (${Number(p.default_sale_price).toFixed(2)} Bs)`;
            const u = p.units_per_pack ?? '';
            const pu = p.price_per_single_unit != null ? p.price_per_single_unit : '';
            return `<option value="${p.id}" data-price="${p.default_sale_price}" data-units-pack="${u}" data-price-unit="${pu}">${label}</option>`;
        }

        function syncSaleUnitRow(row) {
            if (!row) return;
            const sel = row.querySelector('.product-select');
            const opt = sel?.selectedOptions[0];
            const p = products.find(x => String(x.id) === String(opt?.value));
            const modeCell = row.querySelector('.sale-unit-cell');
            const modeSelect = row.querySelector('.sale-unit-mode');
            const qtyLabel = row.querySelector('.sale-qty-label');
            const priceInput = row.querySelector('.sale-unit-price');
            if (!p || !p.units_per_pack) {
                modeCell?.classList.add('hidden');
                if (modeSelect) {
                    modeSelect.value = '';
                    modeSelect.disabled = true;
                }
                if (qtyLabel) qtyLabel.textContent = 'Cantidad';
                if (priceInput && p) priceInput.value = Number(p.default_sale_price).toFixed(2);
                else if (priceInput) priceInput.value = '';
                return;
            }
            modeCell?.classList.remove('hidden');
            if (modeSelect) {
                modeSelect.disabled = false;
                if (!modeSelect.value || (modeSelect.value !== 'pack' && modeSelect.value !== 'each')) modeSelect.value = 'each';
            }
            const mode = modeSelect?.value || 'each';
            if (qtyLabel) qtyLabel.textContent = mode === 'pack' ? 'Cajetillas' : 'Cigarros';
            if (priceInput) {
                priceInput.value = mode === 'pack'
                    ? Number(p.default_sale_price).toFixed(2)
                    : Number(p.price_per_single_unit ?? 0).toFixed(2);
            }
        }

        function syncProductSelectState(selectEl) {
            if (!selectEl) return;
            const hasValue = Boolean(selectEl.value);
            selectEl.classList.toggle('text-slate-500', !hasValue);
            selectEl.classList.toggle('text-slate-900', hasValue);
            selectEl.classList.toggle('border-indigo-300', hasValue);
            selectEl.classList.toggle('font-semibold', hasValue);
            selectEl.classList.toggle('font-normal', !hasValue);
        }

        function addSaleLine() {
            const idx = saleLineIdx;
            const html = `
                <div class="sale-line grid grid-cols-12 gap-2 items-end p-2 bg-gray-50 rounded" data-line-index="${idx}">
                    <div class="col-span-6">
                        <label class="block text-xs text-gray-500">Producto</label>
                        <select name="lines[${idx}][product_id]" required class="product-select mt-1 block w-full rounded-md border-gray-300 bg-white text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Seleccionar producto</option>
                            ${products.map(productOptionHtml).join('')}
                        </select>
                    </div>
                    <div class="sale-unit-cell col-span-2 hidden">
                        <label class="block text-xs text-gray-500">Modo</label>
                        <select name="lines[${idx}][sale_unit]" class="sale-unit-mode mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" disabled>
                            <option value="each">Cigarro (unidad)</option>
                            <option value="pack">Cajetilla</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="sale-qty-label block text-xs text-gray-500">Cantidad</label>
                        <input type="number" name="lines[${idx}][quantity]" step="0.001" min="0.001" required class="sale-qty mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs text-gray-500">Precio unit.</label>
                        <input type="number" name="lines[${idx}][unit_price]" step="0.01" min="0" required class="unit-price sale-unit-price mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div class="col-span-1"><button type="button" onclick="removeSaleLine(this)" class="text-red-600 hover:text-red-800 text-sm">Quitar</button></div>
                </div>
            `;
            document.getElementById('sale-lines').insertAdjacentHTML('beforeend', html);
            const newRow = document.querySelector('#sale-lines .sale-line:last-child');
            syncProductSelectState(newRow?.querySelector('.product-select'));
            saleLineIdx++;
            updateSaleTotalsUi();
        }
        function removeSaleLine(btn) {
            if (document.querySelectorAll('.sale-line').length > 1) btn.closest('.sale-line').remove();
            updateSaleTotalsUi();
        }

        function addPayment() {
            const html = `
                <div class="payment-row flex gap-2 items-end">
                    <div class="flex-1">
                        <label class="block text-xs text-gray-500">Método</label>
                        <select name="payments[${paymentIdx}][method]" class="payment-method mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="cash">Efectivo</option>
                            <option value="qr">QR</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs text-gray-500">Monto (Bs)</label>
                        <input type="number" name="payments[${paymentIdx}][amount]" step="0.01" min="0" class="payment-amount mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div><button type="button" onclick="removePayment(this)" class="text-red-600 hover:text-red-800 text-sm">Quitar</button></div>
                </div>
            `;
            document.getElementById('payments-container').insertAdjacentHTML('beforeend', html);
            paymentIdx++;
            updateSaleTotalsUi();
        }
        function removePayment(btn) {
            if (document.querySelectorAll('.payment-row').length > 1) btn.closest('.payment-row').remove();
            updateSaleTotalsUi();
        }

        function formatMoneyBs(n) {
            return (Math.round(n * 100) / 100).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function saleLinesTotal() {
            let t = 0;
            document.querySelectorAll('.sale-line').forEach(row => {
                const q = parseFloat(row.querySelector('.sale-qty')?.value) || 0;
                const u = parseFloat(row.querySelector('.sale-unit-price')?.value) || 0;
                t += q * u;
            });
            return Math.round(t * 100) / 100;
        }

        function updateSaleTotalsUi() {
            const saleT = saleLinesTotal();
            const el = document.getElementById('sale-total-display');
            if (el) el.textContent = formatMoneyBs(saleT) + ' Bs';

            const paid = paymentsTotal();
            const payEl = document.getElementById('payments-total-display');
            const vsEl = document.getElementById('payments-vs-sale');
            const isCash = document.querySelector('input[name="sale_kind"]:checked')?.value === 'cash';
            if (payEl) payEl.textContent = formatMoneyBs(paid);
            if (vsEl && isCash) {
                const diff = Math.round((paid - saleT) * 100) / 100;
                if (Math.abs(diff) < 0.005) {
                    vsEl.textContent = '(igual al total)';
                    vsEl.className = 'ml-2 text-emerald-700';
                } else if (diff > 0) {
                    vsEl.textContent = '(+' + formatMoneyBs(diff) + ' sobre el total — vuelto)';
                    vsEl.className = 'ml-2 text-amber-800';
                } else {
                    vsEl.textContent = '(' + formatMoneyBs(diff) + ' — falta cobrar)';
                    vsEl.className = 'ml-2 text-red-700';
                }
            } else if (vsEl) {
                vsEl.textContent = '';
            }

            syncQrChangeBlock();
        }

        function paymentsTotal() {
            let t = 0;
            document.querySelectorAll('.payment-row').forEach(row => {
                const a = parseFloat(row.querySelector('.payment-amount')?.value) || 0;
                t += a;
            });
            return Math.round(t * 100) / 100;
        }

        function qrPaymentsTotal() {
            let t = 0;
            document.querySelectorAll('.payment-row').forEach(row => {
                const method = row.querySelector('.payment-method')?.value;
                const a = parseFloat(row.querySelector('.payment-amount')?.value) || 0;
                if (method === 'qr') t += a;
            });
            return Math.round(t * 100) / 100;
        }

        function syncQrChangeBlock() {
            const block = document.getElementById('qr-change-block');
            const summary = document.getElementById('qr-change-summary');
            const cashKind = document.querySelector('input[name="sale_kind"]:checked')?.value === 'cash';
            if (!block || !cashKind) {
                if (block) block.classList.add('hidden');
                return;
            }
            const saleT = saleLinesTotal();
            const paid = paymentsTotal();
            const qr = qrPaymentsTotal();
            const diff = Math.round((paid - saleT) * 100) / 100;
            const changeInput = document.getElementById('cash_change_delivered');

            if (diff > 0.001) {
                block.classList.remove('hidden');
                summary.textContent = `Total venta: ${formatMoneyBs(saleT)} Bs · Total cobrado: ${formatMoneyBs(paid)} Bs · Suma pagos QR: ${formatMoneyBs(qr)} Bs · Diferencia (vuelto esperado): ${formatMoneyBs(diff)} Bs`;
                if (changeInput && (changeInput.value === '' || parseFloat(changeInput.value) === 0)) {
                    changeInput.value = (Math.round(diff * 100) / 100).toFixed(2);
                }
            } else {
                block.classList.add('hidden');
                if (changeInput) changeInput.value = '';
            }
        }

        document.getElementById('sale-form').addEventListener('change', function(e) {
            if (e.target.classList.contains('product-select') || e.target.classList.contains('sale-unit-mode')) {
                const row = e.target.closest('.sale-line');
                syncSaleUnitRow(row);
            }
            if (e.target.classList.contains('product-select')) syncProductSelectState(e.target);
            updateSaleTotalsUi();
        });
        document.getElementById('sale-form').addEventListener('input', function() { updateSaleTotalsUi(); });

        syncCustomerFieldsFromSelect();
        document.querySelectorAll('.product-select').forEach(syncProductSelectState);
        document.querySelectorAll('.sale-line').forEach(syncSaleUnitRow);
        updateSaleTotalsUi();
    </script>
</x-app-layout>
