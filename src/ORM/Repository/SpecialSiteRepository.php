<?php
/**
 * SpecialSiteRepository.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */


namespace Cinemasunshine\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;

use Cinemasunshine\PortalAdmin\ORM\Entity\SpecialSite;

/**
 * SpecialSite repository class
 */
class SpecialSiteRepository extends EntityRepository
{
    /**
     * find
     * 
     * @return SpecialSite[]
     */
    public function findActive()
    {
        $qb = $this->createQueryBuilder('s');
        $qb->where('s.isDeleted = false');
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * find by ids
     *
     * @param array $ids
     * @return SpecialSite[]
     */
    public function findByIds(array $ids)
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->where('s.isDeleted = false')
            ->andWhere('s.id IN (:ids)')
            ->setParameter('ids', $ids);
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * find one by id
     *
     * @param int $id
     * @return SpecialSite|null
     */
    public function findOneById(int $id)
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->where('s.id = :id')
            ->andWhere('s.isDeleted = false')
            ->setParameter('id', $id);
            
        return $qb->getQuery()->getOneOrNullResult();
    }
}
