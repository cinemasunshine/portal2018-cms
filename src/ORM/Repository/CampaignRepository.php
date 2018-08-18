<?php
/**
 * CampaignRepository.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;

use Cinemasunshine\PortalAdmin\ORM\Entity\Campaign;
use Cinemasunshine\PortalAdmin\Pagination\DoctrinePaginator;

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
        
        $query = $qb->getQuery();
        
        return new DoctrinePaginator($query, $page, $maxPerPage);
    }
    
    /**
     * find for list API
     *
     * @param string $name
     * @return Title[]
     */
    public function findForListApi(string $name)
    {
        if (empty($name)) {
           throw new \InvalidArgumentException('invalid "name".'); 
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