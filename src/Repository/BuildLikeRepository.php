<?php

namespace App\Repository;

use App\Entity\Build;
use App\Entity\BuildLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<BuildLike>
 */
class BuildLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuildLike::class);
    }


    public function existsForBuildAndUser(Uuid $buildId, Uuid $userId): bool
    {
        $id = $this->createQueryBuilder('bl')
            ->select('bl')
            ->andWhere('IDENTITY(bl.build) = :buildId')
            ->andWhere('IDENTITY(bl.user) = :userId')
            ->setParameter('buildId', $buildId)
            ->setParameter('userId', $userId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $id !== null;
    }
}
