<?php
/**
 * TrailerController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Cinemasunshine\PortalAdmin\Form;
use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * Trailer controller
 */
class TrailerController extends BaseController
{
    use ImageManagerTrait;
    
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
        $page = (int) $request->getParam('p', 1);
        $this->data->set('page', $page);
        
        $form = new Form\TrailerFindForm($this->em);
        $this->data->set('form', $form);
        
        $form->setData($request->getParams());
        $cleanValues = [];
        
        if ($form->isValid()) {
            $cleanValues = $form->getData();
            $values = $cleanValues;
        } else {
            $values = $request->getParams();
            $this->data->set('errors', $form->getMessages());
        }
        
        $this->data->set('values', $values);
        $this->data->set('params', $cleanValues);
        
        /** @var \Cinemasunshine\PortalAdmin\Pagination\DoctrinePaginator $pagenater */
        $pagenater = $this->em->getRepository(Entity\Trailer::class)->findForList($cleanValues, $page);
        
        $this->data->set('pagenater', $pagenater);
    }
    
    /**
     * new action
     * 
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeNew($request, $response, $args)
    {
         $form = new Form\TrailerForm($this->em);
         $this->data->set('form', $form);
    }
    
    /**
     * create action
     * 
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeCreate($request, $response, $args)
    {
        // Zend_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);
        
        $form = new Form\TrailerForm($this->em);
        $form->setData($params);
        
        if (!$form->isValid()) {
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);
            
            return 'new';
        }
        
        $cleanData = $form->getData();
        
        $bannerImage = $cleanData['banner_image'];
        
        // rename
        $newName = Entity\File::createName($bannerImage['name']);
        
        // @todo サイズ調整
        $imageStream = $this->resizeImage($bannerImage['tmp_name'], 500);
        
        // upload storage
        // @todo storageと同期するような仕組みをFileへ
        $options = new \MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions();
        $options->setContentType($bannerImage['type']);
        $this->bc->createBlockBlob(
            Entity\File::getBlobContainer(),
            $newName,
            $imageStream,
            $options);
        
        $file = new Entity\File();
        $file->setName($newName);
        $file->setOriginalName($bannerImage['name']);
        $file->setMimeType($bannerImage['type']);
        $file->setSize($imageStream->getSize());
        
        $this->em->persist($file);
        
        $title = null;
        
        if ($cleanData['title_id']) {
            $title =  $this->em->getRepository(Entity\Title::class)->findOneById($cleanData['title_id']);
        }
        
        $trailer = new Entity\Trailer();
        $this->em->persist($trailer);
        
        $trailer->setTitle($title);
        $trailer->setName($cleanData['name']);
        $trailer->setYoutube($cleanData['youtube']);
        $trailer->setBannerImage($file);
        $trailer->setBannerLinkUrl($cleanData['banner_link_url']);
        
        if ($cleanData['page']) {
            $pages = $this->em->getRepository(Entity\Page::class)->findByIds($cleanData['page']);
            
            foreach ($pages as $page) {
                /** @var Entity\Page $page */
                
                $pageTrailer = new Entity\PageTrailer();
                $this->em->persist($pageTrailer);
                
                $pageTrailer->setPage($page);
                $pageTrailer->setTrailer($trailer);
            }
        }
        
        if ($cleanData['theater']) {
            $theaters = $this->em->getRepository(Entity\Theater::class)->findByIds($cleanData['theater']);
            
            foreach ($theaters as $theater) {
                /** @var Entity\Theater $theater */
                
                $theaterTrailer = new Entity\TheaterTrailer();
                $this->em->persist($theaterTrailer);
                
                $theaterTrailer->setTheater($theater);
                $theaterTrailer->setTrailer($trailer);
            }
        }
        
        $this->em->flush();
        
        // @todo 編集ページへリダイレクト
        exit;
    }
}
