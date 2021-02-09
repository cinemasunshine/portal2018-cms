<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\Campaign;
use Doctrine\ORM\EntityRepository;

/**
 * SpecialSiteCampaign repository class
 */
class SpecialSiteCampaignRepository extends EntityRepository
{
    public function deleteByCampaign(Campaign $campaign): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $query = $qb
            ->delete($this->getEntityName(), 'sc')
            ->where('sc.campaign = :campaign')
            ->setParameter('campaign', $campaign->getId())
            ->getQuery();

        return $query->execute();
    }
}
