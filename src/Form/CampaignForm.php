<?php
/**
 * CampaignForm.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

/**
 * Campaign form class
 */
class CampaignForm extends BaseForm
{
    const TYPE_NEW = 1;
    const TYPE_EDIT = 2;
    
    /** @var int */
    protected $type;
    
    /**
     * construct
     * 
     * @param int $type
     */
    public function __construct($type)
    {
        $this->type = $type;
        
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
        
        $this->add([
            'name' => 'title_id',
            'type' => 'Hidden',
        ]);
        
        $this->add([
            'name' => 'start_dt',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);
        
        $this->add([
            'name' => 'end_dt',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);
        
        $this->add([
            'name' => 'url',
            'type' => 'Url',
        ]);
        
        $this->add([
            'name' => 'image',
            'type' => 'File',
        ]);
        
        
        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'name',
            'required' => true,
        ]);
        
        $inputFilter->add([
            'name' => 'title_id',
            'required' => false,
        ]);
        
        $inputFilter->add([
            'name' => 'start_dt',
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
            'name' => 'end_dt',
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
            'name' => 'url',
            'required' => true,
        ]);
        
        $inputFilter->add([
            'name' => 'image',
            'required' => ($this->type === self::TYPE_NEW),
            'validators' => [
                [
                    'name' => Validator\File\Size::class,
                    'options' => [
                        'max' => '200KB', // @todo 調整
                    ],
                ],
                [
                    'name' => Validator\File\MimeType::class,
                    'options' => [
                        'mimeType' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                    ],
                ],
            ],
        ]);
        
        $this->setInputFilter($inputFilter);
    }
}