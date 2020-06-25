<?php

/**
 * Schedule.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Cinemasunshine\ORM\Entity\Schedule as BaseSchedule;
use Doctrine\ORM\Mapping as ORM;

/**
 * Schedule entity class
 *
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\ScheduleRepository")
 * @ORM\Table(name="schedule", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class Schedule extends BaseSchedule
{
}
