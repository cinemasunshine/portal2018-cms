<?php
/**
 * NotFound.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Application\Handlers;

use Slim\Container;
use Slim\Handlers\NotFound as BaseHandler;

use Psr\Http\Message\ServerRequestInterface;

/**
 * NotFound handler
 */
class NotFound extends BaseHandler
{
    /** @var Container */
    protected $container;
    
    /** @var \Slim\Views\Twig */
    protected $view;
    
    /**
     * construct
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->view = $container->get('view');
    }
    
    /**
     * {@inheritdoc}
     */
    protected function renderHtmlNotFoundOutput(ServerRequestInterface $request)
    {
        if (APP_ENV === 'dev') {
            return parent::renderHtmlNotFoundOutput($request);
        }
        
        return $this->view->fetch('error/client.html.twig', [
            'title' => 'Not Found',
            'status' => 404,
            'message' => 'このページは存在しません。',
        ]);
    }
}
