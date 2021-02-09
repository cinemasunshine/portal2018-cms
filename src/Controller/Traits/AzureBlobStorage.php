<?php

declare(strict_types=1);

namespace App\Controller\Traits;

/**
 * AzureBlobStorage trait
 *
 * 本来はストレージやファイルシステムの機能としてまとめるべき。
 * あくまで取り急ぎの対応として。
 */
trait AzureBlobStorage
{
    /**
     * return Blob URL
     *
     * Blobへのpublicアクセスを許可する必要があります。
     */
    protected function getBlobUrl(string $container, string $blob): string
    {
        $publicEndpoint = $this->settings['storage']['public_endpoint'];

        if ($publicEndpoint) {
            return sprintf('%s/%s/%s', $publicEndpoint, $container, $blob);
        }

        return $this->bc->getBlobUrl($container, $blob);
    }
}
