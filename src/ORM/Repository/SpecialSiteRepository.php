<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\SpecialSite;
use Doctrine\ORM\EntityRepository;

/**
 * SpecialSite repository class
 */
class SpecialSiteRepository extends EntityRepository
{
    /**
     * @return SpecialSite[]
     */
    public function findActive(): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb->where('s.isDeleted = false');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int[] $ids
     * @return SpecialSite[]
     */
    public function findByIds(array $ids): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->where('s.isDeleted = false')
            ->andWhere('s.id IN (:ids)')
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getResult();
    }

    public function findOneById(int $id): ?SpecialSite
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->where('s.id = :id')
            ->andWhere('s.isDeleted = false')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
