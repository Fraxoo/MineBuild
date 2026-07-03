<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\CommentLike;
use App\Entity\Notification;
use App\Entity\User;
use App\Enum\NotificationType;
use App\Enum\Visibility;
use App\Repository\CommentLikeRepository;
use App\Repository\CommentRepository;
use DateTimeImmutable;
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

            $this->removeLikeNotifications($comment, $user);
            $this->entityManager->flush();

            return false;
        }

        $author = $comment->getAuthor();
        if ($author instanceof User && $author !== $user) {
            $existingNotification = $this->entityManager->getRepository(Notification::class)->findOneBy([
                'actor' => $user,
                'recipient' => $author,
                'type' => NotificationType::LIKE,
                'comment' => $comment,
            ]);

            if (!$existingNotification instanceof Notification) {
                $notification = new Notification();
                $notification->setMessage(' a aimé votre commentaire ');
                $notification->setComment($comment);
                $notification->setType(NotificationType::LIKE);
                $notification->setCreatedAt(new DateTimeImmutable());
                $notification->setActor($user);
                $notification->setRecipient($author);
                $this->entityManager->persist($notification);
            }
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

    private function removeLikeNotifications(Comment $comment, User $user): void
    {
        $author = $comment->getAuthor();
        if (!$author instanceof User || $author === $user) {
            return;
        }

        $notifications = $this->entityManager->getRepository(Notification::class)->findBy([
            'actor' => $user,
            'recipient' => $author,
            'type' => NotificationType::LIKE,
            'comment' => $comment,
        ]);

        foreach ($notifications as $notification) {
            $this->entityManager->remove($notification);
        }
    }
}
