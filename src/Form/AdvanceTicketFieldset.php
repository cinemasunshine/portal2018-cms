<?php
/**
 * AdvanceTicketFieldset.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Form;

use zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator;

/**
 * AdvanceTicket fieldset class
 */
class AdvanceTicketFieldset extends Fieldset implements InputFilterProviderInterface
{
    /** @var array */
    protected static $typeChoices = [
        '1' => 'ムビチケ',
        '2' => '紙券',
    ];
    
    /** @var array */
    protected static $specialGiftStockChoices = [
        '1' => '有り',
        '2' => '無し',
    ];
    
    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct('campaign');
        
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
            'name' => 'release_date',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);
        
        $this->add([
            'name' => 'release_date_text',
            'type' => 'Text',
        ]);
        
        $this->add([
            'name' => 'is_sales_end',
            'type' => 'Checkbox',
            'options' => [
                'checked_value' => '1',
                'unchecked_value' => '0',
            ],
        ]);
        
        $this->add([
            'name' => 'type',
            'type' => 'Radio',
            'options' => [
                'value_options' => self::$typeChoices,
            ],
        ]);
        
        $this->add([
            'name' => 'price_text',
            'type' => 'Text',
        ]);
        
        $this->add([
            'name' => 'special_gift',
            'type' => 'Text',
        ]);
        
        $this->add([
            'name' => 'special_gift_stock',
            'type' => 'Select',
            'options' => [
                'empty_option' => '',
                'value_options' => self::$specialGiftStockChoices,
            ],
        ]);
        
        $this->add([
            'name' => 'special_gift_image',
            'type' => 'File',
        ]);
    }
    
    /**
     * return inpu filter specification
     * 
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'release_date' => [
                'required' => false,
                'validators' => [
                    [
                        'name' => Validator\Date::class,
                        'options' => [
                            'format' => 'Y/m/d H:i',
                        ],
                    ],
                ],
            ],
            'release_date_text' => [
                'required' => false,
            ],
            'is_sales_end' => [
                'required' => false,
            ],
            'type' => [
                'required' => true,
            ],
            'price_text' => [
                'required' => true,
            ],
            'special_gift' => [
                'required' => false,
            ],
            'special_gift_stock' => [
                'required' => false,
            ],
            'special_gift_image' => [
                'required' => false,
                'allow_empty' => true,
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
            ],
        ];
    }
    
    /**
     * return type choices
     *
     * @return array
     */
    public static function getTypeChoices()
    {
        return self::$typeChoices;
    }
    
    /**
     * return special_gift_stock choices
     *
     * @return array
     */
    public static function getSpecialGiftStockChoices()
    {
        return self::$specialGiftStockChoices;
    }
}