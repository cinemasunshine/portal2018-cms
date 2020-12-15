<?php

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Index controller
 */
class IndexController extends BaseController
{
    /**
     * index action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeIndex(Request $request, Response $response, array $args)
    {
        return $this->render($response, 'index/index.html.twig');
    }
}
