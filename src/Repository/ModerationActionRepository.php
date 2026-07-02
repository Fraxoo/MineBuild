<?php

namespace App\Repository;

use App\Entity\ModerationAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModerationAction>
 */
class ModerationActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModerationAction::class);
    }

    public function countHistoryReport(): int
    {
        return (int) $this->createQueryBuilder('ma')
            ->select('COUNT(DISTINCT ma.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllWithIncludeAndPagination(int $limit, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('ma')
            ->leftJoin('ma.target_user', 'target_user')->addSelect('target_user')
            ->leftJoin('ma.moderator', 'moderator')->addSelect('moderator')
            ->leftJoin('ma.build', 'build')->addSelect('build')
            ->leftJoin('ma.comment', 'comment')->addSelect('comment')
            ->leftJoin('ma.report', 'r')->addSelect('r')
            ->leftJoin('r.reporter', 'reporter')->addSelect('reporter')
            ->orderBy('ma.created_at', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($queryBuilder, true);

        return iterator_to_array($paginator->getIterator());
    }
}
