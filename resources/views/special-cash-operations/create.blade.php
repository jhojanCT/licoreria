<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="ui-section-label">Caja</p>
            <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Cambio de billete</h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <div class="mb-5 rounded-2xl border border-indigo-200/70 bg-gradient-to-br from-indigo-50 to-violet-50/80 p-4 text-sm leading-relaxed text-indigo-950 shadow-soft ring-1 ring-indigo-900/5">
                El <strong>vuelto por pagar de más con QR</strong> se registra al crear la venta (aparece solo si el total cobrado supera el total de la venta).
            </div>
            <div class="ui-form-card">
                <form method="post" action="{{ route('special-cash-operations.store') }}">
                    @csrf
                    <input type="hidden" name="operation_type" value="bill_break">
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600">Cliente entrega billete grande y recibe cortes chicos (ej. 100 Bs → 5×20 Bs).</p>
                        <div>
                            <x-input-label for="cash_in" value="Billete recibido (Bs)" />
                            <x-text-input id="cash_in" name="cash_in" type="number" step="0.01" min="0" value="{{ old('cash_in') }}" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('cash_in')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="cash_out" value="Efectivo entregado en total (Bs)" />
                            <x-text-input id="cash_out" name="cash_out" type="number" step="0.01" min="0" value="{{ old('cash_out') }}" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('cash_out')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="breakdown" value="Cortes que entregaste (recomendado)" />
                            <p class="mt-1 text-xs text-gray-600">Formato: cantidad × valor, separado por comas (ej. <kbd class="rounded bg-gray-100 px-1">5x20, 2x10</kbd>).</p>
                            <x-text-input id="breakdown" name="breakdown" value="{{ old('breakdown') }}" class="mt-1 block w-full" placeholder="5x20, 2x10" />
                        </div>
                        <div>
                            <x-input-label for="description" value="Notas / descripción" />
                            <textarea id="description" name="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex gap-2">
                        <x-primary-button>Registrar</x-primary-button>
                        <a href="{{ route('special-cash-operations.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
