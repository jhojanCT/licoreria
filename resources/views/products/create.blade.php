<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="ui-section-label">Catálogo</p>
            <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Nuevo producto</h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <div class="ui-form-card">
                <form method="post" action="{{ route('products.store') }}">
                    @csrf
                    <div class="space-y-4">
                        @if (!empty($setupPending))
                            <div class="rounded-2xl border border-indigo-200/70 bg-indigo-50/80 p-3 text-xs text-indigo-900">
                                Estás en configuración inicial. Aquí puedes cargar también el stock de este producto.
                            </div>
                        @endif
                        <div>
                            <x-input-label for="name" value="Nombre" />
                            <x-text-input id="name" name="name" value="{{ old('name') }}" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            @if (!empty($setupPending))
                                <div>
                                    <x-input-label for="initial_stock" value="Stock inicial (tienda)" />
                                    <x-text-input id="initial_stock" name="initial_stock" type="number" step="0.001" min="0" value="{{ old('initial_stock', 0) }}" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('initial_stock')" class="mt-1" />
                                </div>
                            @endif
                            <div>
                                <x-input-label for="min_stock_alert" value="Stock mínimo alerta" />
                                <x-text-input id="min_stock_alert" name="min_stock_alert" type="number" step="0.01" min="0" value="{{ old('min_stock_alert', 0) }}" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('min_stock_alert')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="default_sale_price" value="Precio de venta — cajetilla completa (Bs)" />
                                <x-text-input id="default_sale_price" name="default_sale_price" type="number" step="0.01" min="0" value="{{ old('default_sale_price', 0) }}" class="mt-1 block w-full" required />
                                <p class="mt-1 text-xs text-slate-500">En productos normales es el precio único. En cigarros con venta dual, es el precio de la cajetilla.</p>
                                <x-input-error :messages="$errors->get('default_sale_price')" class="mt-1" />
                            </div>
                        </div>
                        <div class="rounded-xl border border-slate-200/80 bg-slate-50/60 p-4 space-y-3">
                            <p class="text-sm font-semibold text-slate-800">Cigarros: cajetilla y unidad (opcional)</p>
                            <p class="text-xs text-slate-600 leading-relaxed">Si completas «cigarros por cajetilla», el inventario se lleva en <strong>cigarros</strong> (unidades). Podrás cobrar la cajetilla o cigarros sueltos a precios distintos. El «stock mínimo alerta» también cuenta en cigarros.</p>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="units_per_pack" value="Cigarros por cajetilla" />
                                    <x-text-input id="units_per_pack" name="units_per_pack" type="number" min="1" max="1000" value="{{ old('units_per_pack') }}" class="mt-1 block w-full" placeholder="Ej. 20" />
                                    <x-input-error :messages="$errors->get('units_per_pack')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="price_per_single_unit" value="Precio por cigarro suelto (Bs)" />
                                    <x-text-input id="price_per_single_unit" name="price_per_single_unit" type="number" step="0.01" min="0" value="{{ old('price_per_single_unit') }}" class="mt-1 block w-full" placeholder="Solo si usas venta dual" />
                                    <x-input-error :messages="$errors->get('price_per_single_unit')" class="mt-1" />
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300">
                                <span class="ml-2 text-sm text-gray-600">Activo</span>
                            </label>
                        </div>
                    </div>
                    <div class="mt-6 flex gap-2">
                        <x-primary-button>Guardar</x-primary-button>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
