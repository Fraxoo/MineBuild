<?php

namespace App\Service;

use App\Entity\ModerationAction;
use App\Entity\Report;
use App\Entity\User;
use App\Enum\ModerationActionType;
use App\Enum\ReportReasonCode;
use App\Enum\ReportStatus;
use App\Enum\TargetType;
use App\Enum\Visibility;
use App\Exception\ReportTargetNotFoundException;
use App\Repository\BuildRepository;
use App\Repository\CommentRepository;
use App\Repository\ModerationActionRepository;
use App\Repository\ReportRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final readonly class ReportService
{
    public function __construct(
        private UserRepository $userRepository,
        private BuildRepository $buildRepository,
        private CommentRepository $commentRepository,
        private ModerationActionRepository $moderationActionRepository,
        private ReportRepository $reportRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function getDashboardData(string $targetType, int $page, int $limit): array
    {
        if ($targetType === 'users') {
            $totalItems = $this->userRepository->countUsers();
            $items = $this->userRepository->findAllWithPagination($limit, $page);
        } elseif ($targetType === 'history') {
            $totalItems = $this->moderationActionRepository->countHistoryReport();
            $items = $this->moderationActionRepository->findAllWithIncludeAndPagination($limit, $page);
        } else {
            $totalItems = $this->reportRepository->countPendingReport();
            $items = $this->reportRepository->findPendingWithIncludeAndPagination($limit, $page);
        }

        return [
            'items' => $items,
            'totalItems' => $totalItems,
            'currentPage' => $page,
            'totalPages' => ceil($totalItems / $limit),
        ];
    }

    public function getUserDashboardData(string $targetType, User $user, int $page, int $limit): array
    {
        $totalItems = $this->reportRepository->countPendingReport();
        $items = 0;

        if ($targetType === 'builds') {
            $totalItems = $this->buildRepository->countVisibleByUser($user);
            $items = $this->buildRepository->findVisibleByUserWithPagination($user, $limit, $page);
        } elseif ($targetType === 'comments') {
            $totalItems = $this->commentRepository->countVisibleByUser($user);
            $items = $this->commentRepository->findVisibleByUserWithPagination($user, $limit, $page);
        } elseif ($targetType === 'reports') {
            $totalItems = $this->reportRepository->countPendingReportByUser($user);
            $items = $this->reportRepository->findPendingByUserWithIncludeAndPagination($user, $limit, $page);
        }

        return [
            'user' => $user,
            'items' => $items,
            'totalItems' => $totalItems,
            'currentPage' => $page,
            'totalPages' => ceil($totalItems / $limit),
        ];
    }

    public function createReport(Report $report, int $id, string $targetType, User $reporter): void
    {
        $targetTypeEnum = TargetType::tryFrom($targetType);
        if (!$targetTypeEnum) {
            throw new ReportTargetNotFoundException('Target not found');
        }

        $report->setCreatedAt(new DateTimeImmutable());
        $report->setReporter($reporter);
        $report->setStatus(ReportStatus::PENDING);
        $report->setTargetType($targetTypeEnum);

        $target = match ($targetType) {
            'comment' => $this->commentRepository->find($id),
            'build' => $this->buildRepository->find($id),
            'user' => $this->userRepository->find($id),
            default => null,
        };

        if (!$target) {
            throw new ReportTargetNotFoundException('Target not found');
        }

        match ($targetType) {
            'comment' => $report->setComment($target)->setUser($target->getAuthor()),
            'build' => $report->setBuild($target)->setUser($target->getAuthor()),
            'user' => $report->setUser($target),
        };

        $targetUser = $report->getUser();
        if ($targetUser instanceof User) {
            $targetUser->setReportsCount(($targetUser->getReportsCount() ?? 0) + 1);
        }

        $this->entityManager->persist($report);
        $this->entityManager->flush();
    }

    public function save(): void
    {
        $this->entityManager->flush();
    }

    public function confirm(Report $report, User $moderator, string $reason): void
    {
        $report->setHandledAt(new DateTimeImmutable());
        $report->setHandledBy($moderator);
        $report->setStatus(ReportStatus::CONFIRMED);
        $action = new ModerationAction();
        $action->setAction(ModerationActionType::DELETE);

        if ($report->getTargetType() === TargetType::COMMENT) {
            $comment = $report->getComment();
            $action->setComment($comment);
            $comment->setVisibility(Visibility::HIDDEN);
        } elseif ($report->getTargetType() === TargetType::BUILD) {
            $build = $report->getBuild();
            $build->setVisibility(Visibility::HIDDEN);
            $action->setBuild($build);
        } elseif ($report->getTargetType() === TargetType::USER) {
            $user = $report->getUser();
            $user->setIsActive(false);
        }

        $action->setCreatedAt(new DateTimeImmutable());
        $action->setTargetType($report->getTargetType());
        $action->setModerator($moderator);
        $action->setTargetUser($report->getUser());
        $action->setReason($reason);
        $action->setReasonCode($report->getReasonCode() ?? ReportReasonCode::OTHER);
        $action->setReport($report);

        $this->entityManager->persist($action);
        $this->entityManager->persist($report);
        $this->entityManager->flush();
    }

    public function reject(Report $report, User $moderator): void
    {
        $report->setHandledAt(new DateTimeImmutable());
        $report->setHandledBy($moderator);
        $report->setStatus(ReportStatus::REJECTED);

        $this->entityManager->persist($report);
        $this->entityManager->flush();
    }
}
