<?php

/**
 * OyakoCinemaResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Responder;

use Psr\Http\Message\ResponseInterface;
use Slim\Collection;
use Slim\Http\Response;

/**
 * OyakoCinema responder
 */
class OyakoCinemaResponder extends BaseResponder
{
    /**
     * list
     *
     * @param Response $response
     * @param Collection $data
     * @return ResponseInterface
     */
    public function list(Response $response, Collection $data)
    {
        return $this->view->render($response, 'oyako_cinema/list.html.twig', $data->all());
    }

    /**
     * new
     *
     * @param Response $response
     * @param Collection $data
     * @return ResponseInterface
     */
    public function new(Response $response, Collection $data)
    {
        return $this->view->render($response, 'oyako_cinema/new.html.twig', $data->all());
    }

    /**
     * edit
     *
     * @param Response $response
     * @param Collection $data
     * @return ResponseInterface
     */
    public function edit(Response $response, Collection $data)
    {
        return $this->view->render($response, 'oyako_cinema/edit.html.twig', $data->all());
    }

    /**
     * setting
     *
     * @param Response $response
     * @param Collection $data
     * @return ResponseInterface
     */
    public function setting(Response $response, Collection $data)
    {
        return $this->view->render($response, 'oyako_cinema/setting/index.html.twig', $data->all());
    }

    /**
     * setting edit
     *
     * @param Response $response
     * @param Collection $data
     * @return ResponseInterface
     */
    public function settingEdit(Response $response, Collection $data)
    {
        return $this->view->render($response, 'oyako_cinema/setting/edit.html.twig', $data->all());
    }
}
