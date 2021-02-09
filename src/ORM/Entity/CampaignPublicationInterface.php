<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Doctrine\Common\Collections\Collection;

/**
 * CampaignPublication interface
 */
interface CampaignPublicationInterface
{
    /**
     * get campaigns
     */
    public function getCampaigns(): Collection;
}
