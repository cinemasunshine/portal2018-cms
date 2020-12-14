<?php

/**
 * Schedule.php
 */

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\Schedule as BaseSchedule;
use Doctrine\ORM\Mapping as ORM;

/**
 * Schedule entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\ScheduleRepository")
 * @ORM\Table(name="schedule", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class Schedule extends BaseSchedule
{
}
