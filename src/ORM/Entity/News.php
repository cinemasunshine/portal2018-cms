<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\News as BaseNews;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * News entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\NewsRepository")
 * @ORM\Table(name="news", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class News extends BaseNews
{
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
     * get category label
     *
     * @return string|null
     */
    public function getCategoryLabel()
    {
        return self::$categories[$this->getCategory()] ?? null;
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
