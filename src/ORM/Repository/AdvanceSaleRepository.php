<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\AdvanceSale;
use Doctrine\ORM\EntityRepository;

/**
 * AdvanceSale repository class
 */
class AdvanceSaleRepository extends EntityRepository
{
    public function findOneById(int $id): ?AdvanceSale
    {
        $qb = $this->createQueryBuilder('sale');
        $qb
            ->where('sale.id = :id')
            ->andWhere('sale.isDeleted = false')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
