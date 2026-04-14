<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaleLine extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'sale_unit',
        'quantity',
        'unit_price',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function quantityLabel(): string
    {
        $q = rtrim(rtrim(number_format((float) $this->quantity, 3, '.', ''), '0'), '.');

        if ($this->sale_unit === 'pack') {
            return $q.' caj.';
        }

        if ($this->sale_unit === 'each') {
            return $q.' cig.';
        }

        return $q;
    }

    public function batchAllocations(): HasMany
    {
        return $this->hasMany(SaleLineBatchAllocation::class);
    }
}
