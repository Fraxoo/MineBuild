<?php

namespace App\Enum;

enum ReportReasonCode: string
{
    case SPAM = 'spam';
    case HARASSMENT = 'harassment';
    case HATE_SPEECH = 'hate_speech';
    case NUDITY = 'nudity';
    case VIOLENCE = 'violence';
    case ILLEGAL = 'illegal';
    case IMPERSONATION = 'impersonation';
    case COPYRIGHT = 'copyright';
    case OTHER = 'other';
}
