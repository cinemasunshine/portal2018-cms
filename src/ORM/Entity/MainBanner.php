<?php

/**
 * MainBanner.php
 */

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\MainBanner as BaseMainBanner;
use Doctrine\ORM\Mapping as ORM;

/**
 * MainBanner entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\MainBannerRepository")
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
