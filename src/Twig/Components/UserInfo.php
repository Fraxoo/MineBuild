<?php

namespace App\Twig\Components;

use App\Entity\User;
use App\Entity\UserFollow;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class UserInfo
{
    use DefaultActionTrait;

    #[LiveProp()]
    public User $user;

    public bool $isFollowedByUser = false;

    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
    ) {
    }

    #[LiveAction()]
    public function follow(): void
    {
        $actualUser = $this->security->getUser();
        if (!$actualUser) {
            return;
        }

        if ($actualUser === $this->user) {
            return;
        }

        $existingFollow = $this->em->getRepository(UserFollow::class)->findOneBy([
            'follower' => $actualUser,
            'following' => $this->user,
        ]);

        if ($existingFollow) {
            $this->em->remove($existingFollow);
            $this->isFollowedByUser = false;
        } else {
            $userFollow = new UserFollow($actualUser, $this->user);
            $this->em->persist($userFollow);
            $this->isFollowedByUser = true;
        }

        $this->em->flush();
    }
}
