<?php

/**
 * PageCampaignRepository.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\ORM\Repository;

use Doctrine\ORM\EntityRepository;
use App\ORM\Entity\Campaign;
use App\ORM\Entity\PageCampaign;

/**
 * PageCampaign repository class
 */
class PageCampaignRepository extends EntityRepository
{
    /**
     * delete by Campaign
     *
     * @param Campaign $campaign
     * @return int
     */
    public function deleteByCampaign(Campaign $campaign)
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
