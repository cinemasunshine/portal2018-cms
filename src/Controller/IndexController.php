<?php

declare(strict_types=1);

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
     * @param array<string, mixed> $args
     */
    public function executeIndex(Request $request, Response $response, array $args): Response
    {
        return $this->render($response, 'index/index.html.twig');
    }
}
