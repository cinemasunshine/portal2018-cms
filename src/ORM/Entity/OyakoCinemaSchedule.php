<?php
/**
 * OyakoCinemaSchedule.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OyakoCinemaSchedule entity class
 *
 * @ORM\Entity
 * @ORM\Table(name="oyako_cinema_schedule", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class OyakoCinemaSchedule extends AbstractEntity
{
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
     * oyako_cinema_title
     *
     * @var OyakoCinemaTitle
     * @ORM\ManyToOne(targetEntity="OyakoCinemaTitle")
     * @ORM\JoinColumn(name="oyako_cinema_title_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $oyakoCinemaTitle;

    /**
     * date
     *
     * @var \DateTime
     * @ORM\Column(type="date")
     */
    protected $date;

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
     * get oyako_cinema_title
     *
     * @return OyakoCinemaTitle
     */
    public function getOyakoCinemaTitle(): OyakoCinemaTitle
    {
        return $this->oyakoCinemaTitle;
    }

    /**
     * set oyako_cinema_title
     *
     * @param OyakoCinemaTitle $oyakoCinemaTitle
     * @return void
     */
    public function setOyakoCinemaTitle(OyakoCinemaTitle $oyakoCinemaTitle)
    {
        $this->oyakoCinemaTitle = $oyakoCinemaTitle;
    }

    /**
     * get date
     *
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * set date
     *
     * @param \DateTime|string $date
     * @return void
     * @throws \InvalidArgumentException
     */
    public function setDate($date)
    {
        if ($date instanceof \DateTime) {
            $this->date = $date;
        } elseif (is_string($date)) {
            $this->date = new \DateTime($date);
        } else {
            throw new \InvalidArgumentException('Invalid argument type.');
        }
    }
}
