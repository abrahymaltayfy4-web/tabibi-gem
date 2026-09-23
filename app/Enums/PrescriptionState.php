<?php

namespace App\Enums;

enum PrescriptionState: string
{
    case Draft = 'Draft';
    case Finalized = 'Finalized';
    case Cancelled = 'Cancelled';
    case Amended = 'Amended';
}
