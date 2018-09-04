<?php
/**
 * MainBanner.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

use Cinemasunshine\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * MainBanner entity class
 * 
 * @ORM\Entity
 * @ORM\Table(name="main_banner", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class MainBanner extends AbstractEntity
{
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
}