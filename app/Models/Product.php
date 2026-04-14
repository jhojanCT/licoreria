<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'min_stock_alert',
        'default_sale_price',
        'units_per_pack',
        'price_per_single_unit',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'min_stock_alert' => 'decimal:2',
            'default_sale_price' => 'decimal:2',
            'units_per_pack' => 'integer',
            'price_per_single_unit' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Cigarros u otros productos con precio por cajetilla y por unidad suelta.
     * El stock se cuenta siempre en unidades (ej. cigarros); la cajetilla descuenta units_per_pack.
     */
    public function isDualUnitProduct(): bool
    {
        return $this->units_per_pack !== null && $this->units_per_pack > 0;
    }

    public function inventoryBatches(): HasMany
    {
        return $this->hasMany(InventoryBatch::class);
    }

    public function saleLines(): HasMany
    {
        return $this->hasMany(SaleLine::class);
    }

    /**
     * Stock actual = suma de quantity_remaining en lotes PEPS.
     */
    public function getStockQuantityAttribute(): string
    {
        return (string) $this->inventoryBatches()->sum('quantity_remaining');
    }

    /**
     * Productos cuyo stock total es menor que min_stock_alert.
     */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('min_stock_alert', '>', 0)
            ->whereRaw('(
                SELECT COALESCE(SUM(ib.quantity_remaining), 0)
                FROM inventory_batches AS ib
                WHERE ib.product_id = products.id
            ) < products.min_stock_alert');
    }
}
