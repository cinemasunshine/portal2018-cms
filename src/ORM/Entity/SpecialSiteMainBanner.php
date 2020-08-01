<?php

/**
 * SpecialSiteMainBanner.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Cinemasunshine\ORM\Entities\SpecialSiteMainBanner as BaseSpecialSiteMainBanner;
use Doctrine\ORM\Mapping as ORM;

/**
 * SpecialSiteMainBanner entity class
 *
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\SpecialSiteMainBannerRepository")
 * @ORM\Table(name="special_site_main_banner", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class SpecialSiteMainBanner extends BaseSpecialSiteMainBanner
{
}
