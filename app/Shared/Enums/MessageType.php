<?php

namespace App\Shared\Enums;

enum MessageType: string
{
    case TEXT = 'text';
    case IMAGE = 'image';
    case FILE = 'file';
    case VOICE = 'voice';
    case SYSTEM = 'system';
}
