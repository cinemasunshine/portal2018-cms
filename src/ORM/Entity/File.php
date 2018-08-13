<?php
/**
 * File.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

use Cinemasunshine\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * File entity class
 * 
 * @todo 削除のイベントでファイルも削除される仕組み
 * 
 * @ORM\Entity
 * @ORM\Table(name="file", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class File extends AbstractEntity
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
     * title
     *
     * @var Title|null
     * @ORM\OneToOne(targetEntity="Title", mappedBy="image")
     */
    protected $title;
    
    /**
     * name
     * 
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    protected $name;
    
    /**
     * original_name
     *
     * @var string
     * @ORM\Column(type="string", name="original_name")
     */
    protected $originalName;
    
    /**
     * mime_type
     *
     * @var string
     * @ORM\Column(type="string", name="mime_type")
     */
    protected $mimeType;
    
    /**
     * size
     *
     * @var int
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     */
    protected $size;
    
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
     * get original_name
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }
    
    /**
     * set original_name
     *
     * @param string $originalName
     * @return void
     */
    public function setOriginalName(string $originalName)
    {
        $this->originalName = $originalName;
    }
    
    /**
     * get mime_type
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }
    
    /**
     * set mime_type
     *
     * @param string $mimeType
     * @return void
     */
    public function setMimeType(string $mimeType)
    {
        $this->mimeType = $mimeType;
    }
    
    /**
     * get size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }
    
    /**
     * set size
     *
     * @param int $size
     * @return void
     */
    public function setSize(int $size)
    {
        $this->size = $size;
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
}