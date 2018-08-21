<?php
/**
 * NewsController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Intervention\Image\ImageManager;

use Cinemasunshine\PortalAdmin\Form;
use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * News controller
 */
class NewsController extends BaseController
{
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
        // Zend_Formの都合で$_FILESを使用する
        // $files = $request->getUploadedFiles();
        $files = $_FILES;
        
        $params = array_merge_recursive(
            $request->getParams(),
            $files
        );
        
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
        
        // News of Information
        if ($cleanData['category'] === Form\NewsForm::CATEGORY_NEWS) {
            $entity = new Entity\News();
        } else {
            $entity = new Entity\Information();
        }
        
        $entity->setTitle($title);
        $entity->setImage($file);
        $entity->setStartDt($cleanData['start_dt']);
        $entity->setEndDt($cleanData['end_dt']);
        $entity->setHeadline($cleanData['headline']);
        $entity->setBody($cleanData['body']);
        
        $this->em->persist($entity);
        $this->em->flush();
        exit;
    }
}