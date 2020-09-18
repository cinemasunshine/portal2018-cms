<?php

/**
 * TitleController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Controller\API;

use App\ORM\Entity;

/**
 * Title API controller
 */
class TitleController extends BaseController
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
        
        if (! empty($name)) {
            $titles = $this->em
                ->getRepository(Entity\Title::class)
                ->findForListApi($name);
            
                
            foreach ($titles as $title) {
                /** @var Entity\Title $title */
                
                $data[] = [
                    'id'            => $title->getId(),
                    'name'          => $title->getName(),
                    'official_site' => $title->getOfficialSite(),
                    'publishing_expected_date' => $title->getPublishingExpectedDate()
                                               ? $title->getPublishingExpectedDate()->format('Y/m/d')
                                               : null,
                ];
            }
        }
        
        $this->data->set('data', $data);
    }
    
    /**
     * autocomplete action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeAutocomplete($request, $response, $args)
    {
        $titles = $this->em
                ->getRepository(Entity\Title::class)
                ->findForAutocomplete($request->getParams());
                
        $data = [];
        
        foreach ($titles as $title) {
            /** @var Entity\Title $title */
            
            $data[] = [
                'name'          => $title->getName(),
                'name_kana'     => $title->getNameKana(),
                'name_original' => $title->getNameOriginal(),
            ];
        }
        
        $this->data->set('data', $data);
    }
}
