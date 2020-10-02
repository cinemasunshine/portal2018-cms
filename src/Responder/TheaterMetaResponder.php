<?php

/**
 * TheaterMetaResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Responder;

use Psr\Http\Message\ResponseInterface;
use Slim\Collection;
use Slim\Http\Response;

/**
 * TheaterMeta responder
 */
class TheaterMetaResponder extends BaseResponder
{
    /**
     * opening hour
     *
     * @param Response   $response
     * @param Collection $data
     * @return ResponseInterface
     */
    public function openingHour(Response $response, Collection $data)
    {
        return $this->view->render($response, 'theater_meta/opening_hour/list.html.twig', $data->all());
    }

    /**
     * opening hour edit
     *
     * @param Response   $response
     * @param Collection $data
     * @return ResponseInterface
     */
    public function openingHourEdit(Response $response, Collection $data)
    {
        return $this->view->render($response, 'theater_meta/opening_hour/edit.html.twig', $data->all());
    }
}
