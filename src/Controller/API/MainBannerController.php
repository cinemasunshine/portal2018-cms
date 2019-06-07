<?php
/**
 * MainBannerController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller\API;

use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * MainBanner API controller
 */
class MainBannerController extends BaseController
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
            $mainBannerList = $this->em
                ->getRepository(Entity\MainBanner::class)
                ->findForListApi($name);
            
                
            foreach ($mainBannerList as $mainBanner) {
                /** @var Entity\MainBanner $mainBanner */
                
                $data[] = [
                    'id'    => $mainBanner->getId(),
                    'name'  => $mainBanner->getName(),
                    'image' => $this->getBlobUrl($mainBanner->getImage()->getName()),
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
            $blob
        );
    }
}
