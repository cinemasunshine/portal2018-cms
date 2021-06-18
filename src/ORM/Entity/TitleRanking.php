<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\TitleRanking as BaseTitleRanking;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TitleRanking entity class
 *
 * @ORM\Entity
 * @ORM\Table(name="title_ranking", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 *
 * @method Collection<int,TitleRankingRank> getRanks()
 */
class TitleRanking extends BaseTitleRanking
{
}
