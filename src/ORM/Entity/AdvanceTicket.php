<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\AdvanceTicket as BaseAdvanceTicket;
use DateTime;
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
    /** @var array<int, string> */
    protected static $types = [
        self::TYPE_MVTK  => 'ムビチケカード',
        self::TYPE_PAPER => '紙券',
    ];

    /** @var array<int, string> */
    protected static $specialGiftStockList = [
        self::SPECIAL_GIFT_STOCK_IN     => '有り',
        self::SPECIAL_GIFT_STOCK_FEW    => '残り僅か',
        self::SPECIAL_GIFT_STOCK_NOT_IN => '特典終了',
    ];

    /** @var array<int, string> */
    protected static $statusList = [
        self::STATUS_PRE_SALE => '販売予定',
        self::STATUS_SALE     => '販売中',
        self::STATUS_SALE_END => '販売終了',
    ];

    public function getTypeLabel(): ?string
    {
        return self::$types[$this->getType()] ?? null;
    }

    public function getSpecialGiftStockLabel(): ?string
    {
        return self::$specialGiftStockList[$this->getSpecialGiftStock()] ?? null;
    }

    public function getStatusLabel(): ?string
    {
        if ($this->isSalseEnd()) {
            return self::$statusList[self::STATUS_SALE_END];
        }

        $now = new DateTime('now');
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
     * @return array<int, string>
     */
    public static function getTypes(): array
    {
        return self::$types;
    }

    /**
     * @return array<int, string>
     */
    public static function getSpecialGiftStockList(): array
    {
        return self::$specialGiftStockList;
    }
}
