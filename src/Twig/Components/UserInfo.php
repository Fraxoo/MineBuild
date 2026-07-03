<?php

namespace App\Twig\Components;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class UserInfo
{
    use DefaultActionTrait;

    #[LiveProp()]
    public User $user;

    public bool $isFollowedByUser = false;

    public function __construct(
        private Security $security,
        private UserService $userService,
    ) {
    }

    #[LiveAction()]
    public function follow(): void
    {
        $actualUser = $this->security->getUser();
        if (!$actualUser instanceof User) {
            return;
        }

        $this->isFollowedByUser = $this->userService->toggleFollow($this->user, $actualUser);
    }
}
