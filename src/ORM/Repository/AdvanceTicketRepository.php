<?php

namespace App\ORM\Repository;

use App\Form\AdvanceTicketFindForm;
use App\ORM\Entity\AdvanceTicket;
use App\Pagination\DoctrinePaginator;
use Doctrine\ORM\EntityRepository;

/**
 * AdvanceTicket repository class
 */
class AdvanceTicketRepository extends EntityRepository
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
        $qb = $this->createQueryBuilder('at');
        $qb
            ->join('at.advanceSale', 'sale')
            ->where('sale.isDeleted = false')
            ->andWhere('at.isDeleted = false')
            ->orderBy('at.createdAt', 'DESC');

        if (isset($params['theater']) && count($params['theater']) > 0) {
            $qb->andWhere($qb->expr()->in('sale.theater', $params['theater']));
        }

        if (isset($params['status']) && count($params['status']) > 0) {
            $or = $qb->expr()->orX();

            if (in_array(AdvanceTicketFindForm::STATUS_SALE, $params['status'])) {
                $or->add($qb->expr()->andX(
                    $qb->expr()->eq('at.isSalesEnd', 'false'),
                    $qb->expr()->lte('at.releaseDt', 'CURRENT_TIMESTAMP()'),
                    $qb->expr()->orX(
                        $qb->expr()->isNull('sale.publishingExpectedDate'),
                        $qb->expr()->gt('sale.publishingExpectedDate', 'CURRENT_DATE()')
                    )
                ));
            }

            if (in_array(AdvanceTicketFindForm::STATUS_PRE_SALE, $params['status'])) {
                $or->add($qb->expr()->andX(
                    $qb->expr()->eq('at.isSalesEnd', 'false'),
                    $qb->expr()->gt('at.releaseDt', 'CURRENT_TIMESTAMP()')
                ));
            }

            if (in_array(AdvanceTicketFindForm::STATUS_SALE_END, $params['status'])) {
                $or->add($qb->expr()->orX(
                    $qb->expr()->eq('at.isSalesEnd', 'true'),
                    $qb->expr()->andX(
                        $qb->expr()->isNotNull('sale.publishingExpectedDate'),
                        $qb->expr()->lte('sale.publishingExpectedDate', 'CURRENT_DATE()')
                    )
                ));
            }

            $qb->andWhere($or);
        }

        if (isset($params['release_dt']) && ! empty($params['release_dt'])) {
            $qb
                ->andWhere('at.releaseDt = :release_dt')
                ->setParameter('release_dt', $params['release_dt']);
        }

        $query = $qb->getQuery();

        return new DoctrinePaginator($query, $page, $maxPerPage);
    }

    /**
     * find one by id
     *
     * @param int $id
     * @return AdvanceTicket|null
     */
    public function findOneById($id)
    {
        $qb = $this->createQueryBuilder('at');
        $qb
            ->where('at.id = :id')
            ->andWhere('at.isDeleted = false')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
