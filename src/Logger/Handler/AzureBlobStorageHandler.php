<?php

declare(strict_types=1);

namespace App\Logger\Handler;

use Blue32a\Monolog\Handler\AzureBlobStorageHandler as BaseHandler;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

class AzureBlobStorageHandler extends BaseHandler
{
    protected bool $isBlobCreated = false;

    protected function createBlob(): void
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
    protected function write(array $record): void
    {
        if (! $this->isBlobCreated) {
            $this->createBlob();
            $this->isBlobCreated = true;
        }

        parent::write($record);
    }
}
