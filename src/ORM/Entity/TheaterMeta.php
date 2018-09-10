<?php
/**
 * TheaterMeta.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * theater
     * 
     * @var Theater
     * @ORM\OneToOne(targetEntity="Theater")
     * @ORM\JoinColumn(name="theater_id", referencedColumnName="id")
     */
    protected $theater;
    
    /**
     * opening_hours
     * 
     * 初回のみnullとなる予定。可能であれば最初からデータを入れてNOT NULLとしたい。
     * 
     * @var Collection|null
     * @ORM\Column(type="object", name="opening_hours", nullable=true)
     */
    protected $openingHours;
    
    /**
     * construct
     */
    public function __construct()
    {
        $this->openingHours = new ArrayCollection();
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
     * @return Collection|null
     */
    public function getOpeningHours()
    {
        return $this->openingHours;
    }
    
    /**
     * set opening_hours
     *
     * @param Collection $openingHours
     * @return void
     */
    public function setOpeningHours(Collection $openingHours)
    {
        $this->openingHours = $openingHours;
    }
}