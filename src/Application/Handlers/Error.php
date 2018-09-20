<?php
/**
 * Error.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Application\Handlers;

use Slim\Container;
use Slim\Handlers\Error as BaseHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, \Exception $exception)
    {
        $this->log($exception);
        
        return parent::__invoke($request, $response, $exception);
    }
    
    /**
     * log
     *
     * @param \Exception $exception
     * @return void
     */
    protected function log(\Exception $exception)
    {
        $this->logger->error($exception->getMessage(), [
            'type' => get_class($exception),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}