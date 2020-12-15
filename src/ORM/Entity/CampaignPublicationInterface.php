<?php

namespace App\ORM\Entity;

use Doctrine\Common\Collections\Collection;

/**
 * CampaignPublication interface
 */
interface CampaignPublicationInterface
{
    /**
     * get campaigns
     *
     * @return Collection
     */
    public function getCampaigns(): Collection;
}
