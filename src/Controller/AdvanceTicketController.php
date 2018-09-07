<?php
/**
 * AdvanceTicketController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Slim\Exception\NotFoundException;

use Cinemasunshine\PortalAdmin\Form;
use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * AdvanceTicket controller
 */
class AdvanceTicketController extends BaseController
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
        
        $form = new Form\AdvanceTicketFindForm();
        $form->setData($request->getParams());
        $cleanValues = [];
        
        if ($form->isValid()) {
            $cleanValues = $form->getData();
            $values = $cleanValues;
        } else {
            $values = $request->getParams();
            $this->data->set('errors', $form->getMessages());
        }
        
        $this->data->set('form', $form);
        $this->data->set('values', $values);
        $this->data->set('params', $cleanValues);
        
        /** @var \Cinemasunshine\PortalAdmin\Pagination\DoctrinePaginator $pagenater */
        $pagenater = $this->em->getRepository(Entity\AdvanceTicket::class)->findForList($cleanValues, $page);
        
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
        $form = new Form\AdvanceSaleForm(Form\AdvanceSaleForm::TYPE_NEW, $this->em);
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
        
        $form = new Form\AdvanceSaleForm(Form\AdvanceSaleForm::TYPE_NEW, $this->em);
        $form->setData($params);
        
        if (!$form->isValid()) {
            $this->data->set('form', $form);
            $this->data->set('values', $params);
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);
            
            return 'new';
        }
        
        $cleanData = $form->getData();
        
        $advanceSale = new Entity\AdvanceSale();
        
        /** @var Entity\Theater $theater */
        $theater = $this->em->getRepository(Entity\Theater::class)->findOneById($cleanData['theater']);
        $advanceSale->setTheater($theater);
        
        /** @var Entity\Title $title */
        $title = $this->em->getRepository(Entity\Title::class)->findOneById($cleanData['title_id']);
        $advanceSale->setTitle($title);
        
        $advanceSale->setPublishingExpectedDate($cleanData['publishing_expected_date']);
        $advanceSale->setPublishingExpectedDateText($cleanData['publishing_expected_date_text']);
        $this->em->persist($advanceSale);
        
        foreach ($cleanData['tickets'] as $ticket) {
            $advanceTicket = new Entity\AdvanceTicket();
            $advanceTicket->setAdvanceSale($advanceSale);
            $advanceTicket->setReleaseDt($ticket['release_dt']);
            $advanceTicket->setReleaseDtText($ticket['release_dt_text']);
            $advanceTicket->setIsSalesEnd($ticket['is_sales_end'] === '1');
            $advanceTicket->setType($ticket['type']);
            $advanceTicket->setPriceText($ticket['price_text']);
            $advanceTicket->setSpecialGift($ticket['special_gift']);
            $advanceTicket->setSpecialGiftStock($ticket['special_gift_stock']);
            
            $image = $ticket['special_gift_image'];
            $file = null;
            
            if ($image['name']) {
                // rename
                $newName = Entity\File::createName($image['name']);
                
                // @todo サイズ調整
                $imageStream = $this->resizeImage($image['tmp_name'], 500);
                
                // upload storage
                // @todo storageと同期するような仕組みをFileへ
                $options = new \MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions();
                $options->setContentType($image['type']);
                $this->bc->createBlockBlob(
                    Entity\File::getBlobContainer(),
                    $newName,
                    $imageStream,
                    $options);
                
                $file = new Entity\File();
                $file->setName($newName);
                $file->setOriginalName($image['name']);
                $file->setMimeType($image['type']);
                $file->setSize((int) $image['size']);
                
                $this->em->persist($file);
            }
            
            $advanceTicket->setSpecialGiftImage($file);
            
            $this->em->persist($advanceTicket);
        }
        
        $this->em->flush();
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => '前売券情報を追加しました。',
        ]);
        
        $this->redirect(
            $this->router->pathFor('advance_ticket_edit', [ 'id' => $advanceSale->getId() ]),
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
        $advanceSale = $this->em->getRepository(Entity\AdvanceSale::class)->findOneById($args['id']);
        
        if (is_null($advanceSale)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Entity\AdvanceSale $advanceSale */
        
        $this->data->set('advanceSale', $advanceSale);
        
        $form = new Form\AdvanceSaleForm(Form\AdvanceSaleForm::TYPE_EDIT, $this->em);
        $this->data->set('form', $form);
        
        $values = [
            'id'         => $advanceSale->getId(),
            'theater'    => $advanceSale->getTheater()->getId(),
            'title_id'   => $advanceSale->getTitle()->getId(),
            'title_name' => $advanceSale->getTitle()->getName(),
            'publishing_expected_date_text' => $advanceSale->getPublishingExpectedDateText(),
            'tickets'    => [],
        ];
        
        $publishingExpectedDate = $advanceSale->getPublishingExpectedDate();
        
        if ($publishingExpectedDate instanceof \DateTime) {
            $values['publishing_expected_date'] = $publishingExpectedDate->format('Y/m/d');
            $values['not_exist_publishing_expected_date'] = null;
        } else {
            $values['publishing_expected_date'] = null;
            $values['not_exist_publishing_expected_date'] = '1';
        }
        
        foreach ($advanceSale->getActiveAdvanceTickets() as $advanceTicket) {
            /** @var Entity\AdvanceTicket $advanceTicket */
            $ticket = [
                'id'                 => $advanceTicket->getId(),
                'release_dt'         => $advanceTicket->getReleaseDt()->format('Y/m/d H:i'),
                'release_dt_text'    => $advanceTicket->getReleaseDtText(),
                'is_sales_end'       => $advanceTicket->getIsSalesEnd() ? '1' : '0',
                'type'               => $advanceTicket->getType(),
                'price_text'         => $advanceTicket->getPriceText(),
                'special_gift'       => $advanceTicket->getSpecialGift(),
                'special_gift_stock' => $advanceTicket->getSpecialGiftStock(),
            ];
            
            $values['tickets'][] = $ticket;
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
        $advanceSale = $this->em->getRepository(Entity\AdvanceSale::class)->findOneById($args['id']);
        
        if (is_null($advanceSale)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Entity\AdvanceSale $advanceSale */
        
        // Zend_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);
        
        $form = new Form\AdvanceSaleForm(Form\AdvanceSaleForm::TYPE_EDIT, $this->em);
        $form->setData($params);
        
        if (!$form->isValid()) {
            $this->data->set('advanceSale', $advanceSale);
            $this->data->set('form', $form);
            $this->data->set('values', $params);
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);
            
            return 'edit';
        }
        
        $cleanData = $form->getData();
        
        /** @var Entity\Theater $theater */
        $theater = $this->em->getRepository(Entity\Theater::class)->findOneById($cleanData['theater']);
        $advanceSale->setTheater($theater);
        
        /** @var Entity\Title $title */
        $title = $this->em->getRepository(Entity\Title::class)->findOneById($cleanData['title_id']);
        $advanceSale->setTitle($title);
        
        $advanceSale->setPublishingExpectedDate($cleanData['publishing_expected_date']);
        $advanceSale->setPublishingExpectedDateText($cleanData['publishing_expected_date_text']);
        
        $advanceTickets = $advanceSale->getActiveAdvanceTickets();
        
        if (is_array($cleanData['delete_tickets'])) {
            // 前売券削除
            
            foreach ($cleanData['delete_tickets'] as $advanceTicketId) {
                // indexByでidをindexにしている
                $advanceTicket = $advanceTickets->get($advanceTicketId);
                
                if (!$advanceTicket) {
                    throw new \RuntimeException(sprintf('advance_ticket(%s) dose not eixist.', $advanceTicketId));
                }
                
                /** @var Entity\AdvanceTicket $advanceTicket */
                
                $advanceTicket->setIsDeleted(true);
            }
        }
        
        foreach ($cleanData['tickets'] as $ticket) {
            if ($ticket['id']) {
                // 前売券編集
                
                // indexByでidをindexにしている
                $advanceTicket = $advanceTickets->get($ticket['id']);
                
                if (!$advanceTicket) {
                    throw new \RuntimeException(sprintf('advance_ticket(%s) dose not eixist.', $ticket['id']));
                }
                
                /** @var Entity\AdvanceTicket $advanceTicket */
                
            } else {
                // 前売券登録
                
                $advanceTicket = new Entity\AdvanceTicket();
                $this->em->persist($advanceTicket);
                
                $advanceTicket->setAdvanceSale($advanceSale);
            }
            
            $advanceTicket->setReleaseDt($ticket['release_dt']);
            $advanceTicket->setReleaseDtText($ticket['release_dt_text']);
            $advanceTicket->setIsSalesEnd($ticket['is_sales_end'] === '1');
            $advanceTicket->setType($ticket['type']);
            $advanceTicket->setPriceText($ticket['price_text']);
            $advanceTicket->setSpecialGift($ticket['special_gift']);
            $advanceTicket->setSpecialGiftStock($ticket['special_gift_stock']);
            
            $image = $ticket['special_gift_image'];
            $isDeleteImage = ($ticket['delete_special_gift_image'] == '1') || $image['name'];
        
            if ($isDeleteImage && $advanceTicket->getSpecialGiftImage()) {
                // @todo preUpdateで出来ないか？ hasChangedField()
                $oldImage = $advanceTicket->getSpecialGiftImage();
                $this->em->remove($oldImage);
                
                // @todo postRemoveイベントへ
                $this->bc->deleteBlob(Entity\File::getBlobContainer(), $oldImage->getName());
                
                $advanceTicket->setSpecialGiftImage(null);
            }
            
            if ($image['name']) {
                // rename
                $newName = Entity\File::createName($image['name']);
                
                // @todo サイズ調整
                $imageStream = $this->resizeImage($image['tmp_name'], 500);
                
                // upload storage
                // @todo storageと同期するような仕組みをFileへ
                $options = new \MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions();
                $options->setContentType($image['type']);
                $this->bc->createBlockBlob(
                    Entity\File::getBlobContainer(),
                    $newName,
                    $imageStream,
                    $options);
                
                $file = new Entity\File();
                $file->setName($newName);
                $file->setOriginalName($image['name']);
                $file->setMimeType($image['type']);
                $file->setSize((int) $image['size']);
                
                $this->em->persist($file);
                
                $advanceTicket->setSpecialGiftImage($file);
            }
        }
        
        $this->em->flush();
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => '前売券情報を編集しました。',
        ]);
        
        $this->redirect(
            $this->router->pathFor('advance_ticket_edit', [ 'id' => $advanceSale->getId() ]),
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
        $advanceTicket = $this->em->getRepository(Entity\AdvanceTicket::class)->findOneById($args['id']);
        
        if (is_null($advanceTicket)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Entity\AdvanceTicket $advanceTicket */
        
        // 関連データの処理はイベントで対応する
        $advanceTicket->setIsDeleted(true);
        
        
        $advanceSale = $advanceTicket->getAdvanceSale();
        
        // 有効なAdvanceTicketの件数確認
        if ($advanceSale->getActiveAdvanceTickets()->count() === 1) {
            
            // この処理で有効なAdvanceTicketが無くなるのでAdvanceSaleも削除する
            $advanceSale->setIsDeleted(true);
        }
        
        $this->em->flush();
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => '前売券情報を削除しました。',
        ]);
        
        return $this->redirect($this->router->pathFor('advance_ticket_list'), 303);
    }
}