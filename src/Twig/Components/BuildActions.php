<?php

namespace App\Twig\Components;

use App\Entity\Build;
use App\Entity\BuildLike;
use App\Entity\BuildRating;
use App\Entity\BuildSave;
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
    public bool $isSavedByUser = false;

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
    public function rate(#[LiveArg] int $value): void
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

            $this->em->persist($rating);
            $this->rating = $value;
        } elseif ($rating->getRating() === $value) {
            $this->em->remove($rating);
            $this->rating = 0;
        } else {
            $rating->setRating($value);
            $this->rating = $value;
        }

        // Sauvegarde d'abord la création / modification / suppression de la note
        $this->em->flush();

        // On récupère les notes actuelles depuis la BDD
        $ratings = $this->buildRatingRepository->findBy([
            'build' => $this->build,
        ]);

        if (count($ratings) === 0) {
            $average = 0;
        } else {
            $total = 0;

            foreach ($ratings as $buildRating) {
                $total += $buildRating->getRating();
            }

            $average = $total / count($ratings);
        }

        $this->build->setRatingAvg(round($average, 1));

        // Sauvegarde de la nouvelle moyenne
        $this->em->flush();
    }

    #[LiveAction()]
    public function save(): void
    {

        $saved = $this->em->getRepository(BuildSave::class)->findOneBy([
            'build' => $this->build,
            'user' => $this->security->getUser(),
        ]);

        if (!$saved) {
            $saved = new BuildSave($this->build, $this->security->getUser());
            $query = $this->build->setSavesCount($this->build->getSavesCount() + 1);
            $this->em->persist($query);
            $this->em->persist($saved);
            $this->isSavedByUser = true;
        } else {
            $query = $this->build->setSavesCount($this->build->getSavesCount() - 1);
            $this->em->persist($query);
            $this->em->remove($saved);
            $this->isSavedByUser = false;
        }

        $this->em->flush();

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