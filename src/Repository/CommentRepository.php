<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findVisibleByUserWithPagination(User $user, int $limit, int $page): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.author', 'author')->addSelect('author')
            ->leftJoin('c.build', 'build')->addSelect('build')
            ->andWhere('c.author = :user')
            ->andWhere('c.visibility = :visibility')
            ->andWhere('c.deleted_at IS NULL')
            ->setParameter('user', $user)
            ->setParameter('visibility', 'PUBLIC')
            ->orderBy('c.created_at', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countVisibleByUser(User $user): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.author = :user')
            ->andWhere('c.visibility = :visibility')
            ->andWhere('c.deleted_at IS NULL')
            ->setParameter('user', $user)
            ->setParameter('visibility', 'PUBLIC')
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return Comment[] Returns an array of Comment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Comment
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
