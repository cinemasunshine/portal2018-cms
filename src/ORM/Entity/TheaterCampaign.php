<?php

/**
 * TheaterCampaign.php
 */

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\TheaterCampaign as BaseTheaterCampaign;
use Doctrine\ORM\Mapping as ORM;

/**
 * TheaterCampaign entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\TheaterCampaignRepository")
 * @ORM\Table(name="theater_campaign", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class TheaterCampaign extends BaseTheaterCampaign
{
}
