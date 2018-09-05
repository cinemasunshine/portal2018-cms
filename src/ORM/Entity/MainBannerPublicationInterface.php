<?php
/**
 * MainBannerPublicationInterface.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\Common\Collections\Collection;

/**
 * MainBannerPublication interface
 */
interface MainBannerPublicationInterface
{
    /**
     * get main_banners
     *
     * @return Collection
     */
    public function getMainBanners() : Collection;
}
