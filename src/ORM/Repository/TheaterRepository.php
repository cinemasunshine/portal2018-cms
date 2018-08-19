<?php
/**
 * TheaterRepository.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */


namespace Cinemasunshine\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;

use Cinemasunshine\PortalAdmin\ORM\Entity\Theater;

/**
 * Theater repository class
 */
class TheaterRepository extends EntityRepository
{
    /**
     * find
     * 
     * @return Theater[]
     */
    public function findActive()
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->where('t.isDeleted = false')
            ->orderBy('t.displayOrder', 'ASC');
        
        return $qb->getQuery()->getResult();
    }
}