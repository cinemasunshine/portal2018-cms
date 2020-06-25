<?php

/**
 * PageCampaign.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Cinemasunshine\ORM\Entity\PageCampaign as BasePageCampaign;
use Doctrine\ORM\Mapping as ORM;

/**
 * PageCampaign entity class
 *
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\PageCampaignRepository")
 * @ORM\Table(name="page_campaign", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class PageCampaign extends BasePageCampaign
{
}
