<?php
/**
 * ScheduleForm.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

use Doctrine\ORM\EntityManager;

use Cinemasunshine\PortalAdmin\ORM\Entity\Theater;

/**
 * Schedule form class
 */
class ScheduleForm extends BaseForm
{
    /** @var EntityManager */
    protected $em;
    
    /** @var array */
    protected $theaterChoices;
    
    /** @var ShowingFormatFieldset */
    protected $showingFormatFieldset;
    
    /**
     * construct
     * 
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        
        parent::__construct();
        
        $this->theaterChoices = [];
        $this->showingFormatFieldset = new ShowingFormatFieldset();
        
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
            'name' => 'title_id',
            'type' => 'Hidden',
        ]);
        
        $this->add([
            'name' => 'start_date',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);
        
        $this->add([
            'name' => 'end_date',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);
        
        $this->add([
            'name' => 'public_start_dt',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);
        
        $this->add([
            'name' => 'public_end_dt',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);
        
        $this->add([
            'name' => 'remark',
            'type' => 'Textarea',
        ]);
        
        $theaters = $this->em->getRepository(Theater::class)->findActive();
        
        foreach ($theaters as $theater) {
            /** @var Theater $theater */
            $this->theaterChoices[$theater->getId()] = $theater->getNameJa();
        }
        
        $this->add([
            'name' => 'theater',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->theaterChoices,
            ],
        ]);
        
        $this->add([
            'name' => 'formats',
            'type' => 'Collection',
            'options' => [
                'target_element' => $this->showingFormatFieldset,
            ],
        ]);
        
        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'title_id',
            'required' => true,
        ]);
        
        $inputFilter->add([
            'name' => 'start_date',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Date::class,
                    'options' => [
                        'format' => 'Y/m/d',
                    ],
                ],
            ],
        ]);
        
        $inputFilter->add([
            'name' => 'end_date',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Date::class,
                    'options' => [
                        'format' => 'Y/m/d',
                    ],
                ],
            ],
        ]);
        
        $inputFilter->add([
            'name' => 'public_start_dt',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Date::class,
                    'options' => [
                        'format' => 'Y/m/d H:i',
                    ],
                ],
            ],
        ]);
        
        $inputFilter->add([
            'name' => 'public_end_dt',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Date::class,
                    'options' => [
                        'format' => 'Y/m/d H:i',
                    ],
                ],
            ],
        ]);
        
        $inputFilter->add([
            'name' => 'theater',
            'required' => true,
        ]);
        
        $inputFilter->add([
            'name' => 'remark',
            'required' => false,
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
    
    /**
     * return format Fieldset
     *
     * @return ShowingFormatFieldset
     */
    public function getShowingFormatFieldset()
    {
        return $this->showingFormatFieldset;
    }
}
