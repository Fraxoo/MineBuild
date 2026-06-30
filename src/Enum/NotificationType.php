<?php

namespace App\Enum;

enum NotificationType: string
{
    case FOLLOW = 'follow';
    case LIKE = 'like';
    case COMMENT = 'comment';
    case RATING = 'rating';
    case MODERATION = 'moderation';
}
