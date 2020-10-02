<?php

/**
 * NewsResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Responder;

use Psr\Http\Message\ResponseInterface;
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
     * @return ResponseInterface
     */
    public function list(Response $response, Collection $data)
    {
        return $this->view->render($response, 'news/list.html.twig', $data->all());
    }

    /**
     * new
     *
     * @param Response   $response
     * @param Collection $data
     * @return ResponseInterface
     */
    public function new(Response $response, Collection $data)
    {
        return $this->view->render($response, 'news/new.html.twig', $data->all());
    }

    /**
     * edit
     *
     * @param Response   $response
     * @param Collection $data
     * @return ResponseInterface
     */
    public function edit(Response $response, Collection $data)
    {
        return $this->view->render($response, 'news/edit.html.twig', $data->all());
    }

    /**
     * publication
     *
     * @param Response   $response
     * @param Collection $data
     * @return ResponseInterface
     */
    public function publication(Response $response, Collection $data)
    {
        return $this->view->render($response, 'news/publication.html.twig', $data->all());
    }
}
