<?php
/**
 * TheaterNewsRepository.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;

use Cinemasunshine\PortalAdmin\ORM\Entity\News;
use Cinemasunshine\PortalAdmin\ORM\Entity\TheaterNews;

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