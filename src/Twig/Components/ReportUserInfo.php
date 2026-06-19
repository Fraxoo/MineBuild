<?php

namespace App\Twig\Components;

use App\Entity\User;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ReportUserInfo
{
    use DefaultActionTrait;

    public User $user;
}
