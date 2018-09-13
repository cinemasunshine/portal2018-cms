<?php
/**
 * TrailerRepository.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;

use Cinemasunshine\PortalAdmin\ORM\Entity\Trailer;
use Cinemasunshine\PortalAdmin\Pagination\DoctrinePaginator;

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
        
        $query = $qb->getQuery();
        
        return new DoctrinePaginator($query, $page, $maxPerPage);
    }
}