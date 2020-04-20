<?php
/**
 * TheaterMeta.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

use Cinemasunshine\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * TheaterMeta entity class
 *
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\TheaterMetaRepository")
 * @ORM\Table(name="theater_meta", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class TheaterMeta extends AbstractEntity
{
    use TimestampableTrait;

    /**
     * id
     *
     * @var int
     * @ORM\Id
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $id;

    /**
     * theater
     *
     * @var Theater
     * @ORM\OneToOne(targetEntity="Theater")
     * @ORM\JoinColumn(name="theater_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $theater;

    /**
     * opening_hours
     *
     * @var array
     * @ORM\Column(type="json", name="opening_hours")
     */
    protected $openingHours;

    /**
     * twitter
     *
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $twitter;

    /**
     * facebook
     *
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $facebook;

    /**
     * oyako_cinema_url
     *
     * @var string|null
     * @ORM\Column(type="string", name="oyako_cinema_url", nullable=true)
     */
    protected $oyakoCinemaUrl;

    /**
     * construct
     */
    public function __construct()
    {
        $this->openingHours = [];
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
     * get theater
     *
     * @return Theater
     */
    public function getTheater()
    {
        return $this->theater;
    }

    /**
     * set theater
     *
     * @param Theater $theater
     * @return void
     */
    public function setTheater(Theater $theater)
    {
        $this->theater = $theater;
    }

    /**
     * get opening_hours
     *
     * @return TheaterOpeningHour[]
     */
    public function getOpeningHours()
    {
        $hours = [];

        if (is_array($this->openingHours)) {
            foreach ($this->openingHours as $hour) {
                $hours[] = TheaterOpeningHour::create($hour);
            }
        }

        return $hours;
    }

    /**
     * set opening_hours
     *
     * @param TheaterOpeningHour[] $openingHours
     * @return void
     */
    public function setOpeningHours(array $openingHours)
    {
        $hours = [];

        foreach ($openingHours as $hour) {
            /** @var TheaterOpeningHour $hour */
            $hours[] = $hour->toArray();
        }

        $this->openingHours = $hours;
    }

    /**
     * get twitter
     *
     * @return string
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * set twitter
     *
     * @param string|null $twitter
     * @return void
     */
    public function setTwitter(?string $twitter)
    {
        $this->twitter = $twitter;
    }

    /**
     * get facebook
     *
     * @return string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * set facebook
     *
     * @param string|null $facebook
     * @return void
     */
    public function setFacebook(?string $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * get oyako_cinema_url
     *
     * @return string|null
     */
    public function getOyakoCinemaUrl(): ?string
    {
        return $this->oyakoCinemaUrl;
    }

    /**
     * set oyako_cinema_url
     *
     * @param string|null $oyakoCinemaUrl
     * @return void
     */
    public function setOyakoCinemaUrl(?string $oyakoCinemaUrl)
    {
        $this->oyakoCinemaUrl = $oyakoCinemaUrl;
    }
}
