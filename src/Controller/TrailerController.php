<?php
/**
 * TrailerController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Slim\Exception\NotFoundException;

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
        $form = new Form\TrailerForm(Form\TrailerForm::TYPE_NEW, $this->em);
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
        
        $form = new Form\TrailerForm(Form\TrailerForm::TYPE_NEW, $this->em);
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
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('予告動画「%s」を追加しました。', $trailer->getName()),
        ]);
        
        $this->redirect(
            $this->router->pathFor('trailer_edit', [ 'id' => $trailer->getId() ]),
            303);
    }
    
    /**
     * edit action
     * 
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeEdit($request, $response, $args)
    {
        $trailer = $this->em->getRepository(Entity\Trailer::class)->findOneById($args['id']);
        
        if (is_null($trailer)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Entity\Trailer $trailer */
        
        $this->data->set('trailer', $trailer);
        
        $values = [
            'id'              => $trailer->getId(),
            'name'            => $trailer->getName(),
            'title_id'        => null,
            'title_name'      => null,
            'youtube'         => $trailer->getYoutube(),
            'banner_link_url' => $trailer->getBannerLinkUrl(),
            'page'            => [],
            'theater'         => [],
        ];
        
        if ($trailer->getTitle()) {
            $values['title_id']   = $trailer->getTitle()->getId();
            $values['title_name'] = $trailer->getTitle()->getName();
        }
        
        foreach ($trailer->getPageTrailers() as $pageTrailer) {
            /** @var Entity\PageTrailer $pageTrailer */
            $values['page'][] = $pageTrailer->getPage()->getId();
        }
        
        foreach ($trailer->getTheaterTrailers() as $theaterTrailer) {
            /** @var Entity\TheaterTrailer $theaterTrailer */
            $values['theater'][] = $theaterTrailer->getTheater()->getId();
        }
        
        $this->data->set('values', $values);
        
        $form = new Form\TrailerForm(Form\TrailerForm::TYPE_EDIT, $this->em);
        $this->data->set('form', $form);
    }
    
    /**
     * update action
     * 
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeUpdate($request, $response, $args)
    {
        $trailer = $this->em->getRepository(Entity\Trailer::class)->findOneById($args['id']);
        
        if (is_null($trailer)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Entity\Trailer $trailer */
        
        // Zend_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);
        
        $form = new Form\TrailerForm(Form\TrailerForm::TYPE_EDIT, $this->em);
        $form->setData($params);
        
        if (!$form->isValid()) {
            $this->data->set('trailer', $trailer);
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);
            
            return 'edit';
        }
        
        $cleanData = $form->getData();
        
        $bannerImage = $cleanData['banner_image'];
        
        if ($bannerImage['name']) {
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
            
            $trailer->setBannerImage($file);
        }
        
        $title = null;
        
        if ($cleanData['title_id']) {
            $title =  $this->em->getRepository(Entity\Title::class)->findOneById($cleanData['title_id']);
        }
        
        $trailer->setTitle($title);
        $trailer->setName($cleanData['name']);
        $trailer->setYoutube($cleanData['youtube']);
        $trailer->setBannerLinkUrl($cleanData['banner_link_url']);
        
        $trailer->getPageTrailers()->clear();
        
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
        
        $trailer->getTheaterTrailers()->clear();
        
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
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('予告動画「%s」を追加しました。', $trailer->getName()),
        ]);
        
        $this->redirect(
            $this->router->pathFor('trailer_edit', [ 'id' => $trailer->getId() ]),
            303);
    }
}
