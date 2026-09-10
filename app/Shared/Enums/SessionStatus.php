<?php

namespace App\Shared\Enums;

enum SessionStatus: string
{
    case SCHEDULED = 'Scheduled';
    case ACTIVE = 'Active';
    case COMPLETED = 'Completed';
    case TERMINATED = 'Terminated';
}
