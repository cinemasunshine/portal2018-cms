<?php
/**
 * TitleRanking.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

use Cinemasunshine\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * TitleRanking entity class
 *
 * @ORM\Entity
 * @ORM\Table(name="title_ranking", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class TitleRanking extends AbstractEntity
{
    use TimestampableTrait;

    /**
     * id
     *
     * @var int
     * @ORM\Id
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * from_date
     *
     * @var \DateTime|null
     * @ORM\Column(type="date", name="from_date", nullable=true)
     */
    protected $fromDate;

    /**
     * to_date
     *
     * @var \DateTime|null
     * @ORM\Column(type="date", name="to_date", nullable=true)
     */
    protected $toDate;

    /**
     * rank1_title
     *
     * @var Title|null
     * @ORM\ManyToOne(targetEntity="Title")
     * @ORM\JoinColumn(name="rank1_title_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     */
    protected $rank1Title;

    /**
     * rank2_title
     *
     * @var Title|null
     * @ORM\ManyToOne(targetEntity="Title")
     * @ORM\JoinColumn(name="rank2_title_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     */
    protected $rank2Title;

    /**
     * rank3_title
     *
     * @var Title|null
     * @ORM\ManyToOne(targetEntity="Title")
     * @ORM\JoinColumn(name="rank3_title_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     */
    protected $rank3Title;

    /**
     * rank4_title
     *
     * @var Title|null
     * @ORM\ManyToOne(targetEntity="Title")
     * @ORM\JoinColumn(name="rank4_title_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     */
    protected $rank4Title;

    /**
     * rank5_title
     *
     * @var Title|null
     * @ORM\ManyToOne(targetEntity="Title")
     * @ORM\JoinColumn(name="rank5_title_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     */
    protected $rank5Title;

    /**
     * construct
     */
    public function __construct()
    {
    }

    /**
     * get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * get from_date
     *
     * @return \DateTime|null
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * set from_date
     *
     * @param \DateTime|string|null $fromDate
     * @return void
     */
    public function setFromDate($fromDate)
    {
        if (is_null($fromDate) || $fromDate instanceof \DateTime) {
            $this->fromDate = $fromDate;
        } else {
            $this->fromDate = new \DateTime($fromDate);
        }
    }

    /**
     * get to_date
     *
     * @return \DateTime|null
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * set to_date
     *
     * @param \DateTime|string|null $toDate
     * @return void
     */
    public function setToDate($toDate)
    {
        if (is_null($toDate) || $toDate instanceof \DateTime) {
            $this->toDate = $toDate;
        } else {
            $this->toDate = new \DateTime($toDate);
        }
    }

    /**
     * get rank1_title
     *
     * @return Title|null
     */
    public function getRank1Title()
    {
        return $this->rank1Title;
    }

    /**
     * set rank1_title
     *
     * @param Title|null $title
     */
    public function setRank1Title($title)
    {
        $this->rank1Title = $title;
    }

    /**
     * get rank2_title
     *
     * @return Title|null
     */
    public function getRank2Title()
    {
        return $this->rank2Title;
    }

    /**
     * set rank2_title
     *
     * @param Title|null $title
     * @return void
     */
    public function setRank2Title($title)
    {
        $this->rank2Title = $title;
    }

    /**
     * get rank3_title
     *
     * @return Title|null
     */
    public function getRank3Title()
    {
        return $this->rank3Title;
    }

    /**
     * set rank3_title
     *
     * @param Title|null $title
     * @return void
     */
    public function setRank3Title($title)
    {
        $this->rank3Title = $title;
    }

    /**
     * get rank4_title
     *
     * @return Title|null
     */
    public function getRank4Title()
    {
        return $this->rank4Title;
    }

    /**
     * set rank4_title
     *
     * @param Title|null $title
     * @return void
     */
    public function setRank4Title($title)
    {
        $this->rank4Title = $title;
    }

    /**
     * get rank5_title
     *
     * @return Title|null
     */
    public function getRank5Title()
    {
        return $this->rank5Title;
    }

    /**
     * set rank5_title
     *
     * @param Title|null $title
     * @return void
     */
    public function setRank5Title($title)
    {
        $this->rank5Title = $title;
    }

    /**
     * get rank
     *
     * @param int $rank
     * @return Title|null
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
     * @param int   $rank
     * @param Title|null $title
     * @return void
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
