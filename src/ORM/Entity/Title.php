<?php

/**
 * Title.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;
use Cinemasunshine\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * Title entity class
 *
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\TitleRepository")
 * @ORM\Table(name="title", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class Title extends AbstractEntity
{
    use SavedUserTrait;
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
     * image
     *
     * @var File|null
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="image_file_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
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
     * name_kana
     *
     * @var string|null
     * @ORM\Column(type="string", name="name_kana", nullable=true)
     */
    protected $nameKana;

    /**
     * name_original
     *
     * @var string|null
     * @ORM\Column(type="string", name="name_original", nullable=true)
     */
    protected $nameOriginal;

    /**
     * credit
     *
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $credit;

    /**
     * catchcopy
     *
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    protected $catchcopy;

    /**
     * introduction
     *
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    protected $introduction;

    /**
     * director
     *
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $director;

    /**
     * cast
     *
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $cast;

    /**
     * publishing_expected_date
     *
     * @var \DateTime|null
     * @ORM\Column(type="date", name="publishing_expected_date", nullable=true)
     */
    protected $publishingExpectedDate;

    /**
     * official_site
     *
     * @var string|null
     * @ORM\Column(type="string", name="official_site", nullable=true)
     */
    protected $officialSite;

    /**
     * rating
     *
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true, options={"unsigned"=true})
     */
    protected $rating;

    /**
     * universal
     *
     * @var array|null
     * @ORM\Column(type="json", nullable=true)
     */
    protected $universal;

    /**
     * レイティング区分
     *
     * @var array
     */
    protected static $ratingTypes = [
        '1' => 'G',
        '2' => 'PG12',
        '3' => 'R15+',
        '4' => 'R18+',
    ];

    /**
     * ユニバーサル区分
     *
     * @var array
     */
    protected static $universalTypes = [
        '1' => '音声上映',
        '2' => '字幕上映',
    ];

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
    public function setImage($image)
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
     * get name_kana
     *
     * @return string|null
     */
    public function getNameKana()
    {
        return $this->nameKana;
    }

    /**
     * set name_kana
     *
     * @param string|null $nameKana
     * @return void
     */
    public function setNameKana(?string $nameKana)
    {
        $this->nameKana = $nameKana;
    }

    /**
     * get name_original
     *
     * @return string|null
     */
    public function getNameOriginal()
    {
        return $this->nameOriginal;
    }

    /**
     * set name_original
     *
     * @param string|null $nameOriginal
     * @return void
     */
    public function setNameOriginal(?string $nameOriginal)
    {
        $this->nameOriginal = $nameOriginal;
    }

    /**
     * credit
     *
     * @return string|null
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * set credit
     *
     * @param string|null $credit
     * @return void
     */
    public function setCredit(?string $credit)
    {
        $this->credit = $credit;
    }

    /**
     * get catchcopy
     *
     * @return string|null
     */
    public function getCatchcopy()
    {
        return $this->catchcopy;
    }

    /**
     * set catchcopy
     *
     * @param string|null $catchcopy
     * @return void
     */
    public function setCatchcopy(?string $catchcopy)
    {
        $this->catchcopy = $catchcopy;
    }

    /**
     * get introduction
     *
     * @return string|null
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     * set introduction
     *
     * @param string|null $introduction
     * @return void
     */
    public function setIntroduction(?string $introduction)
    {
        $this->introduction = $introduction;
    }

    /**
     * get director
     *
     * @return string|null
     */
    public function getDirector()
    {
        return $this->director;
    }

    /**
     * set director
     *
     * @param string|null $director
     * @return void
     */
    public function setDirector(?string $director)
    {
        $this->director = $director;
    }

    /**
     * get cast
     *
     * @return string|null
     */
    public function getCast()
    {
        return $this->cast;
    }

    /**
     * set cast
     *
     * @param string|null $cast
     * @return void
     */
    public function setCast(?string $cast)
    {
        $this->cast = $cast;
    }

    /**
     * get publishing_expected_date
     *
     * @return \DateTime|null
     */
    public function getPublishingExpectedDate()
    {
        return $this->publishingExpectedDate;
    }

    /**
     * set publishing_dxpected_date
     *
     * @param \DateTime|string|null $publishingExpectedDate
     * @return void
     */
    public function setPublishingExpectedDate($publishingExpectedDate)
    {
        if (is_null($publishingExpectedDate) || ($publishingExpectedDate instanceof \DateTime)) {
            $this->publishingExpectedDate = $publishingExpectedDate;
        } else {
            $this->publishingExpectedDate = new \DateTime($publishingExpectedDate);
        }
    }

    /**
     * get official_site
     *
     * @return string
     */
    public function getOfficialSite()
    {
        return $this->officialSite;
    }

    /**
     * set official_site
     *
     * @param string $officialSite
     * @return void
     */
    public function setOfficialSite(string $officialSite)
    {
        $this->officialSite = $officialSite;
    }

    /**
     * get rating
     *
     * @return int|null
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * set rating
     *
     * @param int|null $rating
     * @return void
     */
    public function setRating(?int $rating)
    {
        $this->rating = $rating;
    }

    /**
     * get universal
     *
     * @return array|null
     */
    public function getUniversal()
    {
        return $this->universal;
    }

    /**
     * get univarsal label
     *
     * @return array
     */
    public function getUniversalLabel()
    {
        $univarsal = $this->getUniversal();
        $types = self::getUniversalTypes();
        $labels = [];

        foreach ($univarsal as $value) {
            if (isset($types[$value])) {
                $labels[] = $types[$value];
            }
        }

        return $labels;
    }

    /**
     * set universal
     *
     * @param array|null $universal
     * @return void
     */
    public function setUniversal(?array $universal)
    {
        $this->universal = $universal;
    }

    /**
     * get レイティング区分
     *
     * @return array
     */
    public static function getRatingTypes()
    {
        return self::$ratingTypes;
    }

    /**
     * get ユニバーサル区分
     *
     * @return array
     */
    public static function getUniversalTypes()
    {
        return self::$universalTypes;
    }
}
