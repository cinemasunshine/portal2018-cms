<?php

/**
 * BaseController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller\API;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Cinemasunshine\PortalAdmin\Controller\AbstractController;
use Cinemasunshine\PortalAdmin\Responder\AbstractResponder;
use Cinemasunshine\PortalAdmin\Responder\API\ResponderFactory;

/**
 * Base controller
 */
abstract class BaseController extends AbstractController
{
    /**
     * pre execute
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @return void
     */
    protected function preExecute($request, $response): void
    {
    }

    /**
     * post execute
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @return void
     */
    protected function postExecute($request, $response): void
    {
    }

    /**
     * get responder
     *
     * @return AbstractResponder
     */
    protected function getResponder(): AbstractResponder
    {
        $path = explode('\\', get_class($this));
        $name = str_replace('Controller', '', array_pop($path));

        return ResponderFactory::factory($name);
    }
}
