<?php
/**
 * Theater.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Cinemasunshine\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * Theater entity class
 * 
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\TheaterRepository")
 * @ORM\Table(name="theater", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class Theater extends AbstractEntity implements CampaignPublicationInterface, NewsPublicationInterface
{
    use SoftDeleteTrait;
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
     * name
     * 
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    protected $name;
    
    /** 
     * name_ja
     * 
     * @var string
     * @ORM\Column(type="string", name="name_ja")
     */
    protected $nameJa;
    
    /**
     * area
     * 
     * @var int
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     */
    protected $area;
    
    /**
     * master_version
     *
     * @var int
     * @ORM\Column(type="smallint", name="master_version", options={"unsigned"=true})
     */
    protected $masterVersion;

    /**
     * master_code
     *
     * @var string
     * @ORM\Column(type="string", name="master_code", length=3, nullable=true, options={"fixed":true})
     */
    protected $masterCode;
    
    /**
     * display_order
     *
     * @var int
     * @ORM\Column(type="smallint", name="display_order", options={"unsigned"=true})
     */
    protected $displayOrder;
    
    /**
     * admin_users
     * 
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AdminUser", mappedBy="theater")
     */
    protected $adminUsers;
    
    /**
     * campaigns
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="TheaterCampaign", mappedBy="theater", orphanRemoval=true)
     * @ORM\OrderBy({"displayOrder" = "ASC"})
     */
    protected $campaigns;
    
    /**
     * news_list
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="TheaterNews", mappedBy="theater", orphanRemoval=true)
     * @ORM\OrderBy({"displayOrder" = "ASC"})
     */
    protected $newsList;
    
    /**
     * construct
     */
    public function __construct()
    {
        $this->adminUsers = new ArrayCollection();
        $this->campaigns = new ArrayCollection();
        $this->newsList =  new ArrayCollection();
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
     * get name_ja
     *
     * @return string
     */
    public function getNameJa()
    {
        return $this->nameJa;
    }
    
    /**
     * set name_ja
     *
     * @param string $nameJa
     * @return void
     */
    public function setNameJa(string $nameJa)
    {
        $this->nameJa = $nameJa;
    }
    
    /**
     * get area
     *
     * @return int
     */
    public function getArea()
    {
        return $this->area;
    }
    
    /**
     * set area
     *
     * @param int $area
     * @return void
     */
    public function setArea($area)
    {
        $this->area = $area;
    }
    
    /**
     * get master_version
     *
     * @return int
     */
    public function getMasterVersion()
    {
        return $this->masterVersion;
    }
    
    /**
     * set master_version
     *
     * @param int $masterVersion
     * @return void
     */
    public function setMasterVersion($masterVersion)
    {
        $this->masterVersion = $masterVersion;
    }
    
    /**
     * get master_code
     *
     * @return string
     */
    public function getMasterCode()
    {
        return $this->masterCode;
    }
    
    /**
     * set master_code
     *
     * @param string $masterCode
     * @return void
     */
    public function setMasterCode($masterCode)
    {
        $this->masterCode = $masterCode;
    }
    
    /**
     * get admin_users
     *
     * @return ArrayCollection
     */
    public function getAdminUsers()
    {
        return $this->adminUsers;
    }
    
    /**
     * get campaigns
     *
     * @return Collection
     */
    public function getCampaigns() : Collection
    {
        return $this->campaigns;
    }
    
    /**
     * get news_list
     *
     * @return Collection
     */
    public function getNewsList(): Collection
    {
        return $this->newsList;
    }
}