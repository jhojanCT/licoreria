<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\InventoryBatch;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InitialSetupController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (AppSetting::initialSetupCompleted()) {
            return redirect()->route('dashboard');
        }

        $products = Product::query()->orderBy('name')->get();

        return view('setup.initial', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (AppSetting::initialSetupCompleted()) {
            return redirect()->route('dashboard');
        }

        if (Product::query()->count() === 0) {
            return back()->withInput()->withErrors([
                'stocks' => 'Primero debes crear al menos un producto para poder cargar stock inicial.',
            ]);
        }

        $validated = $request->validate([
            'opening_cash' => 'required|numeric|min:0',
            'opening_qr' => 'required|numeric|min:0',
            'stocks' => 'required|array',
            'stocks.*.product_id' => 'required|integer|exists:products,id',
            'stocks.*.quantity' => 'nullable|numeric|min:0',
        ]);

        $productsById = Product::query()
            ->whereIn('id', collect($validated['stocks'])->pluck('product_id'))
            ->get()
            ->keyBy('id');

        $rows = collect($validated['stocks'])
            ->map(function (array $row) use ($productsById) {
                $targetQuantity = (float) ($row['quantity'] ?? 0);
                $product = $productsById->get((int) $row['product_id']);
                $currentQuantity = $product ? (float) $product->stock_quantity : 0;
                $delta = round($targetQuantity - $currentQuantity, 3);

                return [
                    'product_id' => (int) $row['product_id'],
                    'target_quantity' => $targetQuantity,
                    'current_quantity' => $currentQuantity,
                    'delta' => $delta,
                ];
            });

        if ($rows->where('target_quantity', '>', 0)->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['stocks' => 'Debes cargar stock inicial en al menos un producto.']);
        }

        $hasLowerThanCurrent = $rows->contains(fn (array $row) => $row['delta'] < 0);
        if ($hasLowerThanCurrent) {
            return back()->withInput()->withErrors([
                'stocks' => 'No puedes poner una cantidad menor al stock ya cargado. Ajusta las cantidades para continuar.',
            ]);
        }

        DB::transaction(function () use ($rows, $validated): void {
            foreach ($rows as $row) {
                if ($row['delta'] <= 0) {
                    continue;
                }

                InventoryBatch::create([
                    'product_id' => $row['product_id'],
                    'purchase_line_id' => null,
                    'quantity_initial' => number_format($row['delta'], 3, '.', ''),
                    'quantity_remaining' => number_format($row['delta'], 3, '.', ''),
                    'unit_cost' => '0.00',
                    'entered_at' => now(),
                    'received_by_user_id' => auth()->id(),
                ]);
            }

            AppSetting::setValue('initial_opening_cash', number_format((float) $validated['opening_cash'], 2, '.', ''));
            AppSetting::setValue('initial_opening_qr', number_format((float) $validated['opening_qr'], 2, '.', ''));
            AppSetting::setValue('initial_opening_balances_seeded', '1');
            AppSetting::setValue('initial_setup_completed_at', now()->toDateTimeString());
            AppSetting::setValue('initial_setup_completed', '1');
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Configuración inicial completada correctamente.');
    }
}

