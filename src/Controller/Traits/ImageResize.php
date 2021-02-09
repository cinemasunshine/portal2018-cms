<?php

declare(strict_types=1);

namespace App\Controller\Traits;

use Intervention\Image\ImageManager;
use Psr\Http\Message\StreamInterface;

/**
 * ImageResize trait
 *
 * @link http://image.intervention.io/
 */
trait ImageResize
{
    /** @var ImageManager|null */
    private $imageManager;

    private function createImageManager(): void
    {
        $this->imageManager = new ImageManager(['driver' => 'gd']);
    }

    private function getImageManager(): ImageManager
    {
        if (! $this->imageManager) {
            $this->createImageManager();
        }

        return $this->imageManager;
    }

    /**
     * @link http://image.intervention.io/api/make
     *
     * @param mixed $data ファイルパスなど。make()を参照。
     */
    protected function resizeImage($data, ?int $width, ?int $height = null): StreamInterface
    {
        $imageManager = $this->getImageManager();
        $image        = $imageManager
            ->make($data)
            ->resize($width, $height, static function ($constraint): void {
                $constraint->aspectRatio(); // アスペクト比を固定
                $constraint->upsize(); // アップサイズしない
            });

        /**
         * テンポラリファイルかつWindows環境の場合、そのままsave()するとエラーが発生する。
         * > Encoding format (tmp) is not supported.
         */
        // $image->save();

        // 上記の問題もあり、ここでは保存せずにストリームオブジェクトを返す
        return $image->stream();
    }
}
