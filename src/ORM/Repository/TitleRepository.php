<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\Title;
use App\Pagination\DoctrinePaginator;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;

/**
 * Title repository class
 */
class TitleRepository extends EntityRepository
{
    /**
     * @param array<string, mixed> $params
     */
    public function findForList(array $params, int $page, int $maxPerPage = 10): DoctrinePaginator
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
     * @return Title[]
     */
    public function findForListApi(string $name): array
    {
        if (empty($name)) {
            throw new InvalidArgumentException('invalid "name".');
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
     * @link https://github.com/sergiodlopes/jquery-flexdatalist
     *
     * @param array<string, mixed> $params
     * @return Title[]
     */
    public function findForAutocomplete(array $params): array
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

    public function findOneById(int $id): ?Title
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->where('t.id = :id')
            ->andWhere('t.isDeleted = false')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
