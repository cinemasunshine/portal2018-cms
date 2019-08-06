<?php
/**
 * ShowingFormat.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

use Cinemasunshine\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * ShowingFormat entity class
 *
 * @ORM\Entity
 * @ORM\Table(name="showing_format", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class ShowingFormat extends AbstractEntity
{
    use TimestampableTrait;
    
    const SYSTEM_2D               = 1;
    const SYSTEM_3D               = 2;
    const SYSTEM_4DX              = 3;
    const SYSTEM_4DX3D            = 4;
    const SYSTEM_IMAX             = 5;
    const SYSTEM_IMAX3D           = 6;
    // const SYSTEM_BESTIA           = 7; 削除 SASAKI-449
    // const SYSTEM_BESTIA3D         = 8; 削除 SASAKI-449
    // const SYSTEM_BTSX             = 9; 削除 SASAKI-449
    const SYSTEM_SCREENX          = 10; // SASAKI-351
    const SYSTEM_4DX_WITH_SCREENX = 11; // SASAKI-428
    const SYSTEM_NONE             = 99;
    
    const SOUND_BESTIA        = 1;
    const SOUND_DTSX          = 2;
    const SOUND_DOLBY_ATMOS   = 3;
    const SOUND_GDC_IMMERSIVE = 4;
    const SOUND_NONE          = 99;
    
    const VOICE_SUBTITLE = 1;
    const VOICE_DUB = 2;
    const VOICE_NONE = 3;
    
    /** @var array */
    protected static $systemList = [
        self::SYSTEM_2D               => '2D',
        self::SYSTEM_3D               => '3D',
        self::SYSTEM_4DX              => '4DX',
        self::SYSTEM_4DX3D            => '4DX3D',
        self::SYSTEM_IMAX             => 'IMAX',
        self::SYSTEM_IMAX3D           => 'IMAX3D',
        self::SYSTEM_SCREENX          => 'ScreenX', // SASAKI-351
        self::SYSTEM_4DX_WITH_SCREENX => '4DX with ScreenX', // SASAKI-428
        self::SYSTEM_NONE             => 'なし',
    ];
    
    /** @var array */
    protected static $soundList = [
        self::SOUND_BESTIA        => 'BESTIA',
        self::SOUND_DTSX          => 'dts-X',
        self::SOUND_DOLBY_ATMOS   => 'dolbyatmos',
        self::SOUND_GDC_IMMERSIVE => 'GDCイマーシブサウンド',
        self::SOUND_NONE          => 'なし',
    ];
    
    /** @var array */
    protected static $voiceList = [
        self::VOICE_SUBTITLE => '字幕',
        self::VOICE_DUB      => '吹替',
        self::VOICE_NONE     => 'なし', // SASAKI-297
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
     * schedule
     *
     * @var Schedule
     * @ORM\ManyToOne(targetEntity="Schedule")
     * @ORM\JoinColumn(name="schedule_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $schedule;
    
    /**
     * system
     *
     * @var int
     * @ORM\Column(type="smallint", nullable=false, options={"unsigned"=true})
     */
    protected $system;
    
    /**
     * sound
     *
     * @var int
     * @ORM\Column(type="smallint", nullable=false, options={"unsigned"=true})
     */
    protected $sound;
    
    /**
     * voice
     *
     * @var int
     * @ORM\Column(type="smallint", nullable=false, options={"unsigned"=true})
     */
    protected $voice;
    
    
    /**
     * return system list
     *
     * @return array
     */
    public static function getSystemList()
    {
        return self::$systemList;
    }
    
    /**
     * return sound list
     *
     * @return array
     */
    public static function getSoundList()
    {
        return self::$soundList;
    }
    
    /**
     * return voice list
     *
     * @return array
     */
    public static function getVoiceList()
    {
        return self::$voiceList;
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
     * get schedule
     *
     * @return Schedule
     */
    public function getSchedule()
    {
        return $this->schedule;
    }
    
    /**
     * schedule
     *
     * @param Schedule $schedule
     * @return void
     */
    public function setSchedule(Schedule $schedule)
    {
        $this->schedule = $schedule;
    }
    
    /**
     * get system
     *
     * @return int
     */
    public function getSystem()
    {
        return $this->system;
    }
    
    /**
     * get system label
     *
     * @return string|null
     */
    public function getSystemLabel()
    {
        return self::$systemList[$this->getSystem()] ?? null;
    }
    
    /**
     * set system
     *
     * @param int $system
     * @return void
     */
    public function setSystem(int $system)
    {
        $this->system = $system;
    }
    
    /**
     * get sound
     *
     * @return int
     */
    public function getSound()
    {
        return $this->sound;
    }
    
    /**
     * set sound
     *
     * @param int $sound
     * @return void
     */
    public function setSound(int $sound)
    {
        $this->sound = $sound;
    }
    
    /**
     * get voice
     *
     * @return int
     */
    public function getVoice()
    {
        return $this->voice;
    }
    
    /**
     * get voice label
     *
     * @return string|null
     */
    public function getVoiceLabel()
    {
        return self::$voiceList[$this->getVoice()] ?? null;
    }
    
    /**
     * set voice
     *
     * @param int $voice
     * @return void
     */
    public function setVoice(int $voice)
    {
        $this->voice = $voice;
    }
}
