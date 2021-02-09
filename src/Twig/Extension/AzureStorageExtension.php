<?php

declare(strict_types=1);

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

    public function __construct(BlobRestProxy $client, ?string $publicEndpoint = null)
    {
        $this->client         = $client;
        $this->publicEndpoint = $publicEndpoint;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
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
     */
    public function blobUrl(string $container, string $blob): string
    {
        if ($this->publicEndpoint) {
            return sprintf('%s/%s/%s', $this->publicEndpoint, $container, $blob);
        }

        return $this->client->getBlobUrl($container, $blob);
    }
}
