<?php

/**
 * TheaterTrailer.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Cinemasunshine\ORM\Entity\TheaterTrailer as BaseTheaterTrailer;
use Doctrine\ORM\Mapping as ORM;

/**
 * TheaterTrailer entity class
 *
 * @ORM\Entity
 * @ORM\Table(name="theater_trailer", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class TheaterTrailer extends BaseTheaterTrailer
{
}
