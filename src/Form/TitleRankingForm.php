<?php
/**
 * TitleRankingForm.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

/**
 * TitleRanking form class
 */
class TitleRankingForm extends BaseForm
{
    /** @var RankFieldset */
    protected $rankFieldset;
    
    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->rankFieldset = new RankFieldset();
        
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
            'name' => 'from_date',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);
        
        $this->add([
            'name' => 'to_date',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);
        
        $this->add([
            'name' => 'ranks',
            'type' => 'Collection',
            'options' => [
                'target_element' => $this->rankFieldset,
            ],
        ]);
        
        $inputFilter = new InputFilter();
        
        $inputFilter->add([
            'name' => 'from_date',
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
            'name' => 'to_date',
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
        
        $this->setInputFilter($inputFilter);
    }
}