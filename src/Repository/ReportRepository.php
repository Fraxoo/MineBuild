<?php

namespace App\Repository;

use App\Entity\Report;
use App\Entity\User;
use App\Enum\ReportStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    public function findPendingWithIncludeAndPagination($limit, $page)
    {
        $offset = ($page - 1) * $limit;

        $queryBuilder = $this->createQueryBuilder('r')
            ->leftJoin('r.reporter', 'reporter')->addSelect('reporter')
            ->leftJoin('r.user', 'target')->addSelect('target')
            ->leftJoin('r.build', 'build')->addSelect('build')
            ->leftJoin('r.comment', 'comment')->addSelect('comment')
            ->andWhere('r.status = :status')
            ->setParameter('status' , ReportStatus::PENDING)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $paginator = new Paginator($queryBuilder, true);

        return iterator_to_array($paginator->getIterator());
    }

    public function countReportByUser($user)
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(DISTINCT r.id)')
            ->andWhere('r.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleResult();
    }

    public function findPendingByUserWithIncludeAndPagination(User $user, int $limit, int $page): array
    {
        $offset = ($page - 1) * $limit;

        $queryBuilder = $this->createQueryBuilder('r')
            ->leftJoin('r.reporter', 'reporter')->addSelect('reporter')
            ->leftJoin('r.user', 'target')->addSelect('target')
            ->leftJoin('r.build', 'build')->addSelect('build')
            ->leftJoin('r.comment', 'comment')->addSelect('comment')
            ->andWhere('r.status = :status')
            ->andWhere('r.user = :user')
            ->setParameter('status', ReportStatus::PENDING)
            ->setParameter('user', $user)
            ->orderBy('r.created_at', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $paginator = new Paginator($queryBuilder, true);

        return iterator_to_array($paginator->getIterator());
    }

    public function countPendingReportByUser(User $user): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(DISTINCT r.id)')
            ->andWhere('r.status = :status')
            ->andWhere('r.user = :user')
            ->setParameter('status', ReportStatus::PENDING)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countPendingReport(){
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(DISTINCT r.id)')
            ->andWhere('r.status = :status')
            ->setParameter('status', ReportStatus::PENDING)
            ->getQuery()
            ->getSingleScalarResult();
    }

    
}
