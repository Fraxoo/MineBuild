<?php

namespace App\Twig\Components;

use App\Entity\Build;
use App\Entity\Comment;
use App\Entity\User;
use App\Service\CommentService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Comments
{
    use DefaultActionTrait;


    #[LiveProp()]
    public Comment $comment;

    #[LiveProp]
    public Build $build;

    public bool $isLikedByUser = false;

    public function __construct(
        private Security $security,
        private CommentService $commentService,
    ) {
    }

    public function mount(Comment $comment): void
    {

        $this->comment = $comment;
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            $this->isLikedByUser = false;
            return;
        }

        $this->isLikedByUser = $this->commentService->isLikedByUser($this->comment, $user);
    }

    #[LiveAction()]
    public function Like(): void
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        $this->isLikedByUser = $this->commentService->toggleLike($this->comment, $user);
    }



}
