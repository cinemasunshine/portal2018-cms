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

use Cinemasunshine\PortalAdmin\ORM\Entity\AdvanceTicket;

/**
 * AdvanceTicket fieldset class
 */
class AdvanceTicketFieldset extends Fieldset implements InputFilterProviderInterface
{
    /** @var array */
    protected $typeChoices;
    
    /** @var array */
    protected $specialGiftStockChoices;
    
    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct('advance_ticket');
        
        $this->typeChoices = AdvanceTicket::getTypes();
        $this->specialGiftStockChoices = AdvanceTicket::getSpecialGiftStockList();
        
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
            'name' => 'release_dt',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);
        
        $this->add([
            'name' => 'release_dt_text',
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
                'value_options' => $this->typeChoices,
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
                'value_options' => $this->specialGiftStockChoices,
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
            'release_dt' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => Validator\Date::class,
                        'options' => [
                            'format' => 'Y/m/d H:i',
                        ],
                    ],
                ],
            ],
            'release_dt_text' => [
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
    public function getTypeChoices()
    {
        return $this->typeChoices;
    }
    
    /**
     * return special_gift_stock choices
     *
     * @return array
     */
    public function getSpecialGiftStockChoices()
    {
        return $this->specialGiftStockChoices;
    }
}