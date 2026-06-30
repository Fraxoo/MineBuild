<?php

namespace App\Service;

use App\Entity\Comment;
use App\Enum\Visibility;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CommentService
{
    public function __construct(
        private CommentRepository $commentRepository,
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
