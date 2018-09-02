<?php
/**
 * TitleResponder.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Responder\API;

use Slim\Collection;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * Title responder
 */
class TitleResponder extends BaseResponder
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
    
    /**
     * master
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function master(Response $response, Collection $data)
    {
        return $response->withJson($data->all());
    }
}