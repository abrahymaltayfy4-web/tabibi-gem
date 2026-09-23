<?php

namespace App\Shared\Enums;

enum PaymentStatus: string
{
    case PENDING = 'Pending';
    case PROCESSING = 'Processing';
    case SUCCEEDED = 'Succeeded';
    case PAID = 'Paid';
    case FAILED = 'Failed';
    case CANCELLED = 'Cancelled';
    case EXPIRED = 'Expired';
    case REFUND_PENDING = 'RefundPending';
    case PARTIALLY_REFUNDED = 'PartiallyRefunded';
    case REFUNDED = 'Refunded';
}
