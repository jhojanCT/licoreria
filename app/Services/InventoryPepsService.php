<?php

namespace App\Services;

use App\Models\InventoryBatch;
use App\Models\Product;
use App\Models\SaleLine;
use App\Models\SaleLineBatchAllocation;
use RuntimeException;

class InventoryPepsService
{
    /**
     * Descuenta stock por orden PEPS y guarda asignaciones por lote.
     * Debe ejecutarse dentro de una transacción de base de datos.
     *
     * @return list<SaleLineBatchAllocation>
     */
    public function allocateAndConsume(SaleLine $saleLine, Product $product, string $quantity): array
    {
        $need = (float) $quantity;
        if ($need <= 0) {
            return [];
        }

        $batches = InventoryBatch::query()
            ->where('product_id', $product->id)
            ->where('quantity_remaining', '>', 0)
            ->orderBy('entered_at')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        $allocations = [];
        $remaining = $need;

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $available = (float) $batch->quantity_remaining;
            if ($available <= 0) {
                continue;
            }

            $take = min($available, $remaining);

            $batch->quantity_remaining = (string) ($available - $take);
            $batch->save();

            $allocations[] = SaleLineBatchAllocation::create([
                'sale_line_id' => $saleLine->id,
                'inventory_batch_id' => $batch->id,
                'quantity' => (string) $take,
            ]);

            $remaining -= $take;
        }

        if ($remaining > 0.0001) {
            throw new RuntimeException(
                "Stock insuficiente para el producto «{$product->name}». Falta: {$remaining}."
            );
        }

        return $allocations;
    }
}
