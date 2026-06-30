<?php

namespace App\Repository;

use App\Entity\Build;
use App\Entity\BuildView;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BuildView>
 */
class BuildViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuildView::class);
    }

    public function hasRecentUserView(Build $build, User $user, DateTimeImmutable $since): bool
    {
        return $this->createQueryBuilder('buildView')
            ->select('1')
            ->andWhere('buildView.build_id = :build')
            ->andWhere('buildView.user_id = :user')
            ->andWhere('buildView.viewed_at >= :since')
            ->setParameter('build', $build)
            ->setParameter('user', $user)
            ->setParameter('since', $since)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult() !== null;
    }

    public function hasRecentIpView(Build $build, string $ipHash, DateTimeImmutable $since): bool
    {
        return $this->createQueryBuilder('buildView')
            ->select('1')
            ->andWhere('buildView.build_id = :build')
            ->andWhere('buildView.ip_hash = :ipHash')
            ->andWhere('buildView.viewed_at >= :since')
            ->setParameter('build', $build)
            ->setParameter('ipHash', $ipHash)
            ->setParameter('since', $since)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult() !== null;
    }

    //    /**
    //     * @return BuildView[] Returns an array of BuildView objects
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

    //    public function findOneBySomeField($value): ?BuildView
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
