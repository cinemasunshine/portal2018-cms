<?php

/**
 * AdvanceSaleRepository.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\ORM\Repository;

use Doctrine\ORM\EntityRepository;
use App\ORM\Entity\AdvanceSale;
use App\Pagination\DoctrinePaginator;

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
