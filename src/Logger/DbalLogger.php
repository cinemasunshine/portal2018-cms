<?php

declare(strict_types=1);

namespace App\Logger;

use Doctrine\DBAL\Logging\SQLLogger;
use Monolog\Logger;

/**
 * DBAL logger
 */
class DbalLogger implements SQLLogger
{
    /** @var Logger */
    protected $logger;

    /** @var int */
    protected $count;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, ?array $params = null, ?array $types = null)
    {
        $this->log($sql, [
            'params' => $params,
            'types' => $types,
        ]);
    }

    public function stopQuery(): void
    {
    }

    /**
     * @param array<mixed> $context
     */
    protected function log(string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }
}
