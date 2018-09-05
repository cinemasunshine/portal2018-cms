<?php
/**
 * MainBannerController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Slim\Exception\NotFoundException;

use Intervention\Image\ImageManager;

use Cinemasunshine\PortalAdmin\Form;
use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * MainBanner controller
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
        $page = (int) $request->getParam('p', 1);
        $this->data->set('page', $page);
        
        $form = new Form\MainBannerFindForm();
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
        $pagenater = $this->em->getRepository(Entity\MainBanner::class)->findForList($cleanValues, $page);
        
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
        $this->data->set('form', new Form\MainBannerForm(Form\MainBannerForm::TYPE_NEW));
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
        
        $form = new Form\MainBannerForm(Form\MainBannerForm::TYPE_NEW);
        $form->setData($params);
        
        if (!$form->isValid()) {
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);
            
            return 'new';
        }
        
        $cleanData = $form->getData();
        
        $image = $cleanData['image'];
        
        // rename
        $newName = Entity\File::createName($image['name']);
        
        // resize
        // @todo サイズ調整
        $imageManager = new ImageManager();
        $imageManager
            ->make($image['tmp_name'])
            ->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->save();
        
        // upload storage
        // @todo storageと同期するような仕組みをFileへ
        $options = new \MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions();
        $options->setContentType($image['type']);
        $this->bc->createBlockBlob(
            Entity\File::getBlobContainer(),
            $newName,
            fopen($image['tmp_name'], 'r'),
            $options);
        
        $file = new Entity\File();
        $file->setName($newName);
        $file->setOriginalName($image['name']);
        $file->setMimeType($image['type']);
        $file->setSize((int) $image['size']);
        
        $this->em->persist($file);
        
        $mainBanner = new Entity\MainBanner();
        $mainBanner->setImage($file);
        $mainBanner->setName($cleanData['name']);
        $mainBanner->setLinkType((int) $cleanData['link_type']);
        $mainBanner->setLinkUrl($cleanData['link_url']);
        
        $this->em->persist($mainBanner);
        $this->em->flush();
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('メインバナー「%s」を追加しました。', $mainBanner->getName()),
        ]);
        
        $this->redirect(
            $this->router->pathFor('main_banner_edit', [ 'id' => $mainBanner->getId() ]),
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
        $mainBanner = $this->em->getRepository(Entity\MainBanner::class)->findOneById($args['id']);
        
        if (is_null($mainBanner)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Entity\MainBanner $mainBanner */
        
        $this->data->set('mainBanner', $mainBanner);
        
        $values = [
            'id'        => $mainBanner->getId(),
            'name'      => $mainBanner->getName(),
            'link_type' => $mainBanner->getLinkType(),
            'link_url'  => $mainBanner->getLinkUrl(),
        ];
        
        $this->data->set('values', $values);
        
        $this->data->set('form', new Form\MainBannerForm(Form\MainBannerForm::TYPE_EDIT));
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
        $mainBanner = $this->em->getRepository(Entity\MainBanner::class)->findOneById($args['id']);
        
        if (is_null($mainBanner)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Entity\MainBanner $mainBanner */
        
        // Zend_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);
        
        $form = new Form\MainBannerForm(Form\MainBannerForm::TYPE_EDIT);
        $form->setData($params);
        
        if (!$form->isValid()) {
            $this->data->set('mainBanner', $mainBanner);
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);
            
            return 'edit';
        }
        
        $cleanData = $form->getData();
        
        $image = $cleanData['image'];
        
        if ($image['name']) {
            // rename
            $newName = Entity\File::createName($image['name']);
            
            // resize
            // @todo サイズ調整
            $imageManager = new ImageManager();
            $imageManager
                ->make($image['tmp_name'])
                ->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->save();
            
            // upload storage
            // @todo storageと同期するような仕組みをFileへ
            $options = new \MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions();
            $options->setContentType($image['type']);
            $this->bc->createBlockBlob(
                Entity\File::getBlobContainer(),
                $newName,
                fopen($image['tmp_name'], 'r'),
                $options);
            
            $file = new Entity\File();
            $file->setName($newName);
            $file->setOriginalName($image['name']);
            $file->setMimeType($image['type']);
            $file->setSize((int) $image['size']); // @todo リサイズ後のサイズに修正
            
            $this->em->persist($file);
            
            $oldImage = $mainBanner->getImage();
            $mainBanner->setImage($file);
            
            
            // @todo preUpdateで出来ないか？ hasChangedField()
            $this->em->remove($oldImage);
            
            // @todo postRemoveイベントへ
            $this->bc->deleteBlob(Entity\File::getBlobContainer(), $oldImage->getName());
        }
        
        $mainBanner->setName($cleanData['name']);
        $mainBanner->setLinkType((int) $cleanData['link_type']);
        $mainBanner->setLinkUrl($cleanData['link_url']);
        
        $this->em->flush();
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('メインバナー「%s」を編集しました。', $mainBanner->getName()),
        ]);
        
        $this->redirect(
            $this->router->pathFor('main_banner_edit', [ 'id' => $mainBanner->getId() ]),
            303);
    }
}
