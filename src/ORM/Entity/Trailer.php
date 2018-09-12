<?php
/**
 * Trailer.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Cinemasunshine\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * Trailer entity class
 * 
 * @ORM\Entity
 * @ORM\Table(name="trailer", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class Trailer extends AbstractEntity
{
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
     * @var Title|null
     * @ORM\ManyToOne(targetEntity="Title")
     * @ORM\JoinColumn(name="title_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     */
    protected $title;
    
    /**
     * name
     *
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;
    
    /**
     * youbute
     *
     * @var string
     * @ORM\Column(type="string")
     */
    protected $youtube;
    
    /**
     * banner_image
     *
     * @var File
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="banner_image_file_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    protected $bannerImage;
    
    /**
     * banner_link_url
     *
     * @var string
     * @ORM\Column(type="string", name="banner_link_url")
     */
    protected $bannerLinkUrl;
    
    /**
     * page_trailers
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="PageTrailer", mappedBy="trailer", orphanRemoval=true)
     */
    protected $pageTrailers;
    
    /**
     * theater_trailers
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="TheaterTrailer", mappedBy="trailer", orphanRemoval=true)
     */
    protected $theaterTrailers;
    
    /**
     * construct
     */
    public function __construct()
    {
        $this->pageTrailers = new ArrayCollection();
        $this->theaterTrailers = new ArrayCollection();
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
     * @return Title|null
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * set title
     *
     * @param Title|null $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    /**
     * get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * set name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
    
    /**
     * get youtube
     *
     * @return string
     */
    public function getYoutube()
    {
        return $this->youtube;
    }
    
    /**
     * set youtube
     *
     * @param string $youtube
     * @return void
     */
    public function setYoutube(string $youtube)
    {
        $this->youtube = $youtube;
    }
    
    /**
     * get banner_image
     *
     * @return File
     */
    public function getBannerImage()
    {
        return $this->bannerImage;
    }
    
    /**
     * set banner_image
     *
     * @param File $bannerImage
     * @return void
     */
    public function setBannerImage(File $bannerImage)
    {
        $this->bannerImage = $bannerImage;
    }
    
    /**
     * get banner_link_url
     *
     * @return string
     */
    public function getBannerLinkUrl()
    {
        return $this->bannerLinkUrl;
    }
    
    /**
     * set banner_link_url
     *
     * @param string $bannerLinkUrl
     * @return void
     */
    public function setBannerLinkUrl(string $bannerLinkUrl)
    {
        $this->bannerLinkUrl = $bannerLinkUrl;
    }
    
    /**
     * get page_trailers
     *
     * @return Collection
     */
    public function getPageTrailers()
    {
        return $this->pageTrailers;
    }
    
    /**
     * set page_trailers
     *
     * @param Collection $pageTrailers
     * @return void
     */
    public function setPageTrailers(Collection $pageTrailers)
    {
        $this->pageTrailers = $pageTrailers;
    }
    
    /**
     * get theater_trailers
     *
     * @return Collection
     */
    public function getTheaterTrailers()
    {
        return $this->theaterTrailers;
    }
    
    /**
     * set theater_trailers
     *
     * @param Collection $theaterTrailers
     * @return void
     */
    public function setTheaterTrailers(Collection $theaterTrailers)
    {
        $this->theaterTrailers = $theaterTrailers;
    }
}