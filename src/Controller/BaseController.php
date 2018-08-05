<?php
/**
 * BaseController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Slim\Collection;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;

use Cinemasunshine\PortalAdmin\Responder;

/**
 * Base controller
 */
abstract class BaseController extends AbstractController
{
    /**
     * data
     * 
     * 主にviewへ値を渡すために作成。
     * 
     * @var Collection
     */
    protected $data;
    
    /**
     * construct
     * 
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        
        $this->data = new Collection();
    }
    
    /**
     * pre execute
     * 
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @return void
     */
    protected function preExecute($request, $response) : void
    {
    }
    
    /**
     * post execute
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @return void
     */
    protected function postExecute($request, $response) : void
    {
        $this->data->set('user', $this->auth->getUser());
    }
    
    /**
     * get responder
     *
     * @return Responder\AbstractResponder
     */
    protected function getResponder() : Responder\AbstractResponder
    {
        $path = explode('\\', get_class($this));
        $container = str_replace('Controller', '', array_pop($path));
        $responder = Responder::class . '\\' . $container . 'Responder';
        
        return new $responder($this->view);
    }
}