<?php

namespace App\Repository;

use App\Entity\Build;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Build>
 */
class BuildRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Build::class);
    }

    public function getAllLikeForAllBuildsByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b', 'COUNT(bl.build) AS likeCount')
            ->leftJoin('b.likes', 'bl')
            ->groupBy('b.id')
            ->where('b.author = :user_id')
            ->setParameter('user_id', $user->getId());

        return $qb->getQuery()->getResult();
    }

    public function getTotalViewForAllBuildsByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b', 'COUNT(b.views_count) AS viewCount')
            ->groupBy('b.id')
            ->where('b.author = :user_id')
            ->setParameter('user_id', $user->getId());

        return $qb->getQuery()->getResult();
    }

    public function getTotalSaveForAllBuildsByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b', 'COUNT(bs.build) AS saveCount')
            ->leftJoin('b.saves', 'bs')
            ->groupBy('b.id')
            ->where('b.author = :user_id')
            ->setParameter('user_id', $user->getId());

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Build[] Returns an array of Build objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Build
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
