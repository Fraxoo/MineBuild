<?php

namespace App\Twig\Components;

use App\Entity\Build;
use App\Entity\BuildLike;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class BuildActions
{
    use DefaultActionTrait;

    #[LiveProp]
    public Build $build;

    #[LiveProp]
    public bool $isLikedByUser = false;

    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
    ) {
    }

    #[LiveAction]
    public function like(): void
    {
        $user = $this->security->getUser();

        if (!$user) {
            return;
        }

        $existingLike = $this->em->getRepository(BuildLike::class)->findOneBy([
            'build' => $this->build,
            'user' => $user,
        ]);

        if ($existingLike) {
            $this->em->remove($existingLike);

            $this->build->setLikesCount(
                max(0, $this->build->getLikesCount() - 1)
            );

            $this->isLikedByUser = false;
        } else {
            $buildLike = new BuildLike($this->build, $user);

            $this->em->persist($buildLike);

            $this->build->setLikesCount(
                $this->build->getLikesCount() + 1
            );

            $this->isLikedByUser = true;
        }

        $this->em->flush();
    }
}