<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="ui-section-label">Catálogo</p>
            <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Editar producto</h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <div class="ui-form-card">
                <form method="post" action="{{ route('products.update', $product) }}">
                    @csrf
                    @method('patch')
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="name" value="Nombre" />
                            <x-text-input id="name" name="name" value="{{ old('name', $product->name) }}" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="min_stock_alert" value="Stock mínimo alerta" />
                                <x-text-input id="min_stock_alert" name="min_stock_alert" type="number" step="0.01" min="0" value="{{ old('min_stock_alert', $product->min_stock_alert) }}" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('min_stock_alert')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="default_sale_price" value="Precio de venta — cajetilla completa (Bs)" />
                                <x-text-input id="default_sale_price" name="default_sale_price" type="number" step="0.01" min="0" value="{{ old('default_sale_price', $product->default_sale_price) }}" class="mt-1 block w-full" required />
                                <p class="mt-1 text-xs text-slate-500">En venta dual (cigarros), este es el precio de la cajetilla.</p>
                                <x-input-error :messages="$errors->get('default_sale_price')" class="mt-1" />
                            </div>
                        </div>
                        <div class="rounded-xl border border-slate-200/80 bg-slate-50/60 p-4 space-y-3">
                            <p class="text-sm font-semibold text-slate-800">Cigarros: cajetilla y unidad (opcional)</p>
                            <p class="text-xs text-slate-600 leading-relaxed">Si indicas cuántos cigarros trae una cajetilla, el stock se cuenta en cigarros y en la venta podrás elegir cajetilla o unidad. El mínimo de alerta también es en cigarros.</p>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="units_per_pack" value="Cigarros por cajetilla" />
                                    <x-text-input id="units_per_pack" name="units_per_pack" type="number" min="1" max="1000" value="{{ old('units_per_pack', $product->units_per_pack) }}" class="mt-1 block w-full" placeholder="Vacío = producto normal" />
                                    <x-input-error :messages="$errors->get('units_per_pack')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="price_per_single_unit" value="Precio por cigarro suelto (Bs)" />
                                    <x-text-input id="price_per_single_unit" name="price_per_single_unit" type="number" step="0.01" min="0" value="{{ old('price_per_single_unit', $product->price_per_single_unit) }}" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('price_per_single_unit')" class="mt-1" />
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded border-gray-300">
                                <span class="ml-2 text-sm text-gray-600">Activo</span>
                            </label>
                        </div>
                    </div>
                    <div class="mt-6 flex gap-2">
                        <x-primary-button>Actualizar</x-primary-button>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
