<?php

namespace App\Service;

use App\Entity\ModerationAction;
use App\Entity\User;
use App\Enum\ModerationActionType;
use App\Enum\ReportReasonCode;
use App\Enum\TargetType;
use App\Enum\Visibility;
use App\Exception\ModerationTargetNotFoundException;
use App\Repository\BuildRepository;
use App\Repository\CommentRepository;
use App\Repository\ModerationActionRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final readonly class ModerationActionService
{
    public function __construct(
        private ModerationActionRepository $moderationActionRepository,
        private UserRepository $userRepository,
        private BuildRepository $buildRepository,
        private CommentRepository $commentRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return ModerationAction[]
     */
    public function findAll(): array
    {
        return $this->moderationActionRepository->findAll();
    }

    public function deleteTarget(
        string $type,
        int $id,
        User $moderator,
        string $reason,
        string $reasonCode,
    ): ModerationActionResult {
        $targetType = TargetType::tryFrom($type);
        if (!$targetType) {
            throw new ModerationTargetNotFoundException('Target not found');
        }

        $action = new ModerationAction();
        $action->setAction(ModerationActionType::DELETE);
        $user = null;
        $build = null;

        if ($type === 'comment') {
            $comment = $this->commentRepository->find($id);
            if (!$comment) {
                throw new ModerationTargetNotFoundException('Comment not found');
            }

            $action->setComment($comment);
            $comment->setVisibility(Visibility::HIDDEN);
            $user = $comment->getAuthor();
            $build = $comment->getBuild();
        } elseif ($type === 'build') {
            $build = $this->buildRepository->find($id);
            if (!$build) {
                throw new ModerationTargetNotFoundException('Build not found');
            }

            $build->setVisibility(Visibility::HIDDEN);
            $action->setBuild($build);
            $user = $build->getAuthor();
        } elseif ($type === 'user') {
            $user = $this->userRepository->find($id);
            if (!$user) {
                throw new ModerationTargetNotFoundException('User not found');
            }

            $user->setIsActive(false);
        }

        $action->setCreatedAt(new DateTimeImmutable());
        $action->setTargetType($targetType);
        $action->setModerator($moderator);
        $action->setTargetUser($user);
        $action->setReason($reason);
        $reasonCode = trim($reasonCode);
        $action->setReasonCode(ReportReasonCode::tryFrom($reasonCode) ?? ReportReasonCode::OTHER);

        $this->entityManager->persist($action);
        $this->entityManager->flush();

        return new ModerationActionResult($action, $build);
    }

    public function save(): void
    {
        $this->entityManager->flush();
    }

    public function remove(ModerationAction $moderationAction): void
    {
        $this->entityManager->remove($moderationAction);
        $this->entityManager->flush();
    }
}
