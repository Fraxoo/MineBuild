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

    public function findPaginatedOnlineBuilds(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        return $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'author')->addSelect('author')
            ->leftJoin('b.images', 'images')->addSelect('images')
            ->andWhere('b.visibility = :visibility')
            ->setParameter('visibility', 'PUBLIC')
            ->orderBy('b.created_at', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    

    public function countOnlineBuilds(): int
    {
        return (int) $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->andWhere('b.visibility = :visibility')
            ->setParameter('visibility', 'PUBLIC')
            ->getQuery()
            ->getSingleScalarResult();
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

    /**
     * 
     */
    public function getBuildWithJoinByUser(Build $build): ?Build
    {
        return $this->createQueryBuilder('b')
            ->where('b.id = :build_id')
            ->setParameter('build_id', $build->getId())
            ->leftJoin('b.ratings', 'ratings')->addSelect('ratings')
            ->leftJoin('b.assets', 'assets')->addSelect('assets')
            ->leftJoin('b.images', 'images')->addSelect('images')
            ->leftJoin('b.comments', 'comments')->addSelect('comments')
            ->orderBy('comments.created_at', 'DESC')
            ->leftJoin('b.materials', 'materials')->addSelect('materials')
            ->leftJoin('b.author', 'author')->addSelect('author')
            ->leftJoin('b.buildCategories', 'buildCategories')->addSelect('buildCategories')
            ->leftJoin('buildCategories.category', 'category')->addSelect('category')
            ->leftJoin('b.buildTags', 'buildTags')->addSelect('buildTags')
            ->leftJoin('buildTags.tag', 'tag')->addSelect('tag')
            ->andWhere('b.author = :user')
            ->setParameter('user', $build->getAuthor()->getId())
            ->getQuery()
            ->getOneOrNullResult();
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
