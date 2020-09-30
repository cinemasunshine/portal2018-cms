<?php

/**
 * Error.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Application\Handlers;

use Monolog\Logger;
use Slim\Handlers\PhpError as BaseHandler;

/**
 * PHP Error handler
 */
class PhpError extends BaseHandler
{
    /** @var Logger */
    protected $logger;

    /**
     * construct
     *
     * @param Logger $logger
     * @param bool   $displayErrorDetails
     */
    public function __construct(Logger $logger, bool $displayErrorDetails = false)
    {
        $this->logger = $logger;

        parent::__construct($displayErrorDetails);
    }

    /**
     *  Write to the error log
     *
     * @see Slim\Handlers\AbstractError
     *
     * @param \Exception|\Throwable $throwable
     * @return void
     */
    protected function writeToErrorLog($throwable)
    {
        $this->log($throwable);
    }

    /**
     * Undocumented function
     *
     * @param \Throwable $error
     * @return void
     */
    protected function log(\Throwable $error)
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
    protected function renderHtmlErrorMessage(\Throwable $error)
    {
        if (APP_DEBUG) {
            return parent::renderHtmlErrorMessage($error);
        }

        return file_get_contents(APP_ROOT . '/error/500.html');
    }
}
