<?php
/**
 * ScheduleController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Cinemasunshine\PortalAdmin\Form;
use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * Schedule controller
 */
class ScheduleController extends BaseController
{
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
        
        $form = new Form\ScheduleFindForm();
        $this->data->set('form', $form);
        
        $form->setData($request->getParams());
        $cleanValues = [];
        
        if ($form->isValid()) {
            $cleanValues = $form->getData();
            $values = $cleanValues;
        } else {
            $values = $request->getParams();
            $this->data->set('errors', $form->getMessages());
        }
        
        $this->data->set('values', $values);
        $this->data->set('params', $cleanValues);
        
        /** @var \Cinemasunshine\PortalAdmin\Pagination\DoctrinePaginator $pagenater */
        $pagenater = $this->em->getRepository(Entity\Schedule::class)->findForList($cleanValues, $page);
        
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
        $this->data->set('form', new Form\ScheduleForm($this->em));
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
        $form = new Form\ScheduleForm($this->em);
        $form->setData($request->getParams());
        
        if (!$form->isValid()) {
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);
            
            return 'new';
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
            $format->setVoice($formatData['voice']);
        }
        
        $this->em->flush();
        
        // @todo 編集ページへリダイレクト
        exit;
    }
}
