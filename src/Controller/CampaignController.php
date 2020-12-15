<?php

namespace App\Controller;

use App\Exception\ForbiddenException;
use App\Form;
use App\ORM\Entity;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Campaign controller
 */
class CampaignController extends BaseController
{
    protected function preExecute(Request $request, Response $response): void
    {
        $this->authorization();

        parent::preExecute($request, $response);
    }

    /**
     * @return void
     *
     * @throws ForbiddenException
     */
    protected function authorization()
    {
        $user = $this->auth->getUser();

        if ($user->isTheater()) {
            throw new ForbiddenException();
        }
    }

    /**
     * list action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeList(Request $request, Response $response, array $args)
    {
        $page = (int) $request->getParam('p', 1);

        $form = new Form\CampaignFindForm($this->em);
        $form->setData($request->getParams());

        $cleanValues = [];
        $errors      = [];

        if ($form->isValid()) {
            $cleanValues = $form->getData();
            $values      = $cleanValues;
        } else {
            $values = $request->getParams();
            $errors = $form->getMessages();
        }

        /** @var \App\Pagination\DoctrinePaginator $pagenater */
        $pagenater = $this->em->getRepository(Entity\Campaign::class)->findForList($cleanValues, $page);

        return $this->render($response, 'campaign/list.html.twig', [
            'page' => $page,
            'form' => $form,
            'values' => $values,
            'errors' => $errors,
            'params' => $cleanValues,
            'pagenater' => $pagenater,
        ]);
    }

    /**
     * new action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeNew(Request $request, Response $response, array $args)
    {
        return $this->renderNew($response);
    }

    /**
     * @param Response $response
     * @param array    $data
     * @return Response
     */
    protected function renderNew(Response $response, array $data = [])
    {
        return $this->render($response, 'campaign/new.html.twig', $data);
    }

    /**
     * create action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeCreate(Request $request, Response $response, $args)
    {
        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = new Form\CampaignForm(Form\CampaignForm::TYPE_NEW);
        $form->setData($params);

        if (! $form->isValid()) {
            return $this->renderNew($response, [
                'form' => $form,
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
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

        $this->logger->info('Created Campaign "{id}"', [
            'id' => $campaign->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('キャンペーン情報「%s」を追加しました。', $campaign->getName()),
        ]);

        $this->redirect(
            $this->router->pathFor('campaign_edit', ['id' => $campaign->getId()]),
            303
        );
    }

    /**
     * edit action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeEdit(Request $request, Response $response, array $args)
    {
        /** @var Entity\Campaign|null $campaign */
        $campaign = $this->em->getRepository(Entity\Campaign::class)->findOneById($args['id']);

        if (is_null($campaign)) {
            throw new NotFoundException($request, $response);
        }

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

        return $this->renderEdit($response, [
            'campaign' => $campaign,
            'values' => $values,
        ]);
    }

    /**
     * @param Response $response
     * @param array    $data
     * @return Response
     */
    protected function renderEdit(Response $response, array $data = [])
    {
        return $this->render($response, 'campaign/edit.html.twig', $data);
    }

    /**
     * update action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeUpdate(Request $request, Response $response, array $args)
    {
        /** @var Entity\Campaign|null $campaign */
        $campaign = $this->em->getRepository(Entity\Campaign::class)->findOneById($args['id']);

        if (is_null($campaign)) {
            throw new NotFoundException($request, $response);
        }

        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = new Form\CampaignForm(Form\CampaignForm::TYPE_EDIT);
        $form->setData($params);

        if (! $form->isValid()) {
            return $this->renderEdit($response, [
                'campaign' => $campaign,
                'form' => $form,
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
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

        $this->logger->info('Updated Campaign "{id}"', [
            'id' => $campaign->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('キャンペーン情報「%s」を編集しました。', $campaign->getName()),
        ]);

        $this->redirect(
            $this->router->pathFor('campaign_edit', ['id' => $campaign->getId()]),
            303
        );
    }

    /**
     * delete action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return void
     */
    public function executeDelete(Request $request, Response $response, array $args)
    {
        /** @var Entity\Campaign|null $campaign */
        $campaign = $this->em->getRepository(Entity\Campaign::class)->findOneById($args['id']);

        if (is_null($campaign)) {
            throw new NotFoundException($request, $response);
        }

        $this->doDelete($campaign);

        $this->logger->info('Deleted Campaign "{id}"', [
            'id' => $campaign->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

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
                'id' => $campaign->getId(),
            ]);

            $this->em->flush();

            $pageCampaignDeleteCount = $this->em
                ->getRepository(Entity\PageCampaign::class)
                ->deleteByCampaign($campaign);

            $this->logger->debug('Delete "PageCampaign"', ['count' => $pageCampaignDeleteCount]);

            $theaterCampaignDeleteCount = $this->em
                ->getRepository(Entity\TheaterCampaign::class)
                ->deleteByCampaign($campaign);

            $this->logger->debug('Delete "TheaterCampaign"', ['count' => $theaterCampaignDeleteCount]);

            $specialSiteCampaignDeleteCount = $this->em
                ->getRepository(Entity\SpecialSiteCampaign::class)
                ->deleteByCampaign($campaign);

            $this->logger->debug('Delete "SpecialSiteCampaign"', ['count' => $specialSiteCampaignDeleteCount]);

            $this->em->getConnection()->commit();
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }

    /**
     * publication action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executePublication(Request $request, Response $response, array $args)
    {
        /** @var Entity\Page[] $pages */
        $pages = $this->em->getRepository(Entity\Page::class)->findActive();

        /** @var Entity\Theater[] $theaters */
        $theaters = $this->em->getRepository(Entity\Theater::class)->findActive();

        /** @var Entity\SpecialSite[] $specialSites */
        $specialSites = $this->em->getRepository(Entity\SpecialSite::class)->findActive();

        return $this->render($response, 'campaign/publication.html.twig', [
            'pages' => $pages,
            'theaters' => $theaters,
            'specialSites' => $specialSites,
        ]);
    }

    /**
     * publication update action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return void
     */
    public function executePublicationUpdate(Request $request, Response $response, $args)
    {
        $target = $args['target'];

        $form = new Form\CampaignPublicationForm($target, $this->em);
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            throw new \LogicException('invalid parameters.');
        }

        $cleanData       = $form->getData();
        $targetEntity    = null;
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

            /** @var Entity\Campaign|null $campaign */
            $campaign = $this->em
                ->getRepository(Entity\Campaign::class)
                ->findOneById((int) $campaignData['campaign_id']);

            if (! $campaign) {
                // @todo formで検証したい
                throw new \LogicException('invalid campaign.');
            }

            $publication->setCampaign($campaign);
            $publication->setDisplayOrder((int) $campaignData['display_order']);

            $this->em->persist($publication);
        }

        $this->em->flush();

        $this->logger->info('Updated Campaign display order ', [
            'target' => $target,
            'target_id' => $targetEntity->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('%sの表示順を保存しました。', $targetEntity->getNameJa()),
        ]);

        $this->redirect($this->router->pathFor('campaign_publication'), 303);
    }
}
