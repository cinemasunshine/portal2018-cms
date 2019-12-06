<?php
/**
 * OyakoCinemaTitleRepository.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Repository;

use Cinemasunshine\PortalAdmin\Pagination\DoctrinePaginator;
use Doctrine\ORM\EntityRepository;

/**
 * OyakoCinemaTitle repository class
 */
class OyakoCinemaTitleRepository extends EntityRepository
{
    /**
     * find for list page
     *
     * @param int   $page
     * @param int   $maxPerPage
     * @return DoctrinePaginator
     */
    public function findForList(int $page, int $maxPerPage = 10)
    {
        $qb = $this->createQueryBuilder('oct');
        $qb
            ->where('oct.isDeleted = false')
            ->orderBy('oct.createdAt', 'DESC');

        $query = $qb->getQuery();

        return new DoctrinePaginator($query, $page, $maxPerPage);
    }
}
