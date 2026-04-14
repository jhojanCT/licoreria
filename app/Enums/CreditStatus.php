<?php

namespace App\Enums;

enum CreditStatus: string
{
    case Pending = 'pending';
    case Partial = 'partial';
    case Paid = 'paid';
}
