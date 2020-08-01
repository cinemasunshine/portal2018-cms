<?php

/**
 * OyakoCinemaTheater.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Cinemasunshine\ORM\Entities\OyakoCinemaTheater as BaseOyakoCinemaTheater;
use Doctrine\ORM\Mapping as ORM;

/**
 * OyakoCinemaTheater entity class
 *
 * @ORM\Entity
 * @ORM\Table(name="oyako_cinema_theater", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class OyakoCinemaTheater extends BaseOyakoCinemaTheater
{
}
