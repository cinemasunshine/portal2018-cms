<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\OyakoCinemaTitle;
use App\Pagination\DoctrinePaginator;
use Doctrine\ORM\EntityRepository;

/**
 * OyakoCinemaTitle repository class
 */
class OyakoCinemaTitleRepository extends EntityRepository
{
    public function findForList(int $page, int $maxPerPage = 10): DoctrinePaginator
    {
        $qb = $this->createQueryBuilder('oct');
        $qb
            ->where('oct.isDeleted = false')
            ->orderBy('oct.createdAt', 'DESC');

        $query = $qb->getQuery();

        return new DoctrinePaginator($query, $page, $maxPerPage);
    }

    public function findOneById(int $id): ?OyakoCinemaTitle
    {
        $qb = $this->createQueryBuilder('oct');
        $qb
            ->where('oct.id = :id')
            ->andWhere('oct.isDeleted = false')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
