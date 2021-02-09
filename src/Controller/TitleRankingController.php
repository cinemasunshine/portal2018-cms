<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ForbiddenException;
use App\Form;
use App\ORM\Entity;
use LogicException;
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
                    ->findOneById((int) $rankValues['title_id']);
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
