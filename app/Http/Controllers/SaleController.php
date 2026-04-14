<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Enums\SaleKind;
use App\Enums\CreditStatus;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class SaleController extends Controller
{
    public function __construct(
        private readonly SaleService $saleService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('sales.view');

        $sales = Sale::query()
            ->with(['soldBy', 'lines.product'])
            ->when($request->filled('kind'), fn ($q) => $q->where('sale_kind', $request->kind))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('sold_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('sold_at', '<=', $request->to))
            ->orderByDesc('sold_at')
            ->paginate(15)
            ->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function create(): View
    {
        $this->authorize('sales.create');

        $products = Product::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::query()->orderBy('name')->get();

        return view('sales.create', compact('products', 'customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('sales.create');

        $validated = $request->validate([
            'sale_kind' => 'required|in:credit,cash',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255|required_if:sale_kind,credit|required_without:customer_id',
            'customer_phone' => 'nullable|string|max:32|required_if:sale_kind,credit|required_without:customer_id',
            'customer_address' => 'nullable|string|max:255',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.quantity' => 'required|numeric|min:0.001',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.sale_unit' => 'nullable|in:pack,each',
            'payments' => 'required_if:sale_kind,cash|array',
            'payments.*.method' => 'required|in:cash,qr',
            'payments.*.amount' => 'nullable|numeric|min:0',
            'cash_change_delivered' => 'nullable|numeric|min:0',
            'cash_change_note' => 'nullable|string|max:500',
        ]);

        $kind = SaleKind::from($validated['sale_kind']);

        foreach ($validated['lines'] as $i => $line) {
            $product = Product::query()->findOrFail($line['product_id']);
            if ($product->isDualUnitProduct() && ! in_array($line['sale_unit'] ?? null, ['pack', 'each'], true)) {
                return back()->withInput()->withErrors([
                    "lines.$i.sale_unit" => 'Indique si vende por cajetilla o por unidad (cigarro).',
                ]);
            }
        }

        $lines = array_map(function (array $l) {
            $saleUnit = $l['sale_unit'] ?? null;

            return [
                'product_id' => (int) $l['product_id'],
                'quantity' => (string) $l['quantity'],
                'unit_price' => (string) $l['unit_price'],
                'sale_unit' => in_array($saleUnit, ['pack', 'each'], true) ? $saleUnit : null,
            ];
        }, $validated['lines']);

        $payments = [];
        if ($kind === SaleKind::Cash && ! empty($validated['payments'] ?? [])) {
            $payments = collect($validated['payments'])
                ->filter(fn ($p) => ! empty($p['amount']) && (float) $p['amount'] > 0)
                ->map(fn ($p) => [
                    'method' => PaymentMethod::from($p['method']),
                    'amount' => (string) $p['amount'],
                ])
                ->values()
                ->all();
        }

        $cashChange = $validated['cash_change_delivered'] ?? null;
        if ($cashChange === '' || $cashChange === null) {
            $cashChange = null;
        } else {
            $cashChange = (string) $cashChange;
        }

        $customerId = null;
        $customerName = $validated['customer_name'] ?? null;
        $customerPhone = $validated['customer_phone'] ?? null;
        $customerAddress = $validated['customer_address'] ?? null;

        if ($kind === SaleKind::Credit) {
            if (! empty($validated['customer_id'])) {
                $customer = Customer::query()->findOrFail($validated['customer_id']);
                $customerId = $customer->id;
                $customerName = $customer->name;
                $customerPhone = $customer->phone;
                $customerAddress = $customer->address;
            } else {
                $customer = Customer::query()->firstOrNew(['phone' => $customerPhone]);
                $customer->name = $customerName;
                if (filled($customerAddress)) {
                    $customer->address = $customerAddress;
                }
                $customer->save();
                $customerId = $customer->id;
                $customerAddress = $customer->address;
            }
        }

        try {
            $sale = $this->saleService->registerSale(
                kind: $kind,
                seller: auth()->user(),
                lines: $lines,
                payments: $payments,
                customerId: $customerId,
                customerName: $customerName,
                customerPhone: $customerPhone,
                customerAddress: $customerAddress,
                notes: $validated['notes'] ?? null,
                cashChangeDelivered: $cashChange,
                cashChangeNote: $validated['cash_change_note'] ?? null,
            );
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['sale' => $e->getMessage()]);
        }

        return redirect()->route('sales.show', $sale)->with('success', 'Venta registrada.');
    }

    public function show(Sale $sale): View
    {
        $this->authorize('sales.view');

        $sale->load(['soldBy', 'lines.product', 'payments.recordedBy', 'specialCashOperations.performedBy']);

        return view('sales.show', compact('sale'));
    }

    public function creditAccounts(): View
    {
        $this->authorize('sales.view');

        $sales = Sale::query()
            ->with(['customer', 'soldBy', 'lines.product', 'payments.recordedBy'])
            ->where('sale_kind', SaleKind::Credit)
            ->whereIn('credit_status', [CreditStatus::Pending, CreditStatus::Partial])
            ->orderBy('sold_at')
            ->get();

        $sales = $sales->map(function (Sale $sale) {
            $paid = (float) $sale->payments->sum('amount');
            $total = (float) $sale->total;
            $pending = max($total - $paid, 0);

            $sale->setAttribute('paid_total', $paid);
            $sale->setAttribute('pending_total', $pending);

            return $sale;
        })->filter(fn (Sale $sale) => $sale->getAttribute('pending_total') > 0.00001);

        $accounts = $sales
            ->groupBy(function (Sale $sale) {
                $phone = trim((string) $sale->customer_phone);
                if ($phone !== '') {
                    return 'phone-'.preg_replace('/\D+/', '', $phone);
                }

                if ($sale->customer_id) {
                    return 'customer-'.$sale->customer_id;
                }

                return 'name-'.mb_strtolower(trim((string) $sale->customer_name));
            })
            ->map(function ($group) {
                $first = $group->first();
                $name = $group->firstWhere('customer_name', '!=', null)?->customer_name ?? $first?->customer_name ?? 'Sin nombre';
                $phone = $group->firstWhere('customer_phone', '!=', null)?->customer_phone ?? $first?->customer_phone ?? '-';
                $address = $group->firstWhere('customer_address', '!=', null)?->customer_address
                    ?? $group->firstWhere('customer.address', '!=', null)?->customer?->address;

                return [
                    'customer_name' => $name,
                    'customer_phone' => $phone,
                    'customer_address' => $address,
                    'sales' => $group->sortBy('sold_at')->values(),
                    'pending_total' => $group->sum(fn (Sale $sale) => (float) $sale->getAttribute('pending_total')),
                ];
            })
            ->sortByDesc('pending_total')
            ->values();

        return view('sales.credit-accounts', compact('accounts'));
    }

    public function settleCredit(Request $request, Sale $sale): RedirectResponse
    {
        $this->authorize('sales.view');

        if ($sale->sale_kind !== SaleKind::Credit) {
            return back()->withErrors(['credit' => 'Solo se pueden cerrar ventas por cobrar.']);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,qr',
        ]);

        $alreadyPaid = (float) $sale->payments()->sum('amount');
        $remaining = (float) $sale->total - $alreadyPaid;
        $amount = (float) $validated['amount'];

        if ($remaining <= 0) {
            $sale->update(['credit_status' => CreditStatus::Paid]);

            return back()->with('success', 'Esta venta ya estaba cerrada.');
        }

        if ($amount - $remaining > 0.00001) {
            return back()->withErrors([
                'credit' => 'El monto excede el saldo pendiente de '.number_format($remaining, 2).' Bs.',
            ]);
        }

        $sale->payments()->create([
            'method' => PaymentMethod::from($validated['method']),
            'amount' => number_format($amount, 2, '.', ''),
            'recorded_by_user_id' => auth()->id(),
        ]);

        $newPaid = (float) $sale->payments()->sum('amount');
        $isPaid = $newPaid + 0.00001 >= (float) $sale->total;
        $sale->update([
            'credit_status' => $isPaid ? CreditStatus::Paid : CreditStatus::Partial,
        ]);

        return redirect()
            ->route('sales.credit-accounts')
            ->with('success', $isPaid ? 'Venta por cobrar cerrada.' : 'Abono registrado correctamente.');
    }
}
