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
}
