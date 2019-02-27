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
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    protected $size;
    
    /**
     * blob container name
     *
     * @var string
     */
    protected static $blobContainer = 'file';
    
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
     * get blob container
     *
     * @return string
     */
    public static function getBlobContainer()
    {
        return self::$blobContainer;
    }
    
    /**
     * create name
     *
     * @param string $file original file
     * @return string
     */
    public static function createName(string $file)
    {
        $info = pathinfo($file);
        
        return md5(uniqid('', true)) . '.' . $info['extension'];
    }
}
