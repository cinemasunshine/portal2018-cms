<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Doctrine\Common\Collections\Collection;

/**
 * MainBannerPublication interface
 */
interface MainBannerPublicationInterface
{
    /**
     * get main_banners
     */
    public function getMainBanners(): Collection;
}
