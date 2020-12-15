<?php

namespace App\Twig\Extension;

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Azure Storage twig extension class
 */
class AzureStorageExtension extends AbstractExtension
{
    /** @var BlobRestProxy $client */
    protected $client;

    /** @var string|null $publicEndpoint */
    protected $publicEndpoint;

    /**
     * construct
     *
     * @param BlobRestProxy $client
     * @param string|null   $publicEndpoint
     */
    public function __construct(BlobRestProxy $client, $publicEndpoint = null)
    {
        $this->client         = $client;
        $this->publicEndpoint = $publicEndpoint;
    }

    /**
     * get functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('blob_url', [$this, 'blobUrl']),
        ];
    }

    /**
     * Blob URL
     *
     * Blobへのpublicアクセスを許可する必要があります。
     *
     * @param string $container Blob container name
     * @param string $blob      Blob name
     * @return string
     */
    public function blobUrl(string $container, string $blob)
    {
        if ($this->publicEndpoint) {
            return sprintf('%s/%s/%s', $this->publicEndpoint, $container, $blob);
        }

        return $this->client->getBlobUrl($container, $blob);
    }
}
