<?php

/**
 * TheaterMetaController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Controller;

use App\Form;
use App\ORM\Entity;
use Cinemasunshine\ORM\Entities\TheaterOpeningHour;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * TheaterMeta controller
 */
class TheaterMetaController extends BaseController
{
    /**
     * opening hour action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeOpeningHour(Request $request, Response $response, array $args)
    {
        $user       = $this->auth->getUser();
        $repository = $this->em->getRepository(Entity\TheaterMeta::class);

        if ($user->isTheater()) {
            $metas = [$repository->findOneByTheaterId($user->getTheater()->getId())];
        } else {
            $metas = $repository->findActive();
        }

        return $this->render($response, 'theater_meta/opening_hour/list.html.twig', ['metas' => $metas]);
    }

    /**
     * opening hour edit action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeOpeningHourEdit(Request $request, Response $response, array $args)
    {
        $theater = $this->em->getRepository(Entity\Theater::class)->findOneById($args['id']);

        if (is_null($theater)) {
            throw new NotFoundException($request, $response);
        }

        /**@var Entity\Theater $theater */

        $values = [
            'hours' => [],
        ];

        foreach ($theater->getMeta()->getOpeningHours() as $hour) {
            /** @var TheaterOpeningHour $hour */
            $values['hours'][] = [
                'type'      => $hour->getType(),
                'from_date' => $hour->getFromDate()->format('Y/m/d'),
                'to_date'   => $hour->getToDate() ? $hour->getToDate()->format('Y/m/d') : null,
                'time'      => $hour->getTime()->format('H:i'),
            ];
        }

        $form = new Form\TheaterOpeningHourForm();

        return $this->renderOpeningHourEdit($response, [
            'theater' => $theater,
            'form' => $form,
            'values' => $values,
        ]);
    }

    /**
     * @param Response $response
     * @param array    $data
     * @return Response
     */
    protected function renderOpeningHourEdit(Response $response, array $data = [])
    {
        return $this->render($response, 'theater_meta/opening_hour/edit.html.twig', $data);
    }

    /**
     * opening hour update action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeOpeningHourUpdate(Request $request, Response $response, array $args)
    {
        $theater = $this->em->getRepository(Entity\Theater::class)->findOneById($args['id']);

        if (is_null($theater)) {
            throw new NotFoundException($request, $response);
        }

        /**@var Entity\Theater $theater */

        $form = new Form\TheaterOpeningHourForm();
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            return $this->renderOpeningHourEdit($response, [
                'theater' => $theater,
                'form' => $form,
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
        }

        $cleanData    = $form->getData();
        $openingHours = [];

        foreach ($cleanData['hours'] as $hourValues) {
            $openingHours[] = TheaterOpeningHour::create($hourValues);
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
            $this->router->pathFor('opening_hour_edit', ['id' => $theater->getId()]),
            303
        );
    }
}
