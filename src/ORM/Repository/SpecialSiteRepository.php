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
}
