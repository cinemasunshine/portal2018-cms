<?php

/**
 * SpecialSiteNewsRepository.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;
use Cinemasunshine\PortalAdmin\ORM\Entity\News;
use Cinemasunshine\PortalAdmin\ORM\Entity\SpecialSiteNews;

/**
 * SpecialSiteNews repository class
 */
class SpecialSiteNewsRepository extends EntityRepository
{
    /**
     * delete by News
     *
     * @param News $news
     * @return int
     */
    public function deleteByNews(News $news)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $query = $qb
            ->delete($this->getEntityName(), 'sn')
            ->where('sn.news = :news')
            ->setParameter('news', $news->getId())
            ->getQuery();
        
        return $query->execute();
    }
}
