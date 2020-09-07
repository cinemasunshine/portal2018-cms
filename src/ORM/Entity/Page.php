<?php

/**
 * Page.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\Page as BasePage;
use Doctrine\ORM\Mapping as ORM;

/**
 * Page entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\PageRepository")
 * @ORM\Table(name="page", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class Page extends BasePage implements
    CampaignPublicationInterface,
    NewsPublicationInterface,
    MainBannerPublicationInterface
{
}
