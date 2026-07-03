<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\NotificationRepository;

final readonly class NotificationService
{
    public function __construct(
        private NotificationRepository $notificationRepository,
    ) {
    }

    public function getNotificationData(int $page, int $limit, User $user): array
    {


        $items = $this->notificationRepository->findNotificationByUserWithPagination($page, $limit, $user);
        $totalItems = $this->notificationRepository->countNotificationByUser($user);
        $unreadItems = $this->notificationRepository->countUnreadByUser($user);

        $this->notificationRepository->markAllAsReadByUser($user);

        return [
            'items' => $items,
            'totalItems' => $totalItems,
            'unreadItems' => $unreadItems,
            'totalPages' => ceil($totalItems / $limit),
            'currentPage' => $page,
        ];
    }

    public function hasUnreadForUser(User $user): bool
    {
        return $this->notificationRepository->countUnreadByUser($user) > 0;
    }
}
