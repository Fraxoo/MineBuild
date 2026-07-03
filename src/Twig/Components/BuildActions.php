<?php

namespace App\Twig\Components;

use App\Entity\Build;
use App\Entity\User;
use App\Service\BuildService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class BuildActions
{
    use DefaultActionTrait;

    #[LiveProp]
    public Build $build;

    #[LiveProp]
    public int $rating = 0;

    #[LiveProp]
    public bool $isSavedByUser = false;

    #[LiveProp]
    public bool $isLikedByUser = false;

    public function __construct(
        private Security $security,
        private BuildService $buildService,
    ) {
    }

    public function mount(Build $build): void
    {
        $this->build = $build;

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        $this->rating = $this->buildService->getRatingByUser($this->build, $user);
    }



    #[LiveAction]
    public function rate(#[LiveArg] int $value): void
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        $this->rating = $this->buildService->rate($this->build, $user, $value);
    }

    #[LiveAction()]
    public function save(): void
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        $this->isSavedByUser = $this->buildService->toggleSave($this->build, $user);
    }

    #[LiveAction]
    public function like(): void
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        $this->isLikedByUser = $this->buildService->toggleLike($this->build, $user);
    }
}
