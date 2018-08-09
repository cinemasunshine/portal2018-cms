<?php
/**
 * TitleController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Cinemasunshine\PortalAdmin\Form\TitleForm;
use Cinemasunshine\PortalAdmin\ORM\Entity\Title;

/**
 * Title controller
 */
class TitleController extends BaseController
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
        /** @var Title[] $titles */
        $titles = $this->em->getRepository(Title::class)->findByActive();
        $this->data->set('titles', $titles);
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
        $form = new TitleForm();
        $this->data->set('form', $form);
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
        $form = new TitleForm();
        $form->setData($request->getParams());
        
        if (!$form->isValid()) {
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);
            
            return 'new';
        }
        
        $cleanData = $form->getData();
        
        $title = new Title();
        $title->setName($cleanData['name']);
        $title->setNameKana($cleanData['name_kana']);
        $title->setNameEn($cleanData['name_en']);
        $title->setCredit($cleanData['credit']);
        $title->setCatchcopy($cleanData['catchcopy']);
        $title->setIntroduction($cleanData['introduction']);
        $title->setDirector($cleanData['director']);
        $title->setCast($cleanData['cast']);
        $title->setPublishingExpectedDate($cleanData['publishing_expected_date']);
        $title->setWebsite($cleanData['website']);
        $title->setRating((int) $cleanData['rating']);
        $title->setUniversal($cleanData['universal']);
        $title->setIsDeleted(false);
        
        $this->em->persist($title);
        $this->em->flush();
        
        echo '登録しました。';
        exit;
    }
}