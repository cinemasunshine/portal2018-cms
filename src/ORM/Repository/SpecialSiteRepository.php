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
