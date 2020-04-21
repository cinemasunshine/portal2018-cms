<?php

/**
 * News.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Cinemasunshine\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * News entity class
 *
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\NewsRepository")
 * @ORM\Table(name="news", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class News extends AbstractEntity
{
    use SavedUserTrait;
    use SoftDeleteTrait;
    use TimestampableTrait;

    const CATEGORY_NEWS       = 1;
    const CATEGORY_INFO       = 2;
    const CATEGORY_IMAX       = 3;
    const CATEGORY_4DX        = 4;
    const CATEGORY_EVENT      = 5;
    const CATEGORY_SCREENX    = 6; // SASAKI-351
    const CATEGORY_4DX_SCREEN = 7; // SASAKI-432、SASAKI-525

    /** @var array */
    public static $categories = [
        self::CATEGORY_NEWS       => 'NEWS',
        self::CATEGORY_INFO       => 'インフォメーション',
        self::CATEGORY_IMAX       => 'IMAXニュース',
        self::CATEGORY_4DX        => '4DXニュース',
        self::CATEGORY_SCREENX    => 'ScreenXニュース',
        self::CATEGORY_EVENT      => 'イベント上映ニュース',
        self::CATEGORY_4DX_SCREEN => '4DX SCREENニュース',
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
     * @var File|null
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="image_file_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     */
    protected $image;

    /**
     * category
     *
     * @var int
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     */
    protected $category;

    /**
     * headline
     *
     * @var string
     * @ORM\Column(type="string")
     */
    protected $headline;

    /**
     * body
     *
     * @var string
     * @ORM\Column(type="text")
     */
    protected $body;

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
     * pages
     *
     * @var Collection<PageNews>
     * @ORM\OneToMany(targetEntity="PageNews", mappedBy="news")
     */
    protected $pages;

    /**
     * theaters
     *
     * @var Collection<TheaterNews>
     * @ORM\OneToMany(targetEntity="TheaterNews", mappedBy="news")
     */
    protected $theaters;

    /**
     * special_sites
     *
     * @var Collection<SpecialSiteNews>
     * @ORM\OneToMany(targetEntity="SpecialSiteNews", mappedBy="news")
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
     * @return File|null
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * set image
     *
     * @param File|null $image
     * @return void
     */
    public function setImage(?File $image)
    {
        $this->image = $image;
    }

    /**
     * get category
     *
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * get category label
     *
     * @return string|null
     */
    public function getCategoryLabel()
    {
        return self::$categories[$this->getCategory()] ?? null;
    }

    /**
     * set category
     *
     * @param int $category
     * @return void
     */
    public function setCategory(int $category)
    {
        $this->category = $category;
    }

    /**
     * get headline
     *
     * @return string
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * set headline
     *
     * @param string $headline
     * @return void
     */
    public function setHeadline(string $headline)
    {
        $this->headline = $headline;
    }

    /**
     * get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * set body
     *
     * @param string $body
     * @return void
     */
    public function setBody(string $body)
    {
        $this->body = $body;
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
        if ($startDt instanceof \DateTime) {
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
        if ($endDt instanceof \DateTime) {
            $this->endDt = $endDt;
        } else {
            $this->endDt = new \DateTime($endDt);
        }
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
     * get special_sites
     *
     * @return Collection
     */
    public function getSpecialSites(): Collection
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

        foreach ($this->getPages() as $pageNews) {
            /** @var PageNews $pageNews */
            $publications->add($pageNews->getPage());
        }

        foreach ($this->getTheaters() as $theaterNews) {
            /** @var TheaterNews $theaterNews */
            $publications->add($theaterNews->getTheater());
        }

        foreach ($this->getSpecialSites() as $specialSiteNews) {
            /** @var SpecialSiteNews $specialSiteNews */
            $publications->add($specialSiteNews->getSpecialSite());
        }

        return $publications;
    }
}
