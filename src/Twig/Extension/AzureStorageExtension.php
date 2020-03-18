<?php
/**
 * AzureStorageExtension.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Twig\Extension;

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

    /** @var string|null $publicUrl */
    protected $publicUrl;

    /**
     * construct
     *
     * @param BlobRestProxy $client
     * @param string|null $publicUrl
     */
    public function __construct(BlobRestProxy $client, $publicUrl = null)
    {
        $this->client = $client;
        $this->publicUrl = $publicUrl;
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
        if ($this->publicUrl) {
            return sprintf('%s/%s/%s', $this->publicUrl, $container, $blob);
        }

        return $this->client->getBlobUrl($container, $blob);
    }
}
