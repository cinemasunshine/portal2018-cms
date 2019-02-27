<?php
/**
 * AzureBlobStorageHandler.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Logger\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

/**
 * Azure Blob Storage handler
 */
class AzureBlobStorageHandler extends AbstractProcessingHandler
{
    /** @var BlobRestProxy */
    protected $client;
    
    /** @var string */
    protected $container;
    
    /** @var string */
    protected $blob;
    
    /**
     * construct
     *
     * @param BlobRestProxy $client
     * @param string        $container blob container name
     * @param string        $blob      blob name
     * @param int           $level
     * @param boolean       $bubble
     */
    public function __construct(
        BlobRestProxy $client,
        string $container,
        string $blob,
        $level = Logger::DEBUG,
        $bubble = true
    ) {
        $this->client = $client;
        $this->container = $container;
        $this->blob = $blob;
        
        parent::__construct($level, $bubble);
        
        $this->createBlob();
    }
    
    /**
     * create blob
     *
     * @return void
     */
    protected function createBlob()
    {
        try {
            $this->client->getBlobMetadata($this->container, $this->blob);
        } catch (ServiceException $e) {
            if ($e->getCode() !== 404) {
                throw $e;
            }
            
            $this->client->createAppendBlob($this->container, $this->blob);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        $this->client->appendBlock($this->container, $this->blob, $record['formatted']);
    }
}
