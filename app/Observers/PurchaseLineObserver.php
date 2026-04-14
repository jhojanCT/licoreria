<?php

namespace App\Observers;

use App\Models\InventoryBatch;
use App\Models\PurchaseLine;

class PurchaseLineObserver
{
    public function created(PurchaseLine $purchaseLine): void
    {
        $purchase = $purchaseLine->purchase;

        InventoryBatch::create([
            'product_id' => $purchaseLine->product_id,
            'purchase_line_id' => $purchaseLine->id,
            'quantity_initial' => $purchaseLine->quantity,
            'quantity_remaining' => $purchaseLine->quantity,
            'unit_cost' => $purchaseLine->unit_purchase_price,
            'entered_at' => $purchase?->received_at ?? now(),
            'received_by_user_id' => $purchase?->received_by_user_id,
        ]);

        if ($purchase) {
            $purchase->total_cost = (string) $purchase->lines()->sum('line_total');
            $purchase->save();
        }
    }
}
