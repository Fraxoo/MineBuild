<?php

namespace App\Repository;

use App\Entity\ModerationAction;
use App\Entity\Report;
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
            ->addSelect('COUNT(DISTINCT targetReports.id) AS targetReportsCount')
            ->leftJoin(Report::class, 'targetReports', 'WITH', 'targetReports.user = target_user')
            ->groupBy('ma.id')
            ->addGroupBy('target_user.id')
            ->addGroupBy('moderator.id')
            ->addGroupBy('build.id')
            ->addGroupBy('comment.id')
            ->addGroupBy('r.id')
            ->addGroupBy('reporter.id')
            ->orderBy('ma.created_at', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($queryBuilder, true);
        $rows = iterator_to_array($paginator->getIterator());

        return array_map(static function (array $row): ModerationAction {
            $moderationAction = $row[0];
            $moderationAction->setTargetReportsCount((int) $row['targetReportsCount']);

            return $moderationAction;
        }, $rows);
    }
}
