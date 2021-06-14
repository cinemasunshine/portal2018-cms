<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\TitleRankingRank as BaseTitleRankingRank;
use Doctrine\ORM\Mapping as ORM;

/**
 * TitleRankingRank entity class
 *
 * @ORM\Entity
 * @ORM\Table(name="title_ranking_rank", options={"collate"="utf8mb4_general_ci"})
 */
class TitleRankingRank extends BaseTitleRankingRank
{
}
