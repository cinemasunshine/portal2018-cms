<?php
/**
 * AdminUser.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

use Cinemasunshine\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * AdminUser entity class
 * 
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\AdminUserRepository")
 * @ORM\Table(name="admin_user", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class AdminUser extends AbstractEntity
{
    use TimestampableTrait;
    
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
     * password
     *
     * @var string
     * @ORM\Column(type="string", length=60, options={"fixed":true})
     */
    protected $password;
    
    /**
     * group
     *
     * @var int
     * @ORM\Column(type="smallint", name="`group`", options={"unsigned"=true})
     */
    protected $group;
    
    /**
     * theater
     *
     * @var Theater
     * @ORM\ManyToOne(targetEntity="Theater", inversedBy="adminUsers")
     * @ORM\JoinColumn(name="theater_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     */
    protected $theater;
    
    /**
     * is_deleted
     *
     * @var bool
     * @ORM\Column(type="boolean", name="is_deleted")
     */
    protected $isDeleted;
    
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
     * get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * set password
     *
     * @param string $password
     * @return void
     */
    public function setPassword(string $password)
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }
    
    /**
     * get group
     *
     * @return int
     */
    public function getGroup()
    {
        return $this->group;
    }
    
    /**
     * set group
     *
     * @param int $group
     * @return void
     */
    public function setGroup(int $group)
    {
        $this->group = $group;
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
}