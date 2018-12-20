<?php
/**
 * SpecialSiteCampaignRepository.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;

use Cinemasunshine\PortalAdmin\ORM\Entity\Campaign;
use Cinemasunshine\PortalAdmin\ORM\Entity\SpecialSiteCampaign;

/**
 * SpecialSiteCampaign repository class
 */
class SpecialSiteCampaignRepository extends EntityRepository
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
            ->delete($this->getEntityName(), 'sc')
            ->where('sc.campaign = :campaign')
            ->setParameter('campaign', $campaign->getId())
            ->getQuery();
        
        return $query->execute();
    }
}