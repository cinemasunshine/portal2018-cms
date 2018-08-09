<?php
/**
 * TitleRepository.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */


namespace Cinemasunshine\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;

use Cinemasunshine\PortalAdmin\ORM\Entity\Title;

/**
 * Title repository class
 */
class TitleRepository extends EntityRepository
{
    /**
     * find by active
     *
     * @return Title[]
     */
    public function findByActive()
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->where('t.isDeleted = false')
            ->orderBy('t.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }
}