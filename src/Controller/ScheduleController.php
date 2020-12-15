<?php

namespace App\Controller;

use App\Exception\ForbiddenException;
use App\Form;
use App\ORM\Entity;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Schedule controller
 */
class ScheduleController extends BaseController
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

        $form = new Form\ScheduleFindForm();
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
        $pagenater = $this->em->getRepository(Entity\Schedule::class)->findForList($cleanValues, $page);

        return $this->render($response, 'schedule/list.html.twig', [
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
        $form = new Form\ScheduleForm(Form\ScheduleForm::TYPE_NEW, $this->em);

        return $this->renderNew($response, ['form' => $form]);
    }

    /**
     * @param Response $response
     * @param array    $data
     * @return Response
     */
    protected function renderNew(Response $response, array $data = [])
    {
        return $this->render($response, 'schedule/new.html.twig', $data);
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
        $form = new Form\ScheduleForm(Form\ScheduleForm::TYPE_NEW, $this->em);
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            return $this->renderNew($response, [
                'form' => $form,
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
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
            $this->router->pathFor('schedule_edit', ['id' => $schedule->getId()]),
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
        /** @var Entity\Schedule|null $schedule */
        $schedule = $this->em->getRepository(Entity\Schedule::class)->findOneById($args['id']);

        if (is_null($schedule)) {
            throw new NotFoundException($request, $response);
        }

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

        $form = new Form\ScheduleForm(Form\ScheduleForm::TYPE_EDIT, $this->em);

        return $this->renderEdit($response, [
            'schedule' => $schedule,
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
        return $this->render($response, 'schedule/edit.html.twig', $data);
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
        /** @var Entity\Schedule|null $schedule */
        $schedule = $this->em->getRepository(Entity\Schedule::class)->findOneById($args['id']);

        if (is_null($schedule)) {
            throw new NotFoundException($request, $response);
        }

        $form = new Form\ScheduleForm(Form\ScheduleForm::TYPE_EDIT, $this->em);
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            return $this->renderEdit($response, [
                'schedule' => $schedule,
                'form' => $form,
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
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
            $this->router->pathFor('schedule_edit', ['id' => $schedule->getId()]),
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
        /** @var Entity\Schedule|null $schedule */
        $schedule = $this->em->getRepository(Entity\Schedule::class)->findOneById($args['id']);

        if (is_null($schedule)) {
            throw new NotFoundException($request, $response);
        }

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
