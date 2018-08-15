<?php
/**
 * TitleRepository.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */


namespace Cinemasunshine\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;

use Cinemasunshine\PortalAdmin\ORM\Entity\Title;
use Cinemasunshine\PortalAdmin\Pagination\DoctrinePaginator;

/**
 * Title repository class
 */
class TitleRepository extends EntityRepository
{
    /**
     * find for list page
     * 
     * @param array $params
     * @param int   $page
     * @param int   $maxPerPage
     * @return DoctrinePaginator
     */
    public function findForList(array $params, int $page, int $maxPerPage = 10)
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->where('t.isDeleted = false')
            ->orderBy('t.createdAt', 'DESC');
        
        if (isset($params['id']) && !empty($params['id'])) {
            $qb
                ->andWhere('t.id = :id')
                ->setParameter('id', $params['id']);
        }
        
        if (isset($params['name']) && !empty($params['name'])) {
            $qb
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like('t.name', ':name'),
                    $qb->expr()->like('t.nameKana', ':name'),
                    $qb->expr()->like('t.nameEn', ':name')
                ))
                ->setParameter('name', '%' . $params['name'] . '%');
        }
        
        $query = $qb->getQuery();
        
        return new DoctrinePaginator($query, $page, $maxPerPage);
    }
    
    /**
     * find one by id
     *
     * @param int $id
     * @return Title|null
     */
    public function findOneById($id)
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->where('t.id = :id')
            ->andWhere('t.isDeleted = false')
            ->setParameter('id', $id);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
}