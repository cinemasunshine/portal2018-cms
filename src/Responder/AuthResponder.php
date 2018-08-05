<?php
/**
 * AuthResponder.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Responder;

use Slim\Collection;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * Auth responder
 */
class AuthResponder extends BaseResponder
{
    /**
     * login
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function login(Response $response, Collection $data)
    {
        return $this->view->render($response, 'auth/login.html.twig', $data->all());
    }
}
