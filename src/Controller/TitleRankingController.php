<?php

/**
 * TitleRankingController.php
 */

namespace App\Controller;

use App\Exception\ForbiddenException;
use App\Form;
use App\ORM\Entity;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * TitleRanking controller
 */
class TitleRankingController extends BaseController
{
    protected function findEntity(): Entity\TitleRanking
    {
        $entity = $this->em->find(Entity\TitleRanking::class, 1);

        if (! $entity) {
            throw new \LogicException('TitleRanking does not exist.');
        }

        return $entity;
    }

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
     * edit action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeEdit(Request $request, Response $response, array $args)
    {
        $titleRanking = $this->findEntity();

        $fromDate = $titleRanking->getFromDate();
        $toDate   = $titleRanking->getToDate();

        $values = [
            'from_date' => $fromDate ? $fromDate->format('Y/m/d') : null,
            'to_date'   => $toDate ? $toDate->format('Y/m/d') : null,
            'ranks'     => [],
        ];

        for ($rank = 1; $rank <= 5; $rank++) {
            /** @var Entity\Title|null $title */
            $title = $titleRanking->getRank($rank);

            if (! $title) {
                continue;
            }

            $values['ranks'][$rank]['title_id']   = $title->getId();
            $values['ranks'][$rank]['title_name'] = $title->getName();
        }

        return $this->renderEdit($response, ['values' => $values]);
    }

    /**
     * @param Response $response
     * @param array    $data
     * @return Response
     */
    protected function renderEdit(Response $response, array $data = [])
    {
        return $this->render($response, 'title_ranking/edit.html.twig', $data);
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
        $form = new Form\TitleRankingForm();
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            return $this->renderEdit($response, [
                'form' => $form,
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
        }

        $cleanData = $form->getData();

        $titleRanking = $this->findEntity();
        $titleRanking->setFromDate($cleanData['from_date']);
        $titleRanking->setToDate($cleanData['to_date']);

        foreach ($cleanData['ranks'] as $rank => $rankValues) {
            $title = null;

            if ($rankValues['title_id']) {
                $title = $this->em
                    ->getRepository(Entity\Title::class)
                    ->findOneById($rankValues['title_id']);
            }

            $titleRanking->setRank((int) $rank, $title);
        }

        $this->em->flush();

        $this->logger->info('Updated TitleRanking "{id}"', [
            'id' => $titleRanking->getId(),
            'admin_user' => $this->auth->getUser()->getId(),
        ]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => 'ランキング情報を更新しました。',
        ]);

        $this->redirect($this->router->pathFor('title_ranking_edit'), 303);
    }
}
