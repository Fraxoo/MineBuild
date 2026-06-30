<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Repository\BuildRepository;
use App\Repository\UserFollowRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private BuildRepository $buildRepository,
        private UserFollowRepository $userFollowRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $parameterBag,
    ) {
    }

    /**
     * @return User[]
     */
    public function findAll(): array
    {
        return $this->userRepository->findAll();
    }

    public function create(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function getProfileData(User $user, string $section, int $page, ?User $actualUser): array
    {
        $items = null;
        $limit = 12;
        $totalItems = 0;
        $isFollow = false;
        $followType = null;

        if ($section === 'app_user_favorites') {
            $items = $this->buildRepository->findPaginatedOnlineBuilds($page, $limit, [], $user, true);
            $totalItems = $this->buildRepository->countOnlineBuildsWithFilters([], $user, true);
        } elseif ($section === 'app_user_following') {
            $items = $this->userFollowRepository->getFollowingsByUser($user, $page, $limit);
            $totalItems = $this->userFollowRepository->countFollowingsByUser($user);
            $isFollow = true;
            $followType = 'following';
        } elseif ($section === 'app_user_followers') {
            $items = $this->userFollowRepository->getFollowersByUser($user, $page, $limit);
            $totalItems = $this->userFollowRepository->countFollowersByUser($user);
            $isFollow = true;
            $followType = 'followers';
        } else {
            $items = $this->buildRepository->findPaginatedOnlineBuilds($page, $limit, [], $user);
            $totalItems = $this->buildRepository->countOnlineBuildsWithFilters([], $user);
        }

        $isFollowedByUser = $actualUser instanceof User
            ? $user->getFollowerRelations()->exists(fn($i, $rel) => $rel->getFollower()->getId() === $actualUser->getId())
            : false;

        return [
            'user' => $user,
            'totalLikes' => $this->buildRepository->getAllLikeForAllBuildsByUser($user),
            'totalViews' => $this->buildRepository->getTotalViewForAllBuildsByUser($user),
            'totalSaves' => $this->buildRepository->getTotalSaveForAllBuildsByUser($user),
            'items' => $items,
            'totalPages' => ceil($totalItems / $limit),
            'totalItems' => $totalItems,
            'currentPage' => $page,
            'isFollow' => $isFollow,
            'followType' => $followType,
            'isFollowedByUser' => $isFollowedByUser,
        ];
    }

    public function updateProfile(User $user, mixed $file): void
    {
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename . '-' . uniqid() . '.' . $file->guessExtension();

            try {
                $file->move(
                    (string) $this->parameterBag->get('avatars_directory'),
                    $newFilename
                );
            } catch (FileException) {
                // Same behavior as the previous controller: ignore upload failure.
            }

            $user->setAvatarUrl($newFilename);
        }

        $this->entityManager->flush();
    }

    public function updatePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
            return false;
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
        $this->entityManager->flush();

        return true;
    }

    public function remove(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function getFollowList(int $id, string $type): array
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new UserNotFoundException('Utilisateur introuvable.');
        }

        if ($type === 'followers') {
            $follows = $this->userFollowRepository->findBy([
                'following' => $user,
            ]);
        } else {
            $follows = $this->userFollowRepository->findBy([
                'follower' => $user,
            ]);
        }

        return [
            'user' => $user,
            'follows' => $follows,
            'type' => $type,
        ];
    }
}
