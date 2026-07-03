<?php

namespace App\Twig\Components;

use App\Entity\Notification;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class NotificationCard
{
    use DefaultActionTrait;

    public Notification $notif;
}
