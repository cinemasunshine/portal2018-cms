<?php

namespace App\Form;

use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

/**
 * AdvanceTicket find form class
 */
class AdvanceTicketFindForm extends BaseForm
{
    public const STATUS_PRE_SALE = 1;
    public const STATUS_SALE     = 2;
    public const STATUS_SALE_END = 3;

    /** @var array */
    protected $statusChoices = [
        self::STATUS_SALE     => '販売中',
        self::STATUS_PRE_SALE => '販売予定',
        self::STATUS_SALE_END => '販売終了',
    ];

    /**
     * construct
     */
    public function __construct()
    {
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
            'name' => 'status',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->statusChoices,
            ],
        ]);

        $this->add([
            'name' => 'release_dt',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);

        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'status',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'release_dt',
            'required' => false,
            'validators' => [
                [
                    'name' => Validator\Date::class,
                    'options' => ['format' => 'Y/m/d H:i'],
                ],
            ],
        ]);

        $this->setInputFilter($inputFilter);
    }

    /**
     * return status choices
     *
     * @return array
     */
    public function getStatusChoices()
    {
        return $this->statusChoices;
    }
}
