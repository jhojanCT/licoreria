<?php

namespace App\Models;

use App\Enums\SpecialCashOperationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialCashOperation extends Model
{
    protected $fillable = [
        'operation_type',
        'sale_id',
        'performed_by_user_id',
        'qr_amount',
        'cash_in',
        'cash_out',
        'description',
        'bill_breakdown',
    ];

    protected function casts(): array
    {
        return [
            'operation_type' => SpecialCashOperationType::class,
            'qr_amount' => 'decimal:2',
            'cash_in' => 'decimal:2',
            'cash_out' => 'decimal:2',
            'bill_breakdown' => 'array',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_user_id');
    }

    public function isBillBreak(): bool
    {
        return $this->operation_type === SpecialCashOperationType::BillBreak;
    }

    /**
     * Cortes entregados en cambio de billete, ej. "5×20 Bs, 2×10 Bs" (mayor denominación primero).
     */
    public function formattedBillCortes(): ?string
    {
        $bd = $this->bill_breakdown;
        if (! is_array($bd) || $bd === []) {
            return null;
        }

        uksort($bd, fn ($a, $b) => (float) $b <=> (float) $a);

        $parts = [];
        foreach ($bd as $denomKey => $qty) {
            $den = (float) $denomKey;
            $q = (int) $qty;
            if ($q <= 0) {
                continue;
            }
            $denDisplay = abs($den - round($den)) < 0.00001
                ? (string) (int) round($den)
                : rtrim(rtrim(number_format($den, 2, '.', ''), '0'), '.');
            $parts[] = "{$q}×{$denDisplay} Bs";
        }

        return $parts === [] ? null : implode(', ', $parts);
    }

    public function hasBillBreakdown(): bool
    {
        return is_array($this->bill_breakdown) && $this->bill_breakdown !== [];
    }
}
