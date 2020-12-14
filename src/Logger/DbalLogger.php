<?php

/**
 * DbalLogger.php
 */

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

    /**
     * construct
     *
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->log($sql, [
            'params' => $params,
            'types' => $types,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
    }

    /**
     * log
     *
     * @param string $message
     * @param array  $context
     * @return void
     */
    protected function log($message, array $context = [])
    {
        $this->logger->debug($message, $context);
    }
}
