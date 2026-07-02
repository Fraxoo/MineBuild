<?php

namespace App\Service;

use App\Entity\BuildImage;
use App\Entity\User;
use App\Repository\BuildImageRepository;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class NotificationService
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

       public function getNotificationData(Request $request, int $page, int $limit,User $user): array
    {


        $items = $this->notificationRepository->findNotificationByUserWithPagination($page, $limit,$user);
        $totalItems = $this->notificationRepository->countNotificationByUser($user);

        return [
            'items' => $items,
            'totalItems' => $totalItems,
            'totalPages' => ceil($totalItems / $limit),
            'currentPage' => $page,
        ];
    }
}
