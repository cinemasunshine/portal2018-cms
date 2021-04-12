<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\SpecialSite;
use Cinemasunshine\ORM\Repositories\SpecialSiteRepository as BaseRepository;

/**
 * @extends BaseRepository<SpecialSite>
 */
class SpecialSiteRepository extends BaseRepository
{
    /**
     * @return SpecialSite[]
     */
    public function findActive(): array
    {
        $alias = 's';
        $qb    = $this->createQueryBuilder($alias);

        $this->addActiveQuery($qb, $alias);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int[] $ids
     * @return SpecialSite[]
     */
    public function findByIds(array $ids): array
    {
        $alias = 's';
        $qb    = $this->createQueryBuilder($alias);

        $this->addActiveQuery($qb, $alias);

        $qb
            ->andWhere($alias . '.id IN (:ids)')
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getResult();
    }

    public function findOneById(int $id): ?SpecialSite
    {
        $alias = 's';
        $qb    = $this->createQueryBuilder($alias);

        $this->addActiveQuery($qb, $alias);

        $qb
            ->andWhere($alias . '.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
