<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="ui-section-label">Configuración</p>
            <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Primera configuración de tienda</h2>
            <p class="mt-1 text-sm font-medium text-slate-500">Este paso se realiza solo una vez.</p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('layouts.partials.flash-success')

            <div class="overflow-hidden rounded-3xl border border-indigo-200/70 bg-gradient-to-br from-indigo-50/95 to-violet-50/85 p-5 text-sm text-indigo-950 shadow-soft ring-1 ring-indigo-900/5">
                <p class="font-semibold">Antes de empezar a vender, configura:</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    <li>Monto con el que abre la caja.</li>
                    <li>Saldo inicial disponible en QR.</li>
                    <li>Stock que ya tenía la tienda físicamente.</li>
                </ul>
            </div>

            <div class="ui-form-card">
                <form method="POST" action="{{ route('initial-setup.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="opening_cash" value="Caja inicial (Bs)" />
                            <x-text-input id="opening_cash" name="opening_cash" type="number" step="0.01" min="0" value="{{ old('opening_cash', 0) }}" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('opening_cash')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="opening_qr" value="Saldo inicial QR (Bs)" />
                            <x-text-input id="opening_qr" name="opening_qr" type="number" step="0.01" min="0" value="{{ old('opening_qr', 0) }}" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('opening_qr')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between gap-2">
                            <x-input-label value="Stock inicial por producto" />
                            <div class="flex items-center gap-2">
                                <p class="text-xs text-slate-500">Carga solo los productos que ya tienes en tienda</p>
                                <a href="{{ route('products.create') }}" class="inline-flex min-h-[36px] items-center justify-center rounded-lg border border-slate-200/90 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-soft transition hover:bg-slate-50">
                                    + Agregar producto
                                </a>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('stocks')" class="mb-2" />

                        @if ($products->isEmpty())
                            <div class="rounded-2xl border border-amber-200/80 bg-amber-50/90 p-4 text-sm text-amber-900 shadow-soft">
                                <p class="font-semibold">Aún no tienes productos creados.</p>
                                <p class="mt-1">Primero crea los productos de tu tienda y luego vuelve aquí para cargar su stock inicial.</p>
                                <a href="{{ route('products.create') }}" class="mt-3 inline-flex min-h-[42px] items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2 text-sm font-bold text-white shadow-soft transition hover:brightness-110">
                                    Crear primer producto
                                </a>
                            </div>
                        @else
                            <div class="ui-table-wrap">
                                <div class="max-h-[30rem] overflow-auto">
                                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                                        <thead class="bg-slate-50/95">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Producto</th>
                                                <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Cantidad inicial</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 bg-white/70">
                                            @foreach ($products as $index => $product)
                                                <tr>
                                                    <td class="px-4 py-3">
                                                        <div class="font-semibold text-slate-900">{{ $product->name }}</div>
                                                        <input type="hidden" name="stocks[{{ $index }}][product_id]" value="{{ $product->id }}">
                                                    </td>
                                                    <td class="px-4 py-3 text-right">
                                                        <input
                                                            type="number"
                                                            name="stocks[{{ $index }}][quantity]"
                                                            value="{{ old('stocks.'.$index.'.quantity', number_format((float) $product->stock_quantity, 3, '.', '')) }}"
                                                            min="0"
                                                            step="0.001"
                                                            class="w-36 max-w-full text-right"
                                                        >
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <x-primary-button>Guardar configuración inicial</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

