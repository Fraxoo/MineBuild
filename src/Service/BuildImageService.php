<?php

namespace App\Service;

use App\Entity\BuildImage;
use App\Repository\BuildImageRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class BuildImageService
{
    public function __construct(
        private BuildImageRepository $buildImageRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return BuildImage[]
     */
    public function findAll(): array
    {
        return $this->buildImageRepository->findAll();
    }

    public function create(BuildImage $buildImage): void
    {
        $this->entityManager->persist($buildImage);
        $this->entityManager->flush();
    }

    public function save(): void
    {
        $this->entityManager->flush();
    }

    public function remove(BuildImage $buildImage): void
    {
        $this->entityManager->remove($buildImage);
        $this->entityManager->flush();
    }
}
