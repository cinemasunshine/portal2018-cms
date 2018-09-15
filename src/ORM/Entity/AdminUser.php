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
    use SoftDeleteTrait;
    use TimestampableTrait;
    
    const GROUP_MASTER  = 1;
    const GROUP_MANAGER = 2;
    const GROUP_THEATER = 3;
    
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
     * display_name
     * 
     * @var string
     * @ORM\Column(type="string", name="display_name")
     */
    protected $displayName;
    
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
     * @var Theater|null
     * @ORM\ManyToOne(targetEntity="Theater", inversedBy="adminUsers")
     * @ORM\JoinColumn(name="theater_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     */
    protected $theater;
    
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
     * get display_name
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }
    
    /**
     * set display_name
     *
     * @param string $displayName
     * @return void
     */
    public function setDisplayName(string $displayName)
    {
        $this->displayName = $displayName;
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
     * is group
     *
     * @param int $group
     * @return boolean
     */
    public function isGroup(int $group)
    {
        return $this->getGroup() === $group;
    }
    
    /**
     * is master group
     *
     * @return boolean
     */
    public function isMaster()
    {
        return $this->isGroup(self::GROUP_MASTER);
    }
    
    /**
     * is manager group
     *
     * @return boolean
     */
    public function isManager()
    {
        return $this->isGroup(self::GROUP_MANAGER);
    }
    
    /**
     * is theater group
     *
     * @return boolean
     */
    public function isTheater()
    {
        return $this->isGroup(self::GROUP_THEATER);
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
}