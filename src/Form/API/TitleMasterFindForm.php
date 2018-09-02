<?php
/**
 * TitleMasterFindForm.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form\API;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

use Doctrine\ORM\EntityManager;

use Cinemasunshine\PortalAdmin\Form\BaseForm;
use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * TitleMaster find form class
 */
class TitleMasterFindForm extends BaseForm
{
    /** @var EntityManager */
    protected $em;
    
    /** @var array */
    protected $theaterChoices;
    
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
        $theaters = $this->em
            ->getRepository(Entity\Theater::class)
            ->findByMasterVersion(Entity\Theater::MASTER_VERSION_V2);
        
        foreach ($theaters as $theater) {
            /** @var Entity\Theater $theater */
            $this->theaterChoices[$theater->getId()] = $theater->getNameJa();
        }
        
        $this->add([
            'name' => 'theater',
            'type' => 'Select',
            'options' => [
                'value_options' => $this->theaterChoices,
            ],
        ]);
        
        $this->add([
            'name' => 'name',
            'type' => 'Text',
        ]);
        
        $inputFilter = new InputFilter();
        
        $inputFilter->add([
            'name' => 'theater',
            'required' => true,
        ]);
        
        $inputFilter->add([
            'name' => 'name',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 2, // ある程度の文字数を入力させる（レスポンスを減らすため）
                    ],
                ],
            ],
        ]);
        
        $this->setInputFilter($inputFilter);
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