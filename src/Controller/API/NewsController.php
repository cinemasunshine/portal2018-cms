<?php
/**
 * NewsController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller\API;

use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * News API controller
 */
class NewsController extends BaseController
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
        $headline = $request->getParam('headline');
        $data = [];
        
        if (!empty($headline)) {
            $newsList = $this->em
                ->getRepository(Entity\News::class)
                ->findForListApi($headline);
            
                
            foreach ($newsList as $news) {
                /** @var Entity\News $news */
                
                $data[] = [
                    'id'             => $news->getId(),
                    'headline'       => $news->getHeadline(),
                    'image'          => $this->getBlobUrl($news->getImage()->getName()),
                    'category_label' => $news->getCategoryLable(),
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