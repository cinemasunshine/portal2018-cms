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
    
    /**
     * find by master version
     *
     * @param integer $version
     * @return void
     */
    public function findByMasterVersion(int $version)
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->where('t.isDeleted = false')
            ->andWhere('t.masterVersion = :version')
            ->orderBy('t.displayOrder', 'ASC')
            ->setParameter('version', $version);
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * find one by id
     *
     * @param int $id
     * @return Theater|null
     */
    public function findOneById(int $id)
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->where('t.id = :id')
            ->andWhere('t.isDeleted = false')
            ->setParameter('id', $id);
            
        return $qb->getQuery()->getOneOrNullResult();
    }
}