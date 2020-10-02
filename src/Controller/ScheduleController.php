<?php

/**
 * ScheduleController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Controller;

use App\Exception\ForbiddenException;
use App\Form;
use App\ORM\Entity;
use Slim\Exception\NotFoundException;

/**
 * Schedule controller
 */
class ScheduleController extends BaseController
{
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

        $form = new Form\ScheduleFindForm();
        $this->data->set('form', $form);

        $form->setData($request->getParams());
        $cleanValues = [];

        if ($form->isValid()) {
            $cleanValues = $form->getData();
            $values      = $cleanValues;
        } else {
            $values = $request->getParams();
            $this->data->set('errors', $form->getMessages());
        }

        $this->data->set('values', $values);
        $this->data->set('params', $cleanValues);

        /** @var \App\Pagination\DoctrinePaginator $pagenater */
        $pagenater = $this->em->getRepository(Entity\Schedule::class)->findForList($cleanValues, $page);

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
        $this->data->set('form', new Form\ScheduleForm(Form\ScheduleForm::TYPE_NEW, $this->em));
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
        $form = new Form\ScheduleForm(Form\ScheduleForm::TYPE_NEW, $this->em);
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);

            return 'new';
        }

        $cleanData = $form->getData();

        $schedule = new Entity\Schedule();
        $this->em->persist($schedule);

        $title =  $this->em->getRepository(Entity\Title::class)->findOneById($cleanData['title_id']);
        $schedule->setTitle($title);

        $schedule->setStartDate($cleanData['start_date']);
        $schedule->setEndDate($cleanData['end_date']);
        $schedule->setPublicStartDt($cleanData['public_start_dt']);
        $schedule->setPublicEndDt($cleanData['public_end_dt']);
        $schedule->setRemark($cleanData['remark']);
        $schedule->setCreatedUser($this->auth->getUser());
        $schedule->setUpdatedUser($this->auth->getUser());

        $theaters = $this->em->getRepository(Entity\Theater::class)->findByIds($cleanData['theater']);

        foreach ($theaters as $theater) {
            /** @var Entity\Theater $theater */

            $showingTheater = new Entity\ShowingTheater();
            $this->em->persist($showingTheater);

            $showingTheater->setSchedule($schedule);
            $showingTheater->setTheater($theater);
        }

        foreach ($cleanData['formats'] as $formatData) {
            $format = new Entity\ShowingFormat();
            $this->em->persist($format);

            $format->setSchedule($schedule);
            $format->setSystem($formatData['system']);
            $format->setSound($formatData['sound']);
            $format->setVoice($formatData['voice']);
        }

        $this->em->flush();

        $this->logger->info('Created Schedule "{id}"', [
            'id' => $schedule->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('「%s」の上映情報を追加しました。', $schedule->getTitle()->getName()),
        ]);

        $this->redirect(
            $this->router->pathFor('schedule_edit', [ 'id' => $schedule->getId() ]),
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
        $schedule = $this->em->getRepository(Entity\Schedule::class)->findOneById($args['id']);

        if (is_null($schedule)) {
            throw new NotFoundException($request, $response);
        }

        /**@var Entity\Schedule $schedule */

        $this->data->set('schedule', $schedule);

        $values = [
            'id' => $schedule->getId(),
            'title_id' => $schedule->getTitle()->getId(),
            'title_name' => $schedule->getTitle()->getName(),
            'start_date' => $schedule->getStartDate()->format('Y/m/d'),
            'end_date' => $schedule->getEndDate()->format('Y/m/d'),
            'public_start_dt' => $schedule->getPublicStartDt()->format('Y/m/d H:i'),
            'public_end_dt' => $schedule->getPublicEndDt()->format('Y/m/d H:i'),
            'remark' => $schedule->getRemark(),
            'theater' => [],
            'formats' => [],
        ];

        foreach ($schedule->getShowingTheaters() as $showingTheater) {
            /** @var Entity\ShowingTheater $showingTheater */
            $values['theater'][] = $showingTheater->getTheater()->getId();
        }

        foreach ($schedule->getShowingFormats() as $showingFormat) {
            /** @var Entity\ShowingFormat $showingFormat */
            $values['formats'][] = [
                'system' => $showingFormat->getSystem(),
                'sound' => $showingFormat->getSound(),
                'voice' => $showingFormat->getVoice(),
            ];
        }

        $this->data->set('values', $values);

        $this->data->set('form', new Form\ScheduleForm(Form\ScheduleForm::TYPE_EDIT, $this->em));
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
        $schedule = $this->em->getRepository(Entity\Schedule::class)->findOneById($args['id']);

        if (is_null($schedule)) {
            throw new NotFoundException($request, $response);
        }

        /**@var Entity\Schedule $schedule */

        $form = new Form\ScheduleForm(Form\ScheduleForm::TYPE_EDIT, $this->em);
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            $this->data->set('schedule', $schedule);
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);

            return 'edit';
        }

        $cleanData = $form->getData();

        $title =  $this->em->getRepository(Entity\Title::class)->findOneById($cleanData['title_id']);
        $schedule->setTitle($title);

        $schedule->setStartDate($cleanData['start_date']);
        $schedule->setEndDate($cleanData['end_date']);
        $schedule->setPublicStartDt($cleanData['public_start_dt']);
        $schedule->setPublicEndDt($cleanData['public_end_dt']);
        $schedule->setRemark($cleanData['remark']);
        $schedule->setUpdatedUser($this->auth->getUser());

        $schedule->getShowingTheaters()->clear();

        $theaters = $this->em->getRepository(Entity\Theater::class)->findByIds($cleanData['theater']);

        foreach ($theaters as $theater) {
            /** @var Entity\Theater $theater */

            $showingTheater = new Entity\ShowingTheater();
            $this->em->persist($showingTheater);

            $showingTheater->setSchedule($schedule);
            $showingTheater->setTheater($theater);
        }

        $schedule->getShowingFormats()->clear();

        foreach ($cleanData['formats'] as $formatData) {
            $format = new Entity\ShowingFormat();
            $this->em->persist($format);

            $format->setSchedule($schedule);
            $format->setSystem($formatData['system']);
            $format->setSound($formatData['sound']);
            $format->setVoice($formatData['voice']);
        }

        $this->em->flush();

        $this->logger->info('Updated Schedule "{id}"', [
            'id' => $schedule->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('「%s」の上映情報を編集しました。', $schedule->getTitle()->getName()),
        ]);

        $this->redirect(
            $this->router->pathFor('schedule_edit', [ 'id' => $schedule->getId() ]),
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
        $schedule = $this->em->getRepository(Entity\Schedule::class)->findOneById($args['id']);

        if (is_null($schedule)) {
            throw new NotFoundException($request, $response);
        }

        /**@var Entity\Schedule $schedule */

        $schedule->setIsDeleted(true);
        $schedule->setUpdatedUser($this->auth->getUser());

        // 関連データの処理はイベントで対応する

        $this->em->flush();

        $this->logger->info('Deleted Schedule "{id}"', [
            'id' => $schedule->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('「%s」の上映情報を削除しました。', $schedule->getTitle()->getName()),
        ]);

        $this->redirect($this->router->pathFor('schedule_list'), 303);
    }
}
