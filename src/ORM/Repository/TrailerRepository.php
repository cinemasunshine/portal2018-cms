<?php

namespace App\ORM\Repository;

use App\ORM\Entity\Trailer;
use App\Pagination\DoctrinePaginator;
use Doctrine\ORM\EntityRepository;

/**
 * Trailer repository class
 */
class TrailerRepository extends EntityRepository
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
        $qb = $this->createQueryBuilder('t');
        $qb
            ->where('t.isDeleted = false')
            ->orderBy('t.createdAt', 'DESC');

        if (isset($params['name'])) {
            $qb
                ->andWhere('t.name LIKE :name')
                ->setParameter('name', '%' . $params['name'] . '%');
        }

        if (isset($params['page']) && count($params['page']) > 0) {
            $qb
                ->join('t.pageTrailers', 'pt')
                ->andWhere($qb->expr()->in('pt.page', $params['page']));
        }

        if (isset($params['theater']) && count($params['theater']) > 0) {
            $qb
                ->join('t.theaterTrailers', 'tt')
                ->andWhere($qb->expr()->in('tt.theater', $params['theater']));
        }

        if (isset($params['special_site']) && count($params['special_site']) > 0) {
            $qb
                ->join('t.specialSiteTrailers', 'st')
                ->andWhere($qb->expr()->in('st.specialSite', $params['special_site']));
        }

        $query = $qb->getQuery();

        return new DoctrinePaginator($query, $page, $maxPerPage);
    }

    /**
     * find one by id
     *
     * @param int $id
     * @return Trailer|null
     */
    public function findOneById($id)
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->where('t.id = :id')
            ->andWhere('t.isDeleted = false')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
