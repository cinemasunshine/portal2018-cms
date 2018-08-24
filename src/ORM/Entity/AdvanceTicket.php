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
 * @ORM\Entity
 * @ORM\Table(name="advance_ticket", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class AdvanceTicket extends AbstractEntity
{
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
     * advance_sale
     *
     * @var AdvanceSale
     * @ORM\ManyToOne(targetEntity="AdvanceSale")
     * @ORM\JoinColumn(name="advance_sale_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    protected $advanceSale;
    
    /**
     * release_date
     *
     * @var \DateTime|null
     * @ORM\Column(type="datetime", name="release_date", nullable=true)
     */
    protected $releaseDate;
    
    /**
     * release_date_text
     *
     * @var string
     * @ORM\Column(type="string", name="release_date_text", nullable=true)
     */
    protected $releaseDateText;
    
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
     * @ORM\Column(type="smallint")
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
     * @ORM\Column(type="smallint", name="special_gift_stock", nullable=true)
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
     * get release_date
     *
     * @return \DateTime|null
     */
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }
    
    /**
     * set release_date
     *
     * @param \DateTime|string|null $releaseDate
     * @return void
     */
    public function setReleaseDate($releaseDate)
    {
        if (is_null($releaseDate) || ($releaseDate instanceof \Datetime)) {
            $this->releaseDate = $releaseDate;
        } else {
            $this->releaseDate = new \DateTime($releaseDate);
        }
    }
    
    /**
     * get release_date_text
     *
     * @return string
     */
    public function getReleaseDateText()
    {
        return $this->releaseDateText;
    }
    
    /**
     * set release_date_text
     *
     * @param string $releaseDateText
     * @return void
     */
    public function setReleaseDateText(string $releaseDateText)
    {
        $this->releaseDateText = $releaseDateText;
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
     * set special_gift_stock
     *
     * @param int|null $specialGiftStock
     * @return void
     */
    public function setSpecialGiftStock($specialGiftStock)
    {
        $this->specialGiftStock = !empty($specialGiftStock) ?: null;
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
}
