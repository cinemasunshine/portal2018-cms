<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\TheaterMeta;
use Doctrine\ORM\EntityRepository;

/**
 * TheaterMeta repository class
 */
class TheaterMetaRepository extends EntityRepository
{
    /**
     * @return TheaterMeta[]
     */
    public function findActive(): array
    {
        $qb = $this->createQueryBuilder('tm');
        $qb
            ->join('tm.theater', 't')
            ->where('t.isDeleted = false')
            ->orderBy('t.displayOrder', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findOneByTheaterId(int $theaterId): ?TheaterMeta
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
