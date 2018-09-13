<?php
/**
 * TrailerFindForm.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

use Doctrine\ORM\EntityManager;

use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * Trailer find form class
 */
class TrailerFindForm extends BaseForm
{
    /** @var EntityManager */
    protected $em;
    
    /** @var array */
    protected $pageChoices = [];
    
    /** @var array */
    protected $theaterChoices = [];
    
    /**
     * construct
     * 
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        
        parent::__construct();
        
        $this->setup();
    }
    
    /**
     * setup
     *
     * @return void
     */
    protected function setup()
    {
        $this->add([
            'name' => 'name',
            'type' => 'Text',
        ]);
        
        $pages = $this->em->getRepository(Entity\Page::class)->findActive();
        
        foreach ($pages as $page) {
            /** @var Entity\Page $page */
            $this->pageChoices[$page->getId()] = $page->getNameJa();
        }
        
        $this->add([
            'name' => 'page',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->pageChoices,
            ],
        ]);
        
        $theaters = $this->em->getRepository(Entity\Theater::class)->findActive();
        
        foreach ($theaters as $theater) {
            /** @var Entity\Theater $theater */
            $this->theaterChoices[$theater->getId()] = $theater->getNameJa();
        }
        
        $this->add([
            'name' => 'theater',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->theaterChoices,
            ],
        ]);
        
        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'name',
            'required' => false,
        ]);
        
        $inputFilter->add([
            'name' => 'page',
            'required' => false,
        ]);
        
        $inputFilter->add([
            'name' => 'theater',
            'required' => false,
        ]);
        
        $this->setInputFilter($inputFilter);
    }
    
    /**
     * return page choices
     *
     * @return array
     */
    public function getPageChoices()
    {
        return $this->pageChoices;
    }
    
    /**
     * return theater choices
     *
     * @return array
     */
    public function getTheaterChoices()
    {
        return $this->theaterChoices;
    }
}