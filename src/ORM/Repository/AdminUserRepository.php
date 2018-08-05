<?php
/**
 * AdminUserRepository.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;

use Cinemasunshine\PortalAdmin\ORM\Entity\AdminUser;

/**
 * AdminUser repository class
 */
class AdminUserRepository extends EntityRepository
{
    /**
     * find one by name
     *
     * @param string $name
     * @return AdminUser|null
     */
    public function findOneByName($name)
    {
        $qb = $this->createQueryBuilder('au');
        $qb
            ->where('au.name = :name')
            ->andWhere('au.isDeleted = false')
            ->setParameter('name', $name);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
}