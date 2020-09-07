<?php

/**
 * TheaterNews.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\TheaterNews as BaseTheaterNews;
use Doctrine\ORM\Mapping as ORM;

/**
 * TheaterNews entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\TheaterNewsRepository")
 * @ORM\Table(name="theater_news", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class TheaterNews extends BaseTheaterNews
{
}
