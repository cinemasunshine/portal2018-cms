<?php

/**
 * SpecialSite.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\SpecialSite as BaseSpecialSite;
use Doctrine\ORM\Mapping as ORM;

/**
 * SpecialSite entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\SpecialSiteRepository")
 * @ORM\Table(name="special_site", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class SpecialSite extends BaseSpecialSite implements
    CampaignPublicationInterface,
    NewsPublicationInterface,
    MainBannerPublicationInterface
{
}
