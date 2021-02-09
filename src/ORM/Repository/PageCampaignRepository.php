<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\Campaign;
use Doctrine\ORM\EntityRepository;

/**
 * PageCampaign repository class
 */
class PageCampaignRepository extends EntityRepository
{
    public function deleteByCampaign(Campaign $campaign): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $query = $qb
            ->delete($this->getEntityName(), 'pc')
            ->where('pc.campaign = :campaign')
            ->setParameter('campaign', $campaign->getId())
            ->getQuery();

        return $query->execute();
    }
}
