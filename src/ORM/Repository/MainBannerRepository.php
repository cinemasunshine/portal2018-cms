<?php

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
     * find for list page
     *
     * @param array $params
     * @param int   $page
     * @param int   $maxPerPage
     * @return DoctrinePaginator
     */
    public function findForList(array $params, int $page, int $maxPerPage = 10)
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
     * find for list API
     *
     * @param string $name
     * @return MainBanner[]
     */
    public function findForListApi(string $name)
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

    /**
     * find one by id
     *
     * @param int $id
     * @return MainBanner|null
     */
    public function findOneById($id)
    {
        $qb = $this->createQueryBuilder('mb');
        $qb
            ->where('mb.id = :id')
            ->andWhere('mb.isDeleted = false')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
