<?php

declare(strict_types=1);

namespace App\Logger\Handler;

use Google\Cloud\Logging\LoggingClient;
use Google\Cloud\Logging\PsrLogger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class GoogleCloudLoggingHandler extends AbstractProcessingHandler
{
    protected PsrLogger $logger;

    public function __construct(
        string $name,
        LoggingClient $client,
        int $level = Logger::DEBUG,
        bool $bubble = true
    ) {
        parent::__construct($level, $bubble);

        $this->logger = $client->psrLogger($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record): void
    {
        $this->logger->log(
            $record['level_name'],
            $record['formatted'],
            $record['context']
        );
    }
}
