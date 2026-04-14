<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyCashClosure extends Model
{
    protected $fillable = [
        'business_date',
        'closed_by_user_id',
        'expected_cash',
        'counted_cash',
        'difference_cash',
        'total_qr_day',
        'notes',
        'admin_reviewed_at',
        'admin_reviewed_by_user_id',
        'admin_adjustment',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'business_date' => 'date',
            'expected_cash' => 'decimal:2',
            'counted_cash' => 'decimal:2',
            'difference_cash' => 'decimal:2',
            'total_qr_day' => 'decimal:2',
            'admin_reviewed_at' => 'datetime',
            'admin_adjustment' => 'decimal:2',
        ];
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    public function adminReviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_reviewed_by_user_id');
    }
}
