<?php

namespace App\Twig\Components;

use App\Entity\Build;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class BuildCard
{
    use DefaultActionTrait;

    public Build $build;
}
