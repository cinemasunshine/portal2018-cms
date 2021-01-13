<?php

namespace App\ORM\Repository;

use App\ORM\Entity\AdvanceSale;
use Doctrine\ORM\EntityRepository;

/**
 * AdvanceSale repository class
 */
class AdvanceSaleRepository extends EntityRepository
{
    /**
     * find one by id
     *
     * @param int $id
     * @return AdvanceSale|null
     */
    public function findOneById($id)
    {
        $qb = $this->createQueryBuilder('sale');
        $qb
            ->where('sale.id = :id')
            ->andWhere('sale.isDeleted = false')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
