<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ForbiddenException;
use App\Form;
use App\ORM\Entity;
use App\Pagination\DoctrinePaginator;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class TrailerController extends BaseController
{
    protected function preExecute(Request $request, Response $response): void
    {
        $this->authorization();

        parent::preExecute($request, $response);
    }

    /**
     * @throws ForbiddenException
     */
    protected function authorization(): void
    {
        $user = $this->auth->getUser();

        if ($user->isTheater()) {
            throw new ForbiddenException();
        }
    }

    /**
     * list action
     *
     * @param array<string, mixed> $args
     */
    public function executeList(Request $request, Response $response, array $args): Response
    {
        $page = (int) $request->getParam('p', 1);

        $form = new Form\TrailerFindForm($this->em);
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

        /** @var DoctrinePaginator $pagenater */
        $pagenater = $this->em->getRepository(Entity\Trailer::class)->findForList($cleanValues, $page);

        return $this->render($response, 'trailer/list.html.twig', [
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
     * @param array<string, mixed> $args
     */
    public function executeNew(Request $request, Response $response, array $args): Response
    {
        $form = new Form\TrailerForm(Form\TrailerForm::TYPE_NEW, $this->em);

        return $this->renderNew($response, ['form' => $form]);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function renderNew(Response $response, array $data = []): Response
    {
        return $this->render($response, 'trailer/new.html.twig', $data);
    }

    /**
     * create action
     *
     * @param array<string, mixed> $args
     */
    public function executeCreate(Request $request, Response $response, array $args): Response
    {
        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = new Form\TrailerForm(Form\TrailerForm::TYPE_NEW, $this->em);
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

        $bannerImage = $cleanData['banner_image'];

        // rename
        $newName = Entity\File::createName($bannerImage['name']);

        // upload storage
        // @todo storageと同期するような仕組みをFileへ
        $options = new CreateBlockBlobOptions();
        $options->setContentType($bannerImage['type']);
        $this->bc->createBlockBlob(
            Entity\File::getBlobContainer(),
            $newName,
            fopen($bannerImage['tmp_name'], 'r'),
            $options
        );

        $file = new Entity\File();
        $file->setName($newName);
        $file->setOriginalName($bannerImage['name']);
        $file->setMimeType($bannerImage['type']);
        $file->setSize((int) $bannerImage['size']);

        $this->em->persist($file);

        $title = null;

        if ($cleanData['title_id']) {
            $title =  $this->em
                ->getRepository(Entity\Title::class)
                ->findOneById((int) $cleanData['title_id']);
        }

        $trailer = new Entity\Trailer();
        $this->em->persist($trailer);

        $trailer->setTitle($title);
        $trailer->setName($cleanData['name']);
        $trailer->setYoutube($cleanData['youtube']);
        $trailer->setBannerImage($file);
        $trailer->setBannerLinkUrl($cleanData['banner_link_url']);
        $trailer->setCreatedUser($this->auth->getUser());
        $trailer->setUpdatedUser($this->auth->getUser());

        if ($cleanData['page']) {
            /** @var Entity\Page[] $pages */
            $pages = $this->em->getRepository(Entity\Page::class)->findByIds($cleanData['page']);

            foreach ($pages as $page) {
                $pageTrailer = new Entity\PageTrailer();
                $this->em->persist($pageTrailer);

                $pageTrailer->setPage($page);
                $pageTrailer->setTrailer($trailer);
            }
        }

        if ($cleanData['theater']) {
            /** @var Entity\Theater[] $theaters */
            $theaters = $this->em->getRepository(Entity\Theater::class)->findByIds($cleanData['theater']);

            foreach ($theaters as $theater) {
                $theaterTrailer = new Entity\TheaterTrailer();
                $this->em->persist($theaterTrailer);

                $theaterTrailer->setTheater($theater);
                $theaterTrailer->setTrailer($trailer);
            }
        }

        if ($cleanData['special_site']) {
            /** @var Entity\SpecialSite[] $specialSites */
            $specialSites = $this->em
                ->getRepository(Entity\SpecialSite::class)
                ->findByIds($cleanData['special_site']);

            foreach ($specialSites as $specialSite) {
                $specialSiteTrailer = new Entity\SpecialSiteTrailer();
                $this->em->persist($specialSiteTrailer);

                $specialSiteTrailer->setSpecialSite($specialSite);
                $specialSiteTrailer->setTrailer($trailer);
            }
        }

        $this->em->flush();

        $this->logger->info('Created Trailer "{id}"', [
            'id' => $trailer->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('予告動画「%s」を追加しました。', $trailer->getName()),
        ]);

        $this->redirect(
            $this->router->pathFor('trailer_edit', ['id' => $trailer->getId()]),
            303
        );
    }

    /**
     * edit action
     *
     * @param array<string, mixed> $args
     */
    public function executeEdit(Request $request, Response $response, array $args): Response
    {
        /** @var Entity\Trailer|null $trailer */
        $trailer = $this->em
            ->getRepository(Entity\Trailer::class)
            ->findOneById((int) $args['id']);

        if (is_null($trailer)) {
            throw new NotFoundException($request, $response);
        }

        $values = [
            'id'              => $trailer->getId(),
            'name'            => $trailer->getName(),
            'title_id'        => null,
            'title_name'      => null,
            'youtube'         => $trailer->getYoutube(),
            'banner_link_url' => $trailer->getBannerLinkUrl(),
            'page'            => [],
            'theater'         => [],
            'special_site'    => [],
        ];

        if ($trailer->getTitle()) {
            $values['title_id']   = $trailer->getTitle()->getId();
            $values['title_name'] = $trailer->getTitle()->getName();
        }

        foreach ($trailer->getPages() as $pageTrailer) {
            $values['page'][] = $pageTrailer->getPage()->getId();
        }

        foreach ($trailer->getTheaters() as $theaterTrailer) {
            $values['theater'][] = $theaterTrailer->getTheater()->getId();
        }

        foreach ($trailer->getSpecialSites() as $specialSiteTrailer) {
            $values['special_site'][] = $specialSiteTrailer->getSpecialSite()->getId();
        }

        $form = new Form\TrailerForm(Form\TrailerForm::TYPE_EDIT, $this->em);

        return $this->renderEdit($response, [
            'trailer' => $trailer,
            'form' => $form,
            'values' => $values,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function renderEdit(Response $response, array $data = []): Response
    {
        return $this->render($response, 'trailer/edit.html.twig', $data);
    }

    /**
     * update action
     *
     * @param array<string, mixed> $args
     */
    public function executeUpdate(Request $request, Response $response, array $args): Response
    {
        /** @var Entity\Trailer|null $trailer */
        $trailer = $this->em
            ->getRepository(Entity\Trailer::class)
            ->findOneById((int) $args['id']);

        if (is_null($trailer)) {
            throw new NotFoundException($request, $response);
        }

        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = new Form\TrailerForm(Form\TrailerForm::TYPE_EDIT, $this->em);
        $form->setData($params);

        if (! $form->isValid()) {
            return $this->renderEdit($response, [
                'trailer' => $trailer,
                'form' => $form,
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
        }

        $cleanData = $form->getData();

        $bannerImage = $cleanData['banner_image'];

        if ($bannerImage['name']) {
            // rename
            $newName = Entity\File::createName($bannerImage['name']);

            // upload storage
            // @todo storageと同期するような仕組みをFileへ
            $options = new CreateBlockBlobOptions();
            $options->setContentType($bannerImage['type']);
            $this->bc->createBlockBlob(
                Entity\File::getBlobContainer(),
                $newName,
                fopen($bannerImage['tmp_name'], 'r'),
                $options
            );

            $file = new Entity\File();
            $file->setName($newName);
            $file->setOriginalName($bannerImage['name']);
            $file->setMimeType($bannerImage['type']);
            $file->setSize((int) $bannerImage['size']);

            $this->em->persist($file);

            $trailer->setBannerImage($file);
        }

        $title = null;

        if ($cleanData['title_id']) {
            $title =  $this->em
                ->getRepository(Entity\Title::class)
                ->findOneById((int) $cleanData['title_id']);
        }

        $trailer->setTitle($title);
        $trailer->setName($cleanData['name']);
        $trailer->setYoutube($cleanData['youtube']);
        $trailer->setBannerLinkUrl($cleanData['banner_link_url']);
        $trailer->setUpdatedUser($this->auth->getUser());

        $trailer->getPages()->clear();

        if ($cleanData['page']) {
            /** @var Entity\Page[] $pages */
            $pages = $this->em
                ->getRepository(Entity\Page::class)
                ->findByIds($cleanData['page']);

            foreach ($pages as $page) {
                $pageTrailer = new Entity\PageTrailer();
                $this->em->persist($pageTrailer);

                $pageTrailer->setPage($page);
                $pageTrailer->setTrailer($trailer);
            }
        }

        $trailer->getTheaters()->clear();

        if ($cleanData['theater']) {
            /** @var Entity\Theater[] $theaters */
            $theaters = $this->em
                ->getRepository(Entity\Theater::class)
                ->findByIds($cleanData['theater']);

            foreach ($theaters as $theater) {
                $theaterTrailer = new Entity\TheaterTrailer();
                $this->em->persist($theaterTrailer);

                $theaterTrailer->setTheater($theater);
                $theaterTrailer->setTrailer($trailer);
            }
        }

        $trailer->getSpecialSites()->clear();

        if ($cleanData['special_site']) {
            /** @var Entity\SpecialSite[] $specialSites */
            $specialSites = $this->em
                ->getRepository(Entity\SpecialSite::class)
                ->findByIds($cleanData['special_site']);

            foreach ($specialSites as $specialSite) {
                $specialSiteTrailer = new Entity\SpecialSiteTrailer();
                $this->em->persist($specialSiteTrailer);

                $specialSiteTrailer->setSpecialSite($specialSite);
                $specialSiteTrailer->setTrailer($trailer);
            }
        }

        $this->em->flush();

        $this->logger->info('Updated Trailer "{id}"', [
            'id' => $trailer->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('予告動画「%s」を編集しました。', $trailer->getName()),
        ]);

        $this->redirect(
            $this->router->pathFor('trailer_edit', ['id' => $trailer->getId()]),
            303
        );
    }

    /**
     * delete action
     *
     * @param array<string, mixed> $args
     */
    public function executeDelete(Request $request, Response $response, array $args): void
    {
        /** @var Entity\Trailer|null $trailer */
        $trailer = $this->em
            ->getRepository(Entity\Trailer::class)
            ->findOneById((int) $args['id']);

        if (is_null($trailer)) {
            throw new NotFoundException($request, $response);
        }

        $trailer->setIsDeleted(true);
        $trailer->setUpdatedUser($this->auth->getUser());

        // 関連データの処理はイベントで対応する

        $this->em->flush();

        $this->logger->info('Deleted Trailer "{id}"', [
            'id' => $trailer->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('予告動画「%s」を削除しました。', $trailer->getName()),
        ]);

        $this->redirect($this->router->pathFor('trailer_list'), 303);
    }
}
