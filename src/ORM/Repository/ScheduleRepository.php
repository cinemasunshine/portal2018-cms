<?php

/**
 * ScheduleRepository.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\ORM\Repository;

use App\Form\ScheduleFindForm;
use App\ORM\Entity\Schedule;
use App\Pagination\DoctrinePaginator;
use Cinemasunshine\ORM\Repositories\ScheduleRepository as BaseRepository;

/**
 * @extends BaseRepository<Schedule>
 */
class ScheduleRepository extends BaseRepository
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
        $qb = $this->createQueryBuilder('s');

        $this->addActiveQuery($qb, 's');

        if (isset($params['title_name']) && ! empty($params['title_name'])) {
            $qb
                ->join('s.title', 't')
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like('t.name', ':name'),
                    $qb->expr()->like('t.nameKana', ':name'),
                    $qb->expr()->like('t.nameOriginal', ':name')
                ))
                ->setParameter('name', '%' . $params['title_name'] . '%');
        }

        if (isset($params['status']) && ! empty($params['status'])) {
            $or = $qb->expr()->orX();

            if (in_array(ScheduleFindForm::STATUS_SHOWING, $params['status'])) {
                $or->add($qb->expr()->andX(
                    $qb->expr()->lte('s.startDate', 'CURRENT_DATE()'),
                    $qb->expr()->gte('s.endDate', 'CURRENT_DATE()')
                ));
            }

            if (in_array(ScheduleFindForm::STATUS_BEFORE, $params['status'])) {
                $or->add(
                    $qb->expr()->gt('s.startDate', 'CURRENT_DATE()')
                );
            }

            if (in_array(ScheduleFindForm::STATUS_END, $params['status'])) {
                $or->add(
                    $qb->expr()->lt('s.endDate', 'CURRENT_DATE()')
                );
            }

            $qb->andWhere($or);
        }

        if (isset($params['format_system']) && ! empty($params['format_system'])) {
            $qb
                ->join('s.showingFormats', 'sf')
                ->andWhere($qb->expr()->in('sf.system', $params['format_system']));
        }

        if (isset($params['public_start_dt']) && ! empty($params['public_start_dt'])) {
            $qb
                ->andWhere('s.publicStartDt = :public_start_dt')
                ->setParameter('public_start_dt', $params['public_start_dt']);
        }

        if (isset($params['public_end_dt']) && ! empty($params['public_end_dt'])) {
            $qb
                ->andWhere('s.publicEndDt = :public_end_dt')
                ->setParameter('public_end_dt', $params['public_end_dt']);
        }

        $qb->orderBy('s.createdAt', 'DESC');

        $query = $qb->getQuery();

        return new DoctrinePaginator($query, $page, $maxPerPage);
    }

    /**
     * @param int $id
     * @return Schedule|null
     */
    public function findOneById($id): ?Schedule
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->where('s.id = :id')
            ->setParameter('id', $id);

        $this->addActiveQuery($qb, 's');

        return $qb->getQuery()->getOneOrNullResult();
    }
}
