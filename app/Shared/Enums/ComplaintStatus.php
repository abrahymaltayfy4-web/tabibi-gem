<?php

namespace App\Shared\Enums;

enum ComplaintStatus: string
{
    case OPEN = 'Open';
    case UNDER_REVIEW = 'UnderReview';
    case RESOLVED = 'Resolved';
    case CLOSED = 'Closed';
}
