<?php

/**
 * TheaterCampaign.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Cinemasunshine\ORM\Entity\TheaterCampaign as BaseTheaterCampaign;
use Doctrine\ORM\Mapping as ORM;

/**
 * TheaterCampaign entity class
 *
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\TheaterCampaignRepository")
 * @ORM\Table(name="theater_campaign", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class TheaterCampaign extends BaseTheaterCampaign
{
}
