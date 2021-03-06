<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\Theater as BaseTheater;
use Doctrine\ORM\Mapping as ORM;

/**
 * Theater entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\TheaterRepository")
 * @ORM\Table(name="theater", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 *
 * @method TheaterMeta|null getMeta()
 */
class Theater extends BaseTheater implements
    CampaignPublicationInterface,
    NewsPublicationInterface,
    MainBannerPublicationInterface
{
}
