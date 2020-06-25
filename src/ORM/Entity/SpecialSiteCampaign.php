<?php

/**
 * SpecialSiteCampaign.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Cinemasunshine\ORM\Entity\SpecialSiteCampaign as BaseSpecialSiteCampaign;
use Doctrine\ORM\Mapping as ORM;

/**
 * SpecialSiteCampaign entity class
 *
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\SpecialSiteCampaignRepository")
 * @ORM\Table(name="special_site_campaign", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class SpecialSiteCampaign extends BaseSpecialSiteCampaign
{
}
