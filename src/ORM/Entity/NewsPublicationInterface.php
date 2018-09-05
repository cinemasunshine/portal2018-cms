<?php
/**
 * NewsPublicationInterface.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\Common\Collections\Collection;

/**
 * NewsPublication interface
 */
interface NewsPublicationInterface
{
    /**
     * get newsList
     *
     * @return Collection
     */
    public function getNewsList() : Collection;
}