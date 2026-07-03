<?php

namespace App\Twig\Components;

use App\Service\NotificationService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Header
{
    use DefaultActionTrait;

    public bool $hasUnreadNotif = false;

    public function __construct(
        private NotificationService $notificationService,
        private Security $security,
    ) {
    }

    public function mount(): void
    {
        $user = $this->security->getUser();

        if ($user) {
            $this->hasUnreadNotif = $this->notificationService->hasUnreadForUser($user);
        }
    }
}
