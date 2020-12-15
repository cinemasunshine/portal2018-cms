<?php

namespace App\ORM\Repository;

use App\ORM\Entity\TheaterMeta;
use Doctrine\ORM\EntityRepository;

/**
 * TheaterMeta repository class
 */
class TheaterMetaRepository extends EntityRepository
{
    /**
     * find
     *
     * @return TheaterMeta[]
     */
    public function findActive()
    {
        $qb = $this->createQueryBuilder('tm');
        $qb
            ->join('tm.theater', 't')
            ->where('t.isDeleted = false')
            ->orderBy('t.displayOrder', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * find one by theater id
     *
     * @param int $theaterId
     * @return TheaterMeta|null
     */
    public function findOneByTheaterId($theaterId)
    {
        $qb = $this->createQueryBuilder('tm');
        $qb
            ->join('tm.theater', 't')
            ->where('t.id = :id')
            ->andWhere('t.isDeleted = false')
            ->setParameter('id', $theaterId);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
