<?php
/**
 * TheaterMetaRepository.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */


namespace Cinemasunshine\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;

use Cinemasunshine\PortalAdmin\ORM\Entity\Theater;

/**
 * TheaterMeta repository class
 */
class TheaterMetaRepository extends EntityRepository
{
    /**
     * find
     * 
     * @return Theater[]
     */
    public function findActive()
    {
        $qb = $this->createQueryBuilder('tm');
        $qb
            ->join('tm.theater', 't')
            ->where('t.isDeleted = false')
            ->orderBy('t.displayOrder', 'ASC');
        
        return $qb->getQuery()->getResult();
    }
}