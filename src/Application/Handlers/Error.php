<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use Exception;
use Monolog\Logger;
use Slim\Handlers\Error as BaseHandler;
use Throwable;

/**
 * Error handler
 */
class Error extends BaseHandler
{
    /** @var Logger */
    protected $logger;

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

    /**
     * @param Exception|Throwable $exception
     */
    protected function log($exception): void
    {
        $this->logger->error($exception->getMessage(), [
            'type' => get_class($exception),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @phpcsSuppress SlevomatCodingStandard.Exceptions.ReferenceThrowableOnly.ReferencedGeneralException
     */
    protected function renderHtmlErrorMessage(Exception $exception)
    {
        if (APP_DEBUG) {
            return parent::renderHtmlErrorMessage($exception);
        }

        return file_get_contents(APP_ROOT . '/error/500.html');
    }
}
