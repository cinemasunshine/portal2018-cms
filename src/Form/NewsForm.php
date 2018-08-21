<?php
/**
 * NewsForm.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

/**
 * News form class
 */
class NewsForm extends BaseForm
{
    const TYPE_NEW = 1;
    const TYPE_EDIT = 2;
    
    const CATEGORY_NEWS = '1';
    const CATEGORY_INFO = '2';
    
    /** @var int */
    protected $type;
    
    /** @var array */
    protected $categoryChoices = [
        self::CATEGORY_NEWS => 'NEWS',
        self::CATEGORY_INFO => 'インフォメーション',
    ];
    
    /**
     * construct
     * 
     * @param int $type
     */
    public function __construct(int $type)
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
            'name' => 'category',
            'type' => 'Radio',
            'options' => [
                'value_options' => $this->categoryChoices,
            ],
        ]);
        
        $this->add([
            'name' => 'title_id',
            'type' => 'Hidden',
        ]);
        
        // 作品名を表示するため
        $this->add([
            'name' => 'title_name',
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
            'name' => 'headline',
            'type' => 'Text',
        ]);
        
        $this->add([
            'name' => 'body',
            'type' => 'Textarea',
        ]);
        
        $this->add([
            'name' => 'image',
            'type' => 'File',
        ]);
        
        
        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'category',
            'required' => true,
        ]);
        
        $inputFilter->add([
            'name' => 'title_id',
            'required' => false,
        ]);
        
        $inputFilter->add([
            'name' => 'title_name',
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
            'name' => 'headline',
            'required' => true,
        ]);
        
        $inputFilter->add([
            'name' => 'body',
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
    
    /**
     * return category choices
     *
     * @return array
     */
    public function getCategoryChoices()
    {
        return $this->categoryChoices;
    }
}