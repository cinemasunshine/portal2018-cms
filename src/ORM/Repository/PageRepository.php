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
    public function findActive()
    {
        $alias = 'p';
        $qb    = $this->createQueryBuilder($alias);

        $this->addActiveQuery($qb, $alias);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $ids
     * @return Page[]
     */
    public function findByIds(array $ids)
    {
        $alias = 'p';
        $qb    = $this->createQueryBuilder($alias);

        $this->addActiveQuery($qb, $alias);

        $qb
            ->andWhere($alias . '.id IN (:ids)')
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $id
     * @return Page|null
     */
    public function findOneById(int $id)
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
