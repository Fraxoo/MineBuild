<?php

namespace App\Twig\Components;

use App\Entity\Build;
use App\Entity\BuildLike;
use App\Entity\BuildRating;
use App\Entity\User;
use App\Repository\BuildRatingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class BuildActions
{
    use DefaultActionTrait;

    #[LiveProp]
    public Build $build;

    #[LiveProp]
    public int $rating = 0;

    #[LiveProp]
    public bool $isLikedByUser = false;

    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private BuildRatingRepository $buildRatingRepository,
    ) {
    }

    public function mount(Build $build): void
    {
        $this->build = $build;

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        $existingRating = $this->buildRatingRepository->findOneBy([
            'build' => $this->build,
            'user' => $user,
        ]);

        $this->rating = $existingRating?->getRating() ?? 0;
    }



    #[LiveAction]
    public function rate(#[LiveArg()] int $value): void
    {
        if ($value < 1 || $value > 5) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        $rating = $this->buildRatingRepository->findOneBy([
            'build' => $this->build,
            'user' => $user,
        ]);

        if (!$rating) {
            $rating = new BuildRating($this->build, $user, $value);
        } else {
            if ($rating->getRating() === $value) {
                $this->rating = 0;
                $this->em->remove($rating);
                $this->em->flush();
                return;
            }
            $rating->setRating($value);
        }

    

        $this->em->persist($rating);
        $this->em->flush();

        $this->rating = $value;
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