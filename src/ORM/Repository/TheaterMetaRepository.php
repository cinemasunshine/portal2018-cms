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
    
    /**
     * find one by theater id
     *
     * @param int $theaterId
     * @return TheaterMeta|null
     */
    public function findOneByTheaterId($theaterId)
    {
        $qb = $this->createQueryBuilder('tm');
        $qb
            ->join('tm.theater', 't')
            ->where('t.id = :id')
            ->andWhere('t.isDeleted = false')
            ->setParameter('id', $theaterId);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
}