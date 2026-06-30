<?php

namespace App\Service;

use App\Entity\Build;
use App\Entity\ModerationAction;

final readonly class ModerationActionResult
{
    public function __construct(
        public ModerationAction $moderationAction,
        public ?Build $redirectBuild = null,
    ) {
    }
}
