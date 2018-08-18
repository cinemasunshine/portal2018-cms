<?php
/**
 * CampaignController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller\API;

use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * Campaign API controller
 */
class CampaignController extends BaseController
{
    /**
     * list action
     * 
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeList($request, $response, $args)
    {
        $name = $request->getParam('name');
        $data = [];
        
        if (!empty($name)) {
            $campaigns = $this->em
                ->getRepository(Entity\Campaign::class)
                ->findForListApi($name);
            
                
            foreach ($campaigns as $campaign) {
                /** @var Entity\Campaign $campaign */
                
                $data[] = [
                    'id'   => $campaign->getId(),
                    'name' => $campaign->getName(),
                    'url'  => $campaign->getUrl(),
                ];
            }
        }
        
        $this->data->set('data', $data);
    }
}