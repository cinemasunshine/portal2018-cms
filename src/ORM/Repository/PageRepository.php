<?php
/**
 * PageRepository.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */


namespace Cinemasunshine\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;

use Cinemasunshine\PortalAdmin\ORM\Entity\Page;

/**
 * Page repository class
 */
class PageRepository extends EntityRepository
{
    /**
     * find
     * 
     * @return Page[]
     */
    public function findActive()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.isDeleted = false');
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * find one by id
     *
     * @param int $id
     * @return Page|null
     */
    public function findOneById(int $id)
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where('p.id = :id')
            ->andWhere('p.isDeleted = false')
            ->setParameter('id', $id);
            
        return $qb->getQuery()->getOneOrNullResult();
    }
}
