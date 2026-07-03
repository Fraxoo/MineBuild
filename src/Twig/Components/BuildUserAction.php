<?php

namespace App\Twig\Components;

use App\Entity\Build;
use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class BuildUserAction
{
    use DefaultActionTrait;

    #[LiveProp()]
    public Build $build;

    public bool $isFollowedByUser = false;

    public function __construct(
        private Security $security,
        private UserService $userService,
    ) {
    }

    #[LiveAction()]
    public function follow(): void
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        $author = $this->build->getAuthor();
        if (!$author instanceof User) {
            return;
        }

        $this->isFollowedByUser = $this->userService->toggleFollow($author, $user);
    }
}
