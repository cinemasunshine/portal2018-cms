<?php

/**
 * Error.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Application\Handlers;

use Slim\Container;
use Slim\Handlers\Error as BaseHandler;

/**
 * Error handler
 */
class Error extends BaseHandler
{
    /** @var Container */
    protected $container;

    /** @var \Monolog\Logger */
    protected $logger;

    /**
     * construct
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->logger = $container->get('logger');

        parent::__construct($container->get('settings')['displayErrorDetails']);
    }

    /**
     * @param \Exception|\Throwable $throwable
     * @return void
     * @see Slim\Handlers\AbstractError
     */
    protected function writeToErrorLog($throwable)
    {
        $this->log($throwable);
    }

    /**
     * @param \Exception|\Throwable $exception
     * @return void
     */
    protected function log($exception)
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
     */
    protected function renderHtmlErrorMessage(\Exception $exception)
    {
        if (APP_DEBUG) {
            return parent::renderHtmlErrorMessage($exception);
        }

        return file_get_contents(APP_ROOT . '/error/500.html');
    }
}
