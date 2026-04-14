<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="ui-section-label">Caja</p>
            <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Registrar cierre</h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:space-y-8 sm:px-6 lg:px-8">
            <div class="ui-form-card min-w-0 !p-6 sm:!p-8">
                <h3 class="text-lg font-bold text-slate-900">Fecha del cierre</h3>
                <p class="mt-1 text-sm font-medium text-slate-600">El resumen inferior corresponde a la fecha elegida. Si cambias el día, actualiza el resumen antes de contar el efectivo.</p>
                <form method="get" action="{{ route('daily-cash-closures.create') }}" class="mt-4 flex flex-wrap items-end gap-3">
                    <div>
                        <x-input-label for="preview_date" value="Ver resumen del día" />
                        <x-text-input id="preview_date" name="date" type="date" value="{{ $businessDate }}" class="mt-1 block w-full sm:w-auto" />
                    </div>
                    <x-secondary-button type="submit">Actualizar resumen</x-secondary-button>
                </form>
            </div>

            <div class="ui-form-card min-w-0 overflow-x-auto !p-4 sm:!p-6">
                @include('daily-cash-closures.partials.day-summary', ['summary' => $summary])
            </div>

            <div class="ui-form-card min-w-0 !p-6 sm:!p-8">
                <h3 class="mb-4 text-lg font-bold text-slate-900">Cierre</h3>
                <form method="post" action="{{ route('daily-cash-closures.store') }}">
                    @csrf
                    <div class="space-y-4 max-w-xl">
                        <div>
                            <x-input-label for="business_date" value="Fecha que se registra en el cierre" />
                            <x-text-input id="business_date" name="business_date" type="date" value="{{ old('business_date', $businessDate) }}" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('business_date')" class="mt-1" />
                            <p class="mt-1 text-xs text-gray-500">Debe coincidir con el día del resumen. Los totales se recalculan al guardar.</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-sm text-gray-600">Efectivo esperado acumulado (hasta esta fecha):</p>
                            <p class="text-xl font-semibold text-indigo-800">{{ number_format($summary['cash_balance_expected'], 2) }} Bs</p>
                            <p class="mt-1 text-xs text-gray-500">QR esperado acumulado: {{ number_format($summary['qr_balance_expected'], 2) }} Bs</p>
                        </div>
                        <div>
                            <x-input-label for="counted_cash" value="Efectivo contado (Bs)" />
                            <x-text-input id="counted_cash" name="counted_cash" type="number" step="0.01" min="0" value="{{ old('counted_cash') }}" class="mt-1 block w-full" required placeholder="0.00" />
                            <x-input-error :messages="$errors->get('counted_cash')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="notes" value="Notas del cierre" />
                            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex gap-2">
                        <x-primary-button>Registrar cierre</x-primary-button>
                        <a href="{{ route('daily-cash-closures.index') }}" class="inline-flex min-h-[44px] items-center justify-center rounded-2xl border border-slate-200/90 bg-white/90 px-5 text-sm font-bold text-slate-800 shadow-soft backdrop-blur-sm transition hover:bg-white">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
