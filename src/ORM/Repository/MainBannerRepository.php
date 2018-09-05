<?php
/**
 * MainBannerRepository.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;

use Cinemasunshine\PortalAdmin\ORM\Entity\MainBanner;
use Cinemasunshine\PortalAdmin\Pagination\DoctrinePaginator;

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
        
        if (isset($params['name']) && !empty($params['name'])) {
            $qb
                ->andWhere('mb.name LIKE :name')
                ->setParameter('name', '%' . $params['name'] . '%');
        }
        
        $query = $qb->getQuery();
        
        return new DoctrinePaginator($query, $page, $maxPerPage);
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