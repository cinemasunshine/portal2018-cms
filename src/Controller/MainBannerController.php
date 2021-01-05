<?php

namespace App\Controller;

use App\Exception\ForbiddenException;
use App\Form;
use App\ORM\Entity;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * MainBanner controller
 */
class MainBannerController extends BaseController
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

        $form = new Form\MainBannerFindForm();
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
        $pagenater = $this->em->getRepository(Entity\MainBanner::class)->findForList($cleanValues, $page);

        return $this->render($response, 'main_banner/list.html.twig', [
            'page' => $page,
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
        return $this->renderNew($response, [
            'form' => new Form\MainBannerForm(Form\MainBannerForm::TYPE_NEW),
        ]);
    }

    /**
     * @param Response $response
     * @param array    $data
     * @return Response
     */
    protected function renderNew(Response $response, array $data = [])
    {
        return $this->render($response, 'main_banner/new.html.twig', $data);
    }

    /**
     * create action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeCreate(Request $request, Response $response, array $args)
    {
        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = new Form\MainBannerForm(Form\MainBannerForm::TYPE_NEW);
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

        $mainBanner = new Entity\MainBanner();
        $mainBanner->setImage($file);
        $mainBanner->setName($cleanData['name']);
        $mainBanner->setLinkType((int) $cleanData['link_type']);
        $mainBanner->setLinkUrl($cleanData['link_url']);
        $mainBanner->setCreatedUser($this->auth->getUser());
        $mainBanner->setUpdatedUser($this->auth->getUser());

        $this->em->persist($mainBanner);
        $this->em->flush();

        $this->logger->info('Created MainBanner "{id}"', [
            'id' => $mainBanner->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('メインバナー「%s」を追加しました。', $mainBanner->getName()),
        ]);

        $this->redirect(
            $this->router->pathFor('main_banner_edit', ['id' => $mainBanner->getId()]),
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
        /** @var Entity\MainBanner|null $mainBanner */
        $mainBanner = $this->em->getRepository(Entity\MainBanner::class)->findOneById($args['id']);

        if (is_null($mainBanner)) {
            throw new NotFoundException($request, $response);
        }

        $values = [
            'id'        => $mainBanner->getId(),
            'name'      => $mainBanner->getName(),
            'link_type' => $mainBanner->getLinkType(),
            'link_url'  => $mainBanner->getLinkUrl(),
        ];

        return $this->renderEdit($response, [
            'mainBanner' => $mainBanner,
            'values' => $values,
            'form' => new Form\MainBannerForm(Form\MainBannerForm::TYPE_EDIT),
        ]);
    }

    /**
     * @param Response $response
     * @param array    $data
     * @return Response
     */
    protected function renderEdit(Response $response, array $data = [])
    {
        return $this->render($response, 'main_banner/edit.html.twig', $data);
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
        /** @var Entity\MainBanner|null $mainBanner */
        $mainBanner = $this->em->getRepository(Entity\MainBanner::class)->findOneById($args['id']);

        if (is_null($mainBanner)) {
            throw new NotFoundException($request, $response);
        }

        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = new Form\MainBannerForm(Form\MainBannerForm::TYPE_EDIT);
        $form->setData($params);

        if (! $form->isValid()) {
            return $this->renderEdit($response, [
                'mainBanner' => $mainBanner,
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
        $mainBanner->setUpdatedUser($this->auth->getUser());

        $this->em->flush();

        $this->logger->info('Updated MainBanner "{id}"', [
            'id' => $mainBanner->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('メインバナー「%s」を編集しました。', $mainBanner->getName()),
        ]);

        $this->redirect(
            $this->router->pathFor('main_banner_edit', ['id' => $mainBanner->getId()]),
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
        /** @var Entity\MainBanner|null $mainBanner */
        $mainBanner = $this->em->getRepository(Entity\MainBanner::class)->findOneById($args['id']);

        if (is_null($mainBanner)) {
            throw new NotFoundException($request, $response);
        }

        $this->doDelete($mainBanner);

        $this->logger->info('Deleted MainBanner "{id}"', [
            'id' => $mainBanner->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('メインバナー「%s」を削除しました。', $mainBanner->getName()),
        ]);

        $this->redirect($this->router->pathFor('main_banner_list'), 303);
    }

    /**
     * do delete
     *
     * @param Entity\MainBanner $mainBanner
     * @return void
     */
    protected function doDelete(Entity\MainBanner $mainBanner)
    {
        $this->em->getConnection()->beginTransaction();

        try {
            $mainBanner->setIsDeleted(true);
            $mainBanner->setUpdatedUser($this->auth->getUser());

            $this->logger->debug('Soft delete "MainBanner".', [
                'id' => $mainBanner->getId(),
            ]);

            $this->em->flush();

            $pageMainBannerDeleteCount = $this->em
                ->getRepository(Entity\PageMainBanner::class)
                ->deleteByMainBanner($mainBanner);

            $this->logger->debug('Delete "PageMainBanner"', ['count' => $pageMainBannerDeleteCount]);

            $theaterMainBannerDeleteCount = $this->em
                ->getRepository(Entity\TheaterMainBanner::class)
                ->deleteByMainBanner($mainBanner);

            $this->logger->debug('Delete "TheaterMainBanner"', ['count' => $theaterMainBannerDeleteCount]);

            $specialSiteMainBannerDeleteCount = $this->em
                ->getRepository(Entity\SpecialSiteMainBanner::class)
                ->deleteByMainBanner($mainBanner);

            $this->logger->debug('Delete "SpecialSiteMainBanner"', ['count' => $specialSiteMainBannerDeleteCount]);

            $this->em->getConnection()->commit();
        } catch (\Throwable $e) {
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
        // @todo ユーザによって取得する情報を変更する

        /** @var Entity\Page[] $pages */
        $pages = $this->em->getRepository(Entity\Page::class)->findActive();

        /** @var Entity\Theater[] $theaters */
        $theaters = $this->em->getRepository(Entity\Theater::class)->findActive();

        /** @var Entity\SpecialSite[] $specialSites */
        $specialSites = $this->em->getRepository(Entity\SpecialSite::class)->findActive();

        return $this->render($response, 'main_banner/publication.html.twig', [
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
    public function executePublicationUpdate(Request $request, Response $response, array $args)
    {
        $target = $args['target'];

        $form = new Form\MainBannerPublicationForm($target, $this->em);
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            throw new \LogicException('invalid parameters.');
        }

        $cleanData       = $form->getData();
        $targetEntity    = null;
        $basePublication = null;

        if ($target === Form\MainBannerPublicationForm::TARGET_TEATER) {
            /** @var Entity\Theater $targetEntity */
            $targetEntity = $this->em
                ->getRepository(Entity\Theater::class)
                ->findOneById((int) $cleanData['theater_id']);

            $basePublication = new Entity\TheaterMainBanner();
            $basePublication->setTheater($targetEntity);
        } elseif ($target === Form\MainBannerPublicationForm::TARGET_PAGE) {
            /** @var Entity\Page $targetEntity */
            $targetEntity = $this->em
                ->getRepository(Entity\Page::class)
                ->findOneById((int) $cleanData['page_id']);

            $basePublication = new Entity\PageMainBanner();
            $basePublication->setPage($targetEntity);
        } elseif ($target === Form\MainBannerPublicationForm::TARGET_SPESICAL_SITE) {
            /** @var Entity\SpecialSite $targetEntity */
            $targetEntity = $this->em
                ->getRepository(Entity\SpecialSite::class)
                ->findOneById((int) $cleanData['special_site_id']);

            $basePublication = new Entity\SpecialSiteMainBanner();
            $basePublication->setSpecialSite($targetEntity);
        }

        // いったん削除する
        $targetEntity->getMainBanners()->clear();

        foreach ($cleanData['main_banners'] as $mainBannerData) {
            $publication = clone $basePublication;

            /** @var Entity\MainBanner|null $mainBanner */
            $mainBanner = $this->em
                ->getRepository(Entity\MainBanner::class)
                ->findOneById((int) $mainBannerData['main_banner_id']);

            if (! $mainBanner) {
                // @todo formで検証したい
                throw new \LogicException('invalid main_banner.');
            }

            $publication->setMainBanner($mainBanner);
            $publication->setDisplayOrder((int) $mainBannerData['display_order']);

            $this->em->persist($publication);
        }

        $this->em->flush();

        $this->logger->info('Updated MainBanner display order ', [
            'target' => $target,
            'target_id' => $targetEntity->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('%sの表示順を保存しました。', $targetEntity->getNameJa()),
        ]);

        $this->redirect($this->router->pathFor('main_banner_publication'), 303);
    }
}
