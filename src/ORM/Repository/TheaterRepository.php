<?php

/**
 * TheaterRepository.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

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
    public function findActive()
    {
        $alias = 't';
        $qb    = $this->createQueryBuilder($alias);

        $this->addActiveQuery($qb, $alias);

        $qb->orderBy($alias . '.displayOrder', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $ids
     * @return Theater[]
     */
    public function findByIds(array $ids)
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

    /**
     * @param int $id
     * @return Theater|null
     */
    public function findOneById(int $id)
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
