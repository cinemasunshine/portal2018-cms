<?php

/**
 * OyakoCinemaTitle.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\OyakoCinemaTitle as BaseOyakoCinemaTitle;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * OyakoCinemaTitle entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\OyakoCinemaTitleRepository")
 * @ORM\Table(name="oyako_cinema_title", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class OyakoCinemaTitle extends BaseOyakoCinemaTitle
{
    /**
     * {@inheritDoc}
     *
     * @return Collection
     */
    public function getOyakoCinemaSchedules(): Collection
    {
        $criteria = Criteria::create()
            ->orderBy(['date' => Criteria::ASC]);

        return $this->oyakoCinemaSchedules->matching($criteria);
    }

    /**
     * clear oyako_cinema_schedules
     *
     * @return void
     */
    public function clearOyakoCinemaSchedules()
    {
        $this->oyakoCinemaSchedules->clear();
    }
}
