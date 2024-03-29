<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\Title as BaseTitle;
use Doctrine\ORM\Mapping as ORM;

/**
 * Title entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\TitleRepository")
 * @ORM\Table(name="title", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 *
 * @method File|null getImage()
 */
class Title extends BaseTitle
{
    /** @var array<int, string> */
    protected static array $ratingTypes = [
        '1' => 'G',
        '2' => 'PG12',
        '3' => 'R15+',
        '4' => 'R18+',
    ];

    /** @var array<int, string> */
    protected static array $universalTypes = [
        '1' => '音声上映',
        '2' => '字幕上映',
    ];

    /**
     * @return array<int, string>
     */
    public static function getRatingTypes(): array
    {
        return self::$ratingTypes;
    }

    /**
     * @return array<int, string>
     */
    public static function getUniversalTypes(): array
    {
        return self::$universalTypes;
    }

    /**
     * @return string[]
     */
    public function getUniversalLabel(): array
    {
        $univarsal = $this->getUniversal();
        $types     = self::getUniversalTypes();
        $labels    = [];

        foreach ($univarsal as $value) {
            if (isset($types[$value])) {
                $labels[] = $types[$value];
            }
        }

        return $labels;
    }
}
