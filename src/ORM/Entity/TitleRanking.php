<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\TitleRanking as BaseTitleRanking;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

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
     * @throws InvalidArgumentException
     */
    public function getRank(int $rank)
    {
        switch ($rank) {
            case 1:
                return $this->getRank1Title();

            case 2:
                return $this->getRank2Title();

            case 3:
                return $this->getRank3Title();

            case 4:
                return $this->getRank4Title();

            case 5:
                return $this->getRank5Title();

            default:
                throw new InvalidArgumentException('invalid "rank".');
        }
    }

    /**
     * set rank
     *
     * @param int        $rank
     * @param Title|null $title
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function setRank(int $rank, $title)
    {
        switch ($rank) {
            case 1:
                $this->setRank1Title($title);
                break;

            case 2:
                $this->setRank2Title($title);
                break;

            case 3:
                $this->setRank3Title($title);
                break;

            case 4:
                $this->setRank4Title($title);
                break;

            case 5:
                $this->setRank5Title($title);
                break;

            default:
                throw new InvalidArgumentException('invalid "rank".');
        }
    }
}
