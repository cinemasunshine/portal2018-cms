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
                    'id'    => $campaign->getId(),
                    'name'  => $campaign->getName(),
                    'image' => $this->getBlobUrl($campaign->getImage()->getName()),
                    'url'   => $campaign->getUrl(),
                ];
            }
        }
        
        $this->data->set('data', $data);
    }
    
    /**
     * return Blob URL
     * 
     * @todo Eitity\Fileから取得できるようにしたい
     *
     * @param string $blob blob name
     * @return string
     */
    protected function getBlobUrl(string $blob)
    {
        $settings = $this->settings['storage'];
        $protocol = $settings['secure'] ? 'https' : 'http';
        $container = Entity\File::getBlobContainer();
        
        return sprintf(
            '%s://%s.blob.core.windows.net/%s/%s',
            $protocol,
            $settings['account']['name'],
            $container,
            $blob);
    }
}