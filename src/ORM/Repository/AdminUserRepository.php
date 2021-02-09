<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\AdminUser;
use App\Pagination\DoctrinePaginator;
use Doctrine\ORM\EntityRepository;

/**
 * AdminUser repository class
 */
class AdminUserRepository extends EntityRepository
{
    /**
     * @param array<string, mixed> $params
     */
    public function findForList(array $params, int $page, int $maxPerPage = 10): DoctrinePaginator
    {
        $qb = $this->createQueryBuilder('au');
        $qb
            ->where('au.isDeleted = false')
            ->orderBy('au.id', 'ASC');

        $query = $qb->getQuery();

        return new DoctrinePaginator($query, $page, $maxPerPage);
    }

    public function findOneById(int $id): ?AdminUser
    {
        $qb = $this->createQueryBuilder('au');
        $qb
            ->where('au.id = :id')
            ->andWhere('au.isDeleted = false')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findOneByName(string $name): ?AdminUser
    {
        $qb = $this->createQueryBuilder('au');
        $qb
            ->where('au.name = :name')
            ->andWhere('au.isDeleted = false')
            ->setParameter('name', $name);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
