<?php
/**
 * OyakoCinemaController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Cinemasunshine\PortalAdmin\Exception\ForbiddenException;
use Cinemasunshine\PortalAdmin\Form\OyakoCinemaForm as Form;
use Cinemasunshine\PortalAdmin\Form\OyakoCinemaSettingForm as SettingForm;
use Cinemasunshine\PortalAdmin\ORM\Entity;
use Slim\Exception\NotFoundException;

/**
 * OyakoCinema controller
 */
class OyakoCinemaController extends BaseController
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

        /** @var \Cinemasunshine\PortalAdmin\Pagination\DoctrinePaginator $pagenater */
        $pagenater = $this->em->getRepository(Entity\OyakoCinemaTitle::class)
            ->findForList($page);

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
        $this->data->set('form', new Form(Form::TYPE_NEW, $this->em));
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
        $form = new Form(Form::TYPE_NEW, $this->em);
        $form->setData($request->getParams());

        if (!$form->isValid()) {
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);

            return 'new';
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
            $this->router->pathFor('oyako_cinema_edit', [ 'id' => $oyakoCinemaTitle->getId() ]),
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
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeEdit($request, $response, $args)
    {
        $oyakoCinemaTitle = $this->em->getRepository(Entity\OyakoCinemaTitle::class)
            ->findOneById($args['id']);

        if (is_null($oyakoCinemaTitle)) {
            throw new NotFoundException($request, $response);
        }

        /**@var Entity\OyakoCinemaTitle $oyakoCinemaTitle */

        $this->data->set('oyakoCinemaTitle', $oyakoCinemaTitle);

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

        $this->data->set('values', $values);

        $this->data->set('form', new Form(Form::TYPE_EDIT, $this->em));
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
        $oyakoCinemaTitle = $this->em->getRepository(Entity\OyakoCinemaTitle::class)
            ->findOneById($args['id']);

        if (is_null($oyakoCinemaTitle)) {
            throw new NotFoundException($request, $response);
        }

        /**@var Entity\OyakoCinemaTitle $oyakoCinemaTitle */

        $form = new Form(Form::TYPE_EDIT, $this->em);
        $form->setData($request->getParams());

        if (!$form->isValid()) {
            $this->data->set('oyakoCinemaTitle', $oyakoCinemaTitle);
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);

            return 'edit';
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
            $this->router->pathFor('oyako_cinema_edit', [ 'id' => $oyakoCinemaTitle->getId() ]),
            303
        );
    }

    /**
     * do update
     *
     * @param Entity\OyakoCinemaTitle $oyakoCinemaTitle
     * @param array $data
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
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeDelete($request, $response, $args)
    {
        $oyakoCinemaTitle = $this->em->getRepository(Entity\OyakoCinemaTitle::class)
            ->findOneById($args['id']);

        if (is_null($oyakoCinemaTitle)) {
            throw new NotFoundException($request, $response);
        }

        /**@var Entity\OyakoCinemaTitle $oyakoCinemaTitle */

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
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeSetting($request, $response, $args)
    {
        $theaterMetas = $this->em->getRepository(Entity\TheaterMeta::class)->findActive();

        $this->data->set('theaterMetas', $theaterMetas);
    }

    /**
     * setting edit action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeSettingEdit($request, $response, $args)
    {
        $theater = $this->em->getRepository(Entity\Theater::class)->findOneById($args['id']);

        if (is_null($theater)) {
            throw new NotFoundException($request, $response);
        }

        /**@var Entity\Theater $theater */

        $this->data->set('theater', $theater);

        $values = [
            'oyako_cinema_url' => $theater->getMeta()->getOyakoCinemaUrl(),
        ];

        $this->data->set('values', $values);
    }

    /**
     * setting update action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeSettingUpdate($request, $response, $args)
    {
        $theater = $this->em->getRepository(Entity\Theater::class)->findOneById($args['id']);

        if (is_null($theater)) {
            throw new NotFoundException($request, $response);
        }

        /**@var Entity\Theater $theater */

        $form = new SettingForm();
        $form->setData($request->getParams());

        if (!$form->isValid()) {
            $this->data->set('theater', $theater);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);

            return 'settingEdit';
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
            $this->router->pathFor('oyako_cinema_setting_edit', [ 'id' => $theater->getId() ]),
            303
        );
    }

    /**
     * do setting update
     *
     * @param Entity\TheaterMeta $theaterMeta
     * @param array $data
     * @return void
     */
    protected function doSettingUpdate(Entity\TheaterMeta $theaterMeta, array $data)
    {
        $theaterMeta->setOyakoCinemaUrl($data['oyako_cinema_url']);
        $this->em->flush();
    }
}
