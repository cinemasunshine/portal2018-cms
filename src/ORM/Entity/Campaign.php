<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\Campaign as BaseCampaign;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Campaign entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\CampaignRepository")
 * @ORM\Table(name="campaign", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class Campaign extends BaseCampaign
{
    /**
     * get published target
     */
    public function getPublishedTargets(): ArrayCollection
    {
        $publications = new ArrayCollection();

        foreach ($this->getPages() as $pageCampaign) {
            /** @var PageCampaign $pageCampaign */
            $publications->add($pageCampaign->getPage());
        }

        foreach ($this->getTheaters() as $theaterCampaign) {
            /** @var TheaterCampaign $theaterCampaign */
            $publications->add($theaterCampaign->getTheater());
        }

        foreach ($this->getSpecialSite() as $specialSiteCampaign) {
            /** @var SpecialSiteCampaign $specialSiteCampaign */
            $publications->add($specialSiteCampaign->getSpecialSite());
        }

        return $publications;
    }
}
