<?php

namespace App\Enum;

enum ReportStatus: string
{
    case PENDING = 'Pending';
    case CONFIRMED = 'Confirmed';
    case REJECTED = 'Rejected';
}
