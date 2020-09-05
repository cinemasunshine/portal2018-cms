<?php

/**
 * SpecialSiteNewsRepository.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\ORM\Repository;

use App\ORM\Entity\News;
use App\ORM\Entity\SpecialSiteNews;
use Doctrine\ORM\EntityRepository;

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
