<?php
/**
 * OyakoCinemaController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Cinemasunshine\PortalAdmin\Exception\ForbiddenException;
use Cinemasunshine\PortalAdmin\Form\OyakoCinemaForm as Form;
use Cinemasunshine\PortalAdmin\ORM\Entity;

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
     * index action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeIndex($request, $response, $args)
    {
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

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('「%s」のおやこシネマ情報を追加しました。', $oyakoCinemaTitle->getTitle()->getName()),
        ]);

        // @todo おやこシネマ編集へリダイレクト
        $this->redirect(
            $this->router->pathFor('homepage'),
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
                ->findByIds($scheduleData['theater']);

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
}
