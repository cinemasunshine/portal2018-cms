<?php

/**
 * PageNewsRepository.php
 */

namespace App\ORM\Repository;

use App\ORM\Entity\News;
use App\ORM\Entity\PageNews;
use Doctrine\ORM\EntityRepository;

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
