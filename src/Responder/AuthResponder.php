<?php

/**
 * AuthResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Responder;

use Psr\Http\Message\ResponseInterface;
use Slim\Collection;
use Slim\Http\Response;

/**
 * Auth responder
 */
class AuthResponder extends BaseResponder
{
    /**
     * login
     *
     * @param Response $response
     * @param Collection $data
     * @return ResponseInterface
     */
    public function login(Response $response, Collection $data)
    {
        return $this->view->render($response, 'auth/login.html.twig', $data->all());
    }
}
