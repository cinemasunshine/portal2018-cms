<?php

/**
 * MainBanner.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Cinemasunshine\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * MainBanner entity class
 *
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\MainBannerRepository")
 * @ORM\Table(name="main_banner", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class MainBanner extends AbstractEntity
{
    use SavedUserTrait;
    use SoftDeleteTrait;
    use TimestampableTrait;

    const LINK_TYPE_NONE = 1;
    const LINK_TYPE_URL = 2;

    /** @var array */
    protected static $linkTypes = [
        self::LINK_TYPE_NONE => 'リンクなし',
        self::LINK_TYPE_URL  => 'URL',
    ];

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
     * link_type
     *
     * @var int
     * @ORM\Column(type="smallint", name="link_type", options={"unsigned"=true})
     */
    protected $linkType;

    /**
     * link_url
     *
     * @var string|null
     * @ORM\Column(type="string", name="link_url", nullable=true)
     */
    protected $linkUrl;

    /**
     * pages
     *
     * @var Collection<PageMainBanner>
     * @ORM\OneToMany(targetEntity="PageMainBanner", mappedBy="mainBanner")
     */
    protected $pages;

    /**
     * theaters
     *
     * @var Collection<TheaterMainBanner>
     * @ORM\OneToMany(targetEntity="TheaterMainBanner", mappedBy="mainBanner")
     */
    protected $theaters;

    /**
     * special_sites
     *
     * @var Collection<SpecialSiteMainBanner>
     * @ORM\OneToMany(targetEntity="SpecialSiteMainBanner", mappedBy="mainBanner")
     */
    protected $specialSites;

    /**
     * return link types
     *
     * @return array
     */
    public static function getLinkTypes()
    {
        return self::$linkTypes;
    }

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
     * get link_type
     *
     * @return int
     */
    public function getLinkType()
    {
        return $this->linkType;
    }

    /**
     * set link_type
     *
     * @param int $linkType
     * @return void
     */
    public function setLinkType(int $linkType)
    {
        $this->linkType = $linkType;
    }

    /**
     * get link_url
     *
     * @return string|null
     */
    public function getLinkUrl()
    {
        return $this->linkUrl;
    }

    /**
     * set link_url
     *
     * @param string|null $linkUrl
     * @return void
     */
    public function setLinkUrl($linkUrl)
    {
        $this->linkUrl = $linkUrl;
    }

    /**
     * get pages
     *
     * @return Collection
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    /**
     * get theaters
     *
     * @return Collection
     */
    public function getTheaters(): Collection
    {
        return $this->theaters;
    }

    /**
     * get special_site
     *
     * @return Collection
     */
    public function getSpecialSite(): Collection
    {
        return $this->specialSites;
    }
}
