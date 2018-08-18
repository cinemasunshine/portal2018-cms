<?php
/**
 * CampaignController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Slim\Exception\NotFoundException;

use Intervention\Image\ImageManager;

use Cinemasunshine\PortalAdmin\Form;
use Cinemasunshine\PortalAdmin\ORM\Entity;
use Cinemasunshine\PortalAdmin\ORM\Entity\Title;

/**
 * Campaign controller
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
        $page = (int) $request->getParam('p', 1);
        $this->data->set('page', $page);
        
        $cleanValues = [];
        $this->data->set('params', $cleanValues);
        
        /** @var \Cinemasunshine\PortalAdmin\Pagination\DoctrinePaginator $pagenater */
        $pagenater = $this->em->getRepository(Entity\Campaign::class)->findForList($cleanValues, $page);
        
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
        // Zend_Formの都合で$_FILESを使用する
        // $files = $request->getUploadedFiles();
        $files = $_FILES;
        
        $params = array_merge_recursive(
            $request->getParams(),
            $files
        );
        
        $form = new Form\CampaignForm(Form\CampaignForm::TYPE_NEW);
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
        
        $title = null;
        
        if ($cleanData['title_id']) {
            $title =  $this->em->getRepository(Entity\Title::class)->findOneById($cleanData['title_id']);
        }
        
        $campaign = new Entity\Campaign();
        $campaign->setTitle($title);
        $campaign->setImage($file);
        $campaign->setName($cleanData['name']);
        $campaign->setStartDt($cleanData['start_dt']);
        $campaign->setEndDt($cleanData['end_dt']);
        $campaign->setUrl($cleanData['url']);
        
        $this->em->persist($campaign);
        $this->em->flush();
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('キャンペーン情報「%s」を追加しました。', $campaign->getName()),
        ]);
        
        $this->redirect(
            $this->router->pathFor('campaign_edit', [ 'id' => $campaign->getId() ]),
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
        $campaign = $this->em->getRepository(Entity\Campaign::class)->findOneById($args['id']);
        
        if (is_null($campaign)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Entity\Campaign $campaign */
        
        $this->data->set('campaign', $campaign);
        
        $values = [
            'id'       => $campaign->getId(),
            'title_id' => null,
            'name'     => $campaign->getName(),
            'start_dt' => $campaign->getStartDt()->format('Y/m/d H:i'),
            'end_dt'   => $campaign->getEndDt()->format('Y/m/d H:i'),
            'url'      => $campaign->getUrl(),
        ];
        
        if ($campaign->getTitle()) {
            $values['title_id'] = $campaign->getTitle()->getId();
        }
        
        $this->data->set('values', $values);
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
        $campaign = $this->em->getRepository(Entity\Campaign::class)->findOneById($args['id']);
        
        if (is_null($campaign)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Entity\Campaign $campaign */
        
        // Zend_Formの都合で$_FILESを使用する
        // $files = $request->getUploadedFiles();
        $files = $_FILES;
        
        $params = array_merge_recursive(
            $request->getParams(),
            $files
        );
        
        $form = new Form\CampaignForm(Form\CampaignForm::TYPE_EDIT);
        $form->setData($params);
        
        if (!$form->isValid()) {
            $this->data->set('campaign', $campaign);
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
            $file->setSize((int) $image['size']);
            
            $this->em->persist($file);
            
            $oldImage = $campaign->getImage();
            $campaign->setImage($file);
            
            
            // @todo preUpdateで出来ないか？ hasChangedField()
            $this->em->remove($oldImage);
            
            // @todo postRemoveイベントへ
            $this->bc->deleteBlob(Entity\File::getBlobContainer(), $oldImage->getName());
        }
        
        
        $title = null;
        
        if ($cleanData['title_id']) {
            $title =  $this->em->getRepository(Entity\Title::class)->findOneById($cleanData['title_id']);
        }
        
        $campaign->setTitle($title);
        
        $campaign->setName($cleanData['name']);
        $campaign->setStartDt($cleanData['start_dt']);
        $campaign->setEndDt($cleanData['end_dt']);
        $campaign->setUrl($cleanData['url']);
        
        $this->em->persist($campaign);
        $this->em->flush();
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('キャンペーン情報「%s」を編集しました。', $campaign->getName()),
        ]);
        
        $this->redirect(
            $this->router->pathFor('campaign_edit', [ 'id' => $campaign->getId() ]),
            303);
    }
    
    /**
     * delete action
     * 
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeDelete($request, $response, $args)
    {
        $campaign = $this->em->getRepository(Entity\Campaign::class)->findOneById($args['id']);
        
        if (is_null($campaign)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Entity\Campaign $campaign */
        
        $campaign->setIsDeleted(true);
        
        // 関連データの処理はイベントで対応する
        
        $this->em->persist($campaign);
        $this->em->flush();
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('キャンペーン情報「%s」を削除しました。', $campaign->getName()),
        ]);
        
        return $this->redirect($this->router->pathFor('campaign_list'), 303);
    }
}
