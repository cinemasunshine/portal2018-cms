<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use Exception;
use Monolog\Logger;
use Slim\Handlers\PhpError as BaseHandler;
use Throwable;

/**
 * PHP Error handler
 */
class PhpError extends BaseHandler
{
    protected Logger $logger;

    public function __construct(Logger $logger, bool $displayErrorDetails = false)
    {
        $this->logger = $logger;

        parent::__construct($displayErrorDetails);
    }

    /**
     * @see Slim\Handlers\AbstractError
     *
     * @param Exception|Throwable $throwable
     */
    protected function writeToErrorLog($throwable): void
    {
        $this->log($throwable);
    }

    protected function log(Throwable $error): void
    {
        $this->logger->error($error->getMessage(), [
            'type' => get_class($error),
            'code' => $error->getCode(),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
            'trace' => $error->getTraceAsString(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function renderHtmlErrorMessage(Throwable $error)
    {
        if (APP_DEBUG) {
            return parent::renderHtmlErrorMessage($error);
        }

        return file_get_contents(APP_ROOT . '/error/500.html');
    }
}
