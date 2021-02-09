<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\Theater;
use Cinemasunshine\ORM\Repositories\TheaterRepository as BaseRepository;

/**
 * @extends BaseRepository<Theater>
 */
class TheaterRepository extends BaseRepository
{
    /**
     * @return Theater[]
     */
    public function findActive(): array
    {
        $alias = 't';
        $qb    = $this->createQueryBuilder($alias);

        $this->addActiveQuery($qb, $alias);

        $qb->orderBy($alias . '.displayOrder', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int[] $ids
     * @return Theater[]
     */
    public function findByIds(array $ids): array
    {
        $alias = 't';
        $qb    = $this->createQueryBuilder($alias);

        $this->addActiveQuery($qb, $alias);

        $qb
            ->andWhere($alias . '.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy($alias . '.displayOrder', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findOneById(int $id): ?Theater
    {
        $alias = 't';
        $qb    = $this->createQueryBuilder($alias);

        $this->addActiveQuery($qb, $alias);

        $qb
            ->andWhere($alias . '.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
