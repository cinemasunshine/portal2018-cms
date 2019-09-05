<?php
/**
 * AzureStorageExtension.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use Psr\Container\ContainerInterface;

/**
 * Azure Storage twig extension class
 */
class AzureStorageExtension extends AbstractExtension
{
    /** @var ContainerInterface container */
    protected $container;

    /**
     * construct
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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
        $settings = $this->container->get('settings')['storage'];
        $protocol = $settings['secure'] ? 'https' : 'http';

        return sprintf(
            '%s://%s.blob.core.windows.net/%s/%s',
            $protocol,
            $settings['account']['name'],
            $container,
            $blob
        );
    }
}
