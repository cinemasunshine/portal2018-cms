<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\Page;
use Cinemasunshine\ORM\Repositories\PageRepository as BaseRepository;

/**
 * @extends BaseRepository<Page>
 */
class PageRepository extends BaseRepository
{
    /**
     * @return Page[]
     */
    public function findActive(): array
    {
        $alias = 'p';
        $qb    = $this->createQueryBuilder($alias);

        $this->addActiveQuery($qb, $alias);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int[] $ids
     * @return Page[]
     */
    public function findByIds(array $ids): array
    {
        $alias = 'p';
        $qb    = $this->createQueryBuilder($alias);

        $this->addActiveQuery($qb, $alias);

        $qb
            ->andWhere($alias . '.id IN (:ids)')
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getResult();
    }

    public function findOneById(int $id): ?Page
    {
        $alias = 'p';
        $qb    = $this->createQueryBuilder($alias);

        $this->addActiveQuery($qb, $alias);

        $qb
            ->andWhere($alias . '.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
