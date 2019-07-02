<?php
/**
 * CampaignController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Slim\Exception\NotFoundException;

use Cinemasunshine\PortalAdmin\Exception\ForbiddenException;
use Cinemasunshine\PortalAdmin\Form;
use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * Campaign controller
 */
class CampaignController extends BaseController
{
    use ImageManagerTrait;
    
    /**
     * {@inheritDoc}
     */
    protected function preExecute($request, $response): void
    {
        $user = $this->auth->getUser();
        
        if ($user->isTheater()) {
            throw new ForbiddenException();
        }
        
        parent::preExecute($request, $response);
    }
    
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
        
        $form = new Form\CampaignFindForm($this->em);
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
        // Zend_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);
        
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
        
        // upload storage
        // @todo storageと同期するような仕組みをFileへ
        $options = new \MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions();
        $options->setContentType($image['type']);
        $this->bc->createBlockBlob(
            Entity\File::getBlobContainer(),
            $newName,
            fopen($image['tmp_name'], 'r'),
            $options
        );
        
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
        $campaign->setCreatedUser($this->auth->getUser());
        $campaign->setUpdatedUser($this->auth->getUser());
        
        $this->em->persist($campaign);
        $this->em->flush();
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('キャンペーン情報「%s」を追加しました。', $campaign->getName()),
        ]);
        
        $this->redirect(
            $this->router->pathFor('campaign_edit', [ 'id' => $campaign->getId() ]),
            303
        );
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
            'id'         => $campaign->getId(),
            'title_id'   => null,
            'title_name' => null,
            'name'       => $campaign->getName(),
            'start_dt'   => $campaign->getStartDt()->format('Y/m/d H:i'),
            'end_dt'     => $campaign->getEndDt()->format('Y/m/d H:i'),
            'url'        => $campaign->getUrl(),
        ];
        
        if ($campaign->getTitle()) {
            $values['title_id']   = $campaign->getTitle()->getId();
            $values['title_name'] = $campaign->getTitle()->getName();
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
        
        // Zend_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);
        
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
            
            // upload storage
            // @todo storageと同期するような仕組みをFileへ
            $options = new \MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions();
            $options->setContentType($image['type']);
            $this->bc->createBlockBlob(
                Entity\File::getBlobContainer(),
                $newName,
                fopen($image['tmp_name'], 'r'),
                $options
            );
            
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
        $campaign->setUpdatedUser($this->auth->getUser());
        
        $this->em->persist($campaign);
        $this->em->flush();
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('キャンペーン情報「%s」を編集しました。', $campaign->getName()),
        ]);
        
        $this->redirect(
            $this->router->pathFor('campaign_edit', [ 'id' => $campaign->getId() ]),
            303
        );
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
        
        $this->doDelete($campaign);
        
        $this->logger->info('Delete "Campaign".', [ 'id' => $campaign->getId() ]);
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('キャンペーン情報「%s」を削除しました。', $campaign->getName()),
        ]);
        
        $this->redirect($this->router->pathFor('campaign_list'), 303);
    }
    
    /**
     * do delete
     *
     * @param Entity\Campaign $campaign
     * @return void
     */
    protected function doDelete(Entity\Campaign $campaign)
    {
        $this->em->getConnection()->beginTransaction();
        
        try {
            $campaign->setIsDeleted(true);
            $campaign->setUpdatedUser($this->auth->getUser());
            
            $this->logger->debug('Soft delete "Campaign".', [
                'id' => $campaign->getId() ]);
            
            $this->em->flush();
            
            
            $pageCampaignDeleteCount = $this->em
                ->getRepository(Entity\PageCampaign::class)
                ->deleteByCampaign($campaign);
            
            $this->logger->debug('Delete "PageCampaign"', [
                'count' => $pageCampaignDeleteCount ]);
            
            
            $theaterCampaignDeleteCount = $this->em
                ->getRepository(Entity\TheaterCampaign::class)
                ->deleteByCampaign($campaign);
            
            $this->logger->debug('Delete "TheaterCampaign"', [
                'count' => $theaterCampaignDeleteCount ]);
            
            
            $specialSiteCampaignDeleteCount = $this->em
                ->getRepository(Entity\SpecialSiteCampaign::class)
                ->deleteByCampaign($campaign);
            
            $this->logger->debug('Delete "SpecialSiteCampaign"', [
                'count' => $specialSiteCampaignDeleteCount ]);
            
            
            $this->em->getConnection()->commit();
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }
    
    /**
     * publication action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executePublication($request, $response, $args)
    {
        /** @var Entity\Page[] */
        $pages = $this->em->getRepository(Entity\Page::class)->findActive();
        $this->data->set('pages', $pages);
        
        /** @var Entity\Theater[] */
        $theaters = $this->em->getRepository(Entity\Theater::class)->findActive();
        $this->data->set('theaters', $theaters);
        
        /** @var Entity\SpecialSite[] */
        $specialSites = $this->em->getRepository(Entity\SpecialSite::class)->findActive();
        $this->data->set('specialSites', $specialSites);
    }
    
    /**
     * publication update action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executePublicationUpdate($request, $response, $args)
    {
        $target = $args['target'];
        
        $form = new Form\CampaignPublicationForm($target, $this->em);
        $form->setData($request->getParams());
        
        if (!$form->isValid()) {
            throw new \LogicException('invalid parameters.');
        }
        
        $cleanData = $form->getData();
        $targetEntity = null;
        $basePublication = null;
        
        if ($target === Form\CampaignPublicationForm::TARGET_TEATER) {
            /** @var Entity\Theater $targetEntity */
            $targetEntity = $this->em
                ->getRepository(Entity\Theater::class)
                ->findOneById((int) $cleanData['theater_id']);
            $basePublication = new Entity\TheaterCampaign();
            $basePublication->setTheater($targetEntity);
        } elseif ($target === Form\CampaignPublicationForm::TARGET_PAGE) {
            /** @var Entity\Page $targetEntity */
            $targetEntity = $this->em
                ->getRepository(Entity\Page::class)
                ->findOneById((int) $cleanData['page_id']);
            $basePublication = new Entity\PageCampaign();
            $basePublication->setPage($targetEntity);
        } elseif ($target === Form\CampaignPublicationForm::TARGET_SPESICAL_SITE) {
            /** @var Entity\SpecialSite $targetEntity */
            $targetEntity = $this->em
                ->getRepository(Entity\SpecialSite::class)
                ->findOneById((int) $cleanData['special_site_id']);
            $basePublication = new Entity\SpecialSiteCampaign();
            $basePublication->setSpecialSite($targetEntity);
        }
        
        // いったん削除する
        $targetEntity->getCampaigns()->clear();
        
        foreach ($cleanData['campaigns'] as $campaignData) {
            $publication = clone $basePublication;
            
            $campaign = $this->em
                ->getRepository(Entity\Campaign::class)
                ->findOneById((int) $campaignData['campaign_id']);
            
            if (!$campaign) {
                // @todo formで検証したい
                throw new \LogicException('invalid campaign.');
            }
            
            /** @var Entity\Campaign $campaign */
            
            $publication->setCampaign($campaign);
            $publication->setDisplayOrder((int) $campaignData['display_order']);
            
            $this->em->persist($publication);
        }
        
        $this->em->flush();
        
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('%sの表示順を保存しました。', $targetEntity->getNameJa()),
        ]);
        
        $this->redirect($this->router->pathFor('campaign_publication'), 303);
    }
}
