<?php

/**
 * PageNewsRepository.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;
use Cinemasunshine\PortalAdmin\ORM\Entity\News;
use Cinemasunshine\PortalAdmin\ORM\Entity\PageNews;

/**
 * PageNews repository class
 */
class PageNewsRepository extends EntityRepository
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
            ->delete($this->getEntityName(), 'pn')
            ->where('pn.news = :news')
            ->setParameter('news', $news->getId())
            ->getQuery();
        
        return $query->execute();
    }
}
