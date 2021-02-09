<?php

declare(strict_types=1);

namespace App\Form;

use App\ORM\Entity\AdvanceTicket;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator;

/**
 * AdvanceTicket fieldset class
 */
class AdvanceTicketFieldset extends Fieldset implements InputFilterProviderInterface
{
    /** @var array<int, string> */
    protected $typeChoices;

    /** @var array<int, string> */
    protected $specialGiftStockChoices;

    public function __construct()
    {
        parent::__construct('advance_ticket');

        $this->typeChoices             = AdvanceTicket::getTypes();
        $this->specialGiftStockChoices = AdvanceTicket::getSpecialGiftStockList();

        $this->setup();
    }

    protected function setup(): void
    {
        $this->add([
            'name' => 'id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'delete_special_gift_image',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'publishing_start_dt',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);

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
     * @return array<string, mixed>
     */
    public function getInputFilterSpecification(): array
    {
        $specification = [
            'id' => ['required' => false],
            'publishing_start_dt' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => Validator\Date::class,
                        'options' => ['format' => 'Y/m/d H:i'],
                    ],
                ],
            ],
            'release_dt' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => Validator\Date::class,
                        'options' => ['format' => 'Y/m/d H:i'],
                    ],
                ],
            ],
            'release_dt_text' => ['required' => false],
            'is_sales_end' => ['required' => false],
            'type' => ['required' => true],
            'price_text' => ['required' => true],
            'special_gift' => ['required' => false],
            'special_gift_stock' => ['required' => false],
            'special_gift_image' => [
                'required' => false,
                'allow_empty' => true,
                'validators' => [
                    [
                        'name' => Validator\File\Size::class,
                        'options' => ['max' => '10MB'], // SASAKI-245
                    ],
                    [
                        'name' => Validator\File\MimeType::class,
                        'options' => [
                            'mimeType' => AdvanceSaleForm::$imageMimeTypes,
                        ],
                    ],
                ],
            ],
            'delete_special_gift_image' => ['required' => false],
        ];

        return $specification;
    }

    /**
     * @return array<int, string>
     */
    public function getTypeChoices(): array
    {
        return $this->typeChoices;
    }

    /**
     * @return array<int, string>
     */
    public function getSpecialGiftStockChoices(): array
    {
        return $this->specialGiftStockChoices;
    }
}
