<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\CommentLike;
use App\Entity\User;
use App\Enum\Visibility;
use App\Repository\CommentLikeRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CommentService
{
    public function __construct(
        private CommentRepository $commentRepository,
        private CommentLikeRepository $commentLikeRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return Comment[]
     */
    public function findAll(): array
    {
        return $this->commentRepository->findAll();
    }

    public function create(Comment $comment): void
    {
        $comment->setVisibility(Visibility::PUBLIC);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }

    public function isLikedByUser(Comment $comment, ?User $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        return $this->commentLikeRepository->findOneBy([
            'comment_id' => $comment,
            'user_id' => $user,
        ]) !== null;
    }

    public function toggleLike(Comment $comment, User $user): bool
    {
        $existingLikes = $this->commentLikeRepository->findBy([
            'comment_id' => $comment,
            'user_id' => $user,
        ]);

        if ($existingLikes !== []) {
            foreach ($existingLikes as $existingLike) {
                $this->entityManager->remove($existingLike);
            }

            $this->entityManager->flush();

            return false;
        }

        $this->entityManager->persist(new CommentLike($comment, $user));
        $this->entityManager->flush();

        return true;
    }

    public function save(): void
    {
        $this->entityManager->flush();
    }

    public function remove(Comment $comment): void
    {
        $this->entityManager->remove($comment);
        $this->entityManager->flush();
    }
}
