<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserFollow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserFollow>
 */
class UserFollowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserFollow::class);
    }

    public function getFollowingsByUser(User $user, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        $queryBuilder = $this->createQueryBuilder('uf')
            ->andWhere('uf.follower = :user')
            ->setParameter('user', $user)
            ->orderBy('uf.created_at', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $paginator = new Paginator($queryBuilder, false);
        $paginator->setUseOutputWalkers(false);

        return iterator_to_array($paginator->getIterator());
    }

    public function getFollowersByUser(User $user, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        $queryBuilder = $this->createQueryBuilder('uf')
            ->andWhere('uf.following = :user')
            ->setParameter('user', $user)
            ->orderBy('uf.created_at', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $paginator = new Paginator($queryBuilder, false);
        $paginator->setUseOutputWalkers(false);

        return iterator_to_array($paginator->getIterator());
    }

    public function countFollowingsByUser(User $user): int
    {
        return (int) $this->createQueryBuilder('uf')
            ->select('COUNT(DISTINCT following.id)')
            ->innerJoin('uf.following', 'following')
            ->andWhere('uf.follower = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countFollowersByUser(User $user): int
    {
        return (int) $this->createQueryBuilder('uf')
            ->select('COUNT(DISTINCT follower.id)')
            ->innerJoin('uf.follower', 'follower')
            ->andWhere('uf.following = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
