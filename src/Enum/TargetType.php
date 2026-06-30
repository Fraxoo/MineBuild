<?php

namespace App\Enum;

enum TargetType: string
{
    case BUILD = 'build';
    case COMMENT = 'comment';
    case USER = 'user';
}
