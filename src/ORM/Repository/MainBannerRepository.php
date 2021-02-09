<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\MainBanner;
use App\Pagination\DoctrinePaginator;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;

/**
 * MainBanner repository class
 */
class MainBannerRepository extends EntityRepository
{
    /**
     * @param array<string, mixed> $params
     */
    public function findForList(array $params, int $page, int $maxPerPage = 10): DoctrinePaginator
    {
        $qb = $this->createQueryBuilder('mb');
        $qb
            ->where('mb.isDeleted = false')
            ->orderBy('mb.createdAt', 'DESC');

        if (isset($params['name']) && ! empty($params['name'])) {
            $qb
                ->andWhere('mb.name LIKE :name')
                ->setParameter('name', '%' . $params['name'] . '%');
        }

        $query = $qb->getQuery();

        return new DoctrinePaginator($query, $page, $maxPerPage);
    }

    /**
     * @return MainBanner[]
     */
    public function findForListApi(string $name): array
    {
        if (empty($name)) {
            throw new InvalidArgumentException('invalid "name".');
        }

        $qb = $this->createQueryBuilder('mb');
        $qb
            ->where('mb.isDeleted = false')
            ->andWhere('mb.name LIKE :name')
            ->orderBy('mb.createdAt', 'DESC')
            ->setParameter('name', '%' . $name . '%');

        return $qb->getQuery()->getResult();
    }

    public function findOneById(int $id): ?MainBanner
    {
        $qb = $this->createQueryBuilder('mb');
        $qb
            ->where('mb.id = :id')
            ->andWhere('mb.isDeleted = false')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
