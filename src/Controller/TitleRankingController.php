<?php

/**
 * TitleRankingController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Controller;

use App\Exception\ForbiddenException;
use App\Form;
use App\ORM\Entity;

/**
 * TitleRanking controller
 */
class TitleRankingController extends BaseController
{
    /**
     * @return Entity\TitleRanking
     */
    protected function findEntity(): Entity\TitleRanking
    {
        $entity = $this->em->find(Entity\TitleRanking::class, 1);

        if (! $entity) {
            throw new \LogicException('TitleRanking does not exist.');
        }

        return $entity;
    }

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
     * edit action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeEdit($request, $response, $args)
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
            $title = $titleRanking->getRank($rank);

            if (! $title) {
                continue;
            }

            /** @var Entity\Title $title */
            $values['ranks'][$rank]['title_id']   = $title->getId();
            $values['ranks'][$rank]['title_name'] = $title->getName();
        }

        $this->data->set('values', $values);
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
        $form = new Form\TitleRankingForm();
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);

            return 'edit';
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
