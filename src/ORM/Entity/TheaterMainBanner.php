<?php

/**
 * TheaterMainBanner.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Cinemasunshine\ORM\Entity\TheaterMainBanner as BaseTheaterMainBanner;
use Doctrine\ORM\Mapping as ORM;

/**
 * TheaterMainBanner entity class
 *
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\TheaterMainBannerRepository")
 * @ORM\Table(name="theater_main_banner", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class TheaterMainBanner extends BaseTheaterMainBanner
{
}
