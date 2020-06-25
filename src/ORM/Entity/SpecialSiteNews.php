<?php

/**
 * SpecialSiteNews.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Cinemasunshine\ORM\Entity\SpecialSiteNews as BaseSpecialSiteNews;
use Doctrine\ORM\Mapping as ORM;

/**
 * SpecialSiteNews entity class
 *
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\SpecialSiteNewsRepository")
 * @ORM\Table(name="special_site_news", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class SpecialSiteNews extends BaseSpecialSiteNews
{
}
