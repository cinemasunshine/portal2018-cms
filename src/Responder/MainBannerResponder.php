<?php
/**
 * MainBannerResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Responder;

use Slim\Collection;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * MainBanner responder
 */
class MainBannerResponder extends BaseResponder
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
        return $this->view->render($response, 'main_banner/list.html.twig', $data->all());
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
        return $this->view->render($response, 'main_banner/new.html.twig', $data->all());
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
        return $this->view->render($response, 'main_banner/edit.html.twig', $data->all());
    }
    
    /**
     * publication
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function publication(Response $response, Collection $data)
    {
        return $this->view->render($response, 'main_banner/publication.html.twig', $data->all());
    }
}
