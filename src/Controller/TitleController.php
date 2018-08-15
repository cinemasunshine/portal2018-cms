<?php
/**
 * TitleController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Slim\Exception\NotFoundException;

use Intervention\Image\ImageManager;

use Cinemasunshine\PortalAdmin\Form;
use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * Title controller
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
        $page = (int) $request->getParam('p', 1);
        $this->data->set('page', $page);
        
        $form = new Form\TitleFindForm();
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
        $pagenater = $this->em->getRepository(Entity\Title::class)->findForList($cleanValues, $page);
        
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
        $form = new Form\TitleForm();
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
        
        $form = new Form\TitleForm();
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
        $file = null;
        
        if ($image['name']) {
            // rename
            // @todo ファイル名生成をFileへ
            $info = pathinfo($image['name']);
            $newName = md5(uniqid('', true)) . '.' . $info['extension'];
            
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
        }
        
        $title = new Entity\Title();
        $title->setImage($file);
        $title->setName($cleanData['name']);
        $title->setNameKana($cleanData['name_kana']);
        $title->setNameEn($cleanData['name_en']);
        $title->setCredit($cleanData['credit']);
        $title->setCatchcopy($cleanData['catchcopy']);
        $title->setIntroduction($cleanData['introduction']);
        $title->setDirector($cleanData['director']);
        $title->setCast($cleanData['cast']);
        $title->setPublishingExpectedDate($cleanData['publishing_expected_date']);
        $title->setWebsite($cleanData['website']);
        $title->setRating((int) $cleanData['rating']);
        $title->setUniversal($cleanData['universal'] ?? []);
        $title->setIsDeleted(false);
        
        $this->em->persist($title);
        $this->em->flush();
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('作品「%s」を追加しました。', $title->getName()),
        ]);
        
        $this->redirect(
            $this->router->pathFor('title_edit', [ 'id' => $title->getId() ]),
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
        $title = $this->em->getRepository(Entity\Title::class)->findOneById($args['id']);
        
        if (is_null($title)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Entity\Title $title */
        
        $this->data->set('title', $title);
        
        $form = new Form\TitleForm();
        $this->data->set('form', $form);
        
        $values = [
            'id'           => $title->getId(),
            'name'         => $title->getName(),
            'name_kana'    => $title->getNameKana(),
            'name_en'      => $title->getNameEn(),
            'credit'       => $title->getCredit(),
            'catchcopy'    => $title->getCatchcopy(),
            'introduction' => $title->getIntroduction(),
            'director'     => $title->getDirector(),
            'cast'         => $title->getCast(),
            'website'      => $title->getWebsite(),
            'rating'       => $title->getRating(),
            'universal'    => $title->getUniversal(),
        ];
        
        $publishingExpectedDate = $title->getPublishingExpectedDate();
        
        if ($publishingExpectedDate instanceof \DateTime) {
            $values['publishing_expected_date'] = $publishingExpectedDate->format('Y/m/d');
            $values['not_exist_publishing_expected_date'] = null;
        } else {
            $values['publishing_expected_date'] = null;
            $values['not_exist_publishing_expected_date'] = '1';
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
        $title = $this->em->getRepository(Entity\Title::class)->findOneById($args['id']);
        
        if (is_null($title)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Entity\Title $title */
        
        // Zend_Formの都合で$_FILESを使用する
        // $files = $request->getUploadedFiles();
        $files = $_FILES;
        
        $params = array_merge_recursive(
            $request->getParams(),
            $files
        );
        
        $form = new Form\TitleForm();
        $form->setData($params);
        
        if (!$form->isValid()) {
            $this->data->set('title', $title);
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);
            
            return 'edit';
        }
        
        $cleanData = $form->getData();
        
        $image = $cleanData['image'];
        $isDeleteImage = $cleanData['delete_image'] || $image['name'];
        $oldImage = $title->getImage();
        $file = null;
        
        if ($isDeleteImage && $oldImage) {
            $this->em->remove($oldImage);
            
            // @todo postRemoveイベントへ
            $this->bc->deleteBlob(Entity\File::getBlobContainer(), $oldImage->getName());
        }
        
        if ($image['name']) {
            // rename
            // @todo ファイル名生成をFileへ
            $info = pathinfo($image['name']);
            $newName = md5(uniqid('', true)) . '.' . $info['extension'];
            
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
        }
        
        $title->setImage($file);
        $title->setName($cleanData['name']);
        $title->setNameKana($cleanData['name_kana']);
        $title->setNameEn($cleanData['name_en']);
        $title->setCredit($cleanData['credit']);
        $title->setCatchcopy($cleanData['catchcopy']);
        $title->setIntroduction($cleanData['introduction']);
        $title->setDirector($cleanData['director']);
        $title->setCast($cleanData['cast']);
        $title->setPublishingExpectedDate($cleanData['publishing_expected_date']);
        $title->setWebsite($cleanData['website']);
        $title->setRating((int) $cleanData['rating']);
        $title->setUniversal($cleanData['universal'] ?? []);
        $title->setIsDeleted(false);
        
        $this->em->persist($title);
        $this->em->flush();
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('作品「%s」を編集しました。', $title->getName()),
        ]);
        
        $this->redirect(
            $this->router->pathFor('title_edit', [ 'id' => $title->getId() ]),
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
        $title = $this->em->getRepository(Entity\Title::class)->findOneById($args['id']);
        
        if (is_null($title)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Entity\Title $title */
        
        $title->setIsDeleted(true);
        
        // 関連データの処理はイベントで対応する
        
        $this->em->persist($title);
        $this->em->flush();
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('作品「%s」を削除しました。', $title->getName()),
        ]);
        
        return $this->redirect($this->router->pathFor('title_list'), 303);
    }
}