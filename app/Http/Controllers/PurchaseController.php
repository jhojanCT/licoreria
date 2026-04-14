<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('purchases.view');

        $purchases = Purchase::query()
            ->with(['supplier', 'receivedBy'])
            ->when($request->filled('supplier'), fn ($q) => $q->where('supplier_id', $request->supplier))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('received_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('received_at', '<=', $request->to))
            ->orderByDesc('received_at')
            ->paginate(15)
            ->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    public function create(): View
    {
        $this->authorize('purchases.create');

        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('purchases.create');

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'received_at' => 'required|date',
            'payment_method' => 'required|in:cash,qr',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.quantity' => 'required|numeric|min:0.001',
            'lines.*.unit_purchase_price' => 'required|numeric|min:0',
        ]);

        $purchase = Purchase::create([
            'supplier_id' => $validated['supplier_id'],
            'received_at' => $validated['received_at'],
            'received_by_user_id' => auth()->id(),
            'total_cost' => 0,
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['lines'] as $line) {
            $lineTotal = (float) $line['quantity'] * (float) $line['unit_purchase_price'];
            $purchase->lines()->create([
                'product_id' => $line['product_id'],
                'quantity' => $line['quantity'],
                'unit_purchase_price' => $line['unit_purchase_price'],
                'line_total' => (string) $lineTotal,
            ]);
        }

        return redirect()->route('purchases.index')->with('success', 'Compra registrada.');
    }

    public function show(Purchase $purchase): View
    {
        $this->authorize('purchases.view');

        $purchase->load(['supplier', 'receivedBy', 'lines.product']);

        return view('purchases.show', compact('purchase'));
    }
}
