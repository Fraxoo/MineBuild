<?php

namespace App\Enum;

enum BuildDifficulty: string
{
    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';
    case EXPERT = 'expert';
}
