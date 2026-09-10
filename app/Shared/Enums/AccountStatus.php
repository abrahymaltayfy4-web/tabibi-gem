<?php

namespace App\Shared\Enums;

enum AccountStatus: string
{
    case ACTIVE = 'Active';
    case INACTIVE = 'Inactive';
    case SUSPENDED = 'Suspended';
    case BLOCKED = 'Blocked';
}
