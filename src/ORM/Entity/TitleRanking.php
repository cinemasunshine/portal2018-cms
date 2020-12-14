<?php

/**
 * TitleRanking.php
 */

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\TitleRanking as BaseTitleRanking;
use Doctrine\ORM\Mapping as ORM;

/**
 * TitleRanking entity class
 *
 * @ORM\Entity
 * @ORM\Table(name="title_ranking", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 *
 * @method Title|null getRank1Title()
 * @method Title|null getRank2Title()
 * @method Title|null getRank3Title()
 * @method Title|null getRank4Title()
 * @method Title|null getRank5Title()
 */
class TitleRanking extends BaseTitleRanking
{
    /**
     * get rank
     *
     * @param int $rank
     * @return Title|null
     *
     * @throws \InvalidArgumentException
     */
    public function getRank(int $rank)
    {
        if ($rank === 1) {
            return $this->getRank1Title();
        } elseif ($rank === 2) {
            return $this->getRank2Title();
        } elseif ($rank === 3) {
            return $this->getRank3Title();
        } elseif ($rank === 4) {
            return $this->getRank4Title();
        } elseif ($rank === 5) {
            return $this->getRank5Title();
        } else {
            throw new \InvalidArgumentException('invalid "rank".');
        }
    }

    /**
     * set rank
     *
     * @param int        $rank
     * @param Title|null $title
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function setRank(int $rank, $title)
    {
        if ($rank === 1) {
            $this->setRank1Title($title);
        } elseif ($rank === 2) {
            $this->setRank2Title($title);
        } elseif ($rank === 3) {
            $this->setRank3Title($title);
        } elseif ($rank === 4) {
            $this->setRank4Title($title);
        } elseif ($rank === 5) {
            $this->setRank5Title($title);
        } else {
            throw new \InvalidArgumentException('invalid "rank".');
        }
    }
}
