<?php
/**
 * TitleController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller;

use Slim\Exception\NotFoundException;

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
        $title->setUniversal($cleanData['universal'] ?? []);
        $title->setIsDeleted(false);
        
        $this->em->persist($title);
        $this->em->flush();
        
        $this->redirect(
            $this->router->pathFor('title_edit', [ 'id' => $title->getId() ]),
            303);
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
        $title = $this->em->find(Title::class, $args['id']);
        
        if (is_null($title)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Title $title */
        
        $this->data->set('title', $title);
        
        $form = new TitleForm();
        $this->data->set('form', $form);
        
        $values = [
            'id'           => $title->getId(),
            'name'         => $title->getName(),
            'name_kana'    => $title->getNameKana(),
            'name_en'      => $title->getNameEn(),
            'credit'       => $title->getCredit(),
            'catchcopy'    => $title->getCatchcopy(),
            'introduction' => $title->getIntroduction(),
            'director'     => $title->getDirector(),
            'cast'         => $title->getCast(),
            'website'      => $title->getWebsite(),
            'rating'       => $title->getRating(),
            'universal'    => $title->getUniversal(),
        ];
        
        $publishingExpectedDate = $title->getPublishingExpectedDate();
        
        if ($publishingExpectedDate instanceof \DateTime) {
            $values['publishing_expected_date'] = $publishingExpectedDate->format('Y/m/d');
            $values['not_exist_publishing_expected_date'] = null;
        } else {
            $values['publishing_expected_date'] = null;
            $values['not_exist_publishing_expected_date'] = '1';
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
        $title = $this->em->find(Title::class, $args['id']);
        
        if (is_null($title)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Title $title */
        
        $form = new TitleForm();
        $form->setData($request->getParams());
        
        if (!$form->isValid()) {
            $this->data->set('title', $title);
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);
            
            return 'edit';
        }
        
        $cleanData = $form->getData();
        
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
        $title->setUniversal($cleanData['universal'] ?? []);
        $title->setIsDeleted(false);
        
        $this->em->persist($title);
        $this->em->flush();
        
        $this->redirect(
            $this->router->pathFor('title_edit', [ 'id' => $title->getId() ]),
            303);
    }
    
    /**
     * delete action
     * 
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeDelete($request, $response, $args)
    {
        $title = $this->em->find(Title::class, $args['id']);
        
        if (is_null($title)) {
            throw new NotFoundException($request, $response);
        }
        
        /**@var Title $title */
        
        $title->setIsDeleted(true);
        
        // 関連データの処理はイベントで対応する
        
        $this->em->persist($title);
        $this->em->flush();
        
        return $this->redirect($this->router->pathFor('title_list'), 303);
    }
}