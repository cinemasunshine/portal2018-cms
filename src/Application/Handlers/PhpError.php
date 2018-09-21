<?php
/**
 * Error.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Application\Handlers;

use Slim\Container;
use Slim\Handlers\PhpError as BaseHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * PHP Error handler
 */
class PhpError extends BaseHandler
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
        if (APP_ENV === 'dev') {
            return parent::renderHtmlErrorMessage($error);
        }
        
        return file_get_contents(APP_ROOT . '/error/500.html');
    }
}
