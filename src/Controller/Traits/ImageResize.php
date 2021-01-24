<?php

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

    /**
     * create ImageManager
     *
     * @return void
     */
    private function createImageManager(): void
    {
        $this->imageManager = new ImageManager(['driver' => 'gd']);
    }

    /**
     * return ImageManager
     *
     * @return ImageManager
     */
    private function getImageManager(): ImageManager
    {
        if (! $this->imageManager) {
            $this->createImageManager();
        }

        return $this->imageManager;
    }

    /**
     * resize image
     *
     * @link http://image.intervention.io/api/make
     *
     * @param mixed    $data   ファイルパスなど。make()を参照。
     * @param int|null $width
     * @param int|null $height
     * @return StreamInterface
     */
    protected function resizeImage($data, ?int $width, ?int $height = null)
    {
        $imageManager = $this->getImageManager();
        $image        = $imageManager
            ->make($data)
            ->resize($width, $height, static function ($constraint) {
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
