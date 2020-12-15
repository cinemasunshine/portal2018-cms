<?php

namespace App\ORM\Repository;

use App\ORM\Entity\Page;
use Doctrine\ORM\EntityRepository;

/**
 * Page repository class
 */
class PageRepository extends EntityRepository
{
    /**
     * find
     *
     * @return Page[]
     */
    public function findActive()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.isDeleted = false');

        return $qb->getQuery()->getResult();
    }

    /**
     * find by ids
     *
     * @param array $ids
     * @return Page[]
     */
    public function findByIds(array $ids)
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where('p.isDeleted = false')
            ->andWhere('p.id IN (:ids)')
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getResult();
    }

    /**
     * find one by id
     *
     * @param int $id
     * @return Page|null
     */
    public function findOneById(int $id)
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where('p.id = :id')
            ->andWhere('p.isDeleted = false')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
