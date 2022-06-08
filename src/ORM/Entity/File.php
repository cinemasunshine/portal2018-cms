<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\File as BaseFile;
use Doctrine\ORM\Mapping as ORM;

/**
 * File entity class
 *
 * @todo 削除のイベントでファイルも削除される仕組み
 *
 * @ORM\Entity
 * @ORM\Table(name="file", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class File extends BaseFile
{
    /**
     * blob container name
     */
    protected static string $blobContainer = 'file';

    /**
     * get blob container
     */
    public static function getBlobContainer(): string
    {
        return self::$blobContainer;
    }

    /**
     * create name
     *
     * @param string $file original file
     */
    public static function createName(string $file): string
    {
        $info = pathinfo($file);

        return md5(uniqid('', true)) . '.' . $info['extension'];
    }
}
