<?php

/**
 * PageMainBanner.php
 */

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\PageMainBanner as BasePageMainBanner;
use Doctrine\ORM\Mapping as ORM;

/**
 * PageMainBanner entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\PageMainBannerRepository")
 * @ORM\Table(name="page_main_banner", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class PageMainBanner extends BasePageMainBanner
{
}
