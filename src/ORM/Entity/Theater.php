<?php
/**
 * Theater.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Cinemasunshine\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * Theater entity class
 * 
 * @ORM\Entity
 * @ORM\Table(name="theater", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class Theater extends AbstractEntity
{
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
     * @ORM\Column(type="smallint", name="master_version")
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
     * @ORM\Column(type="smallint", name="display_order")
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
     * construct
     */
    public function __construct()
    {
        $this->adminUsers = new ArrayCollection();
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
}