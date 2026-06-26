<?php

namespace App\Repository;

use App\Entity\Build;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Build>
 */
class BuildRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Build::class);
    }

    public function findPaginatedOnlineBuilds(int $page, int $limit = 12, array $filters = [], $user = null, $isFavorite = false): array
    {
        $offset = ($page - 1) * $limit;

        $queryBuilder = $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'author')->addSelect('author')
            ->leftJoin('b.images', 'images')->addSelect('images')
            ->leftJoin('b.buildVersions', 'buildVersions')->addSelect('buildVersions')
            ->leftJoin('buildVersions.version', 'mcVersion')->addSelect('mcVersion')
            ->leftJoin('b.buildCategories', 'buildCategories')->addSelect('buildCategories')
            ->leftJoin('buildCategories.category', 'category')->addSelect('category')
            ->andWhere('b.visibility = :visibility')
            ->andWhere('b.deleted_at IS NULL')
            ->setParameter('visibility', 'PUBLIC');


        if ($isFavorite) {
            $queryBuilder->leftJoin('b.saves', 'saves')
                ->andWhere('saves.user = :user')
                ->setParameter('user', $user);
        } else {
            if ($user !== null) {
                $queryBuilder->andWhere('b.author = :user')
                    ->setParameter('user', $user);
            }
        }

        if ($filters) {
            if (in_array(strtoupper($filters['sort']), ['ASC', 'DESC'])) {
                $queryBuilder->orderBy('b.created_at', $filters['sort']);
            } else {
                $queryBuilder->orderBy('b.views_count', 'DESC');
            }
            if ($filters['difficulty'] !== null) {
                $queryBuilder->andWhere('b.difficulty = :difficulty')
                    ->setParameter('difficulty', $filters['difficulty']);
            }

            if ($filters['category'] !== null) {
                $queryBuilder->andWhere('buildCategories.category = :category')
                    ->setParameter('category', $filters['category']);
            }

            if ($filters['versions'] !== null) {
                $queryBuilder->andWhere('buildVersions.version = :version')
                    ->setParameter('version', $filters['versions']);
            }

            if (!empty($filters['search'])) {
                $queryBuilder
                    ->andWhere(
                        $queryBuilder->expr()->orX(
                            'LOWER(b.title) LIKE :search',
                            'LOWER(author.username) LIKE :search',
                            'LOWER(mcVersion.number) LIKE :search',
                            'LOWER(category.name) LIKE :search',
                            'LOWER(category.name_fr) LIKE :search'
                        )
                    )
                    ->setParameter('search', '%' . strtolower($filters['search']) . '%');
            }
        } else {
            $queryBuilder->orderBy('b.created_at', 'DESC');
        }

        $queryBuilder->setFirstResult($offset)
            ->setMaxResults($limit);


        $paginator = new Paginator($queryBuilder, true);

        return iterator_to_array($paginator->getIterator());
    }


    public function countOnlineBuilds(): int
    {
        return (int) $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->andWhere('b.visibility = :visibility')
            ->andWhere('b.deleted_at IS NULL')
            ->setParameter('visibility', 'PUBLIC')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findVisibleByUserWithPagination(User $user, int $limit, int $page): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'author')->addSelect('author')
            ->andWhere('b.author = :user')
            ->andWhere('b.visibility = :visibility')
            ->andWhere('b.deleted_at IS NULL')
            ->setParameter('user', $user)
            ->setParameter('visibility', 'PUBLIC')
            ->orderBy('b.created_at', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countVisibleByUser(User $user): int
    {
        return (int) $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->andWhere('b.author = :user')
            ->andWhere('b.visibility = :visibility')
            ->andWhere('b.deleted_at IS NULL')
            ->setParameter('user', $user)
            ->setParameter('visibility', 'PUBLIC')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countOnlineBuildsWithFilters(array $filters = [], $user = null, $isFavorite = false): int
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'author')->addSelect('author')
            ->leftJoin('b.images', 'images')->addSelect('images')
            ->leftJoin('b.buildVersions', 'buildVersions')->addSelect('buildVersions')
            ->leftJoin('buildVersions.version', 'mcVersion')->addSelect('mcVersion')
            ->leftJoin('b.buildCategories', 'buildCategories')->addSelect('buildCategories')
            ->leftJoin('buildCategories.category', 'category')->addSelect('category')
            ->andWhere('b.visibility = :visibility')
            ->andWhere('b.deleted_at IS NULL')
            ->setParameter('visibility', 'PUBLIC')
            ->select('COUNT(b.id)');


        if ($isFavorite) {
            $queryBuilder->innerJoin('b.saves', 'saves')
                ->andWhere('saves.user = :user')
                ->setParameter('user', $user);
        } else {
            if ($user !== null) {
                $queryBuilder->andWhere('b.author = :user')
                    ->setParameter('user', $user);
            }
        }

        if ($filters) {
            if ($filters['difficulty'] !== null) {
                $queryBuilder->andWhere('b.difficulty = :difficulty')
                    ->setParameter('difficulty', $filters['difficulty']);
            }

            if ($filters['category'] !== null) {
                $queryBuilder->andWhere('buildCategories.category = :category')
                    ->setParameter('category', $filters['category']);
            }

            if ($filters['versions'] !== null) {
                $queryBuilder->andWhere('buildVersions.version = :version')
                    ->setParameter('version', $filters['versions']);
            }

            if (!empty($filters['search'])) {
                $queryBuilder
                    ->andWhere(
                        $queryBuilder->expr()->orX(
                            'LOWER(b.title) LIKE :search',
                            'LOWER(author.username) LIKE :search',
                            'LOWER(mcVersion.number) LIKE :search',
                            'LOWER(category.name) LIKE :search',
                            'LOWER(category.name_fr) LIKE :search'
                        )
                    )
                    ->setParameter('search', '%' . strtolower($filters['search']) . '%');
            }
        }

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }


    public function getAllLikeForAllBuildsByUser(User $user): int
    {
        return (int) $this->createQueryBuilder('b')
            ->select('COALESCE(SUM(b.likes_count), 0) AS totalLikes')
            ->where('b.author = :user_id')
            ->andWhere('b.deleted_at IS NULL')
            ->setParameter('user_id', $user->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalViewForAllBuildsByUser(User $user): int
    {
        return (int) $this->createQueryBuilder('b')
            ->select('COALESCE(SUM(b.views_count), 0) AS totalViews')
            ->where('b.author = :user_id')
            ->andWhere('b.deleted_at IS NULL')
            ->setParameter('user_id', $user->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalSaveForAllBuildsByUser(User $user): int
    {
        return (int) $this->createQueryBuilder('b')
            ->select('COALESCE(SUM(b.saves_count), 0) AS totalSaves')
            ->where('b.author = :user_id')
            ->andWhere('b.deleted_at IS NULL')
            ->setParameter('user_id', $user->getId())
            ->getQuery()
            ->getSingleScalarResult();
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
            ->leftJoin('b.comments', 'comments', 'WITH', 'comments.visibility = :commentVisibility')
            ->orderBy('comments.created_at', 'DESC')
            ->addSelect('comments')
            ->setParameter('commentVisibility', 'PUBLIC')
            ->leftJoin('b.materials', 'materials')->addSelect('materials')
            ->leftJoin('b.author', 'author')->addSelect('author')
            ->leftJoin('b.buildCategories', 'buildCategories')->addSelect('buildCategories')
            ->leftJoin('buildCategories.category', 'category')->addSelect('category')
            ->leftJoin('b.buildTags', 'buildTags')->addSelect('buildTags')
            ->leftJoin('buildTags.tag', 'tag')->addSelect('tag')
            ->leftJoin('b.buildVersions', 'buildVersions')->addSelect('buildVersions')
            ->leftJoin('buildVersions.version', 'Mcversion')->addSelect('Mcversion')
            ->andWhere('b.author = :user')
            ->andWhere('b.deleted_at IS NULL')
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
