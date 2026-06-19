<?php

namespace App\Twig\Components;

use App\Entity\User;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ReportUsersComponent
{
    use DefaultActionTrait;

    public array $items = [];

    public ?User $user = null;
}
