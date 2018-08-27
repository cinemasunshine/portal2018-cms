<?php
/**
 * NewsController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Slim\Exception\NotFoundException;

use Intervention\Image\ImageManager;

use Cinemasunshine\PortalAdmin\Form;
use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * News controller
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
        $page = (int) $request->getParam('p', 1);
        
        $cleanValues = [];
        $this->data->set('params', $cleanValues);
        
        /** @var \Cinemasunshine\PortalAdmin\Pagination\DoctrinePaginator $pagenater */
        $pagenater = $this->em->getRepository(Entity\News::class)->findForList($cleanValues, $page);
        
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
        $form = new Form\NewsForm(Form\NewsForm::TYPE_NEW);
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
        
        $form = new Form\NewsForm(Form\NewsForm::TYPE_NEW);
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
        
        // title
        $title = null;
        
        if ($cleanData['title_id']) {
            $title =  $this->em->getRepository(Entity\Title::class)->findOneById($cleanData['title_id']);
        }
        
        $news = new Entity\News();
        
        $news->setTitle($title);
        $news->setImage($file);
        $news->setCategory((int) $cleanData['category']);
        $news->setStartDt($cleanData['start_dt']);
        $news->setEndDt($cleanData['end_dt']);
        $news->setHeadline($cleanData['headline']);
        $news->setBody($cleanData['body']);
        
        $this->em->persist($news);
        $this->em->flush();
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('NEWS・インフォメーション「%s」を追加しました。', $news->getHeadline()),
        ]);
        
        $this->redirect(
            $this->router->pathFor('news_edit', [ 'id' => $news->getId() ]),
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
        $news = $this->em->getRepository(Entity\News::class)->findOneById($args['id']);
        
        if (is_null($news)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Entity\News $news */
        
        $this->data->set('news', $news);
        
        $values = [
            'id'         => $news->getId(),
            'title_id'   => null,
            'title_name' => null,
            'category'   => $news->getCategory(),
            'start_dt'   => $news->getStartDt()->format('Y/m/d H:i'),
            'end_dt'     => $news->getEndDt()->format('Y/m/d H:i'),
            'headline'   => $news->getHeadline(),
            'body'       => $news->getBody(),
        ];
        
        if ($news->getTitle()) {
            $values['title_id']   = $news->getTitle()->getId();
            $values['title_name'] = $news->getTitle()->getName();
        }
        
        $this->data->set('values', $values);
        
        $form = new Form\NewsForm(Form\NewsForm::TYPE_EDIT);
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
        $news = $this->em->getRepository(Entity\News::class)->findOneById($args['id']);
        
        if (is_null($news)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Entity\News $news */
        
        // Zend_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);
        
        $form = new Form\NewsForm(Form\NewsForm::TYPE_EDIT);
        $form->setData($params);
        
        if (!$form->isValid()) {
            $this->data->set('news', $news);
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
            
            $oldImage = $news->getImage();
            $news->setImage($file);
            
            
            // @todo preUpdateで出来ないか？ hasChangedField()
            $this->em->remove($oldImage);
            
            // @todo postRemoveイベントへ
            $this->bc->deleteBlob(Entity\File::getBlobContainer(), $oldImage->getName());
        }
        
        
        $title = null;
        
        if ($cleanData['title_id']) {
            $title =  $this->em->getRepository(Entity\Title::class)->findOneById($cleanData['title_id']);
        }
        
        $news->setTitle($title);
        
        $news->setCategory((int) $cleanData['category']);
        $news->setStartDt($cleanData['start_dt']);
        $news->setEndDt($cleanData['end_dt']);
        $news->setHeadline($cleanData['headline']);
        $news->setBody($cleanData['body']);
        
        $this->em->persist($news);
        $this->em->flush();
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('NEWS・インフォメーション「%s」を追加しました。', $news->getHeadline()),
        ]);
        
        $this->redirect(
            $this->router->pathFor('news_edit', [ 'id' => $news->getId() ]),
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
        $news = $this->em->getRepository(Entity\News::class)->findOneById($args['id']);
        
        if (is_null($news)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Entity\News $news */
        
        $news->setIsDeleted(true);
        
        // 関連データの処理はイベントで対応する
        
        $this->em->persist($news);
        $this->em->flush();
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('NEWS・インフォメーション「%s」を削除しました。', $news->getHeadline()),
        ]);
        
        return $this->redirect($this->router->pathFor('news_list'), 303);
    }
}