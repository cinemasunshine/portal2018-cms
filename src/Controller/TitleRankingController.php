<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ForbiddenException;
use App\Form\TitleRankingForm;
use App\ORM\Entity\Title;
use App\ORM\Entity\TitleRanking;
use LogicException;
use Slim\Http\Request;
use Slim\Http\Response;

class TitleRankingController extends BaseController
{
    protected function findOneEntity(): TitleRanking
    {
        $entity = $this->em->find(TitleRanking::class, 1);

        if (! $entity) {
            throw new LogicException('TitleRanking does not exist.');
        }

        return $entity;
    }

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
     * edit action
     *
     * @param array<string, mixed> $args
     */
    public function executeEdit(Request $request, Response $response, array $args): Response
    {
        $titleRanking = $this->findOneEntity();

        $fromDate = $titleRanking->getFromDate();
        $toDate   = $titleRanking->getToDate();

        $values = [
            'from_date' => $fromDate ? $fromDate->format('Y/m/d') : null,
            'to_date'   => $toDate ? $toDate->format('Y/m/d') : null,
            'ranks'     => [],
        ];

        $ranks = $titleRanking->getRanks();

        foreach ($ranks as $rank) {
            $rankValues = [
                'id' => $rank->getId(),
                'rank' => $rank->getRank(),
                'title_id' => null,
                'title_name' => null,
                'detail_url' => $rank->getDetailUrl(),
            ];

            $title = $rank->getTitle();

            if ($title) {
                $rankValues['title_id']   = $title->getId();
                $rankValues['title_name'] = $title->getName();
            }

            $values['ranks'][$rank->getRank()] = $rankValues;
        }

        return $this->renderEdit($response, [
            'values' => $values,
            'title_ranking' => $titleRanking,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function renderEdit(Response $response, array $data = []): Response
    {
        return $this->render($response, 'title_ranking/edit.html.twig', $data);
    }

    /**
     * update action
     *
     * @param array<string, mixed> $args
     */
    public function executeUpdate(Request $request, Response $response, array $args): Response
    {
        $titleRanking = $this->findOneEntity();

        $form = new TitleRankingForm();
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            return $this->renderEdit($response, [
                'title_ranking' => $titleRanking,
                'form' => $form,
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
        }

        $cleanData = $form->getData();

        $titleRanking->setFromDate($cleanData['from_date']);
        $titleRanking->setToDate($cleanData['to_date']);

        $ranks = $titleRanking->getRanks();

        foreach ($ranks as $rank) {
            $rankValues = $cleanData['ranks'][$rank->getRank()];

            $rank->setDetailUrl($rankValues['detail_url']);

            $title = null;

            if ($rankValues['title_id']) {
                $title = $this->em
                    ->getRepository(Title::class)
                    ->findOneById((int) $rankValues['title_id']);
            }

            $rank->setTitle($title);
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
