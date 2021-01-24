<?php

namespace App\ORM\Repository;

use App\ORM\Entity\Campaign;
use App\Pagination\DoctrinePaginator;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;

/**
 * Campaign repository class
 */
class CampaignRepository extends EntityRepository
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
        $qb = $this->createQueryBuilder('c');
        $qb
            ->where('c.isDeleted = false')
            ->orderBy('c.createdAt', 'DESC');

        if (isset($params['status']) && count($params['status']) > 0) {
            $or = $qb->expr()->orX();

            if (in_array('1', $params['status'])) {
                $or->add($qb->expr()->andX(
                    $qb->expr()->lte('c.startDt', 'CURRENT_TIMESTAMP()'),
                    $qb->expr()->gt('c.endDt', 'CURRENT_TIMESTAMP()')
                ));
            }

            if (in_array('2', $params['status'])) {
                $or->add($qb->expr()->lte('c.endDt', 'CURRENT_TIMESTAMP()'));
            }

            $qb->andWhere($or);
        }

        if (isset($params['page']) && count($params['page']) > 0) {
            $qb
                ->join('c.pages', 'cp')
                ->andWhere($qb->expr()->in('cp.page', $params['page']));
        }

        if (isset($params['theater']) && count($params['theater']) > 0) {
            $qb
                ->join('c.theaters', 'ct')
                ->andWhere($qb->expr()->in('ct.theater', $params['theater']));
        }

        if (isset($params['special_site']) && count($params['special_site']) > 0) {
            $qb
                ->join('c.specialSites', 'cs')
                ->andWhere($qb->expr()->in('cs.specialSite', $params['special_site']));
        }

        $query = $qb->getQuery();

        return new DoctrinePaginator($query, $page, $maxPerPage);
    }

    /**
     * find for list API
     *
     * @param string $name
     * @return Campaign[]
     */
    public function findForListApi(string $name)
    {
        if (empty($name)) {
            throw new InvalidArgumentException('invalid "name".');
        }

        $qb = $this->createQueryBuilder('c');
        $qb
            ->where('c.isDeleted = false')
            ->andWhere('c.name LIKE :name')
            ->andWhere('c.endDt > CURRENT_TIMESTAMP()')
            ->orderBy('c.createdAt', 'DESC')
            ->setParameter('name', '%' . $name . '%');

        return $qb->getQuery()->getResult();
    }

    /**
     * find one by id
     *
     * @param int $id
     * @return Campaign|null
     */
    public function findOneById($id)
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->where('c.id = :id')
            ->andWhere('c.isDeleted = false')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
