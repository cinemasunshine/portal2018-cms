<?php
/**
 * CampaignPublicationsInterface.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\Common\Collections\Collection;

/**
 * CampaignPublications interface
 */
interface CampaignPublicationsInterface
{
    /**
     * get campaign_publications
     *
     * @return Collection
     */
    public function getCampaignPublications() : Collection;
}