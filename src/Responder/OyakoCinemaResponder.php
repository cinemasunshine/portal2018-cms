<?php
/**
 * OyakoCinemaResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Responder;

use Slim\Collection;
use Slim\Http\Response;

/**
 * OyakoCinema responder
 */
class OyakoCinemaResponder extends BaseResponder
{
    /**
     * index
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function index(Response $response, Collection $data)
    {
        return $this->view->render($response, 'oyako_cinema/index.html.twig', $data->all());
    }

    /**
     * list
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function list(Response $response, Collection $data)
    {
        return $this->view->render($response, 'oyako_cinema/list.html.twig', $data->all());
    }

    /**
     * new
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function new(Response $response, Collection $data)
    {
        return $this->view->render($response, 'oyako_cinema/new.html.twig', $data->all());
    }

    /**
     * edit
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function edit(Response $response, Collection $data)
    {
        return $this->view->render($response, 'oyako_cinema/edit.html.twig', $data->all());
    }
}
