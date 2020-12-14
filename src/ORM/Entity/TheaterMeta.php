<?php

/**
 * TheaterMeta.php
 */

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\TheaterMeta as BaseTheaterMeta;
use Doctrine\ORM\Mapping as ORM;

/**
 * TheaterMeta entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\TheaterMetaRepository")
 * @ORM\Table(name="theater_meta", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class TheaterMeta extends BaseTheaterMeta
{
}
