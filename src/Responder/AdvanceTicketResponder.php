<?php

/**
 * AdvanceTicketResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Responder;

use Psr\Http\Message\ResponseInterface;
use Slim\Collection;
use Slim\Http\Response;

/**
 * AdvanceTicket responder
 */
class AdvanceTicketResponder extends BaseResponder
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
        return $this->view->render($response, 'advance_ticket/list.html.twig', $data->all());
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
        return $this->view->render($response, 'advance_ticket/new.html.twig', $data->all());
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
        return $this->view->render($response, 'advance_ticket/edit.html.twig', $data->all());
    }
}
