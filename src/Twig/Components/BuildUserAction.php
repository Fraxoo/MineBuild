<?php

namespace App\Twig\Components;

use App\Entity\Build;
use App\Entity\UserFollow;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class BuildUserAction
{
    use DefaultActionTrait;

    #[LiveProp()]
    public Build $build;

    public bool $isFollowedByUser = false;

    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
    ) {
    }

    #[LiveAction()]
    public function follow(): void
    {
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        if($user === $this->build->getAuthor()) {
            return;
        }

        $existingFollow = $this->em->getRepository(UserFollow::class)->findOneBy([
            'follower' => $user,
            'following' => $this->build->getAuthor(),
        ]);

        if ($existingFollow) {
            $this->em->remove($existingFollow);
            $this->isFollowedByUser = false;
        } else {
            $userFollow = new UserFollow($user, $this->build->getAuthor());
            $this->em->persist($userFollow);
            $this->isFollowedByUser = true;
        }

        $this->em->flush();
    }
}


