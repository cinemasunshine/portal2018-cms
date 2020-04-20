<?php
/**
 * PageMainBanner.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

use Cinemasunshine\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * PageMainBanner entity class
 *
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\PageMainBannerRepository")
 * @ORM\Table(name="page_main_banner", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class PageMainBanner extends AbstractEntity
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
     * main_banner
     *
     * @var MainBanner
     * @ORM\ManyToOne(targetEntity="MainBanner")
     * @ORM\JoinColumn(name="main_banner_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $mainBanner;

    /**
     * page
     *
     * @var Page
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="newsList")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $page;

    /**
     * display_order
     *
     * @var int
     * @ORM\Column(type="smallint", name="display_order", options={"unsigned"=true})
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
     * get main_banner
     *
     * @return MainBanner
     */
    public function getMainBanner()
    {
        return $this->mainBanner;
    }

    /**
     * set main_banner
     *
     * @param MainBanner $mainBanner
     * @return void
     */
    public function setMainBanner(MainBanner $mainBanner)
    {
        $this->mainBanner = $mainBanner;
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
