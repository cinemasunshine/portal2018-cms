<?php
/**
 * TheaterMetaController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * TheaterMeta controller
 */
class TheaterMetaController extends BaseController
{
    /**
     * opening hour action
     * 
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeOpeningHour($request, $response, $args)
    {
        // @todo ユーザによってデータを調整
        $metas = $this->em->getRepository(Entity\TheaterMeta::class)->findActive();
        $this->data->set('metas', $metas);
    }
}
