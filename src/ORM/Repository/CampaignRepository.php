<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\Campaign;
use App\Pagination\DoctrinePaginator;
use Cinemasunshine\ORM\Repositories\CampaignRepository as BaseRepository;
use InvalidArgumentException;

/**
 * @extends BaseRepository<Campaign>
 */
class CampaignRepository extends BaseRepository
{
    public function findOneById(int $id): ?Campaign
    {
        $alias = 'c';
        $qb    = $this->createQueryBuilder($alias);

        $this->addActiveQuery($qb, $alias);

        $qb
            ->andWhere($alias . '.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param array<string, mixed> $params
     */
    public function findForList(array $params, int $page, int $maxPerPage = 10): DoctrinePaginator
    {
        $alias = 'c';
        $qb    = $this->createQueryBuilder($alias);

        $this->addActiveQuery($qb, $alias);

        $qb->orderBy($alias . '.createdAt', 'DESC');

        if (isset($params['status']) && count($params['status']) > 0) {
            $or = $qb->expr()->orX();

            if (in_array('1', $params['status'])) {
                $or->add($qb->expr()->andX(
                    $qb->expr()->lte($alias . '.startDt', 'CURRENT_TIMESTAMP()'),
                    $qb->expr()->gt($alias . '.endDt', 'CURRENT_TIMESTAMP()')
                ));
            }

            if (in_array('2', $params['status'])) {
                $or->add($qb->expr()->lte($alias . '.endDt', 'CURRENT_TIMESTAMP()'));
            }

            $qb->andWhere($or);
        }

        if (isset($params['page']) && count($params['page']) > 0) {
            $qb
                ->join($alias . '.pages', 'cp')
                ->andWhere($qb->expr()->in('cp.page', $params['page']));
        }

        if (isset($params['theater']) && count($params['theater']) > 0) {
            $qb
                ->join($alias . '.theaters', 'ct')
                ->andWhere($qb->expr()->in('ct.theater', $params['theater']));
        }

        if (isset($params['special_site']) && count($params['special_site']) > 0) {
            $qb
                ->join($alias . '.specialSites', 'cs')
                ->andWhere($qb->expr()->in('cs.specialSite', $params['special_site']));
        }

        $query = $qb->getQuery();

        return new DoctrinePaginator($query, $page, $maxPerPage);
    }

    /**
     * @return Campaign[]
     */
    public function findForListApi(string $name): array
    {
        if (empty($name)) {
            throw new InvalidArgumentException('invalid "name".');
        }

        $alias = 'c';
        $qb    = $this->createQueryBuilder($alias);

        $this->addActiveQuery($qb, $alias);

        $qb
            ->andWhere($alias . '.name LIKE :name')
            ->andWhere($alias . '.endDt > CURRENT_TIMESTAMP()')
            ->orderBy($alias . '.createdAt', 'DESC')
            ->setParameter('name', '%' . $name . '%');

        return $qb->getQuery()->getResult();
    }
}
