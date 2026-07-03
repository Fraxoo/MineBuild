<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function countUnreadByUser(User $user): int
    {
        return (int) $this->createQueryBuilder('n')
            ->select('COUNT(DISTINCT n.id)')
            ->andWhere('n.recipient = :user')
            ->andWhere('n.read_at IS NULL')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function markAllAsReadByUser(User $user): void
    {
        $this->getEntityManager()->createQueryBuilder()
            ->update(Notification::class, 'n')
            ->set('n.read_at', ':readAt')
            ->andWhere('n.recipient = :user')
            ->andWhere('n.read_at IS NULL')
            ->setParameter('readAt', new \DateTimeImmutable())
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    public function findNotificationByUserWithPagination(int $page, int $limit = 12, $user = null)
    {
        $offset = ($page - 1) * $limit;

        $queryBuilder = $this->createQueryBuilder('n')
            ->leftJoin('n.actor', 'actor')->addSelect('actor')
            ->leftJoin('n.comment', 'comment')->addSelect('comment')
            ->leftJoin('n.build', 'build')->addSelect('build')
            ->leftJoin('n.recipient', 'recipient')->addSelect('recipient')
            ->orderBy('n.created_at', 'DESC')
            ->andWhere('n.recipient = :user')
            ->setParameter('user', $user)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $paginator = new Paginator($queryBuilder, true);

        return iterator_to_array($paginator->getIterator());
    }

    public function countNotificationByUser($user)
    {
        return (int) $this->createQueryBuilder('n')
            ->select('COUNT(DISTINCT n.id)')
            ->andWhere('n.recipient = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }


}
