<?php

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
     *
     * @param string $container
     * @param string $blob
     * @return string
     */
    protected function getBlobUrl(string $container, string $blob)
    {
        $publicEndpoint = $this->settings['storage']['public_endpoint'];

        if ($publicEndpoint) {
            return sprintf('%s/%s/%s', $publicEndpoint, $container, $blob);
        }

        return $this->bc->getBlobUrl($container, $blob);
    }
}
