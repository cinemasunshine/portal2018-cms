<?php

/**
 * OyakoCinemaSchedule.php
 */

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\OyakoCinemaSchedule as BaseOyakoCinemaSchedule;
use Doctrine\ORM\Mapping as ORM;

/**
 * OyakoCinemaSchedule entity class
 *
 * @ORM\Entity
 * @ORM\Table(name="oyako_cinema_schedule", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class OyakoCinemaSchedule extends BaseOyakoCinemaSchedule
{
}
