<?php

/**
 * MainBanner.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Cinemasunshine\ORM\Entity\MainBanner as BaseMainBanner;
use Doctrine\ORM\Mapping as ORM;

/**
 * MainBanner entity class
 *
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\MainBannerRepository")
 * @ORM\Table(name="main_banner", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class MainBanner extends BaseMainBanner
{
    /** @var array */
    protected static $linkTypes = [
        self::LINK_TYPE_NONE => 'リンクなし',
        self::LINK_TYPE_URL  => 'URL',
    ];

    /**
     * return link types
     *
     * @return array
     */
    public static function getLinkTypes()
    {
        return self::$linkTypes;
    }
}
