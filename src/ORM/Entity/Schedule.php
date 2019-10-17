<?php
/**
 * Schedule.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Cinemasunshine\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * Schedule entity class
 *
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\ScheduleRepository")
 * @ORM\Table(name="schedule", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class Schedule extends AbstractEntity
{
    use SavedUserTrait;
    use SoftDeleteTrait;
    use TimestampableTrait;

    /**
     * id
     *
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * title
     *
     * @var Title
     * @ORM\ManyToOne(targetEntity="Title")
     * @ORM\JoinColumn(name="title_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    protected $title;

    /**
     * start_date
     *
     * @var \DateTime
     * @ORM\Column(type="date", nullable=false, name="start_date")
     */
    protected $startDate;

    /**
     * end_date
     *
     * @var \DateTime
     * @ORM\Column(type="date", nullable=false, name="end_date")
     */
    protected $endDate;

    /**
     * public_start_dt
     *
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false, name="public_start_dt")
     */
    protected $publicStartDt;

    /**
     * public_end_dt
     *
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false, name="public_end_dt")
     */
    protected $publicEndDt;

    /**
     * remark
     *
     * @var string
     * @ORM\Column(type="text")
     */
    protected $remark;

    /**
     * showing_formats
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="ShowingFormat", mappedBy="schedule", orphanRemoval=true)
     */
    protected $showingFormats;

    /**
     * showing_theaters
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="ShowingTheater", mappedBy="schedule", orphanRemoval=true)
     */
    protected $showingTheaters;

    /**
     * construct
     */
    public function __construct()
    {
        $this->showingFormats = new ArrayCollection();
        $this->showingTheaters = new ArrayCollection();
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
     * get title
     *
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * set title
     *
     * @param Title $title
     * @return void
     */
    public function setTitle(Title $title)
    {
        $this->title = $title;
    }

    /**
     * get start_date
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * set start_date
     *
     * @param \DateTime|string $startDate
     * @return void
     */
    public function setStartDate($startDate)
    {
        if ($startDate instanceof \DateTime) {
            $this->startDate = $startDate;
        } else {
            $this->startDate = new \DateTime($startDate);
        }
    }

    /**
     * get end_date
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * set end_date
     *
     * @param \DateTime|string $endDate
     * @return void
     */
    public function setEndDate($endDate)
    {
        if ($endDate instanceof \DateTime) {
            $this->endDate = $endDate;
        } else {
            $this->endDate = new \DateTime($endDate);
        }
    }

    /**
     * get public_start_dt
     *
     * @return \DateTime
     */
    public function getPublicStartDt()
    {
        return $this->publicStartDt;
    }

    /**
     * set public_start_dt
     *
     * @param \DateTime|string $publicStartDt
     * @return void
     */
    public function setPublicStartDt($publicStartDt)
    {
        if ($publicStartDt instanceof \DateTime) {
            $this->publicStartDt = $publicStartDt;
        } else {
            $this->publicStartDt = new \DateTime($publicStartDt);
        }
    }

    /**
     * get public_end_dt
     *
     * @return \DateTime
     */
    public function getPublicEndDt()
    {
        return $this->publicEndDt;
    }

    /**
     * set public_end_dt
     *
     * @param \DateTime|string $publicEndDt
     * @return void
     */
    public function setPublicEndDt($publicEndDt)
    {
        if ($publicEndDt instanceof \DateTime) {
            $this->publicEndDt = $publicEndDt;
        } else {
            $this->publicEndDt = new \DateTime($publicEndDt);
        }
    }

    /**
     * get remark
     *
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * set remark
     *
     * @param string $remark
     * @return void
     */
    public function setRemark(string $remark)
    {
        $this->remark = $remark;
    }

    /**
     * get showing_formats
     *
     * @return Collection
     */
    public function getShowingFormats()
    {
        return $this->showingFormats;
    }

    /**
     * set showing_formats
     *
     * @param Collection $showingFormats
     * @return void
     */
    public function setShowingFormats(Collection $showingFormats)
    {
        $this->showingFormats = $showingFormats;
    }

    /**
     * get showing_theaters
     *
     * @return Collection
     */
    public function getShowingTheaters()
    {
        return $this->showingTheaters;
    }

    /**
     * set showing_theaters
     *
     * @param Collection $showingTheaters
     * @return void
     */
    public function setShowingTheaters(Collection $showingTheaters)
    {
        $this->showingTheaters = $showingTheaters;
    }
}
