<?php
/**
 * TheaterMetaController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Slim\Exception\NotFoundException;

use Cinemasunshine\PortalAdmin\Form;
use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * TheaterMeta controller
 */
class TheaterMetaController extends BaseController
{
    /**
     * opening hour action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeOpeningHour($request, $response, $args)
    {
        $user = $this->auth->getUser();
        $repository = $this->em->getRepository(Entity\TheaterMeta::class);

        if ($user->isTheater()) {
            $metas = [
                $repository->findOneByTheaterId($user->getTheater()->getId())
            ];
        } else {
            $metas = $repository->findActive();
        }

        $this->data->set('metas', $metas);
    }

    /**
     * opening hour edit action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeOpeningHourEdit($request, $response, $args)
    {
        $theater = $this->em->getRepository(Entity\Theater::class)->findOneById($args['id']);

        if (is_null($theater)) {
            throw new NotFoundException($request, $response);
        }

        /**@var Entity\Theater $theater */

        $this->data->set('theater', $theater);

        $values = [
            'hours' => [],
        ];

        foreach ($theater->getMeta()->getOpeningHours() as $hour) {
            /** @var Entity\TheaterOpeningHour $hour */
            $values['hours'][] = [
                'type'      => $hour->getType(),
                'from_date' => $hour->getFromDate()->format('Y/m/d'),
                'to_date'   => $hour->getToDate() ? $hour->getToDate()->format('Y/m/d') : null,
                'time'      => $hour->getTime()->format('H:i'),
            ];
        }

        $this->data->set('values', $values);

        $this->data->set('form', new Form\TheaterOpeningHourForm());
    }

    /**
     * opening hour update action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeOpeningHourUpdate($request, $response, $args)
    {
        $theater = $this->em->getRepository(Entity\Theater::class)->findOneById($args['id']);

        if (is_null($theater)) {
            throw new NotFoundException($request, $response);
        }

        /**@var Entity\Theater $theater */

        $form = new Form\TheaterOpeningHourForm();
        $form->setData($request->getParams());

        if (!$form->isValid()) {
            $this->data->set('theater', $theater);
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);

            return 'openingHourEdit';
        }

        $cleanData = $form->getData();
        $openingHours = [];

        foreach ($cleanData['hours'] as $hourValues) {
            $openingHours[] = Entity\TheaterOpeningHour::create($hourValues);
        }

        $theater->getMeta()->setOpeningHours($openingHours);

        $this->em->flush();

        $this->logger->info('Updated theater opening hour', [
            'theater_id' => $theater->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('「%s」の開館時間を編集しました。', $theater->getNameJa()),
        ]);

        $this->redirect(
            $this->router->pathFor('opening_hour_edit', [ 'id' => $theater->getId() ]),
            303
        );
    }
}
