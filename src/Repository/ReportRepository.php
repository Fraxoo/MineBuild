<?php

namespace App\Repository;

use App\Entity\Report;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Report>
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    public function findAllWithIncludeAndPagination($limit, $page)
    {
        $offset = ($page - 1) * $limit;

        $queryBuilder = $this->createQueryBuilder('r')
            ->addSelect('COUNT(DISTINCT targetReports.id) AS targetReportsCount')
            ->leftJoin('r.reporter', 'reporter')->addSelect('reporter')
            ->leftJoin('r.user', 'target')->addSelect('target')
            ->leftJoin('r.build', 'build')->addSelect('build')
            ->leftJoin('r.comment', 'comment')->addSelect('comment')
            ->leftJoin(Report::class, 'targetReports', 'WITH', 'targetReports.user = target')
            ->groupBy('r.id')
            ->addGroupBy('reporter.id')
            ->addGroupBy('target.id')
            ->addGroupBy('build.id')
            ->addGroupBy('comment.id')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $rows = $queryBuilder->getQuery()->getResult();

        return array_map(static function (array $row): Report {
            $report = $row[0];
            $report->setTargetReportsCount((int) $row['targetReportsCount']);

            return $report;
        }, $rows);
    }

    public function countReportByUser($user)
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleResult();
    }
}
