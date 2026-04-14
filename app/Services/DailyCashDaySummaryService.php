<?php

namespace App\Services;

use App\Enums\SaleKind;
use App\Models\AppSetting;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\SpecialCashOperation;
use Carbon\Carbon;

class DailyCashDaySummaryService
{
    /**
     * Resumen operativo del día para cierre de caja (ventas, cobros, compras recibidas, caja especial).
     *
     * @return array{
     *   business_date: string,
     *   expected_cash: float,
     *   qr_total: float,
     *   cash_from_sales: float,
     *   cash_in_specials: float,
     *   cash_out_specials: float,
     *   sales: \Illuminate\Support\Collection,
     *   purchases: \Illuminate\Support\Collection,
     *   special_operations: \Illuminate\Support\Collection,
     *   totals: array<string, float|int>
     * }
     */
    public function summarize(string $businessDate): array
    {
        $businessDate = $this->normalizeDate($businessDate);
        $toDateTime = Carbon::parse($businessDate)->endOfDay();
        $setupCompletedAt = AppSetting::getValue('initial_setup_completed_at');
        $fromDateTime = $setupCompletedAt ? Carbon::parse($setupCompletedAt) : null;

        $cashFromSales = (float) SalePayment::query()
            ->whereDate('created_at', $businessDate)
            ->where('method', 'cash')
            ->sum('amount');

        $qrTotal = (float) SalePayment::query()
            ->whereDate('created_at', $businessDate)
            ->where('method', 'qr')
            ->sum('amount');

        $cashOutFromSpecials = (float) SpecialCashOperation::whereDate('created_at', $businessDate)->sum('cash_out');
        $cashInFromSpecials = (float) SpecialCashOperation::whereDate('created_at', $businessDate)->sum('cash_in');
        $cashOutFromPurchases = (float) Purchase::query()
            ->whereDate('received_at', $businessDate)
            ->where(function ($q) {
                $q->where('payment_method', 'cash')
                    ->orWhereNull('payment_method');
            })
            ->sum('total_cost');
        $qrOutFromPurchases = (float) Purchase::query()
            ->whereDate('received_at', $businessDate)
            ->where('payment_method', 'qr')
            ->sum('total_cost');

        $expectedCash = $cashFromSales + $cashInFromSpecials - $cashOutFromSpecials - $cashOutFromPurchases;

        $initialCash = (float) AppSetting::getValue('initial_opening_cash', 0);
        $initialQr = (float) AppSetting::getValue('initial_opening_qr', 0);

        $cashPaymentsAccumulated = (float) SalePayment::query()
            ->when($fromDateTime, fn ($q) => $q->where('created_at', '>=', $fromDateTime))
            ->where('created_at', '<=', $toDateTime)
            ->where('method', 'cash')
            ->sum('amount');
        $qrPaymentsAccumulated = (float) SalePayment::query()
            ->when($fromDateTime, fn ($q) => $q->where('created_at', '>=', $fromDateTime))
            ->where('created_at', '<=', $toDateTime)
            ->where('method', 'qr')
            ->sum('amount');
        $cashSpecialInAccumulated = (float) SpecialCashOperation::query()
            ->when($fromDateTime, fn ($q) => $q->where('created_at', '>=', $fromDateTime))
            ->where('created_at', '<=', $toDateTime)
            ->sum('cash_in');
        $cashSpecialOutAccumulated = (float) SpecialCashOperation::query()
            ->when($fromDateTime, fn ($q) => $q->where('created_at', '>=', $fromDateTime))
            ->where('created_at', '<=', $toDateTime)
            ->sum('cash_out');
        $cashPurchasesOutAccumulated = (float) Purchase::query()
            ->when($fromDateTime, fn ($q) => $q->where('received_at', '>=', $fromDateTime))
            ->where('received_at', '<=', $toDateTime)
            ->where(function ($q) {
                $q->where('payment_method', 'cash')
                    ->orWhereNull('payment_method');
            })
            ->sum('total_cost');
        $qrPurchasesOutAccumulated = (float) Purchase::query()
            ->when($fromDateTime, fn ($q) => $q->where('received_at', '>=', $fromDateTime))
            ->where('received_at', '<=', $toDateTime)
            ->where('payment_method', 'qr')
            ->sum('total_cost');

        $cashBalanceExpected = $initialCash + $cashPaymentsAccumulated + $cashSpecialInAccumulated - $cashSpecialOutAccumulated - $cashPurchasesOutAccumulated;
        $qrBalanceExpected = $initialQr + $qrPaymentsAccumulated - $qrPurchasesOutAccumulated;

        $sales = Sale::query()
            ->whereDate('sold_at', $businessDate)
            ->with(['soldBy', 'lines.product', 'payments.recordedBy'])
            ->orderBy('sold_at')
            ->get();

        $purchases = Purchase::query()
            ->whereDate('received_at', $businessDate)
            ->with(['supplier', 'receivedBy', 'lines.product'])
            ->orderBy('received_at')
            ->get();

        $specialOperations = SpecialCashOperation::query()
            ->whereDate('created_at', $businessDate)
            ->with(['performedBy', 'sale'])
            ->orderBy('created_at')
            ->get();

        $creditCollections = SalePayment::query()
            ->whereDate('sale_payments.created_at', $businessDate)
            ->join('sales', 'sales.id', '=', 'sale_payments.sale_id')
            ->where('sales.sale_kind', SaleKind::Credit->value)
            ->orderBy('sale_payments.created_at')
            ->get([
                'sale_payments.id',
                'sale_payments.sale_id',
                'sale_payments.method',
                'sale_payments.amount',
                'sale_payments.created_at',
                'sales.customer_name',
                'sales.customer_phone',
            ]);

        $salesMoneyTotal = (float) $sales->sum(fn (Sale $s) => (float) $s->total);
        $purchasesTotalCost = (float) $purchases->sum(fn (Purchase $p) => (float) $p->total_cost);

        return [
            'business_date' => $businessDate,
            'expected_cash' => $expectedCash,
            'qr_total' => $qrTotal,
            'cash_balance_expected' => $cashBalanceExpected,
            'qr_balance_expected' => $qrBalanceExpected,
            'cash_from_sales' => $cashFromSales,
            'cash_in_specials' => $cashInFromSpecials,
            'cash_out_specials' => $cashOutFromSpecials,
            'cash_out_purchases' => $cashOutFromPurchases,
            'qr_out_purchases' => $qrOutFromPurchases,
            'sales' => $sales,
            'purchases' => $purchases,
            'special_operations' => $specialOperations,
            'credit_collections' => $creditCollections,
            'totals' => [
                'sales_count' => $sales->count(),
                'sales_subtotal' => (float) $sales->sum(fn (Sale $s) => (float) $s->subtotal),
                'sales_total' => $salesMoneyTotal,
                'credit_sales_count' => $sales->filter(fn (Sale $s) => $s->sale_kind === SaleKind::Credit)->count(),
                'cash_sales_count' => $sales->filter(fn (Sale $s) => $s->sale_kind === SaleKind::Cash)->count(),
                'purchases_count' => $purchases->count(),
                'purchases_total_cost' => $purchasesTotalCost,
                'purchases_cash_total' => (float) $purchases->filter(fn (Purchase $p) => ($p->payment_method ?? 'cash') === 'cash')->sum('total_cost'),
                'purchases_qr_total' => (float) $purchases->where('payment_method', 'qr')->sum('total_cost'),
                'purchase_lines_count' => (int) $purchases->sum(fn (Purchase $p) => $p->lines->count()),
                'special_operations_count' => $specialOperations->count(),
                'credit_collections_count' => $creditCollections->count(),
                'credit_collections_total' => (float) $creditCollections->sum('amount'),
            ],
        ];
    }

    private function normalizeDate(string $businessDate): string
    {
        $parsed = Carbon::createFromFormat('Y-m-d', $businessDate);

        return $parsed && $parsed->format('Y-m-d') === $businessDate
            ? $businessDate
            : now()->toDateString();
    }
}
