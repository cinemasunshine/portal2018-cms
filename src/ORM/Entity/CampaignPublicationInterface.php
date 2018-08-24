<?php
/**
 * CampaignPublicationInterface.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\Common\Collections\Collection;

/**
 * CampaignPublication interface
 */
interface CampaignPublicationInterface
{
    /**
     * get campaign_publications
     *
     * @return Collection
     */
    public function getCampaigns() : Collection;
}