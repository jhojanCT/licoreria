<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\InventoryBatch;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('products.view');

        $products = Product::query()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->search.'%'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $lowStockProducts = Product::query()->lowStock()->get()->map(function (Product $product) {
            $stock = (float) $product->stock_quantity;
            $min = (float) $product->min_stock_alert;
            $ratio = $min > 0 ? ($stock / $min) : 1;
            $level = $stock <= 0 || $ratio <= 0.35 ? 'critical' : 'warning';

            $product->setAttribute('stock_alert_level', $level);
            $product->setAttribute('stock_deficit', max($min - $stock, 0));

            return $product;
        });

        $lowStockSummary = [
            'total' => $lowStockProducts->count(),
            'critical' => $lowStockProducts->where('stock_alert_level', 'critical')->count(),
            'warning' => $lowStockProducts->where('stock_alert_level', 'warning')->count(),
            'critical_products' => $lowStockProducts
                ->where('stock_alert_level', 'critical')
                ->sortByDesc('stock_deficit')
                ->take(6)
                ->values(),
        ];

        return view('products.index', compact('products', 'lowStockSummary'));
    }

    public function create(): View
    {
        $this->authorize('products.manage');

        return view('products.create', [
            'setupPending' => ! AppSetting::initialSetupCompleted(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('products.manage');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'min_stock_alert' => 'required|numeric|min:0',
            'default_sale_price' => 'required|numeric|min:0',
            'units_per_pack' => 'nullable|integer|min:1|max:1000',
            'price_per_single_unit' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'initial_stock' => 'nullable|numeric|min:0',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        if (empty($validated['units_per_pack'])) {
            $validated['units_per_pack'] = null;
            $validated['price_per_single_unit'] = null;
        } else {
            $request->validate([
                'price_per_single_unit' => 'required|numeric|min:0',
            ]);
        }
        $initialStock = (float) ($validated['initial_stock'] ?? 0);
        unset($validated['initial_stock']);

        DB::transaction(function () use ($validated, $initialStock): void {
            $product = Product::create($validated);

            if (! AppSetting::initialSetupCompleted() && $initialStock > 0) {
                InventoryBatch::create([
                    'product_id' => $product->id,
                    'purchase_line_id' => null,
                    'quantity_initial' => number_format($initialStock, 3, '.', ''),
                    'quantity_remaining' => number_format($initialStock, 3, '.', ''),
                    'unit_cost' => '0.00',
                    'entered_at' => now(),
                    'received_by_user_id' => auth()->id(),
                ]);
            }
        });

        if (! AppSetting::initialSetupCompleted()) {
            return redirect()
                ->route('initial-setup.create')
                ->with('success', 'Producto creado. Ahora puedes cargar su stock inicial.');
        }

        return redirect()->route('products.index')->with('success', 'Producto creado.');
    }

    public function edit(Product $product): View
    {
        $this->authorize('products.manage');

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('products.manage');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'min_stock_alert' => 'required|numeric|min:0',
            'default_sale_price' => 'required|numeric|min:0',
            'units_per_pack' => 'nullable|integer|min:1|max:1000',
            'price_per_single_unit' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        if (empty($validated['units_per_pack'])) {
            $validated['units_per_pack'] = null;
            $validated['price_per_single_unit'] = null;
        } else {
            $request->validate([
                'price_per_single_unit' => 'required|numeric|min:0',
            ]);
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Producto actualizado.');
    }
}
