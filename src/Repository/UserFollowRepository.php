<?php

namespace App\Repository;

use App\Entity\UserFollow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function getFollowingsByUser($user, $page, $limit)
    {
        $offset = ($page - 1) * $limit;


        $queryBuilder = $this->createQueryBuilder('uf')
            ->andWhere('uf.following = :user')
            ->setParameter('user', $user)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $queryBuilder->getQuery()->getResult();

    }

        public function getFollowersByUser($user, $page, $limit)
    {
        $offset = ($page - 1) * $limit;


        $queryBuilder = $this->createQueryBuilder('uf')
            ->andWhere('uf.follower = :user')
            ->setParameter('user', $user)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $queryBuilder->getQuery()->getResult();

    }
}
