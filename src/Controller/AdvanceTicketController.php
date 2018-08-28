<?php
/**
 * AdvanceTicketController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Intervention\Image\ImageManager;

use Cinemasunshine\PortalAdmin\Form;
use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * AdvanceTicket controller
 */
class AdvanceTicketController extends BaseController
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
        $form = new Form\AdvanceSaleForm($this->em);
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
        
        $form = new Form\AdvanceSaleForm($this->em);
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
        
        $form = new Form\AdvanceSaleForm($this->em);
        $this->data->set('form', $form);
        
        $values = [
            'id'         => $advanceSale->getId(),
            'theater'    => $advanceSale->getTheater()->getId(),
            'title_id'   => $advanceSale->getTitle()->getId(),
            'title_name' => $advanceSale->getTitle()->getName(),
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
        
        // @todo 有効な前売券だけ取得
        foreach ($advanceSale->getAdvanceTickets() as $advanceTicket) {
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
                'special_gift_image' => $advanceTicket->getSpecialGiftImage(),
            ];
            
            $values['tickets'][] = $ticket;
        }
        
        $this->data->set('values', $values);
    }
}