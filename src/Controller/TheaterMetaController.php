<?php
/**
 * TheaterMetaController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Slim\Exception\NotFoundException;

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
    
    /**
     * opening hour edit action
     * 
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeOpeningHourEdit($request, $response, $args)
    {
        $theater = $this->em->getRepository(Entity\Theater::class)->findOneById($args['id']);
        
        if (is_null($theater)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Entity\Theater $theater */
        
        $this->data->set('theater', $theater);
    }
}
