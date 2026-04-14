<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\DailyCashClosure;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\SpecialCashOperation;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = Carbon::today();

        $stats = [
            'today_sales_count' => 0,
            'today_sales_total' => 0.0,
            'today_purchases_count' => 0,
            'today_purchases_total' => 0.0,
            'products_active' => 0,
            'products_total' => 0,
            'suppliers_count' => 0,
            'users_count' => 0,
            'closure_today' => null,
            'low_stock' => collect(),
            'cash_balance_now' => 0.0,
            'qr_balance_now' => 0.0,
        ];

        $initialCash = (float) AppSetting::getValue('initial_opening_cash', 0);
        $initialQr = (float) AppSetting::getValue('initial_opening_qr', 0);
        $setupCompletedAt = AppSetting::getValue('initial_setup_completed_at');
        $fromDateTime = $setupCompletedAt ? Carbon::parse($setupCompletedAt) : null;

        $cashPayments = (float) SalePayment::query()
            ->when($fromDateTime, fn ($q) => $q->where('created_at', '>=', $fromDateTime))
            ->where('method', 'cash')
            ->sum('amount');
        $qrPayments = (float) SalePayment::query()
            ->when($fromDateTime, fn ($q) => $q->where('created_at', '>=', $fromDateTime))
            ->where('method', 'qr')
            ->sum('amount');
        $cashSpecialIn = (float) SpecialCashOperation::query()
            ->when($fromDateTime, fn ($q) => $q->where('created_at', '>=', $fromDateTime))
            ->sum('cash_in');
        $cashSpecialOut = (float) SpecialCashOperation::query()
            ->when($fromDateTime, fn ($q) => $q->where('created_at', '>=', $fromDateTime))
            ->sum('cash_out');
        $cashPurchasesOut = (float) Purchase::query()
            ->when($fromDateTime, fn ($q) => $q->where('received_at', '>=', $fromDateTime))
            ->where(function ($q) {
                $q->where('payment_method', 'cash')
                    ->orWhereNull('payment_method');
            })
            ->sum('total_cost');
        $qrPurchasesOut = (float) Purchase::query()
            ->when($fromDateTime, fn ($q) => $q->where('received_at', '>=', $fromDateTime))
            ->where('payment_method', 'qr')
            ->sum('total_cost');

        $stats['cash_balance_now'] = $initialCash + $cashPayments + $cashSpecialIn - $cashSpecialOut - $cashPurchasesOut;
        $stats['qr_balance_now'] = $initialQr + $qrPayments - $qrPurchasesOut;

        if (auth()->user()?->can('sales.view')) {
            $stats['today_sales_count'] = Sale::query()->whereDate('sold_at', $today)->count();
            $stats['today_sales_total'] = (float) Sale::query()->whereDate('sold_at', $today)->sum('total');
        }

        if (auth()->user()?->can('purchases.view')) {
            $stats['today_purchases_count'] = Purchase::query()->whereDate('received_at', $today)->count();
            $stats['today_purchases_total'] = (float) Purchase::query()->whereDate('received_at', $today)->sum('total_cost');
            $stats['suppliers_count'] = Supplier::query()->count();
        }

        if (auth()->user()?->can('products.view')) {
            $stats['products_total'] = Product::query()->count();
            $stats['products_active'] = Product::query()->where('is_active', true)->count();
            $lowStock = Product::query()->lowStock()->orderBy('name')->limit(10)->get();
            $stats['low_stock'] = $lowStock->map(function (Product $product) {
                $stock = (float) $product->stock_quantity;
                $min = (float) $product->min_stock_alert;
                $ratio = $min > 0 ? ($stock / $min) : 1;
                $level = $stock <= 0 || $ratio <= 0.35 ? 'critical' : 'warning';

                $product->setAttribute('stock_alert_level', $level);
                $product->setAttribute('stock_deficit', max($min - $stock, 0));

                return $product;
            });
            $stats['low_stock_critical_count'] = $stats['low_stock']->where('stock_alert_level', 'critical')->count();
            $stats['low_stock_warning_count'] = $stats['low_stock']->where('stock_alert_level', 'warning')->count();
        }

        if (auth()->user()?->can('cash.close_basic')) {
            $stats['closure_today'] = DailyCashClosure::query()
                ->whereDate('business_date', $today)
                ->with('closedBy')
                ->first();
        }

        if (auth()->user()?->can('users.manage')) {
            $stats['users_count'] = User::query()->count();
        }

        $todayLabel = $today->copy()->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY');

        $user = auth()->user();
        $show_catalog_column = $user && (
            $user->can('products.view')
            || $user->can('purchases.view')
            || $user->can('users.manage')
            || $user->can('roles.manage')
        );

        return view('dashboard', [
            'stats' => $stats,
            'today' => $today,
            'today_label' => $todayLabel,
            'show_catalog_column' => $show_catalog_column,
        ]);
    }
}
