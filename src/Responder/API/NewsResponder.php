<?php

/**
 * NewsResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Responder\API;

use Slim\Collection;
use Slim\Http\Response;

/**
 * News responder
 */
class NewsResponder extends BaseResponder
{
    /**
     * list
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function list(Response $response, Collection $data)
    {
        return $response->withJson($data->all());
    }
}
