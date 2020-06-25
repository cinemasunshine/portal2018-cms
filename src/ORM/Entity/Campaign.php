<?php

/**
 * Campaign.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Cinemasunshine\ORM\Entity\Campaign as BaseCampaign;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Campaign entity class
 *
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\CampaignRepository")
 * @ORM\Table(name="campaign", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class Campaign extends BaseCampaign
{
    /**
     * get published target
     *
     * @return ArrayCollection
     */
    public function getPublishedTargets()
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
