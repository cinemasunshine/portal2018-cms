<?php

/**
 * AdvanceTicket.php
 */

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\AdvanceTicket as BaseAdvanceTicket;
use Doctrine\ORM\Mapping as ORM;

/**
 * AdvanceTicket entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\AdvanceTicketRepository")
 * @ORM\Table(name="advance_ticket", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 *
 * @method AdvanceSale getAdvanceSale()
 */
class AdvanceTicket extends BaseAdvanceTicket
{
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
     * get type label
     *
     * @return string|null
     */
    public function getTypeLabel()
    {
        return self::$types[$this->getType()] ?? null;
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
