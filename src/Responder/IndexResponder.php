<?php

/**
 * IndexResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Responder;

use Psr\Http\Message\ResponseInterface;
use Slim\Collection;
use Slim\Http\Response;

/**
 * Index responder
 */
class IndexResponder extends BaseResponder
{
    /**
     * index
     *
     * @param Response $response
     * @param Collection $data
     * @return ResponseInterface
     */
    public function index(Response $response, Collection $data)
    {
        return $this->view->render($response, 'index/index.html.twig', $data->all());
    }
}
