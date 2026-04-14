<?php

namespace App\Models;

use App\Enums\CreditStatus;
use App\Enums\SaleKind;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'sale_kind',
        'sold_at',
        'sold_by_user_id',
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'subtotal',
        'total',
        'credit_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'sale_kind' => SaleKind::class,
            'sold_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'credit_status' => CreditStatus::class,
        ];
    }

    public function soldBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_by_user_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(SaleLine::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalePayment::class);
    }

    public function specialCashOperations(): HasMany
    {
        return $this->hasMany(SpecialCashOperation::class);
    }
}
