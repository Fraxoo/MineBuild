<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class RegistrationService
{
    public function __construct(
        private RoleRepository $roleRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function register(User $user, string $plainPassword): void
    {
        $userRole = $this->roleRepository->findOneBy(['code' => 'ROLE_USER']);
        $user->setRole($userRole);
        $user->setIsActive(true);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
