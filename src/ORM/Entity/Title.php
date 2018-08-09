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
     * name
     * 
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;
    
    /** 
     * name_kana
     * 
     * @var string
     * @ORM\Column(type="string", name="name_kana")
     */
    protected $nameKana;
    
    /** 
     * name_en
     * 
     * @var string
     * @ORM\Column(type="string", name="name_en")
     */
    protected $nameEn;
    
    /**
     * credit
     *
     * @var string
     * @ORM\Column(type="string")
     */
    protected $credit;
    
    /**
     * catchcopy
     *
     * @var string
     * @ORM\Column(type="text")
     */
    protected $catchcopy;
    
    /**
     * introduction
     *
     * @var string
     * @ORM\Column(type="text")
     */
    protected $introduction;
    
    /**
     * director
     *
     * @var string
     * @ORM\Column(type="string")
     */
    protected $director;
    
    /**
     * cast
     *
     * @var string
     * @ORM\Column(type="string")
     */
    protected $cast;
    
    /**
     * publishing_expected_date
     *
     * @var \DateTime
     * @ORM\Column(type="date", name="publishing_expected_date", nullable=true)
     */
    protected $publishingExpectedDate;
    
    /**
     * website
     *
     * @var string
     * @ORM\Column(type="string")
     */
    protected $website;
    
    /**
     * rating
     *
     * @var string
     * @ORM\Column(type="smallint")
     */
    protected $rating;
    
    /**
     * universal
     *
     * @var array
     * @ORM\Column(type="json_array")
     */
    protected $universal;
    
    /**
     * is_deleted
     *
     * @var bool
     * @ORM\Column(type="boolean", name="is_deleted")
     */
    protected $isDeleted;
    
    /**
     * crated_at
     *
     * @var \DateTime
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;
    
    /**
     * updated_at
     *
     * @var \DateTime
     * @ORM\Column(type="datetime", name="updated_at")
     */
    protected $updatedAt;
    
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
     * @return string
     */
    public function getNameKana()
    {
        return $this->nameKana;
    }
    
    /**
     * set name_kana
     *
     * @param string $nameKana
     * @return void
     */
    public function setNameKana(string $nameKana)
    {
        $this->nameKana = $nameKana;
    }
    
    /**
     * get name_en
     *
     * @return string
     */
    public function getNameEn()
    {
        return $this->nameEn;
    }
    
    /**
     * set name_en
     *
     * @param string $nameEn
     * @return void
     */
    public function setNameEn(string $nameEn)
    {
        $this->nameEn = $nameEn;
    }
    
    /**
     * credit
     *
     * @return string
     */
    public function getCredit()
    {
        return $this->credit;
    }
    
    /**
     * set credit
     *
     * @param string $credit
     * @return void
     */
    public function setCredit(string $credit)
    {
        $this->credit = $credit;
    }
    
    /**
     * get catchcopy
     *
     * @return string
     */
    public function getCatchcopy()
    {
        return $this->catchcopy;
    }
    
    /**
     * set catchcopy
     *
     * @param string $catchcopy
     * @return void
     */
    public function setCatchcopy(string $catchcopy)
    {
        $this->catchcopy = $catchcopy;
    }
    
    /**
     * get introduction
     *
     * @return string
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }
    
    /**
     * set introduction
     *
     * @param string $introduction
     * @return void
     */
    public function setIntroduction(string $introduction)
    {
        $this->introduction = $introduction;
    }
    
    /**
     * get director
     *
     * @return string
     */
    public function getDirector()
    {
        return $this->director;
    }
    
    /**
     * set director
     *
     * @param string $director
     * @return void
     */
    public function setDirector(string $director)
    {
        $this->director = $director;
    }
    
    /**
     * get cast
     *
     * @return string
     */
    public function getCast()
    {
        return $this->cast;
    }
    
    /**
     * set cast
     *
     * @param string $cast
     * @return void
     */
    public function setCast(string $cast)
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
        if (is_null($publishingExpectedDate) || ($publishingExpectedDate instanceof \Datetime)) {
            $this->publishingExpectedDate = $publishingExpectedDate;
        } else {
            $this->publishingExpectedDate = new \DateTime($publishingExpectedDate);
        }
    }
    
    /**
     * get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }
    
    /**
     * set website
     *
     * @param string $website
     * @return void
     */
    public function setWebsite(string $website)
    {
        $this->website = $website;
    }
    
    /**
     * get rating
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }
    
    /**
     * set rating
     *
     * @param int $rating
     * @return void
     */
    public function setRating(int $rating)
    {
        $this->rating = $rating;
    }
    
    /**
     * get universal
     *
     * @return array
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
     * @param array $universal
     * @return void
     */
    public function setUniversal(array $universal)
    {
        $this->universal = $universal;
    }
    
    /**
     * get is_deleted
     *
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }
    
    /**
     * set is_deleted
     *
     * @param bool $isDeleted
     * @return void
     */
    public function setIsDeleted(bool $isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }
    
    /**
     * is deleted
     * 
     * alias ::getIsDeleted()
     *
     * @return bool
     */
    public function isDeleted()
    {
        return $this->getIsDeleted();
    }
    
    /**
     * get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * set created_at
     *
     * @param \DateTime|string $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = ($createdAt instanceof \Datetime)
                        ? $createdAt
                        : new \DateTime($createdAt);
    }
    
    /**
     * get updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    
    /**
     * set updated_at
     *
     * @param \DateTime|string $updatedAt
     * @return void
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = ($updatedAt instanceof \Datetime)
                        ? $updatedAt
                        : new \DateTime($updatedAt);
    }
    
    /**
     * pre persist
     * 
     * @ORM\PrePersist
     * @return void
     */
    public function prePersist()
    {
        $this->setCreatedAt('now');
        $this->setUpdatedAt('now');
    }
    
    /**
     * pre update
     *
     * @ORM\PreUpdate
     * @return void
     */
    public function preUpdate()
    {
        $this->setUpdatedAt('now');
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