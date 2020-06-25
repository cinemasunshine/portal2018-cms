<?php

/**
 * Title.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Cinemasunshine\PortalAdmin\ORM\Entity;

use Cinemasunshine\ORM\Entity\Title as BaseTitle;
use Doctrine\ORM\Mapping as ORM;

/**
 * Title entity class
 *
 * @ORM\Entity(repositoryClass="Cinemasunshine\PortalAdmin\ORM\Repository\TitleRepository")
 * @ORM\Table(name="title", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class Title extends BaseTitle
{
    /** @var array */
    protected static $ratingTypes = [
        '1' => 'G',
        '2' => 'PG12',
        '3' => 'R15+',
        '4' => 'R18+',
    ];

    /** @var array */
    protected static $universalTypes = [
        '1' => '音声上映',
        '2' => '字幕上映',
    ];

    /**
     * Return rating types
     *
     * @return array
     */
    public static function getRatingTypes(): array
    {
        return self::$ratingTypes;
    }

    /**
     * Return universal types
     *
     * @return array
     */
    public static function getUniversalTypes(): array
    {
        return self::$universalTypes;
    }

    /**
     * Return univarsal label
     *
     * @return array
     */
    public function getUniversalLabel(): array
    {
        $univarsal = $this->getUniversal();
        $types = self::getUniversalTypes();
        $labels = [];

        foreach ($univarsal as $value) {
            if (isset($types[$value])) {
                $labels[] = $types[$value];
            }
        }

        return $labels;
    }
}
