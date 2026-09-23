<?php

namespace App\Shared\Enums;

enum SessionStatus: string
{
    case SCHEDULED = 'Scheduled';
    case READY = 'Ready';
    case WAITING_FOR_PARTICIPANTS = 'WaitingForParticipants';
    case ACTIVE = 'Active';
    case RECONNECTING = 'Reconnecting';
    case COMPLETED = 'Completed';
    case CANCELLED = 'Cancelled';
    case FAILED = 'Failed';
    case TERMINATED = 'Terminated';
}
