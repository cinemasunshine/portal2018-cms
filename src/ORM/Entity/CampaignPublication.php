<?php
/**
 * CampaignPublication.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

use Cinemasunshine\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * CampaignPublication entity class
 * 
 * @ORM\Entity
 * @ORM\Table(name="campaign_publication", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class CampaignPublication extends AbstractEntity
{
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
     * campaign
     *
     * @var Campaign
     * @ORM\ManyToOne(targetEntity="Campaign")
     * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $campaign;
    
    /**
     * theater
     *
     * @var Theater
     * @ORM\ManyToOne(targetEntity="Theater")
     * @ORM\JoinColumn(name="theater_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $theater;
    
    /**
     * page
     *
     * @var Page
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="publicationCampaigns")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $page;
    
    /**
     * special_site
     *
     * @var SpecialSite
     * @ORM\ManyToOne(targetEntity="SpecialSite")
     * @ORM\JoinColumn(name="special_site_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $specialSite;
    
    /**
     * display_order
     *
     * @var int
     * @ORM\Column(type="smallint", name="display_order")
     */
    protected $displayOrder;
    
    
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
     * get campaign
     *
     * @return Campaign
     */
    public function getCampaign()
    {
        return $this->campaign;
    }
    
    /**
     * set campaign
     *
     * @param Campaign $campaign
     * @return void
     */
    public function setCampaign(Campaign $campaign)
    {
        $this->campaign = $campaign;
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
     * page
     *
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }
    
    /**
     * set page
     *
     * @param Page $page
     * @return void
     */
    public function setPage(Page $page)
    {
        $this->page = $page;
    }
    
    /**
     * get special_site
     *
     * @return SpecialSite
     */
    public function getSpecialSite()
    {
        return $this->specialSite;
    }
    
    /**
     * set special_site
     *
     * @param SpecialSite $specialSite
     * @return void
     */
    public function setSpecialSite(SpecialSite $specialSite)
    {
        $this->specialSite = $specialSite;
    }
    
    /**
     * get display_order
     *
     * @return int
     */
    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }
    
    /**
     * set display_order
     *
     * @param int $displayOrder
     * @return void
     */
    public function setDisplayOrder(int $displayOrder)
    {
        $this->displayOrder = $displayOrder;
    }
}
