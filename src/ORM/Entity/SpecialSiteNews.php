<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\SpecialSiteNews as BaseSpecialSiteNews;
use Doctrine\ORM\Mapping as ORM;

/**
 * SpecialSiteNews entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\SpecialSiteNewsRepository")
 * @ORM\Table(name="special_site_news", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class SpecialSiteNews extends BaseSpecialSiteNews
{
}
