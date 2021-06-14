<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\Trailer as BaseTrailer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trailer entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\TrailerRepository")
 * @ORM\Table(name="trailer", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 *
 * @method Collection<int, PageTrailer> getPages()
 * @method Collection<int, TheaterTrailer> getTheater()
 * @method Collection<int, SpecialSiteTrailer> getSpecialSites()
 */
class Trailer extends BaseTrailer
{
    /**
     * get published target
     */
    public function getPublishedTargets(): ArrayCollection
    {
        $publications = new ArrayCollection();

        foreach ($this->getPages() as $pageTrailer) {
            $publications->add($pageTrailer->getPage());
        }

        foreach ($this->getTheater() as $theaterTrailer) {
            $publications->add($theaterTrailer->getTheater());
        }

        foreach ($this->getSpecialSites() as $specialSiteTrailer) {
            $publications->add($specialSiteTrailer->getSpecialSite());
        }

        return $publications;
    }
}
