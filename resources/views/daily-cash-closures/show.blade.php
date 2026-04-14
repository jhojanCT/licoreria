<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 min-w-0 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
            <div>
                <p class="ui-section-label">Caja</p>
                <h2 class="mt-1 min-w-0 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Cierre {{ $closure->business_date->format('d/m/Y') }}</h2>
            </div>
            <a href="{{ route('daily-cash-closures.index') }}" class="inline-flex shrink-0 items-center justify-center rounded-2xl border border-slate-200/90 bg-white/90 px-5 py-2.5 text-sm font-bold text-indigo-600 shadow-soft backdrop-blur-sm transition hover:bg-white w-full sm:w-auto touch-manipulation">Volver</a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:space-y-8 sm:px-6 lg:px-8">
            <div class="ui-form-card !p-5 sm:!p-8">
                <h3 class="text-lg font-bold text-slate-900">Datos del cierre</h3>
                <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-gray-500">Cerrado por</dt>
                        <dd>{{ $closure->closedBy->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Efectivo esperado</dt>
                        <dd class="font-medium">{{ number_format($closure->expected_cash, 2) }} Bs</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Efectivo contado</dt>
                        <dd class="font-medium">{{ number_format($closure->counted_cash, 2) }} Bs</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Diferencia</dt>
                        <dd class="font-medium {{ $closure->difference_cash != 0 ? ($closure->difference_cash > 0 ? 'text-green-600' : 'text-red-600') : '' }}">
                            {{ number_format($closure->difference_cash, 2) }} Bs
                        </dd>
                    </div>
                    @if($closure->total_qr_day)
                    <div>
                        <dt class="text-sm text-gray-500">QR esperado acumulado</dt>
                        <dd>{{ number_format($closure->total_qr_day, 2) }} Bs</dd>
                    </div>
                    @endif
                    @if($closure->notes)
                    <div class="sm:col-span-2">
                        <dt class="text-sm text-gray-500">Notas</dt>
                        <dd>{{ $closure->notes }}</dd>
                    </div>
                    @endif
                    @can('cash.close_admin')
                    @if($closure->admin_reviewed_at)
                    <div class="sm:col-span-2 pt-4 border-t">
                        <dt class="text-sm text-gray-500">Revisado por admin</dt>
                        <dd>{{ $closure->adminReviewedBy?->name ?? '-' }} el {{ $closure->admin_reviewed_at->format('d/m/Y H:i') }}</dd>
                        @if($closure->admin_adjustment)
                        <dd class="mt-1">Ajuste: {{ number_format($closure->admin_adjustment, 2) }} Bs</dd>
                        @endif
                        @if($closure->admin_notes)
                        <dd class="mt-1 text-sm text-gray-600">{{ $closure->admin_notes }}</dd>
                        @endif
                    </div>
                    @endif
                    @endcan
                </dl>
            </div>

            <div class="ui-form-card min-w-0 overflow-x-auto !p-5 sm:!p-8">
                <p class="mb-4 text-sm font-medium leading-relaxed text-slate-600">Detalle del día según los datos actuales en el sistema (ventas, compras recibidas y operaciones especiales para esa fecha).</p>
                @include('daily-cash-closures.partials.day-summary', ['summary' => $summary])
            </div>
        </div>
    </div>
</x-app-layout>
