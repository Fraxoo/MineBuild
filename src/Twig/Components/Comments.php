<?php

namespace App\Twig\Components;

use App\Entity\Comment;
use App\Entity\CommentLike;
use App\Entity\User;
use App\Repository\CommentLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    public bool $isLikedByUser = false;

    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private CommentLikeRepository $commentLikeRepository,
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

        $existingLike = $this->commentLikeRepository->findOneBy([
            'comment_id' => $this->comment,
            'user_id' => $user,
        ]);

        $this->isLikedByUser = $existingLike !== null;
    }

    #[LiveAction()]
    public function Like(): void
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        $existingLikes = $this->commentLikeRepository->findBy([
            'comment_id' => $this->comment,
            'user_id' => $user,
        ]);

        if ($existingLikes !== []) {
            foreach ($existingLikes as $existingLike) {
                $this->em->remove($existingLike);
            }

            $this->em->flush();
            $this->isLikedByUser = false;
            return;
        }

        $like = new CommentLike($this->comment, $user);
        $this->em->persist($like);
        $this->em->flush();
        $this->isLikedByUser = true;
    }



}
