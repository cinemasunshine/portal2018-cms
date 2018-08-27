<?php
/**
 * NewsRepository.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;

use Cinemasunshine\PortalAdmin\ORM\Entity\News;
use Cinemasunshine\PortalAdmin\Pagination\DoctrinePaginator;

/**
 * News repository class
 */
class NewsRepository extends EntityRepository
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
        $qb = $this->createQueryBuilder('n');
        $qb
            ->where('n.isDeleted = false')
            ->orderBy('n.createdAt', 'DESC');
        
        $query = $qb->getQuery();
        
        return new DoctrinePaginator($query, $page, $maxPerPage);
    }
    
    /**
     * find one by id
     *
     * @param int $id
     * @return News|null
     */
    public function findOneById($id)
    {
        $qb = $this->createQueryBuilder('n');
        $qb
            ->where('n.id = :id')
            ->andWhere('n.isDeleted = false')
            ->setParameter('id', $id);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
}