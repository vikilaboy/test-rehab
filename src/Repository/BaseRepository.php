<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class BaseRepository
 *
 * @package App\Repository
 */
class BaseRepository extends ServiceEntityRepository
{
    public const NUM_ITEMS = 10;

    public function __construct(ManagerRegistry $registry, $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param int $currentPage
     * @param int $pageSize
     * @return array
     */
    protected function createPaginator(QueryBuilder $queryBuilder, int $currentPage, int $pageSize = self::NUM_ITEMS)
    {
        $currentPage = $currentPage < 1 ? 1 : $currentPage;
        $firstResult = ($currentPage - 1) * $pageSize;
        $query = $queryBuilder
            ->setFirstResult($firstResult)
            ->setMaxResults($pageSize)
            ->getQuery();
        $paginator = new Paginator($query);
        $numResults = $paginator->count();
        $hasPreviousPage = $currentPage > 1;
        $hasNextPage = ($currentPage * $pageSize) < $numResults;

        return [
            'results' => $paginator->getIterator(),
            'currentPage' => $currentPage,
            'hasPreviousPage' => $hasPreviousPage,
            'hasNextPage' => $hasNextPage,
            'previousPage' => $hasPreviousPage ? $currentPage - 1 : null,
            'nextPage' => $hasNextPage ? $currentPage + 1 : null,
            'numPages' => (int)ceil($numResults / $pageSize),
            'haveToPaginate' => $numResults > $pageSize,
        ];
    }
}
