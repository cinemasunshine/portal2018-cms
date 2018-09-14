<?php
/**
 * TitleRankingController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Slim\Exception\NotFoundException;

use Cinemasunshine\PortalAdmin\Form;
use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * TitleRanking controller
 */
class TitleRankingController extends BaseController
{
    /** @var Entity\TitleRanking */
    protected $titleRanking;
    
    /**
     * return entity
     *
     * @return Entity\TitleRanking|null
     */
    protected function getEntity()
    {
        if (!$this->titleRanking) {
            $this->titleRanking = $this->em->find(Entity\TitleRanking::class, 1);
        }
        
        return $this->titleRanking;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function preExecute($request, $response): void
    {
        $titleRanking = $this->getEntity();
        
        if (!$titleRanking) {
            throw new NotFoundException($request, $response);
        }
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
        $titleRanking = $this->getEntity();
        
        $fromDate = $titleRanking->getFromDate();
        $toDate = $titleRanking->getToDate();
        
        $values = [
            'from_date' => $fromDate ? $fromDate->format('Y/m/d') : null,
            'to_date'   => $toDate ? $toDate->format('Y/m/d') : null,
            'ranks'     => [],
        ];
        
        for ($rank = 1; $rank <= 5; $rank++) { 
            $title = $titleRanking->getRank($rank);
            
            if (!$title) {
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
        
        if (!$form->isValid()) {
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);
            
            return 'edit';
        }
        
        $cleanData = $form->getData();
        
        $titleRanking = $this->getEntity();
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
        
        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => 'ランキング情報を更新しました。',
        ]);
        
        return $this->redirect($this->router->pathFor('title_ranking_edit'), 303);
    }
}
