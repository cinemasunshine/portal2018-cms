<?php

/**
 * OyakoCinemaController.php
 */

namespace App\Controller;

use App\Exception\ForbiddenException;
use App\Form\OyakoCinemaForm as Form;
use App\Form\OyakoCinemaSettingForm as SettingForm;
use App\ORM\Entity;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * OyakoCinema controller
 */
class OyakoCinemaController extends BaseController
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

        /** @var \App\Pagination\DoctrinePaginator $pagenater */
        $pagenater = $this->em->getRepository(Entity\OyakoCinemaTitle::class)
            ->findForList($page);

        return $this->render($response, 'oyako_cinema/list.html.twig', [
            'page' => $page,
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
        $form =  new Form(Form::TYPE_NEW, $this->em);

        return $this->renderNew($response, ['form' => $form]);
    }

    /**
     * @param Response $response
     * @param array    $data
     * @return Response
     */
    protected function renderNew(Response $response, array $data = [])
    {
        return $this->render($response, 'oyako_cinema/new.html.twig', $data);
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
        $form = new Form(Form::TYPE_NEW, $this->em);
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            return $this->renderNew($response, [
                'form' => $form,
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
        }

        /** @var array $cleanData */
        $cleanData = $form->getData();

        $oyakoCinemaTitle = $this->doCleate($cleanData);

        $this->logger->info('Created OyakoCinema "{id}"', [
            'id' => $oyakoCinemaTitle->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('「%s」のおやこシネマ情報を追加しました。', $oyakoCinemaTitle->getTitle()->getName()),
        ]);

        $this->redirect(
            $this->router->pathFor('oyako_cinema_edit', ['id' => $oyakoCinemaTitle->getId()]),
            303
        );
    }

    /**
     * do cleate
     *
     * @param array $data
     * @return Entity\OyakoCinemaTitle
     */
    protected function doCleate(array $data): Entity\OyakoCinemaTitle
    {
        $oyakoCinemaTitle = new Entity\OyakoCinemaTitle();
        $this->em->persist($oyakoCinemaTitle);

        $title =  $this->em->getRepository(Entity\Title::class)
            ->findOneById($data['title_id']);
        $oyakoCinemaTitle->setTitle($title);
        $oyakoCinemaTitle->setTitleUrl($data['title_url']);
        $oyakoCinemaTitle->setCreatedUser($this->auth->getUser());
        $oyakoCinemaTitle->setUpdatedUser($this->auth->getUser());

        foreach ($data['schedules'] as $scheduleData) {
            $oyakoCinemaSchedule = new Entity\OyakoCinemaSchedule();
            $this->em->persist($oyakoCinemaSchedule);

            $oyakoCinemaSchedule->setOyakoCinemaTitle($oyakoCinemaTitle);
            $oyakoCinemaSchedule->setDate($scheduleData['date']);

            $theaters = $this->em->getRepository(Entity\Theater::class)
                ->findByIds($scheduleData['theaters']);

            foreach ($theaters as $theater) {
                $oyakoCinemaTheater = new Entity\OyakoCinemaTheater();
                $this->em->persist($oyakoCinemaTheater);

                $oyakoCinemaTheater->setOyakoCinemaSchedule($oyakoCinemaSchedule);
                $oyakoCinemaTheater->setTheater($theater);
            }
        }

        $this->em->flush();

        return $oyakoCinemaTitle;
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
        /** @var Entity\OyakoCinemaTitle|null $oyakoCinemaTitle */
        $oyakoCinemaTitle = $this->em->getRepository(Entity\OyakoCinemaTitle::class)
            ->findOneById($args['id']);

        if (is_null($oyakoCinemaTitle)) {
            throw new NotFoundException($request, $response);
        }

        $values = [
            'id' => $oyakoCinemaTitle->getId(),
            'title_id' => $oyakoCinemaTitle->getTitle()->getId(),
            'title_name' => $oyakoCinemaTitle->getTitle()->getName(),
            'title_url' => $oyakoCinemaTitle->getTitleUrl(),
            'schedules' => [],
        ];

        foreach ($oyakoCinemaTitle->getOyakoCinemaSchedules() as $oyakoCinemaSchedule) {
            /** @var Entity\OyakoCinemaSchedule $oyakoCinemaSchedule */
            $scheduleValue = [
                'date' => $oyakoCinemaSchedule->getDate()->format('Y/m/d'),
                'theaters' => [],
            ];

            foreach ($oyakoCinemaSchedule->getOyakoCinemaTheaters() as $oyakoCinemaTheater) {
                /** @var Entity\OyakoCinemaTheater $oyakoCinemaTheater */
                $scheduleValue['theaters'][] = $oyakoCinemaTheater->getTheater()->getId();
            }

            $values['schedules'][] = $scheduleValue;
        }

        $form = new Form(Form::TYPE_EDIT, $this->em);

        return $this->renderEdit($response, [
            'oyakoCinemaTitle' => $oyakoCinemaTitle,
            'form' => $form,
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
        return $this->render($response, 'oyako_cinema/edit.html.twig', $data);
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
        /** @var Entity\OyakoCinemaTitle|null $oyakoCinemaTitle */
        $oyakoCinemaTitle = $this->em->getRepository(Entity\OyakoCinemaTitle::class)
            ->findOneById($args['id']);

        if (is_null($oyakoCinemaTitle)) {
            throw new NotFoundException($request, $response);
        }

        $form = new Form(Form::TYPE_EDIT, $this->em);
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            return $this->renderEdit($response, [
                'oyakoCinemaTitle' => $oyakoCinemaTitle,
                'form' => $form,
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
        }

        /** @var array $cleanData */
        $cleanData = $form->getData();

        $this->doUpdate($oyakoCinemaTitle, $cleanData);

        $this->logger->info('Updated OyakoCinema "{id}"', [
            'id' => $oyakoCinemaTitle->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('「%s」のおやこシネマ情報を編集しました。', $oyakoCinemaTitle->getTitle()->getName()),
        ]);

        $this->redirect(
            $this->router->pathFor('oyako_cinema_edit', ['id' => $oyakoCinemaTitle->getId()]),
            303
        );
    }

    /**
     * do update
     *
     * @param Entity\OyakoCinemaTitle $oyakoCinemaTitle
     * @param array                   $data
     * @return void
     */
    protected function doUpdate(Entity\OyakoCinemaTitle $oyakoCinemaTitle, array $data)
    {
        $title =  $this->em->getRepository(Entity\Title::class)
            ->findOneById($data['title_id']);
        $oyakoCinemaTitle->setTitle($title);
        $oyakoCinemaTitle->setTitleUrl($data['title_url']);
        $oyakoCinemaTitle->setUpdatedUser($this->auth->getUser());

        // clearして再登録する
        $oyakoCinemaTitle->clearOyakoCinemaSchedules();

        foreach ($data['schedules'] as $scheduleData) {
            $oyakoCinemaSchedule = new Entity\OyakoCinemaSchedule();
            $this->em->persist($oyakoCinemaSchedule);

            $oyakoCinemaSchedule->setOyakoCinemaTitle($oyakoCinemaTitle);
            $oyakoCinemaSchedule->setDate($scheduleData['date']);

            $theaters = $this->em->getRepository(Entity\Theater::class)
                ->findByIds($scheduleData['theaters']);

            foreach ($theaters as $theater) {
                $oyakoCinemaTheater = new Entity\OyakoCinemaTheater();
                $this->em->persist($oyakoCinemaTheater);

                $oyakoCinemaTheater->setOyakoCinemaSchedule($oyakoCinemaSchedule);
                $oyakoCinemaTheater->setTheater($theater);
            }
        }

        $this->em->flush();
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
        /** @var Entity\OyakoCinemaTitle|null $oyakoCinemaTitle */
        $oyakoCinemaTitle = $this->em->getRepository(Entity\OyakoCinemaTitle::class)
            ->findOneById($args['id']);

        if (is_null($oyakoCinemaTitle)) {
            throw new NotFoundException($request, $response);
        }

        $this->doDelete($oyakoCinemaTitle);

        $this->logger->info('Deleted OyakoCinema "{id}"', [
            'id' => $oyakoCinemaTitle->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('「%s」のおやこシネマ情報を削除しました。', $oyakoCinemaTitle->getTitle()->getName()),
        ]);

        $this->redirect($this->router->pathFor('oyako_cinema_list'), 303);
    }

    /**
     * do delete
     *
     * @param Entity\OyakoCinemaTitle $oyakoCinemaTitle
     * @return void
     */
    protected function doDelete(Entity\OyakoCinemaTitle $oyakoCinemaTitle)
    {
        $oyakoCinemaTitle->setIsDeleted(true);
        $oyakoCinemaTitle->setUpdatedUser($this->auth->getUser());

        $this->em->flush();
    }

    /**
     * setting action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeSetting(Request $request, Response $response, array $args)
    {
        $theaterMetas = $this->em->getRepository(Entity\TheaterMeta::class)->findActive();

        return $this->render($response, 'oyako_cinema/setting/index.html.twig', ['theaterMetas' => $theaterMetas]);
    }

    /**
     * setting edit action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeSettingEdit(Request $request, Response $response, array $args)
    {
        /** @var Entity\Theater|null $theater */
        $theater = $this->em->getRepository(Entity\Theater::class)->findOneById($args['id']);

        if (is_null($theater)) {
            throw new NotFoundException($request, $response);
        }

        $values = [
            'oyako_cinema_url' => $theater->getMeta()->getOyakoCinemaUrl(),
        ];

        return $this->renderSettingEdit($response, [
            'theater' => $theater,
            'values' => $values,
        ]);
    }

    /**
     * @param Response $response
     * @param array    $data
     * @return Response
     */
    protected function renderSettingEdit(Response $response, array $data = [])
    {
        return $this->render($response, 'oyako_cinema/setting/edit.html.twig', $data);
    }

    /**
     * setting update action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeSettingUpdate(Request $request, Response $response, $args)
    {
        /** @var Entity\Theater|null $theater */
        $theater = $this->em->getRepository(Entity\Theater::class)->findOneById($args['id']);

        if (is_null($theater)) {
            throw new NotFoundException($request, $response);
        }

        $form = new SettingForm();
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            return $this->renderSettingEdit($response, [
                'theater' => $theater,
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
        }

        $cleanData = $form->getData();

        $this->doSettingUpdate($theater->getMeta(), $cleanData);

        $this->logger->info('Updated OyakoCinema settings', [
            'theater' => $theater->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('「%s」の劇場リンク設定を編集しました。', $theater->getNameJa()),
        ]);

        $this->redirect(
            $this->router->pathFor('oyako_cinema_setting_edit', ['id' => $theater->getId()]),
            303
        );
    }

    /**
     * do setting update
     *
     * @param Entity\TheaterMeta $theaterMeta
     * @param array              $data
     * @return void
     */
    protected function doSettingUpdate(Entity\TheaterMeta $theaterMeta, array $data)
    {
        $theaterMeta->setOyakoCinemaUrl($data['oyako_cinema_url']);
        $this->em->flush();
    }
}
