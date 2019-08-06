<?php
/**
 * AdvanceTicket.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

use Cinemasunshine\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * AdvanceTicket entity class
 *
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\AdvanceTicketRepository")
 * @ORM\Table(name="advance_ticket", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class AdvanceTicket extends AbstractEntity
{
    use SoftDeleteTrait;
    use TimestampableTrait;
    
    const TYPE_MVTK  = 1;
    const TYPE_PAPER = 2;
    
    const SPECIAL_GIFT_STOCK_IN     = 1;
    const SPECIAL_GIFT_STOCK_FEW    = 2;
    const SPECIAL_GIFT_STOCK_NOT_IN = 3;
    
    const STATUS_PRE_SALE = 1;
    const STATUS_SALE     = 2;
    const STATUS_SALE_END = 3;
    
    /** @var array */
    protected static $types = [
        self::TYPE_MVTK  => 'ムビチケカード',
        self::TYPE_PAPER => '紙券',
    ];
    
    /** @var array */
    protected static $specialGiftStockList = [
        self::SPECIAL_GIFT_STOCK_IN     => '有り',
        self::SPECIAL_GIFT_STOCK_FEW    => '残り僅か',
        self::SPECIAL_GIFT_STOCK_NOT_IN => '特典終了',
    ];
    
    /** @var array */
    protected static $statusList = [
        self::STATUS_PRE_SALE => '販売予定',
        self::STATUS_SALE     => '販売中',
        self::STATUS_SALE_END => '販売終了',
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
     * advance_sale
     *
     * @var AdvanceSale
     * @ORM\ManyToOne(targetEntity="AdvanceSale")
     * @ORM\JoinColumn(name="advance_sale_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    protected $advanceSale;
    
    /**
     * publishing_start_dt
     *
     * @var \DateTime
     * @ORM\Column(type="datetime", name="publishing_start_dt")
     */
    protected $publishingStartDt;
    
    /**
     * release_dt
     *
     * @var \DateTime
     * @ORM\Column(type="datetime", name="release_dt")
     */
    protected $releaseDt;
    
    /**
     * release_dt_text
     *
     * @var string
     * @ORM\Column(type="string", name="release_dt_text", nullable=true)
     */
    protected $releaseDtText;
    
    /**
     * is_sales_end
     *
     * @var bool
     * @ORM\Column(type="boolean", name="is_sales_end")
     */
    protected $isSalesEnd;
    
    /**
     * type
     *
     * @var int
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     */
    protected $type;
    
    /**
     * price_text
     *
     * @var string
     * @ORM\Column(type="string", name="price_text", nullable=true)
     */
    protected $priceText;
    
    /**
     * special_gift
     *
     * @var string
     * @ORM\Column(type="string", name="special_gift", nullable=true)
     */
    protected $specialGift;
    
    /**
     * special_gift_stock
     *
     * @var int|null
     * @ORM\Column(type="smallint", name="special_gift_stock", nullable=true, options={"unsigned"=true})
     */
    protected $specialGiftStock;
    
    /**
     * special_gift_image
     *
     * @var File|null
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="special_gift_image", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     */
    protected $specialGiftImage;
    
    
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
     * get advance_sale
     *
     * @return AdvanceSale
     */
    public function getAdvanceSale()
    {
        return $this->advanceSale;
    }
    
    /**
     * set advance_sale
     *
     * @param AdvanceSale $advanceSale
     * @return void
     */
    public function setAdvanceSale(AdvanceSale $advanceSale)
    {
        $this->advanceSale = $advanceSale;
    }
    
    /**
     * get publishing_start_dt
     *
     * @return \DateTime
     */
    public function getPublishingStartDt()
    {
        return $this->publishingStartDt;
    }
    
    /**
     * set publishing_start_dt
     *
     * @param \DateTime|string $publishingStartDt
     * @return void
     */
    public function setPublishingStartDt($publishingStartDt)
    {
        if ($publishingStartDt instanceof \Datetime) {
            $this->publishingStartDt = $publishingStartDt;
        } else {
            $this->publishingStartDt = new \DateTime($publishingStartDt);
        }
    }
    
    /**
     * get release_dt
     *
     * @return \DateTime
     */
    public function getReleaseDt()
    {
        return $this->releaseDt;
    }
    
    /**
     * set release_dt
     *
     * @param \DateTime|string $releaseDt
     * @return void
     */
    public function setReleaseDt($releaseDt)
    {
        if ($releaseDt instanceof \Datetime) {
            $this->releaseDt = $releaseDt;
        } else {
            $this->releaseDt = new \DateTime($releaseDt);
        }
    }
    
    /**
     * get release_dt_text
     *
     * @return string
     */
    public function getReleaseDtText()
    {
        return $this->releaseDtText;
    }
    
    /**
     * set release_dt_text
     *
     * @param string $releaseDtText
     * @return void
     */
    public function setReleaseDtText(string $releaseDtText)
    {
        $this->releaseDtText = $releaseDtText;
    }
    
    /**
     * get is_sales_end
     *
     * @return bool
     */
    public function getIsSalesEnd()
    {
        return $this->isSalesEnd;
    }
    
    /**
     * is salse end
     *
     * alias getIsSalesEnd()
     *
     * @return bool
     */
    public function isSalseEnd()
    {
        return $this->getIsSalesEnd();
    }
    
    /**
     * set is_salse_end
     *
     * @param bool $isSalesEnd
     * @return void
     */
    public function setIsSalesEnd(bool $isSalesEnd)
    {
        $this->isSalesEnd = $isSalesEnd;
    }
    
    /**
     * get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * get type label
     *
     * @return string|null
     */
    public function getTypeLabel()
    {
        return self::$types[$this->getType()] ?? null;
    }
    
    /**
     * set type
     *
     * @param int $type
     * @return void
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }
    
    /**
     * get price_text
     *
     * @return string
     */
    public function getPriceText()
    {
        return $this->priceText;
    }
    
    /**
     * set price_text
     *
     * @param string $priceText
     * @return void
     */
    public function setPriceText(string $priceText)
    {
        $this->priceText = $priceText;
    }
    
    /**
     * get special_gift
     *
     * @return string
     */
    public function getSpecialGift()
    {
        return $this->specialGift;
    }
    
    /**
     * set special_gift
     *
     * @param string $specialGift
     * @return void
     */
    public function setSpecialGift(string $specialGift)
    {
        $this->specialGift = $specialGift;
    }
    
    /**
     * get special_gift_stock
     *
     * @return int|null
     */
    public function getSpecialGiftStock()
    {
        return $this->specialGiftStock;
    }
    
    /**
     * get special_gift_stock label
     *
     * @return string|null
     */
    public function getSpecialGiftStockLabel()
    {
        return self::$specialGiftStockList[$this->getSpecialGiftStock()] ?? null;
    }
    
    /**
     * set special_gift_stock
     *
     * @param int|null $specialGiftStock
     * @return void
     */
    public function setSpecialGiftStock($specialGiftStock)
    {
        $this->specialGiftStock = $specialGiftStock ?: null;
    }
    
    /**
     * get special_gift_image
     *
     * @return File|null
     */
    public function getSpecialGiftImage()
    {
        return $this->specialGiftImage;
    }
    
    /**
     * set special_gift_image
     *
     * @param File|null $specialGiftImage
     * @return void
     */
    public function setSpecialGiftImage($specialGiftImage)
    {
        $this->specialGiftImage = $specialGiftImage;
    }
    
    /**
     * get status label
     *
     * @return string|null
     */
    public function getStatusLabel()
    {
        if ($this->isSalseEnd()) {
            return self::$statusList[self::STATUS_SALE_END];
        }
        
        $now = new \DateTime('now');
        $end = $this->getAdvanceSale()->getPublishingExpectedDate();
        
        if ($end && $now > $end) {
            return self::$statusList[self::STATUS_SALE_END];
        }
        
        $start = $this->getReleaseDt();
        
        if ($now < $start) {
            return self::$statusList[self::STATUS_PRE_SALE];
        }
        
        // 終了日（作品公開予定日）が設定されていなくても発売される
        return self::$statusList[self::STATUS_SALE];
    }
    
    /**
     * return types
     *
     * @return array
     */
    public static function getTypes()
    {
        return self::$types;
    }
    
    /**
     * return special gift stock list
     *
     * @return array
     */
    public static function getSpecialGiftStockList()
    {
        return self::$specialGiftStockList;
    }
}
