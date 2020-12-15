<?php

namespace App\ORM\Repository;

use App\ORM\Entity\MainBanner;
use App\ORM\Entity\TheaterMainBanner;
use Doctrine\ORM\EntityRepository;

/**
 * TheaterMainBanner repository class
 */
class TheaterMainBannerRepository extends EntityRepository
{
    /**
     * delete by MainBanner
     *
     * @param MainBanner $mainBanner
     * @return int
     */
    public function deleteByMainBanner(MainBanner $mainBanner)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $query = $qb
            ->delete($this->getEntityName(), 'tm')
            ->where('tm.mainBanner = :main_banner')
            ->setParameter('main_banner', $mainBanner->getId())
            ->getQuery();

        return $query->execute();
    }
}
