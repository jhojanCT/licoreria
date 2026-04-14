<?php

namespace App\Services;

use App\Enums\SpecialCashOperationType;
use App\Models\Sale;
use App\Models\SpecialCashOperation;
use App\Models\User;

class SpecialCashService
{
    /**
     * Cliente paga más por QR y recibe vuelto en efectivo (ej. producto 10 Bs, paga 20 Bs QR, recibe 10 Bs efectivo).
     */
    public function recordDepositChange(
        ?Sale $sale,
        User $performedBy,
        string $qrAmount,
        string $cashOut,
        ?string $description = null
    ): SpecialCashOperation {
        return SpecialCashOperation::create([
            'operation_type' => SpecialCashOperationType::DepositChange,
            'sale_id' => $sale?->id,
            'performed_by_user_id' => $performedBy->id,
            'qr_amount' => $qrAmount,
            'cash_in' => '0',
            'cash_out' => $cashOut,
            'description' => $description,
        ]);
    }

    /**
     * Cambio de billete grande a cortes (ej. 100 Bs → 5×20 Bs).
     *
     * @param  array<string, int|string>  $breakdown  ej. ['20' => 5] para cinco billetes de 20
     */
    public function recordBillBreak(
        User $performedBy,
        string $cashIn,
        string $cashOutTotal,
        array $breakdown,
        ?string $description = null
    ): SpecialCashOperation {
        return SpecialCashOperation::create([
            'operation_type' => SpecialCashOperationType::BillBreak,
            'sale_id' => null,
            'performed_by_user_id' => $performedBy->id,
            'qr_amount' => '0',
            'cash_in' => $cashIn,
            'cash_out' => $cashOutTotal,
            'description' => $description,
            'bill_breakdown' => $breakdown,
        ]);
    }
}
