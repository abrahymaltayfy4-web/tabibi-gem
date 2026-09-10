<?php

namespace App\Shared\Enums;

enum PaymentStatus: string
{
    case PENDING = 'Pending';
    case PROCESSING = 'Processing';
    case PAID = 'Paid';
    case FAILED = 'Failed';
    case REFUNDED = 'Refunded';
}
