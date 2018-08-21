<?php
/**
 * NewsResponder.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Responder;

use Slim\Collection;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * News responder
 */
class NewsResponder extends BaseResponder
{
    /**
     * new
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function new(Response $response, Collection $data)
    {
        return $this->view->render($response, 'news/new.html.twig', $data->all());
    }
}
