<?php

/**
 * File.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

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
     *
     * @var string
     */
    protected static $blobContainer = 'file';

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
