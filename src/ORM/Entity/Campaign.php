<?php
/**
 * Campaign.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Cinemasunshine\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * Campaign entity class
 * 
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\CampaignRepository")
 * @ORM\Table(name="campaign", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class Campaign extends AbstractEntity
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
     * image
     *
     * @var File
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="image_file_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    protected $image;
    
    /**
     * name
     * 
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;
    
    /**
     * start_dt
     *
     * @var \DateTime
     * @ORM\Column(type="datetime", name="start_dt")
     */
    protected $startDt;
    
    /**
     * end_dt
     *
     * @var \DateTime
     * @ORM\Column(type="datetime", name="end_dt")
     */
    protected $endDt;
    
    /**
     * url
     * 
     * @var string
     * @ORM\Column(type="string")
     */
    protected $url;
    
    /**
     * pages
     * 
     * @var Collection
     * @ORM\OneToMany(targetEntity="PageCampaign", mappedBy="campaign")
     */
    protected $pages;
    
    /**
     * theaters
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="TheaterCampaign", mappedBy="campaign")
     */
    protected $theaters;
    
    /**
     * special_sites
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="SpecialSiteCampaign", mappedBy="campaign")
     */
    protected $specialSites;
    
    /**
     * construct
     */
    public function __construct()
    {
        $this->pages = new ArrayCollection();
        $this->theaters = new ArrayCollection();
        $this->specialSites = new ArrayCollection();
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
     * get image
     *
     * @return File
     */
    public function getImage()
    {
        return $this->image;
    }
    
    /**
     * set image
     *
     * @param File $image
     * @return void
     */
    public function setImage(File $image)
    {
        $this->image = $image;
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
     * get start_dt
     *
     * @return \DateTime
     */
    public function getStartDt()
    {
        return $this->startDt;
    }
    
    /**
     * set start_dt
     *
     * @param \DateTime|string $startDt
     * @return void
     */
    public function setStartDt($startDt)
    {
        if ($startDt instanceof \Datetime) {
            $this->startDt = $startDt;
        } else {
            $this->startDt = new \DateTime($startDt);
        }
    }
    
    /**
     * get end_dt
     *
     * @return \DateTime
     */
    public function getEndDt()
    {
        return $this->endDt;
    }
    
    /**
     * set end_dt
     *
     * @param \DateTime|string $endDt
     * @return void
     */
    public function setEndDt($endDt)
    {
        if ($endDt instanceof \Datetime) {
            $this->endDt = $endDt;
        } else {
            $this->endDt = new \DateTime($endDt);
        }
    }
    
    /**
     * get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * set url
     *
     * @param string $url
     * @return void
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }
    
    /**
     * get pages
     *
     * @return Collection
     */
    public function getPages() : Collection
    {
        return $this->pages;
    }
    
    /**
     * get theaters
     *
     * @return Collection
     */
    public function getTheaters() : Collection
    {
        return $this->theaters;
    }
    
    /**
     * get special_site
     *
     * @return Collection
     */
    public function getSpecialSite() : Collection
    {
        return $this->specialSites;
    }
    
    /**
     * get published target
     *
     * @return ArrayCollection
     */
    public function getPublishedTargets()
    {
        $publications = new ArrayCollection();
        
        foreach ($this->getPages() as $pageCampaign) {
            /** @var PageCampaign $pageCampaign */
            $publications->add($pageCampaign->getPage());
        }
        
        foreach ($this->getTheaters() as $theaterCampaign) {
            /** @var TheaterCampaign $theaterCampaign */
            $publications->add($theaterCampaign->getTheater());
        }
        
        foreach ($this->getSpecialSite() as $specialSiteCampaign) {
            /** @var SpecialSiteCampaign $specialSiteCampaign */
            $publications->add($specialSiteCampaign->getSpecialSite());
        }
        
        return $publications;
    }
}