<?php

namespace App\Shared\Enums;

enum ReviewStatus: string
{
    case PUBLISHED = 'Published';
    case HIDDEN = 'Hidden';
    case UNDER_REVIEW = 'UnderReview';
}
