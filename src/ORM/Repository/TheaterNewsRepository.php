<?php

/**
 * TheaterNewsRepository.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\ORM\Repository;

use App\ORM\Entity\News;
use App\ORM\Entity\TheaterNews;
use Doctrine\ORM\EntityRepository;

/**
 * TheaterNews repository class
 */
class TheaterNewsRepository extends EntityRepository
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
            ->delete($this->getEntityName(), 'tn')
            ->where('tn.news = :news')
            ->setParameter('news', $news->getId())
            ->getQuery();

        return $query->execute();
    }
}
