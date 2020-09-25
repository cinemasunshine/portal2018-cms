<?php

/**
 * TitleRepository.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\ORM\Repository;

use App\ORM\Entity\Title;
use App\Pagination\DoctrinePaginator;
use Doctrine\ORM\EntityRepository;

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
        
        if (isset($params['id']) && ! empty($params['id'])) {
            $qb
                ->andWhere('t.id = :id')
                ->setParameter('id', $params['id']);
        }
        
        if (isset($params['name']) && ! empty($params['name'])) {
            $qb
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like('t.name', ':name'),
                    $qb->expr()->like('t.nameKana', ':name'),
                    $qb->expr()->like('t.nameOriginal', ':name')
                ))
                ->setParameter('name', '%' . $params['name'] . '%');
        }
        
        $query = $qb->getQuery();
        
        return new DoctrinePaginator($query, $page, $maxPerPage);
    }
    
    /**
     * find for list API
     *
     * @param string $name
     * @return Title[]
     */
    public function findForListApi(string $name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('invalid "name".');
        }
        
        $qb = $this->createQueryBuilder('t');
        $qb
            ->where('t.isDeleted = false')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->like('t.name', ':name'),
                $qb->expr()->like('t.nameKana', ':name'),
                $qb->expr()->like('t.nameOriginal', ':name')
            ))
            ->orderBy('t.createdAt', 'DESC')
            ->setParameter('name', '%' . $name . '%');
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * find for autocomplete
     *
     * @param array $params
     * @return Title[]
     * @link https://github.com/sergiodlopes/jquery-flexdatalist
     */
    public function findForAutocomplete(array $params)
    {
        $keyword = $params['keyword'];
        
        if ($params['contain'] === 'true') {
            $name = '%' . $keyword . '%';
        } else {
            $name = $keyword . '%';
        }
        
        $qb = $this->createQueryBuilder('t');
        $qb
            ->where('t.isDeleted = false')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->like('t.name', ':name'),
                $qb->expr()->like('t.nameKana', ':name'),
                $qb->expr()->like('t.nameOriginal', ':name')
            ))
            ->orderBy('t.createdAt', 'DESC')
            ->setParameter('name', $name);
        
        return $qb->getQuery()->getResult();
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
