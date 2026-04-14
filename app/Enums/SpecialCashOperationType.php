<?php

namespace App\Enums;

enum SpecialCashOperationType: string
{
    /** Compra con QR por más del valor y entrega de vuelto en efectivo. */
    case DepositChange = 'deposit_change';

    /** Cambio de billete grande a cortes chicos. */
    case BillBreak = 'bill_break';
}
