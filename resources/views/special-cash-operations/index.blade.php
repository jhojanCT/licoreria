<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 min-w-0 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
            <div>
                <p class="ui-section-label">Caja</p>
                <h2 class="mt-1 min-w-0 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Operaciones especiales</h2>
            </div>
            <a href="{{ route('special-cash-operations.create', ['type' => 'bill_break']) }}" class="inline-flex shrink-0 items-center justify-center rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 px-5 py-2.5 text-center text-sm font-semibold text-white shadow-soft transition hover:brightness-110 w-full sm:w-auto touch-manipulation">Nuevo cambio de billete</a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10 lg:py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @include('layouts.partials.flash-success')

            <form method="get" class="mb-6 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-end">
                <select name="type" class="min-h-[44px] w-full min-w-0 rounded-2xl border-slate-200/80 bg-white/90 text-base shadow-soft backdrop-blur-sm sm:min-w-[12rem] sm:flex-1">
                    <option value="">Todas</option>
                    <option value="deposit_change" {{ request('type') === 'deposit_change' ? 'selected' : '' }}>Vuelto por QR (venta)</option>
                    <option value="bill_break" {{ request('type') === 'bill_break' ? 'selected' : '' }}>Cambio de billete</option>
                </select>
                <input type="date" name="from" value="{{ request('from') }}" class="min-h-[44px] w-full min-w-0 rounded-2xl border-slate-200/80 bg-white/90 text-base shadow-soft backdrop-blur-sm sm:w-auto sm:min-w-[10rem]">
                <input type="date" name="to" value="{{ request('to') }}" class="min-h-[44px] w-full min-w-0 rounded-2xl border-slate-200/80 bg-white/90 text-base shadow-soft backdrop-blur-sm sm:w-auto sm:min-w-[10rem]">
                <button type="submit" class="inline-flex min-h-[44px] items-center justify-center rounded-2xl border border-slate-200/80 bg-white px-5 text-sm font-semibold text-slate-800 shadow-soft transition hover:bg-slate-50 touch-manipulation sm:shrink-0">Filtrar</button>
            </form>

            <div class="ui-table-wrap">
                <div class="-mx-px overflow-x-auto overscroll-x-contain">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/95">
                        <tr>
                            <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Fecha</th>
                            <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Tipo</th>
                            <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Realizado por</th>
                            <th class="px-4 py-3.5 text-right text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">QR / Entrada</th>
                            <th class="px-4 py-3.5 text-right text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Efectivo salida</th>
                            <th class="min-w-[11rem] px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Cortes / vuelto QR</th>
                            <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-6">Descripción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white/50">
                        @forelse ($operations as $op)
                        <tr class="align-top transition hover:bg-amber-50/30">
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-700 sm:px-6">{{ $op->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-4 text-sm sm:px-6">
                                @if($op->operation_type->value === 'deposit_change')
                                    <span class="inline-flex rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-bold text-indigo-800">Vuelto QR</span>
                                @else
                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-900">Cambio billete</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-700 sm:px-6">{{ $op->performedBy->name }}</td>
                            <td class="px-4 py-4 text-right text-sm font-semibold tabular-nums text-slate-800 sm:px-6">{{ number_format($op->qr_amount + $op->cash_in, 2) }} Bs</td>
                            <td class="px-4 py-4 text-right text-sm font-semibold tabular-nums text-slate-800 sm:px-6">{{ number_format($op->cash_out, 2) }} Bs</td>
                            <td class="px-4 py-4 text-xs text-slate-800 sm:px-6">
                                @if ($op->isBillBreak())
                                    @if ($op->formattedBillCortes())
                                        <span class="font-semibold">{{ $op->formattedBillCortes() }}</span>
                                    @else
                                        <span class="font-medium text-amber-800">Sin desglose</span>
                                    @endif
                                @elseif ($op->operation_type->value === 'deposit_change')
                                    <span class="text-slate-700">QR {{ number_format($op->qr_amount, 2) }} → vuelto {{ number_format($op->cash_out, 2) }}</span>
                                    @if ($op->sale_id)
                                        <span class="mt-0.5 block text-slate-500">Venta #{{ $op->sale_id }}</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="max-w-xs px-4 py-4 text-sm text-slate-500 sm:px-6">{{ $op->description ? \Illuminate\Support\Str::limit($op->description, 48) : '—' }}</td>
                        </tr>
                        <tr class="bg-slate-50/80">
                            <td colspan="7" class="px-4 py-3 sm:px-6">
                                <details class="group">
                                    <summary class="cursor-pointer text-xs font-bold uppercase tracking-wider text-indigo-600 hover:text-indigo-500">Ver detalles · #{{ $op->id }}</summary>
                                    @include('special-cash-operations.partials.detail-body', ['op' => $op])
                                </details>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-6 py-12 text-center text-sm font-medium text-slate-500">No hay operaciones.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
                <div class="border-t border-slate-100/80 bg-slate-50/50 px-4 py-3">{{ $operations->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
