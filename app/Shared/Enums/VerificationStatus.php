<?php

namespace App\Shared\Enums;

enum VerificationStatus: string
{
    case DRAFT = 'Draft';
    case PENDING = 'Pending';
    case UNDER_REVIEW = 'UnderReview';
    case APPROVED = 'Approved';
    case REJECTED = 'Rejected';
    case SUSPENDED = 'Suspended';
    case EXPIRED = 'Expired';
}
