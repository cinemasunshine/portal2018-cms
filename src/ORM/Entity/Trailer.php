<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\Trailer as BaseTrailer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trailer entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\TrailerRepository")
 * @ORM\Table(name="trailer", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class Trailer extends BaseTrailer
{
    /**
     * get published target
     */
    public function getPublishedTargets(): ArrayCollection
    {
        $publications = new ArrayCollection();

        foreach ($this->getPageTrailers() as $pageTrailer) {
            /** @var PageTrailer $pageTrailer */
            $publications->add($pageTrailer->getPage());
        }

        foreach ($this->getTheaterTrailers() as $theaterTrailer) {
            /** @var TheaterTrailer $theaterTrailer */
            $publications->add($theaterTrailer->getTheater());
        }

        foreach ($this->getSpecialSiteTrailers() as $specialSiteTrailer) {
            /** @var SpecialSiteTrailer $specialSiteTrailer */
            $publications->add($specialSiteTrailer->getSpecialSite());
        }

        return $publications;
    }
}
